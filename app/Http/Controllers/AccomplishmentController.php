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
use Carbon\Carbon;
use App\Models\CourseSchedule;

class AccomplishmentController extends Controller
{
    
    public function showAccomplishmentPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $department = $user->department;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $folders = FolderName::all();
    
        $departments = [
            'College of Engineering',
            'College of Education',
            'College of Accountant',
            'College of Business Administration',
            'College of Information Technology',
            'College of Office Administration',
            'College of Psychology',
        ];
    
        return view('admin.accomplishment.accomplishment', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user, 
            'department' => $department,
            'departments' => $departments,
        ]);
    }
    
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

        $facultyUsers = UserLogin::where('role', 'faculty')
            ->where('department', 'LIKE', trim($decodedDepartment)) 
            ->get();

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

        return view('admin.accomplishment.main-folder', [
            'faculty' => $faculty,
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
        ]);
    }


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
    
        $folderNames = FolderName::where('main_folder_name', $main_folder_name)
            ->get();
    
        return view('admin.accomplishment.view-folder-names', [
            'faculty' => $faculty,
            'folderNames' => $folderNames,
            'main_folder_name' => $main_folder_name,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
        ]);
    }
    
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
    
        $folderName = FolderName::findOrFail($folder_name_id);
    
        return view('admin.accomplishment.view-academic-year', [
            'allAcademicYears' => $academicYearsWithFiles,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'folder_name' => $folderName->folder_name,
            'faculty' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname
        ]);
    }

    
}
