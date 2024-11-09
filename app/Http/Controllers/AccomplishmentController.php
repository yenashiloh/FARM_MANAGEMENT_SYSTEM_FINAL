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
use Carbon\Carbon;
use App\Models\CourseSchedule;

class AccomplishmentController extends Controller
{
    //show the accomplishtment department
    public function showAccomplishmentPage()
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
    
        $departments = Department::all();
    
        return view('admin.accomplishment.accomplishment', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user, 
            'departments' => $departments,
        ]);
    }
    
    //show the faculty members per department
    public function showAccomplishmentDepartment($department, $folder_name_id)
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
    
        $folder = FolderName::findOrFail($folder_name_id);
        $folders = FolderName::all();
    
        $decodedDepartment = urldecode($department);
        
        // Check if the department exists
        $departmentRecord = Department::where('name', $decodedDepartment)->first();
        
        if (!$departmentRecord) {
            return redirect()->back()->withErrors(['Department not found']);
        }
    
        $facultyUsers = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator'])
            ->where('department_id', $departmentRecord->department_id)
            ->get();
    
        return view('admin.accomplishment.view-accomplishment-faculty', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'user_login_id' => $user->user_login_id, 
            'department' => $departmentRecord,  
            'facultyUsers' => $facultyUsers,
            'folder_name_id' => $folder_name_id,
            'folder' => $folder,
            'folderName' => $folder->folder_name
        ]);
    }
        
    //show main requirements
    public function viewFacultyAccomplishments($user_login_id)
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
    
        $faculty = UserLogin::findOrFail($user_login_id);
    
        $folders = FolderName::select('main_folder_name')->distinct()->get(); 
    
        $department = Department::find($faculty->department_id);
        $departmentName = $department ? $department->name : '';
    
        $facultyUsers = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator']) 
            ->where('department_id', $faculty->department_id) 
            ->get();
    
        return view('admin.accomplishment.main-folder', [
            'faculty' => $faculty,
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'department' => $departmentName,
        ]);
    }
    
    //show folder names 
    public function viewFolderNames($user_login_id, $main_folder_name)
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
    
        $faculty = UserLogin::findOrFail($user_login_id);
    
        $folders = FolderName::select('main_folder_name')->distinct()->get(); 
    
        $folderNames = FolderName::where('main_folder_name', $main_folder_name)
            ->get();
    
        $department = Department::find($faculty->department_id);
        $departmentName = $department ? $department->name : '';
        
        $currentFolder = $folders->firstWhere('main_folder_name', $main_folder_name);
    
        return view('admin.accomplishment.view-folder-names', [
            'faculty' => $faculty,
            'folderNames' => $folderNames,
            'main_folder_name' => $main_folder_name,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'department' => $departmentName,
            'folders' => $folders,
            'currentFolder' => $currentFolder, 
        ]);
    }

    //view academic year
    public function viewAcademicYear($user_login_id, $folder_name_id)
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
    
        $academicYearsWithFiles = CourseSchedule::select('course_schedules.sem_academic_year')
            ->join('courses_files', 'course_schedules.course_schedule_id', '=', 'courses_files.course_schedule_id')
            ->where('course_schedules.user_login_id', $user_login_id)
            ->where('courses_files.folder_name_id', $folder_name_id)
            ->distinct()
            ->orderBy('course_schedules.sem_academic_year', 'desc')
            ->pluck('sem_academic_year');
    
        $folder = FolderName::findOrFail($folder_name_id);
    
        $faculty = UserLogin::findOrFail($user_login_id);
    
        $department = Department::find($faculty->department_id);
        $departmentName = $department ? $department->name : '';
    
        $folders = FolderName::select('main_folder_name')->distinct()->get();
        
        $currentFolder = $folders->firstWhere('main_folder_name', $folder->main_folder_name);
    
        return view('admin.accomplishment.view-academic-year', [
            'allAcademicYears' => $academicYearsWithFiles,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'folder_name' => $folder->folder_name,
            'faculty' => $faculty,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'department' => $departmentName,
            'currentFolder' => $currentFolder,
            'folders' => $folders,
            'folder' => $folder, 
        ]);
    }

    //show department page
    public function showDepartmentPage($folder_name_id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $folder = FolderName::findOrFail($folder_name_id);

            $notifications = Notification::where('user_login_id', $user->user_login_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $notificationCount = $notifications->where('is_read', 0)->count();
            $departments = Department::orderBy('name', 'asc')->get();
            
            $folders = FolderName::all();  
            
            $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];

            $departments = Department::all();
            $departmentProgress = [];
            
            foreach ($departments as $department) {
                $userIds = UserLogin::where('department_id', $department->department_id)
                    ->pluck('user_login_id');
                $totalApprovedFiles = 0;
                $totalFiles = 0;
            
                foreach ($mainFolders as $mainFolder) {
                    $subFolders = FolderName::where('main_folder_name', $mainFolder)
                        ->pluck('folder_name_id');
                    
                    $totalFiles += CoursesFile::whereIn('user_login_id', $userIds)
                        ->whereIn('folder_name_id', $subFolders)
                        ->count();
            
                    $totalApprovedFiles += CoursesFile::whereIn('user_login_id', $userIds)
                        ->whereIn('folder_name_id', $subFolders)
                        ->where('status', 'Approved')
                        ->count();
                }
            
                $departmentOverallProgress = ($totalFiles > 0) ? 
                    ($totalApprovedFiles / $totalFiles) * 100 : 0;
                $departmentProgress[$department->name] = $departmentOverallProgress;
            }

            
            return view('admin.accomplishment.department', [
                'folders' => $folders,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'firstName' => $user->first_name,
                'surname' => $user->surname,
                'user' => $user,
                'departments' => $departments,
                'folder_name_id' => $folder_name_id,
                'folderName' => $folder->folder_name,
                'folder' => $folder,
                'departmentProgress' => $departmentProgress,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Folder not found.');
        }
    }
}
