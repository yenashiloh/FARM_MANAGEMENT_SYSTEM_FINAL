<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;

class FacultyController extends Controller
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

    //view accomplishment page
    public function accomplishmentPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $folders = FolderName::all();

        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())->get();
        $notificationCount = $notifications->count();
    
        return view('faculty.faculty-accomplishment', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }
    

    //faculty logout
    public function facultyLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    //view uploaded files
    public function showUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('faculty.faculty-accomplishment')->with('error', 'Folder not found.');
        }
    
        $facultyInfo = json_decode($this->getFacultyInfo(), true);
        $semester = $facultyInfo['faculty']['subjects'][0]['semester']['semester'];
        $files = CoursesFile::where('folder_name_id', $folder_name_id)
            ->where('user_login_id', auth()->id())
            ->get();

        $filesWithSubjects = $files->map(function ($file) use ($facultyInfo) {
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
    
            return $file;
        });
    
        $groupedFiles = $filesWithSubjects->groupBy('semester');
    
        $subjects = $facultyInfo['faculty']['subjects'] ?? [];
    
        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())->get();
        $notificationCount = $notifications->count();
    
        return view('faculty.accomplishment.uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $files->groupBy('semester'),
            'semester' => $semester,
            'subjects' => $facultyInfo['faculty']['subjects'] ?? [],
            'filesWithSubjects' => $files,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }
    
    
}
