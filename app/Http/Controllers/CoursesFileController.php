<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;

class CoursesFileController extends Controller
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
    

    //store files
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'folder_name_id' => 'required|exists:folder_name,folder_name_id'
        ]);
        
        try {
            $userLoginId = auth()->user()->user_login_id;
            $facultyInfoJson = $this->getFacultyInfo();
            $facultyInfo = json_decode($facultyInfoJson, true);
            $semester = $facultyInfo['faculty']['subjects'][0]['semester']['semester']; 
            $folder_name_id = $request->input('folder_name_id');
    
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('courses_files', 'public'); 
                
                $subject_name = $facultyInfo['faculty']['subjects'][$index]['name'] ?? null; 
    
                CoursesFile::create([
                    'files' => $path,
                    'original_file_name' => $file->getClientOriginalName(), 
                    'user_login_id' => $userLoginId,
                    'folder_name_id' => $folder_name_id,
                    'semester' => $semester,
                    'subject' => $subject_name, 
                ]);
            }
    
            return redirect()->back()->with('success', 'Files uploaded successfully!');
        } catch (\Exception $e) {
            logger()->error('File upload failed: ' . $e->getMessage());
    
            return redirect()->back()->with('error', 'File upload failed. Please try again.');
        }
    }
    
    
    //update file
    public function update(Request $request)
    {
        $request->validate([
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'existingFiles.*' => 'required|string', 
            'semester' => 'required|string', 
            'folder_name_id' => 'required|exists:folder_name,folder_name_id' 
        ]);
    
        try {
            $userLoginId = auth()->user()->user_login_id;
            $semester = $request->input('semester');
            $folder_name_id = $request->input('folder_name_id');
    
            foreach ($request->file('files') as $index => $file) {
                if ($file) {
                    $path = $file->store('courses_files', 'public');
                    
                    $existingFileData = explode('|', $request->input("existingFiles.$index"));
                    $existingFilePath = $existingFileData[0]; 
                    $existingFileName = $existingFileData[1]; 
                    $originalStatus = $request->input("originalStatus.$index"); // Get the original status
                    
                    // Determine the new status
                    $newStatus = ($originalStatus === 'Declined') ? 'To Review' : $originalStatus;
    
                    if ($existingFilePath && Storage::disk('public')->exists($existingFilePath)) {
                        Storage::disk('public')->delete($existingFilePath);
                    }
                    
                    CoursesFile::where('files', $existingFilePath)->update([
                        'files' => $path,
                        'original_file_name' => $file->getClientOriginalName(),
                        'user_login_id' => $userLoginId,
                        'folder_name_id' => $folder_name_id,
                        'semester' => $semester,
                        'subject' => $request->input("subject.$index"), 
                        'status' => $newStatus // Update the status to "To Review" if originally "Declined"
                    ]);
                }
            }
    
            return redirect()->back()->with('success', 'Files updated successfully!');
        } catch (\Exception $e) {
            logger()->error('File update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'File update failed. Please try again.');
        }
    }
    
    //edit file
    public function edit($semester)
    {
        $files = CoursesFile::where('semester', $semester)->get();
        return view('your-view-file', [
            'groupedFiles' => $files->groupBy('semester'),
            'files' => $files,
        ]);
    }
    public function getFileDetails($id)
    {
        $file = CoursesFile::find($id);
    
        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }
    
        return response()->json($file);
    }
    
   
    }
