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

class AdminController extends Controller
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
        $userDetails = $user->userDetails; 
    
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('faculty.faculty-accomplishment')->with('error', 'Folder not found.');
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
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
    
        return view('admin.accomplishment.admin-uploaded-files', [
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
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }

    //view accomplishment    
    public function viewAccomplishmentFaculty($user_login_id, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $userDetails = $user->userDetails; 

        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('faculty.faculty-accomplishment')->with('error', 'Folder not found.');
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
        ->orderBy('created_at', 'desc')
        ->get();

        $notificationCount = $notifications->where('is_read', 0)->count();

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
    
        return view('admin.accomplishment.view-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'semester' => $semester,
            'subjects' => $subjects,
            'filesWithSubjects' => $filesWithSubjects,
            'files' => $files,
            'folders' => $folders, 
            'userDetails' => $userDetails,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }

    //show admin account
    public function adminAccountPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $userDetails = $user->userDetails;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $folders = FolderName::all();
    
        return view('admin.admin-account', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'userDetails' => $userDetails,
            'user' => $user, 
        ]);
    }

    //update account
    public function updateAccount(Request $request)
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

        return redirect()->route('admin.admin-account')->with('success', 'Account details updated successfully!');
    }

    public function adminLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }
}
