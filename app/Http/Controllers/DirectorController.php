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
use App\Exports\GenerateAllReports;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;


class DirectorController extends Controller
{
    //show faculty uploaded files
    public function showDirectorUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
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


        return view('director.accomplishment.director-uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'folder_name_id' => $folder_name_id,
            'folders' => $folders,
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'allSemesters' => $allSemesters,
            'selectedSemester' => $selectedSemester, 
            'user' => $user, 
            
        ]);
    }

    //director dashboard
    public function directorDashboardPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

        $folders = FolderName::all();
        $folder = FolderName::first(); 
    
        $facultyCount = UserLogin::where('role', 'faculty')->count();
    
        $filesCount = CoursesFile::count();
        $toReviewCount = CoursesFile::where('status', 'To Review')->count();
        $approvedCount = CoursesFile::where('status', 'Approved')->count();
        $declinedCount = CoursesFile::where('status', 'Declined')->count();
        $completedReviewsCount = CoursesFile::whereIn('status', ['Approved', 'Declined'])->count();
        $folderCounts = FolderName::withCount('coursesFiles')->get();
    
        $folderStatusCounts = FolderName::withCount([
            'coursesFiles as to_review_count' => function ($query) {
                $query->where('status', 'To Review');
            },
            'coursesFiles as approved_count' => function ($query) {
                $query->where('status', 'Approved');
            },
            'coursesFiles as declined_count' => function ($query) {
                $query->where('status', 'Declined');
            },
        ])->get();
    
        $chartData = $folderStatusCounts->map(function ($folder) {
            return [
                'folder_name' => $folder->folder_name,
                'to_review_count' => $folder->to_review_count,
                'approved_count' => $folder->approved_count,
                'declined_count' => $folder->declined_count,
            ];
        });
    
        $semesters = CoursesFile::select('semester')->distinct()->get();

        return view('director.director-dashboard', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyCount' => $facultyCount,
            'filesCount' => $filesCount,
            'toReviewCount' => $toReviewCount,
            'completedReviewsCount' => $completedReviewsCount,
            'approvedCount' => $approvedCount,
            'declinedCount' => $declinedCount,
            'folderCounts' => $folderCounts,
            'user' => $user, 
            'chartData' => $chartData,
            'semesters' => $semesters,
        ]);
    }

    //view faculty accomplishment 
    public function viewFacultyAccomplishment($user_login_id, $folder_name_id)
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
        $departmentName = $department ? $department->name : 'Department not found'; 
    
        $currentFolder = $folders->firstWhere('main_folder_name', $folder->main_folder_name);
    
        $groupedFiles = $files->groupBy(function ($file) {
            return $file->courseSchedule->course_code . '|' . 
                   $file->courseSchedule->year_section . '|' . 
                   $file->courseSchedule->program . '|' . 
                   $file->semester . '|' . 
                   $file->school_year . '|' . 
                   $file->status;
        });
    
        return view('director.accomplishment.view-faculty-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'files' => $files,
            'folders' => $folders,
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folder_name_id' => $folder_name_id,
            'semesters' => $semesters,
            'schoolYears' => $schoolYears,
            'departmentName' => $departmentName, 
            'faculty' => $faculty,  
        ]);
    }
    
    //show the director department page
    public function showDirectorDepartmentPage($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
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

            
            return view('director.department', [
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

    //show the faculty members in dept
    public function showDirectorAccomplishmentDepartment($department, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
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
        
        $departmentRecord = Department::where('name', $decodedDepartment)->first();
        
        if (!$departmentRecord) {
            return redirect()->back()->withErrors(['Department not found']);
        }
    
        $facultyUsers = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator'])
            ->where('department_id', $departmentRecord->department_id)
            ->get();
    
        return view('director.accomplishment.view-accomplishment-faculty', [
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

    //show director account
    public function directorAccountPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
     
        $user = auth()->user();
     
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
     
        $folders = FolderName::all();
     
        return view('director.director-account', [
            'folders' => $folders,
            'user' => $user,
        ]);
    }
     
    //update the director account
    public function updateDirectorAccount(Request $request)
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
            'password' => $request->input('new-password') ? bcrypt($request->input('new-password')) : $user->password,
            'first_name' => $request->input('first-name'),
            'surname' => $request->input('last-name'),

        ]);
     
        return redirect()->route('director.director-account')->with('success', 'Account details updated successfully!');
     }
     
    //director logout
    public function directorLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
     }
    
    //generate reports
    public function generateAllReportsDirector($semester)
    {
        Log::info('Starting generateAllReportsDirector for semester: ' . $semester);

        $export = new GenerateAllReports($semester);

        Log::info('Created GenerateAllReports instance');

        $result = Excel::download($export, 'faculty_report_director.xlsx');

        Log::info('Excel::download completed');

        return $result;
    }
}
