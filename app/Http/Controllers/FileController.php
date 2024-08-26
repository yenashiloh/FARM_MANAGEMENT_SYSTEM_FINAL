<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;
use App\Models\UserDetails;
use App\Exports\CoursesFilesExport;
use App\Exports\ExportNotPassed;
use App\Exports\GenerateAllReports;
use App\Exports\AllReportNotPassed;
use Maatwebsite\Excel\Facades\Excel;


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

    //approve files
    public function approve($courses_files_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if ($user->role !== 'admin') {
            $loginUrl = url('login');
            return redirect($loginUrl);
        }

        $file = CoursesFile::findOrFail($courses_files_id);
        $file->status = 'Approved';
        $file->save();

        $folder = FolderName::find($file->folder_name_id);
        $userDetails = UserDetails::where('user_login_id', $user->user_login_id)->first();

        $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->last_name : 'Unknown Sender';

        Notification::create([
            'courses_files_id' => $file->courses_files_id,
            'user_login_id' => $file->user_login_id,
            'folder_name_id' => $file->folder_name_id,
            'sender' => $senderName,
       'notification_message' => 'approved the course ' . $file->subject . ' in ' . ($folder ? $folder->folder_name : 'Unknown Folder') . '.',

        ]);

        return redirect()->back()->with('success', 'File approved successfully!');
    }

    //decline files   
    public function decline($courses_files_id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== 'admin') {
            $loginUrl = url('login');
            return redirect($loginUrl);
        }

        try {
            $file = CoursesFile::findOrFail($courses_files_id);

            $file->status = 'Declined';
            $file->declined_reason = $request->input('declineReason');
            $file->save();

            $folder = FolderName::find($file->folder_name_id);
            $userDetails = UserDetails::where('user_login_id', $user->user_login_id)->first();

            $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->last_name : 'Unknown Sender';

            Notification::create([
                'courses_files_id' => $file->courses_files_id,
                'user_login_id' => $file->user_login_id,
                'folder_name_id' => $file->folder_name_id,
                'sender' => $senderName,
              'notification_message' =>'declined the course ' . $file->subject . ' in ' . ($folder ? $folder->folder_name : 'Unknown Folder'),

            ]);

            return redirect()->route('admin.accomplishment.view-accomplishment', [
                'user_login_id' => $file->user_login_id,
                'folder_name_id' => $file->folder_name_id 
            ])->with('success', 'File declined successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the file. Please try again.');
        }
    }

    //delete files
    public function destroy($courses_files_id)
    {
        try {
            $file = CoursesFile::findOrFail($courses_files_id);
            Storage::delete('/' . $file->files);
            $file->delete();

            return response()->json(['success' => 'File deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting file.'], 500);
        }
    }

    //generate reports
    public function generateAllReports($semester)
    {
        return Excel::download(new GenerateAllReports($semester), 'faculty_report.xlsx');
    }


}
