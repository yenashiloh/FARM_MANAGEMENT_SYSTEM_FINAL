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
use App\Models\LogoutLog;
use App\Models\LoginLog;
use App\Models\Department;
use App\Models\CourseSchedule;
use App\Models\Message;
use App\Models\RequestUploadAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    //show accomplishment page
    public function accomplishmentPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $folders = FolderName::all();

        return view('admin.admin-accomplishment', [
            'folders' => $folders
        ]);
    }

    //show all uploaded files
    public function showAdminUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $folder = FolderName::findOrFail($folder_name_id);

        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $notificationCount = $notifications->where('is_read', 0)->count();

        $allSemesters = CoursesFile::where('folder_name_id', $folder_name_id)
            ->select('semester')
            ->distinct()
            ->pluck('semester');

        $selectedSemester = request('semester');

        $filesQuery = CoursesFile::where('folder_name_id', $folder_name_id)
            ->with(['userLogin', 'courseSchedule']);
        
        if ($selectedSemester) {
            $filesQuery->where('semester', $selectedSemester);
        }

        $files = $filesQuery->get();

        $groupedFiles = $files->groupBy(function ($file) {
            return $file->user_login_id . '-' . $file->semester;
        })->map(function ($group) {
            $firstFile = $group->first();
            return [
                'user_name' => $firstFile->userLogin->first_name . ' ' . $firstFile->userLogin->surname,
                'user_login_id' => $firstFile->user_login_id,
                'semester' => $firstFile->semester,
                'created_at' => $group->max('created_at'),
                'file_count' => $group->count(),
            ];
        })->values();

        $folders = FolderName::all();

        return view('admin.accomplishment.admin-uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'folder_name_id' => $folder_name_id,
            'folders' => $folders,
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'allSemesters' => $allSemesters,
            'selectedSemester' => $selectedSemester, 
        ]);
    }
    
    //view accomplishment    
    public function viewAccomplishmentFaculty($user_login_id, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('login')->with('error', 'Folder not found.');
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $files = CoursesFile::where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.user_login_id', $user_login_id)
            ->with(['userLogin', 'courseSchedule', 'folderName'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        $semesters = CoursesFile::distinct()->pluck('semester')->sort();
        $schoolYears = CoursesFile::distinct()->pluck('school_year');
    
        $folders = FolderName::all();
        $viewedUser = UserLogin::find($user_login_id);
        $faculty = UserLogin::findOrFail($user_login_id);
        $department = Department::find($faculty->department_id);
        $departmentName = $department ? $department->name : '';
    
        $currentFolder = $folders->firstWhere('main_folder_name', $folder->main_folder_name);
    
       $messages = Message::whereIn('courses_files_id', $files->pluck('courses_files_id'))
        ->with('userLogin')
        ->orderBy('created_at', 'asc')
        ->get();
    
        return view('admin.accomplishment.view-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'files' => $files, 
            'folders' => $folders,
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'viewedUser' => $viewedUser,
            'department' => $departmentName,
            'faculty' => $faculty,
            'currentFolder' => $currentFolder,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'semesters' => $semesters,
            'schoolYears' => $schoolYears, 
            'messages' => $messages,
        ]);
    }

    //send message 
    public function sendMessageFaculty(Request $request)
    {
        $message = Message::create([
            'user_login_id' => auth()->id(), 
            'courses_files_id' => $request->courses_files_id,
            'folder_name_id' => $request->folder_name_id,
            'message_body' => $request->message_body,
        ]);
    
        $message->load('userLogin');
    
        $facultyUserLoginId = CoursesFile::findOrFail($request->courses_files_id)->user_login_id;
    
        $adminDetails = UserLogin::find(auth()->id()); 
        $senderName = $adminDetails ? $adminDetails->first_name . ' ' . $adminDetails->surname : 'Unknown Sender';
    
        Notification::create([
            'courses_files_id' => $request->courses_files_id,
            'user_login_id' => $facultyUserLoginId, 
            'folder_name_id' => $request->folder_name_id,
            'sender' => $senderName,
            'notification_message' => 'sent a message regarding your documents.',
        ]);
    
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    //show admin account
    public function adminAccountPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $folders = FolderName::all();
    
        return view('admin.admin-account', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user, 
        ]);
    }

    //update account
    public function updateAccount(Request $request)
    {
        $user = auth()->user();
    
        $request->validate([
            'first-name' => 'required|string|max:255',
            'last-name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'recent-password' => 'required_with:new-password|current_password',
            'new-password' => 'nullable|confirmed|min:6',
            'confirm-password' => 'nullable|same:new-password',
        ]);
    
        $user->update([
            'email' => $request->input('email'),
            'first_name' => $request->input('first-name'),
            'surname' => $request->input('last-name'),
            'password' => $request->input('new-password') ? bcrypt($request->input('new-password')) : $user->password,
        ]);
    
        return redirect()->route('admin.admin-account')->with('success', 'Account details updated successfully!');
    }

    //admin logout
    public function adminLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    //audit trail page
    public function showAuditTrail()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $folders = FolderName::all();
        
        // Get logs
        $logoutLogs = LogoutLog::with('user')
            ->select('user_login_id', 'logout_time', 'logout_message')
            ->orderBy('logout_time', 'desc')
            ->get();
            
        $loginLogs = LoginLog::with('user')
            ->select('user_login_id', 'login_time', 'login_message')
            ->orderBy('login_time', 'desc')
            ->get();
    
        $allLogs = collect();
        
        foreach ($loginLogs as $log) {
            $allLogs->push([
                'email' => $log->user->email,
                'message' => $log->login_message,
                'time' => $log->login_time,
                'type' => 'Login'
            ]);
        }
        
        foreach ($logoutLogs as $log) {
            $allLogs->push([
                'email' => $log->user->email,
                'message' => $log->logout_message,
                'time' => $log->logout_time,
                'type' => 'Logout'
            ]);
        }
    
        $sortedLogs = $allLogs->sortByDesc('time')->values();
        
        \Log::info('All Logs:', $sortedLogs->toArray());
        
        return view('admin.maintenance.audit-trail', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'logs' => $sortedLogs,
        ]);
    }

    //show request upload access page
    public function showRequestUploadAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;

        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $uploadRequests = RequestUploadAccess::with('user')
        ->orderBy('created_at', 'desc') 
        ->get();
    
        $folders = FolderName::all();

        RequestUploadAccess::where('status', 'unread')->update(['status' => 'read']);
        $requests = RequestUploadAccess::all();
        return view('admin.request-upload-access', [
            'uploadRequests' => $uploadRequests, 
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'requests' => $requests,
            'folders' => $folders,
        ]);
    }
    
    public function checkNewRequests()
    {
        $newRequestsCount = \App\Models\RequestUploadAccess::where('status', 'unread')->count();
        return response()->json(['new_requests_count' => $newRequestsCount]);
    }
    
    public function markRequestsAsRead()
    {
        \App\Models\RequestUploadAccess::where('status', 'unread')->update(['status' => 'read']);
        return response()->json(['success' => true]);
    }

    //realtime table of upload access
    public function realTimeUploadAccess()
    {
        $allRequests = RequestUploadAccess::with('user')
             ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($request) {
            return [
                'created_at_date' => $request->created_at->format('F j, Y'),
                'created_at_time' => $request->created_at->format('g:i A'),
                'user_name' => $request->user->first_name . ' ' . $request->user->surname,
                'reason' => $request->reason,
                'status_request' => $request->status_request,
                'request_upload_id' => $request->request_upload_id
            ];
        });


        return response()->json([

            'uploadRequests' => $allRequests 
        ]);
    }
    
    //show submission tracker
    public function showSubmissionTracker()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $allUsers = DB::table('user_login')
            ->select('user_login_id', 'first_name', 'surname', 'role', 'faculty_code')
            ->whereIn('role', ['faculty', 'faculty-coordinator'])
            ->get();
    
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $progressData = [];
    
        foreach ($allUsers as $user) {
            $totalProgress = 0;
    
            foreach ($mainFolders as $mainFolder) {
                $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
                $mainFolderProgress = 0;
    
                foreach ($subFolders as $subFolder) {
                    $approvedFiles = $subFolder->coursesFiles()
                        ->where('user_login_id', $user->user_login_id)
                        ->where('status', 'Approved') 
                        ->count();
    
                    $totalFiles = $subFolder->coursesFiles()
                        ->where('user_login_id', $user->user_login_id)
                        ->count();
    
                    $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                    $mainFolderProgress += $subFolderProgress;
                }
    
                $mainFolderProgress = ($subFolders->count() > 0) ?
                    $mainFolderProgress / $subFolders->count() : 0;
    
                $totalProgress += $mainFolderProgress;
            }
    
            $overallProgress = count($mainFolders) > 0 ? $totalProgress / count($mainFolders) : 0;
            $progressData[$user->user_login_id] = $overallProgress;
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $folders = FolderName::all();
    
        return view('admin.submission-tracker', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'allUsers' => $allUsers,
            'progressData' => $progressData
        ]);
    }

    //view folder page
    public function viewFolder($user_login_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = DB::table('user_login')->where('user_login_id', $user_login_id)->first();
        $authUser = auth()->user();
        $firstName = $authUser->first_name;
        $surname = $authUser->surname;
    
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
    
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
    
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $user_login_id)
                    ->count();
    
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $user_login_id)
                    ->where('status', 'Approved')
                    ->count();
    
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
    
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ? round($mainFolderProgress / $subFolders->count()) : 0;
        }
    
        $folders = FolderName::all();
        $notifications = Notification::where('user_login_id', $authUser->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        return view('admin.view-folder', [
            'user' => $user,
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'folderProgress' => $folderProgress
        ]);
    }

    
    //view sub folder
    public function viewSubfolder($user_login_id, $folder_name)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = DB::table('user_login')->where('user_login_id', $user_login_id)->first();
        
        $authUser = auth()->user();
        $firstName = $authUser->first_name;
        $surname = $authUser->surname;
        
        $subfolders = FolderName::where('main_folder_name', $folder_name)->get();
        
        $folders = FolderName::all(); 
        
        $subfolderProgress = [];
        foreach ($subfolders as $subfolder) {
            $totalFiles = $subfolder->coursesFiles()
                ->where('user_login_id', $user_login_id)
                ->count();
                
            $approvedFiles = $subfolder->coursesFiles()
                ->where('user_login_id', $user_login_id)
                ->where('status', 'Approved')
                ->count();
                
            $progress = $totalFiles > 0 ? round(($approvedFiles / $totalFiles) * 100) : 0;
            
            $subfolderProgress[$subfolder->folder_name_id] = [
                'name' => $subfolder->folder_name,
                'progress' => $progress,
                'files_count' => $totalFiles
            ];
        }
        
        $notifications = Notification::where('user_login_id', $authUser->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        
        return view('admin.view-subfolder', [
            'user' => $user,
            'mainFolder' => $folder_name,
            'subfolders' => $subfolderProgress,
            'folders' => $folders, 
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname
        ]);
    }
    
    public function sendReminder($user_login_id)
    {
        $faculty = DB::table('user_login')->where('user_login_id', $user_login_id)->first();
        $sender = auth()->user();
    
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $totalProgress = 0;
    
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
    
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $user_login_id)
                    ->count();
    
                $subFolderProgress = ($totalFiles > 0) ? 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
    
            $mainFolderProgress = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
                
            $totalProgress += $mainFolderProgress;
        }
    
        $progress = count($mainFolders) > 0 ? $totalProgress / count($mainFolders) : 0;
    
        Mail::send('emails.reminder', [
            'faculty' => $faculty,
            'sender' => $sender,
            'progress' => $progress
        ], function($message) use ($faculty) {
            $message->to($faculty->email)
                    ->subject('Reminder: Submit Required Documents');
        });
    
        return redirect()->back()->with('success', 'Reminder email sent successfully.');
    }
    
    //approved request 
    public function approveUploadRequest($id)
    {
        $request = RequestUploadAccess::with('user')->where('request_upload_id', $id)->firstOrFail();
        
        Notification::create([
            'courses_files_id' => null,
            'user_login_id' => $request->user_login_id,
            'folder_name_id' => null,
            'sender' => 'PUPT Admin',
            'notification_message' => 'Approved your request for uploading. The upload access is now open.',
            'is_read' => 1
        ]);
    
        // Update using the correct primary key
        RequestUploadAccess::where('request_upload_id', $id)
            ->update(['status_request' => 'Approved']);
    
        return redirect()->back()->with('success', 'Upload Request Approved Successfully!');
    }
    
    public function showTotalUsers()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $folders = FolderName::all();
        
        // Get all departments to use for name lookups
        $departments = Department::pluck('name', 'department_id')->toArray();
        
        // Get users with role 'faculty' or 'faculty-coordinator'
        $users = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator'])->get();
        
        return view('admin.dashboard-totals.users', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'users' => $users,
            'departments' => $departments, 
        ]);
    }
    
    public function showTotalCompletedReviews()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $folders = FolderName::all();
        
        // Get current folder and user_login_id
        $folder_name_id = request('folder_name_id');
        $user_login_id = request('user_login_id', $user->user_login_id);
        
        // Get the current folder
        $currentFolder = FolderName::find($folder_name_id);
        
        // Get faculty information if a specific faculty is selected
        $faculty = null;
        if ($user_login_id && $user_login_id != $user->user_login_id) {
            $faculty = UserLogin::find($user_login_id);
        }
        
        // Get completed reviews
        $files = CoursesFile::where('status', 'Approved')
            ->orWhere('status', 'Completed')
            ->when($folder_name_id, function($query) use ($folder_name_id) {
                return $query->where('folder_name_id', $folder_name_id);
            })
            ->when($user_login_id, function($query) use ($user_login_id) {
                return $query->where('user_login_id', $user_login_id);
            })
            ->with(['userLogin', 'courseSchedule', 'folderName'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Just convert to array - let the view handle the file processing
        $processedFiles = $files->toArray();
    
        return view('admin.dashboard-totals.completed-reviews', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'faculty' => $faculty,
            'currentFolder' => $currentFolder,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'files' => $files,
            'processedFiles' => $processedFiles,
        ]);
    }
    
    //show total declined files 
    public function showPendingReviewFiles()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $folders = FolderName::all();
        
        // Get current folder and user_login_id
        $folder_name_id = request('folder_name_id');
        $user_login_id = request('user_login_id', $user->user_login_id);
        
        // Get the current folder
        $currentFolder = FolderName::find($folder_name_id);

        // Get faculty information if a specific faculty is selected
        $faculty = null;
        if ($user_login_id && $user_login_id != $user->user_login_id) {
            $faculty = UserLogin::find($user_login_id);
        }
        
        // Get completed reviews
        $files = CoursesFile::where('status', 'To Review')
            ->orWhere('status', 'To Review')
            ->when($folder_name_id, function($query) use ($folder_name_id) {
                return $query->where('folder_name_id', $folder_name_id);
            })
            ->when($user_login_id, function($query) use ($user_login_id) {
                return $query->where('user_login_id', $user_login_id);
            })
            ->with(['userLogin', 'courseSchedule', 'folderName'])
            ->orderBy('created_at', 'desc')
            ->get();

        $processedFiles = $files->toArray();
    
        return view('admin.dashboard-totals.pending-review', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'faculty' => $faculty,
            'currentFolder' => $currentFolder,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
               'files' => $files,
    'processedFiles' => $files,
        ]);
    }
    
    //total files submitted
    public function showTotalFilesSubmitted()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $folders = FolderName::all();
        
        // Get the current folder and user parameters
        $folder_name_id = request()->input('folder_name_id');  
        $user_login_id = request()->input('user_login_id');
        
        // Get the current folder
        $currentFolder = FolderName::find($folder_name_id);
        
        // Get faculty details if different user is selected
        $faculty = null;
        if ($user_login_id && $user_login_id != $user->user_login_id) {
            $faculty = UserLogin::find($user_login_id);
        }
        
        // Retrieve ALL submitted files without any filtering
        $files = CoursesFile::with(['userLogin', 'courseSchedule', 'folderName'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Debugging: Log query results
        Log::info('Retrieved files: ' . $files->count());
        
        return view('admin.dashboard-totals.files-submitted', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'faculty' => $faculty,
            'currentFolder' => $currentFolder,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'files' => $files,
            'processedFiles' => $files, 
        ]);
    }
   
}
