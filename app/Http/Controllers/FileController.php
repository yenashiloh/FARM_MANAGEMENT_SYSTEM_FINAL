<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;

class FileController extends Controller
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

   public function approve($courses_files_id)
    {
        $file = CoursesFile::findOrFail($courses_files_id);
        $file->status = 'Approved';
        $file->save();

        $folder = FolderName::find($file->folder_name_id);

        Notification::create([
            'courses_files_id' => $file->courses_files_id,
            'user_login_id' => $file->user_login_id,
            'folder_name_id' => $file->folder_name_id,
            'sender' => 'Admin', 
            'notification_message' => 'The course ' . $file->subject . ' in ' . ($folder ? $folder->folder_name : 'Unknown Folder') . ' has been approved.',
        ]);

        return redirect()->back()->with('success', 'File approved successfully!');
    }

    public function decline($courses_files_id, Request $request)
    {
        try {
            $file = CoursesFile::findOrFail($courses_files_id);

            $file->status = 'Declined';
            $file->declined_reason = $request->input('declineReason');
            $file->save();

            $folder = FolderName::find($file->folder_name_id);

            Notification::create([
                'courses_files_id' => $file->courses_files_id,
                'user_login_id' => $file->user_login_id,
                'folder_name_id' => $file->folder_name_id,
                'sender' => 'Admin', 
                'notification_message' => 'The course' . $file->subject . ' in ' . ($folder ? $folder->folder_name : 'Unknown Folder') . ' is declined. Reason: ' . $file->declined_reason,
            ]);

            return redirect()->route('admin.accomplishment.view-accomplishment', [
                'user_login_id' => $file->user_login_id,
                'folder_name_id' => $file->folder_name_id 
            ])->with('success', 'File declined successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the file. Please try again.');
        }
    }


    
}
