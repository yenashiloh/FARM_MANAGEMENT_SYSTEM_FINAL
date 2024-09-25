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


class DirectorController extends Controller
{
    public function getFacultyInfo()
    {
        $semester = [
            "id" => 1,
            "semester" => "1st Semester 2024-2025",
            "user_login_id" => 2
        ];
    
        $programs = [
            "Bachelor of Science in Applied Mathematics (BSAM)",
            "Bachelor of Science in Information Technology (BSIT)",
            "Bachelor of Science in Entrepreneurship (BSENTREP)"
        ];
    
        $subjects = [
            [
                "id" => 1,
                "code" => "MGT101",
                "name" => "Principles of Management and Organization",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "1st Year",
                        "program" => "BSAM"
                    ]
                ]
            ],
            [
                "id" => 2,
                "code" => "IT202",
                "name" => "Applications Development and Emerging Technologies",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "2nd Year",
                        "program" => "BSIT"
                    ]
                ]
            ],
            [
                "id" => 3,
                "code" => "ENT301",
                "name" => "Technopreneurship",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "3rd Year",
                        "program" => "BSENTREP"
                    ]
                ]
            ],
            [
                "id" => 4,
                "code" => "SYS202",
                "name" => "Systems Analysis and Design",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "2nd Year",
                        "program" => "BSIT"
                    ]
                ]
            ],
            [
                "id" => 5,
                "code" => "CS303",
                "name" => "Computer Science",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "4th Year",
                        "program" => "BSIT"
                    ],
                    [
                        "year" => "3rd Year",
                        "program" => "BSAM"
                    ]
                ]
            ]
        ];
    
        $data = [
            "faculty" => [
                "faculty_id" => 2,
                "first_name" => "Diana",
                "middle_name" => "M.",
                "last_name" => "Rose",
                "programs" => $programs,
                "subjects" => $subjects
            ]
        ];
    
        return json_encode($data);
    }

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
    
        $filesQuery = CoursesFile::where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.user_login_id', $user_login_id)
            ->with(['userLogin', 'courseSchedule']);
    
        $allSemesters = CoursesFile::where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.user_login_id', $user_login_id)
            ->join('course_schedules', 'courses_files.course_schedule_id', '=', 'course_schedules.course_schedule_id')
            ->select('course_schedules.sem_academic_year')
            ->distinct()
            ->pluck('course_schedules.sem_academic_year');
    
        $semester = $allSemesters->first();
    
        $files = $filesQuery
            ->whereHas('courseSchedule', function ($query) use ($semester) {
                $query->where('sem_academic_year', $semester);
            })
            ->get();
    
        $groupedFiles = $files->groupBy(function ($file) {
            return $file->courseSchedule->sem_academic_year;
        });
    
        $folders = FolderName::all();
        $viewedUser = UserLogin::find($user_login_id);
    
        return view('director.accomplishment.view-faculty-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'files' => $files,
            'folders' => $folders,
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'viewedUser' => $viewedUser,
            'currentSemester' => $semester,
            'allSemesters' => $allSemesters,
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
