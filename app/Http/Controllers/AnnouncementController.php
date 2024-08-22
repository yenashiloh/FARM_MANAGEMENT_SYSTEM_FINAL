<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Announcement;
use Illuminate\Support\Facades\Validator;


class AnnouncementController extends Controller
{
      // Show Announcement Page
      public function showAnnouncementPage()
      {
          if (!auth()->check()) {
              return redirect()->route('login');
          }
      
          $userLoginId = auth()->user()->user_login_id;
          $userRole = auth()->user()->role; // Assuming role is stored in the user model
      
          // Fetch faculty emails
          $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
      
          // Fetch all folders and the first folder
          $folders = FolderName::all();
          $folder = FolderName::first();
      
          // Fetch announcements
          $announcements = Announcement::orderBy('created_at', 'desc')->get()->filter(function ($announcement) use ($userLoginId, $userRole, $facultyEmails) {
              // If the user is an admin, they see all announcements
              if ($userRole === 'admin') {
                  return true;
              }
      
              // If the announcement is for all faculty
              if ($announcement->type_of_recepient === 'All Faculty') {
                  return $userRole === 'faculty'; // Ensure the current user is a faculty member
              }
      
              // If the announcement is for specific emails
              $recipientEmails = explode(', ', $announcement->type_of_recepient);
              $currentUserEmail = UserLogin::where('user_login_id', $userLoginId)->pluck('email')->first();
      
              return in_array($currentUserEmail, $recipientEmails);
          });
      
          return view('admin.announcement.admin-announcement', [
              'announcements' => $announcements,
              'folders' => $folders,
              'folder' => $folder,
              'facultyEmails' => $facultyEmails,
          ]);
      }
      
      
      
      //Show Add Announcement Page
      public function showAddAnnouncementPage()
      {
        $folders = FolderName::all();
        $folder = FolderName::first(); 
        $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();

          return view('admin.announcement.add-announcement', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyEmails' => $facultyEmails,
        ]);
      }
      
      
      // Save the Announcement
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
      
          // Retrieve selected recipient emails from the request
          $recipientEmails = $request->input('recipient_emails', []);
          
          // Check if 'all-faculty' is the only selected option
          if ($recipientEmails === ['all-faculty']) {
              $recipientEmailsString = 'All Faculty';
          } else {
              // Remove 'all-faculty' if it's in the array along with other emails
              $recipientEmails = array_diff($recipientEmails, ['all-faculty']);
              $recipientEmailsString = !empty($recipientEmails) ? implode(', ', $recipientEmails) : 'No Recipients Selected';
          }
      
          // Create a new announcement
          $announcement = new Announcement();
          $announcement->subject = $request->input('announcement_subject');
          $announcement->message = $request->input('announcement_message');
          $announcement->published = false;
          $announcement->type_of_recepient = $recipientEmailsString;
          $announcement->user_login_id = auth()->user()->user_login_id;
          $announcement->save();
      
          $request->session()->flash('success', 'Announcement Added Successfully!');
          return redirect()->route('admin.announcement.admin-announcement');
      }
      
      
  
      // Edit Page
      public function showEditPage()
      {
          if (!auth()->check()) {
              return redirect()->route('login');
          }
      
          // Fetch all folders
          $folders = FolderName::all();
          
          // Fetch the first folder or adjust as needed
          $folder = FolderName::first(); 
      
          // Optionally fetch other data if needed in the edit view
          // If you need to provide `$announcements` and `$facultyEmails`, fetch them here
          // Uncomment if needed:
          // $announcements = Announcement::all();
          // $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
      
          return view('admin.announcement.edit-announcement', [
              'folders' => $folders,
              'folder' => $folder,
              // Uncomment if needed:
              // 'announcements' => $announcements,
              // 'facultyEmails' => $facultyEmails,
          ]);
      }
      
  
      // Display the edit form
      public function editAnnouncement($id_announcement)
      {
          if (!auth()->check()) {
              return redirect()->route('login');
          }
      
          // Fetch all folders
          $folders = FolderName::all();
      
          // Fetch faculty emails
          $facultyEmails = UserLogin::where('role', 'faculty')->pluck('email')->toArray();
      
          // Fetch the announcement by ID
          $announcement = Announcement::findOrFail($id_announcement);
      
          // Return the view with the announcement, folders, and faculty emails data
          return view('admin.announcement.edit-announcement', [
              'announcement' => $announcement,
              'folders' => $folders,
              'facultyEmails' => $facultyEmails,
          ]);
      }
      
  
      // Update the announcement
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
  
      //Delete the Announcement
      public function deleteAnnouncement($id_announcement)
      {
          $announcement = Announcement::findOrFail($id_announcement);
          $announcement->delete();
      
          return response()->json(['success' => 'Announcement deleted successfully.']);
      }
  
      // Publish and Unpublish the announcement
      public function publishAnnouncement($id_announcement)
      {
          $announcement = Announcement::findOrFail($id_announcement);
          $announcement->published = true;
          $announcement->save();
      
          return redirect()->back()->with('success', 'Announcement published successfully!');
      }
      
      public function unpublishAnnouncement($id_announcement)
      {
          $announcement = Announcement::findOrFail($id_announcement);
          $announcement->published = false;
          $announcement->save();
      
          return redirect()->back()->with('success', 'Announcement unpublished successfully!');
      }
}
