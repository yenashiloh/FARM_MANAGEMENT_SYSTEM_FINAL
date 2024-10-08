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
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
        $folders = FolderName::all();
        $folder = FolderName::first();

        $query = $request->input('query');
        $announcements = Announcement::when($query, function ($queryBuilder) use ($query) {
            return $queryBuilder->where('subject', 'LIKE', "%{$query}%")
                ->orWhere('message', 'LIKE', "%{$query}%");
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->filter(function ($announcement) use ($userLoginId, $userRole, $facultyEmails) {
            if ($userRole === 'admin') {
                return true;
            }
            if ($announcement->type_of_recepient === 'All Faculty') {
                return $userRole === 'faculty';
            }
            $recipientEmails = explode(', ', $announcement->type_of_recepient);
            $currentUserEmail = UserLogin::where('user_login_id', $userLoginId)->pluck('email')->first();
            return in_array($currentUserEmail, $recipientEmails);
        });

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
              ->get(['user_login_id', 'email', 'department_id']);
          
          $facultyEmails = $facultyUsers->pluck('email')->toArray();
      
          $departments = Department::whereNotNull('department_id')->whereNotNull('name')->get();
      
          return view('admin.announcement.add-announcement', [
              'folders' => $folders,
              'folder' => $folder,
              'facultyUsers' => $facultyUsers,
              'facultyEmails' => $facultyEmails,
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
      
          if (in_array('all-faculty', $recipientEmails)) {
              $recipientEmailsString = 'All Faculty';
              $facultyUsers = UserLogin::where('role', 'faculty')
                  ->orWhere('role', 'faculty-coordinator')
                  ->get();
              $facultyEmails = $facultyUsers->pluck('email')->toArray();
              $recipientIds = $facultyUsers->pluck('user_login_id')->toArray();
          } else {
              $recipientEmails = array_diff($recipientEmails, ['all-faculty']);
      
              foreach ($recipientEmails as $key => $recipient) {
                  if (strpos($recipient, 'department-') === 0) {
                      $departmentId = str_replace('department-', '', $recipient);
                      $department = Department::find($departmentId); 
                      if ($department) {
                          $recipientEmailsString = $department->name; 
      
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
                          $facultyEmails[] = $user->email;
                          $recipientIds[] = $user->user_login_id;
                      }
                  }
              }

              if (empty($recipientEmailsString)) {
                  $recipientEmailsString = 'No Recipients Selected';
              }
          }
      
          $announcement = new Announcement();
          $announcement->subject = $request->input('announcement_subject');
          $announcement->message = $request->input('announcement_message');
          $announcement->published = false;
          $announcement->type_of_recepient = $recipientEmailsString; 
          $announcement->user_login_id = auth()->user()->user_login_id;
          $announcement->save();
      
          $this->sendAnnouncementEmails($announcement, $facultyEmails);
      
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
      
      //search
    public function search(Request $request)
    {
        $query = $request->input('query');
        $announcements = Announcement::where('subject', 'LIKE', "%{$query}%")
                                    ->orWhere('message', 'LIKE', "%{$query}%")
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        
        if ($request->ajax()) {
            return view('admin.announcement.partials.announcement-list', ['announcements' => $announcements])->render();
        }

        return view('admin.announcement.admin-announcement', ['announcements' => $announcements]);
    }

}
