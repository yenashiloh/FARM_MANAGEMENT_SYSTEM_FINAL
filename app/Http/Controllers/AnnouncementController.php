<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    // show Announcement Page
    public function showAnnouncementPage(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $userLoginId = $user->user_login_id;
        $userRole = $user->role;
    
        $notifications = Notification::where('user_login_id', $userLoginId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
        $folders = FolderName::all();
        $folder = FolderName::first();
    
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(5);
    
        foreach ($announcements as $announcement) {
            $emails = explode(', ', $announcement->type_of_recepient);
            if (count($emails) > 3) {
                $announcement->displayEmails = array_slice($emails, 0, 3);
                $announcement->moreEmailsCount = count($emails) - 3;
            } else {
                $announcement->displayEmails = $emails;
                $announcement->moreEmailsCount = 0;
            }
        }
    
        if ($request->ajax()) {
            return view('admin.announcement.admin-announcement', [
                'announcements' => $announcements,
                'notifications' => $notifications,
                'firstName' => $firstName,
                'surname' => $surname,
                'notificationCount' => $notificationCount,
            ])->render();
        }
    
        return view('admin.announcement.admin-announcement', [
            'announcements' => $announcements,
            'folders' => $folders,
            'folder' => $folder,
            'facultyEmails' => $facultyEmails,
            'firstName' => $firstName,
            'surname' => $surname,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }
    
    
    //show Add Announcement Page
    public function showAddAnnouncementPage()
    {
        $folders = FolderName::all();
        $folder = FolderName::first();
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $facultyUsers = UserLogin::where('role', 'faculty')
            ->orWhere('role', 'faculty-coordinator')
            ->get();
    
        $departments = Department::whereNotNull('department_id')
            ->whereNotNull('name')
            ->get();
    
        return view('admin.announcement.add-announcement', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyUsers' => $facultyUsers,
            'firstName' => $firstName,
            'surname' => $surname,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'departments' => $departments,
        ]);
    }
      
    //save the Announcement
    public function saveAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announcement_subject' => 'required',
            'announcement_message' => 'required',
            'recipient_emails' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $recipientEmails = $request->input('recipient_emails', []);
        $facultyEmails = [];
        $recipientIds = [];
        $recipientEmailsString = '';
        $departmentId = null;

        if (in_array('all-faculty', $recipientEmails)) {
            // Handle "All Faculty" case
            $recipientEmailsString = 'All Faculty';
            $facultyUsers = UserLogin::where('role', 'faculty')
                ->orWhere('role', 'faculty-coordinator')
                ->get();
            $facultyEmails = $facultyUsers->pluck('email')->toArray();
            $recipientIds = $facultyUsers->pluck('user_login_id')->toArray();
        } else {
            // Handle department and individual selections
            foreach ($recipientEmails as $recipient) {
                if (strpos($recipient, 'department-') === 0) {
                    // Handle department selection
                    $departmentId = str_replace('department-', '', $recipient);
                    $department = Department::find($departmentId);
                    
                    if ($department) {
                        if (empty($recipientEmailsString)) {
                            $recipientEmailsString = $department->name;
                        } else {
                            $recipientEmailsString .= ', ' . $department->name;
                        }

                        $departmentFaculty = UserLogin::where(function($query) {
                                $query->where('role', 'faculty')
                                    ->orWhere('role', 'faculty-coordinator');
                            })
                            ->where('department_id', $departmentId)
                            ->get();
                        
                        $facultyEmails = array_merge($facultyEmails, $departmentFaculty->pluck('email')->toArray());
                        $recipientIds = array_merge($recipientIds, $departmentFaculty->pluck('user_login_id')->toArray());
                    }
                } else {
                    $user = UserLogin::find($recipient);
                    if ($user) {
                        if (empty($recipientEmailsString)) {
                            $recipientEmailsString = $user->email;
                        } else {
                            $recipientEmailsString .= ', ' . $user->email;
                        }
                        
                        $facultyEmails[] = $user->email;
                        $recipientIds[] = $user->user_login_id;
                    }
                }
            }
        }

        $announcement = new Announcement();
        $announcement->subject = $request->input('announcement_subject');
        $announcement->message = $request->input('announcement_message');
        $announcement->published = false;
        $announcement->type_of_recepient = $recipientEmailsString ?: 'No Recipients Selected';
        $announcement->user_login_id = auth()->user()->user_login_id;

        if ($departmentId) {
            $announcement->department_id = $departmentId;
        }

        $announcement->save();

        if (!empty($facultyEmails)) {
            $this->sendAnnouncementEmails($announcement, array_unique($facultyEmails));
        }

        $request->session()->flash('success', 'Announcement Added Successfully!');
        return redirect()->route('admin.announcement.admin-announcement');
    }
        
    
    //send announcement
    protected function sendAnnouncementEmails($announcement, $recipients)
    {
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                \Log::warning("Invalid email address: {$email}");
                continue;
            }

        try {
            Mail::send('admin.emails.announcement', ['announcement' => $announcement], function ($message) use ($announcement, $email) {
                $message->to($email)
                    ->subject($announcement->subject);
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                });
            } catch (\Exception $e) {
                \Log::error("Failed to send email to {$email}: " . $e->getMessage());
            }
        }
    }
      
    //edit Page
    public function showEditPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
      
        $notifications = Notification::where('user_login_id', $user->user_login_id)
        ->orderBy('created_at', 'desc')
        ->get();
   
        $folders = FolderName::all();
          
        $folder = FolderName::first(); 
      
        return view('admin.announcement.edit-announcement', [
            'folders' => $folders,
            'folder' => $folder,
            'notifications' => $notifications,
        ]);
      }
  
    //display the edit form
    public function editAnnouncement($id_announcement)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $folders = FolderName::all();
        $departments = Department::whereNotNull('department_id')
                                ->whereNotNull('name')
                                ->get();
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
        $announcement = Announcement::findOrFail($id_announcement);
    
        return view('admin.announcement.edit-announcement', [
            'announcement' => $announcement,
            'folders' => $folders,
            'facultyEmails' => $facultyEmails,
            'departments' => $departments, 
            'firstName' => $firstName,
            'surname' => $surname,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }

    //update the announcement
    public function updateAnnouncement(Request $request, $id_announcement)
    {
        $validator = Validator::make($request->all(), [
            'announcement_subject' => 'required',
            'announcement_message' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $announcement = Announcement::findOrFail($id_announcement);
        $announcement->subject = $request->input('announcement_subject');
        $announcement->message = $request->input('announcement_message');
        
        // Retrieve the selected recipient emails
        $recipientEmails = $request->input('recipient_emails');
        
        // Handle the 'All Faculty' special case
        if (in_array('all-faculty', $recipientEmails)) {
            $announcement->type_of_recepient = 'All Faculty';
            $announcement->save();
            
            $request->session()->flash('success', 'Announcement Updated Successfully!');
            return redirect()->route('admin.announcement.admin-announcement');
        }
    
        // Separate departments and regular emails
        $departmentIds = [];
        $regularEmails = [];
        
        foreach ($recipientEmails as $recipient) {
            if (strpos($recipient, 'department-') === 0) {
                $departmentIds[] = str_replace('department-', '', $recipient);
            } else {
                $regularEmails[] = $recipient;
            }
        }
        
        // Get department names
        $departmentNames = Department::whereIn('department_id', $departmentIds)
            ->pluck('name')
            ->toArray();
        
        // Combine department names and regular emails
        $finalRecipients = array_merge($departmentNames, $regularEmails);
        
        // Save as comma-separated string
        $announcement->type_of_recepient = implode(', ', $finalRecipients);
        $announcement->save();
    
        $request->session()->flash('success', 'Announcement Updated Successfully!');
        return redirect()->route('admin.announcement.admin-announcement');
    }
    
    
    
  
    //delete the Announcement
    public function deleteAnnouncement($id_announcement)
    {
        $announcement = Announcement::findOrFail($id_announcement);
        $announcement->delete();
      
        return response()->json(['success' => 'Announcement deleted successfully.']);
    }
  
    //publish and unpublish the announcement
    public function publishAnnouncement($id_announcement)
    {
        $announcement = Announcement::findOrFail($id_announcement);
        $announcement->published = true;
        $announcement->save();
      
        return redirect()->back()->with('success', 'Announcement published successfully!');
    }
      
    //unpublish
    public function unpublishAnnouncement($id_announcement)
    {
        $announcement = Announcement::findOrFail($id_announcement);
        $announcement->published = false;
        $announcement->save();
      
        return redirect()->back()->with('success', 'Announcement unpublished successfully!');
    }
       
    //search announcement for admin
    public function searchAnnouncements(Request $request)
    {
        $search = $request->get('search');
      
        $announcements = Announcement::where('subject', 'LIKE', '%' . $search . '%')
            ->orWhere('message', 'LIKE', '%' . $search . '%')
            ->orWhere('type_of_recepient', 'LIKE', '%' . $search . '%')
            ->orWhereDate('created_at', 'LIKE', '%' . $search . '%') 
            ->orderBy('created_at', 'desc')
            ->get();
      
        foreach ($announcements as $announcement) {
            $emails = explode(', ', $announcement->type_of_recepient);
            if (count($emails) > 3) {
                $announcement->displayEmails = array_slice($emails, 0, 3);
                $announcement->moreEmailsCount = count($emails) - 3;
            } else {
                $announcement->displayEmails = $emails;
                $announcement->moreEmailsCount = 0;
            }
        }
      
        return view('admin.announcement.announcement-list', compact('announcements'));
    }

    //search announcement for faculty
    public function searchFacultyAnnouncements(Request $request)
    {
        $search = $request->get('search');
        \Log::info('Search term: ' . $search); // Log the search term
    
        $announcements = Announcement::where('subject', 'LIKE', '%' . $search . '%')
            ->orWhere('message', 'LIKE', '%' . $search . '%')
            ->orWhere('type_of_recepient', 'LIKE', '%' . $search . '%')
            ->orWhereDate('created_at', 'LIKE', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    
        \Log::info('Announcements found: ' . $announcements->count());
      
        foreach ($announcements as $announcement) {
            $emails = explode(', ', $announcement->type_of_recepient);
            if (count($emails) > 3) {
                $announcement->displayEmails = array_slice($emails, 0, 3);
                $announcement->moreEmailsCount = count($emails) - 3;
            } else {
                $announcement->displayEmails = $emails;
                $announcement->moreEmailsCount = 0;
            }
        }

        return view('faculty.announcement-list', compact('announcements'));
    }
}
