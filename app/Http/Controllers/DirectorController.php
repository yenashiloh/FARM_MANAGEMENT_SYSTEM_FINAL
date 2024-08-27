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

    public function showDirectorUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $userDetails = $user->userDetails; 
    
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('login')->with('error', 'Folder not found.');
        }
    
        $facultyInfo = json_decode($this->getFacultyInfo(), true);
        $semester = $facultyInfo['faculty']['subjects'][0]['semester']['semester'];
        $files = CoursesFile::where('folder_name_id', $folder_name_id)->get();
    
        $faculty = $facultyInfo['faculty'];
        $userIdToName = [$faculty['faculty_id'] => $faculty['first_name'] . ' ' . $faculty['last_name']];
    
        $filesWithSubjects = $files->map(function ($file) use ($facultyInfo, $userIdToName) {
            $file->subject_name = $file->subject;
    
            $subjectInfo = collect($facultyInfo['faculty']['subjects'])->firstWhere('name', $file->subject);
    
            if ($subjectInfo) {
                $file->year = $subjectInfo['year_programs'][0]['year'] ?? 'N/A';
                $file->program = $subjectInfo['year_programs'][0]['program'] ?? 'N/A';
                $file->code = $subjectInfo['code'] ?? 'N/A';
            } else {
                $file->year = 'N/A';
                $file->program = 'N/A';
                $file->code = 'N/A';
            }
    
            $file->user_name = $userIdToName[$file->user_login_id] ?? 'N/A';
    
            return $file;
        });
    
        $groupedFiles = $filesWithSubjects->groupBy('semester');
    
        $subjects = $facultyInfo['faculty']['subjects'] ?? [];
        $user_login_id = $files->first()->user_login_id ?? null;
    
        $folders = FolderName::all();
    
        return view('director.accomplishment.director-uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'semester' => $semester,
            'subjects' => $subjects,
            'filesWithSubjects' => $filesWithSubjects,
            'files' => $files,
            'user_login_id' => $user_login_id, 
            'folder_name_id' => $folder_name_id, 
            'folders' => $folders, 
            'userDetails' => $userDetails,
        ]);
    }

    //director dashboard
    public function directorDashboardPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

    
        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
    
        $userDetails = $user->userDetails; 
    
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
            'userDetails' => $userDetails,
            'chartData' => $chartData,
            'semesters' => $semesters,
        ]);
    }

    public function viewFacultyAccomplishment($user_login_id, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

    
        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('login')->with('error', 'Folder not found.');
        }

        $userDetails = $user->userDetails; 
        
        $facultyInfo = json_decode($this->getFacultyInfo(), true);
        $semester = $facultyInfo['faculty']['subjects'][0]['semester']['semester'];
        
        $files = CoursesFile::where('folder_name_id', $folder_name_id)->get();
    
        $faculty = $facultyInfo['faculty'];
        $userIdToName = [$faculty['faculty_id'] => $faculty['first_name'] . ' ' . $faculty['last_name']];
    
        $filesWithSubjects = $files->map(function ($file) use ($facultyInfo, $userIdToName) {
            $file->subject_name = $file->subject;
    
            $subjectInfo = collect($facultyInfo['faculty']['subjects'])->firstWhere('name', $file->subject);
    
            if ($subjectInfo) {
                $file->year = $subjectInfo['year_programs'][0]['year'] ?? 'N/A';
                $file->program = $subjectInfo['year_programs'][0]['program'] ?? 'N/A';
                $file->code = $subjectInfo['code'] ?? 'N/A';
            } else {
                $file->year = 'N/A';
                $file->program = 'N/A';
                $file->code = 'N/A';
            }
    
            $file->user_name = $userIdToName[$file->user_login_id] ?? 'N/A';
    
            return $file;
        });
    
        $groupedFiles = $filesWithSubjects->groupBy('semester');
    
        $subjects = $facultyInfo['faculty']['subjects'] ?? [];
    
        $folders = FolderName::all();
    
        return view('director.accomplishment.view-faculty-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'semester' => $semester,
            'subjects' => $subjects,
            'filesWithSubjects' => $filesWithSubjects,
            'files' => $files,
            'folders' => $folders, 
            'userDetails' => $userDetails,
        ]);
    }

     //show director account
     public function directorAccountPage()
     {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

    
        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

         $userDetails = $user->userDetails;
     
         $folders = FolderName::all();
     
         return view('director.director-account', [
             'folders' => $folders,
             'userDetails' => $userDetails,
             'user' => $user, 
         ]);
     }
 
     //update account
     public function updateDirectorAccount(Request $request)
     {
         $user = auth()->user();
         $userDetails = $user->userDetails;
 
         $request->validate([
             'first-name' => 'required|string|max:255',
             'last-name' => 'required|string|max:255',
             'email' => 'required|email|max:255',
             'contact-number' => 'required|numeric',
             'recent-password' => 'required_with:new-password|current_password', 
             'new-password' => 'nullable|confirmed|min:6', 
             'confirm-password' => 'nullable|same:new-password', 
         ]);
 
         $user->update([
             'email' => $request->input('email'),
             'password' => $request->input('new-password') ? bcrypt($request->input('new-password')) : $user->password,
         ]);
 
         $userDetails->update([
             'first_name' => $request->input('first-name'),
             'last_name' => $request->input('last-name'),
             'phone_number' => $request->input('contact-number'),
         ]);
 
         return redirect()->route('director.director-account')->with('success', 'Account details updated successfully!');
     }
 
     public function directorLogout(Request $request)
     {
         auth()->logout();
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return response()->json(['success' => true]);
     }

}
