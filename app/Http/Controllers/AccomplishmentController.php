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
    public function showAccomplishmentDepartment($department)
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
    
        $decodedDepartment = urldecode($department);
    
        $departmentRecord = Department::where('name', $decodedDepartment)->first();
    
        $facultyUsers = $departmentRecord 
            ? UserLogin::whereIn('role', ['faculty', 'faculty-coordinator']) 
                ->where('department_id', $departmentRecord->department_id) 
                ->get() 
            : collect(); 
    

        return view('admin.accomplishment.view-accomplishment-faculty', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'department' => $decodedDepartment, 
            'facultyUsers' => $facultyUsers,
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

    
}
