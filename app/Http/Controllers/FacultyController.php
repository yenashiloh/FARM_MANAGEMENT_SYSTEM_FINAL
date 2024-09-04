<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;
use App\Models\FolderInput;
use App\Http\Middleware\RoleAuthenticate;
use Carbon\Carbon;
use App\Models\UploadSchedule;
use Illuminate\Support\Facades\Log;

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
    // public function accomplishmentPage()
    // {
    //     if (!auth()->check()) {
    //         return redirect()->route('login');
    //     }
        
    //     $folders = FolderName::all();

    //     $notifications = \App\Models\Notification::where('user_login_id', auth()->id())->get();
    //     $notificationCount = $notifications->count();
    
    //     return view('faculty.faculty-accomplishment', [
    //         'folders' => $folders,
    //         'notifications' => $notifications,
    //         'notificationCount' => $notificationCount,
    //     ]);
    // }
    
    //faculty logout
    public function facultyLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    public function showUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'faculty') {
            return redirect()->route('login');
        }
    
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('faculty.faculty-accomplishment')->with('error', 'Folder not found.');
        }
    
        $currentDateTime = Carbon::now('Asia/Manila');
        $uploadSchedule = UploadSchedule::first();
        
        $isUploadOpen = false;
        $statusMessage = '';
        $remainingTime = null;
        $formattedStartDate = null;
        $formattedEndDate = null;
        
        if ($uploadSchedule) {
            $startDateTime = Carbon::parse($uploadSchedule->start_date . ' ' . $uploadSchedule->start_time, 'Asia/Manila');
            $endDateTime = Carbon::parse($uploadSchedule->end_date . ' ' . $uploadSchedule->stop_time, 'Asia/Manila');
        
            $formattedStartDate = $startDateTime->format('l, j F Y, g:i A');
            $formattedEndDate = $endDateTime->format('l, j F Y, g:i A');
        
            // Log::info("Current DateTime: " . $currentDateTime);
            // Log::info("Start DateTime: " . $startDateTime);
            // Log::info("End DateTime: " . $endDateTime);
        
            if ($currentDateTime->between($startDateTime, $endDateTime)) {
                $isUploadOpen = true;
                $remainingTime = $currentDateTime->diffForHumans($endDateTime, [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                $statusMessage = "Upload is open. Closes in {$remainingTime}.";
            } elseif ($currentDateTime->lt($startDateTime)) {
                $isUploadOpen = false;
                $remainingTime = $currentDateTime->diffForHumans($startDateTime, [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                $statusMessage = "Upload opens in {$remainingTime}.";
            } elseif ($currentDateTime->gt($endDateTime)) {
                $isUploadOpen = false;
                $statusMessage = "The upload period has ended.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
        
    
        $facultyInfo = json_decode($this->getFacultyInfo(), true);
        $semester = $facultyInfo['faculty']['subjects'][0]['semester']['semester'] ?? 'N/A';
        $files = CoursesFile::where('folder_name_id', $folder_name_id)
            ->where('user_login_id', auth()->id())
            ->get();
    
        $filesWithSubjects = $files->map(function ($file) use ($facultyInfo) {
            $subjectInfo = collect($facultyInfo['faculty']['subjects'])->firstWhere('name', $file->subject);
    
            if ($subjectInfo) {
                $file->subject_name = $file->subject;
                $file->year = $subjectInfo['year_programs'][0]['year'] ?? 'N/A';
                $file->program = $subjectInfo['year_programs'][0]['program'] ?? 'N/A';
                $file->code = $subjectInfo['code'] ?? 'N/A';
            } else {
                $file->subject_name = 'N/A';
                $file->year = 'N/A';
                $file->program = 'N/A';
                $file->code = 'N/A';
            }
    
            return $file;
        });
    
        $groupedFiles = $filesWithSubjects->groupBy('semester');
    
        $subjects = $facultyInfo['faculty']['subjects'] ?? [];
    
        $folderInputs = FolderInput::where('folder_name_id', $folder->folder_name_id)->get();
    
        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())->get();
        $notificationCount = $notifications->count();
    
        $folders = FolderName::all();
    
        $firstName = $facultyInfo['faculty']['first_name'] ?? 'N/A';
        $lastName = $facultyInfo['faculty']['last_name'] ?? 'N/A';
    
        return view('faculty.accomplishment.uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'semester' => $semester,
            'subjects' => $subjects,
            'filesWithSubjects' => $filesWithSubjects,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folders' => $folders,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'folder_inputs' => $folderInputs,
            'isUploadOpen' => $isUploadOpen,
            'statusMessage' => $statusMessage,
            'formattedStartDate' => $formattedStartDate,
            'formattedEndDate' => $formattedEndDate
        ]);
    }
    
    
    //show announcement page
    public function announcementPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $userEmail = auth()->user()->email; 
    
        $folders = FolderName::all();
    
        $notifications = \App\Models\Notification::where('user_login_id', $userId)->get();
        $notificationCount = $notifications->count();
    
        $announcements = \App\Models\Announcement::where(function ($query) use ($userEmail) {
            $query->where('type_of_recepient', 'All Faculty')
                  ->orWhere('type_of_recepient', $userEmail);
        })->where('published', 1) 
          ->orderBy('created_at', 'desc') 
          ->get();
    
        foreach ($announcements as $announcement) {
            $emails = explode(',', $announcement->type_of_recepient);
            if (count($emails) > 3) {
                $announcement->displayEmails = array_slice($emails, 0, 3);
                $announcement->moreEmailsCount = count($emails) - 3;
            } else {
                $announcement->displayEmails = $emails;
                $announcement->moreEmailsCount = 0;
            }
        }

        $folder = $folders->first(); 
    
        $facultyInfo = json_decode($this->getFacultyInfo(), true);
        $firstName = $facultyInfo['faculty']['first_name'];
        $lastName = $facultyInfo['faculty']['last_name'];
    

        return view('faculty.announcement', [
            'folders' => $folders,
            'folder' => $folder, 
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'announcements' => $announcements,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);
    }
  
    //show upload schedule
    public function showUploadForm()
    {
        $currentDateTime = Carbon::now('Asia/Manila');
        $uploadSchedule = UploadSchedule::first();

        $isUploadOpen = $uploadSchedule && 
                        $currentDateTime->between(
                            Carbon::parse($uploadSchedule->start_date . ' ' . $uploadSchedule->start_time),
                            Carbon::parse($uploadSchedule->end_date . ' ' . $uploadSchedule->stop_time)
                        );

        return view('upload_form', compact('isUploadOpen'));
    }

    public function archiveFile($id)
    {
        $file = CoursesFile::find($id);

        if ($file->status !== 'Approved') {
            return back()->with('error', 'Only approved files can be archived.');
        }

        // Set the file as archived
        $file->is_archived = true;
        $file->save();

        // Subtract the file size from the total storage used
        $userId = auth()->id();
        $totalStorageUsed = \App\Models\CoursesFile::where('user_login_id', $userId)->sum('file_size');
        $file->user->total_storage_used = $totalStorageUsed - $file->file_size;
        $file->user->save();

        return back()->with('success', 'File archived successfully!');
    }

}
