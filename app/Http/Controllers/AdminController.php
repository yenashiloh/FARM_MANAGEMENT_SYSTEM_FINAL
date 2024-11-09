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
use App\Models\RequestUploadAccess;
use Carbon\Carbon;

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
            ->with(['userLogin', 'courseSchedule'])
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
    
        $groupedFiles = $files->groupBy(function ($file) {
            return $file->courseSchedule->course_code . '|' . 
                   $file->courseSchedule->year_section . '|' . 
                   $file->courseSchedule->program . '|' . 
                   $file->semester . '|' . 
                   $file->school_year . '|' . 
                   $file->status;
        });
    
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
            'groupedFiles' => $groupedFiles,
            'semesters' => $semesters,
            'schoolYears' => $schoolYears, 
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

        $logoutLogs = LogoutLog::with('user')->select('user_login_id', 'logout_time', 'logout_message')->orderBy('logout_time', 'desc')->get();
        $loginLogs = LoginLog::with('user')->select('user_login_id', 'login_time', 'login_message')->orderBy('login_time', 'desc')->get(); 

        $allLogs = $loginLogs->map(function ($log) {
            return [
                'email' => $log->user->email,
                'message' => $log->login_message,
                'time' => $log->login_time,
                'type' => 'Login'
            ];
        })->merge($logoutLogs->map(function ($log) {
            return [
                'email' => $log->user->email,
                'message' => $log->logout_message,
                'time' => $log->logout_time,
                'type' => 'Logout'
            ];
        }));

        $sortedLogs = $allLogs->sortByDesc('time');

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
                ];
            });

        return response()->json([

            'uploadRequests' => $allRequests 
        ]);
    }
}
