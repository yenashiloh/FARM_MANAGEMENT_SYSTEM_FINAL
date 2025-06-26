<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\CourseSchedule;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\FLSSApiService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class CoursesFileController extends Controller
{
     protected $flssApiService;

    public function __construct(FLSSApiService $flssApiService)
    {
        $this->flssApiService = $flssApiService;
    }
    
    //store classroom management files
    public function store(Request $request)
    {
        try {
            $messages = [
                'semester.required' => 'Please select a semester.',
                'school_year.required' => 'Please select a school year.',
                'classroom_folder.required' => 'Please select a requirement folder.',
                'files.*.*.required' => 'Please select a file to upload.',
                'files.*.*.mimes' => 'Only PDF files are allowed.',
                'files.*.*.max' => 'File size should not exceed 1gb.'
            ];
            
            $validator = \Validator::make($request->all(), [
                'semester' => 'required|string|in:First Semester,Second Semester,Summer',
                'school_year' => 'required|string',
                'classroom_folder' => 'required|string|exists:folder_name,folder_name',
                'files' => 'required|array',
                'files.*.*' => 'required|mimes:pdf|max:1048576',
            ], $messages);
    
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
    
            $userId = auth()->id();
            
            $folderName = FolderName::where('folder_name', $request->classroom_folder)
                                   ->where('main_folder_name', 'Classroom Management')
                                   ->first();
            
            if (!$folderName) {
                return back()
                    ->withErrors(['folder' => 'Please select a valid requirement folder'])
                    ->withInput();
            }
    
            \DB::beginTransaction();
            try {
                foreach ($request->file('files') as $subject => $fileArray) {
                    $files = is_array($fileArray) ? $fileArray : [$fileArray];
    
                    $fileData = [];
                    $fileNames = [];
                    $totalSize = 0;
    
                    foreach ($files as $file) {
                        if (!$file->isValid()) {
                            throw new \Exception('File upload failed for ' . $subject);
                        }
    
                        $originalName = $file->getClientOriginalName();
                        $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('course_files', $filename, 'public');
                        $fileSize = $file->getSize();
    
                        $fileData[] = [
                            'path' => $path,
                            'name' => $originalName,
                            'size' => $fileSize,
                        ];
                        $fileNames[] = $originalName;
                        $totalSize += $fileSize;
                    }
    
                    CoursesFile::create([
                        'user_login_id' => $userId,
                        'folder_name_id' => $folderName->folder_name_id,
                        'course_schedule_id' => null,
                        'files' => json_encode($fileData), 
                        'semester' => $request->semester,
                        'school_year' => $request->school_year,
                        'original_file_name' => json_encode($fileNames), 
                        'subject' => $subject,
                        'status' => 'To Review',
                        'file_size' => $totalSize, 
                        'is_archived' => false
                    ]);
                }
    
                \DB::commit();
                return back()->with('success', 'Files uploaded successfully');
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Transaction error: ' . $e->getMessage());
                return back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred while uploading files. Please try again.'])
                ->withInput();
        }
    }

    
    //store test administaration files
    public function storeTestAdministration(Request $request)
    {
        try {
            $messages = [
                'semester.required' => 'Please select a semester.',
                'school_year.required' => 'Please select a school year.',
                'classroom_folder.required' => 'Please select a requirement folder.',
                'files.*.*.required' => 'Please select a file to upload.',
                'files.*.*.mimes' => 'Only PDF files are allowed.',
                'files.*.*.max' => 'File size should not exceed 1gb.'
            ];
            
            $validator = \Validator::make($request->all(), [
                'semester' => 'required|string|in:First Semester,Second Semester,Summer',
                'school_year' => 'required|string',
                'classroom_folder' => 'required|string|exists:folder_name,folder_name',
                'files' => 'required|array',
                'files.*.*' => 'required|mimes:pdf|max:1048576',
            ], $messages);
    
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
    
            $userId = auth()->id();
            
            $folderName = FolderName::where('folder_name', $request->classroom_folder)
                                   ->where('main_folder_name', 'Test Administration')
                                   ->first();
            
            if (!$folderName) {
                return back()
                    ->withErrors(['folder' => 'Please select a valid requirement folder'])
                    ->withInput();
            }
    
            \DB::beginTransaction();
            try {
                foreach ($request->file('files') as $subject => $fileArray) {
                    $files = is_array($fileArray) ? $fileArray : [$fileArray];
    
                    $fileData = [];
                    $fileNames = [];
                    $totalSize = 0;
    
                    foreach ($files as $file) {
                        if (!$file->isValid()) {
                            throw new \Exception('File upload failed for ' . $subject);
                        }
    
                        $originalName = $file->getClientOriginalName();
                        $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('course_files', $filename, 'public');
                        $fileSize = $file->getSize();
    
                        $fileData[] = [
                            'path' => $path,
                            'name' => $originalName,
                            'size' => $fileSize,
                        ];
                        $fileNames[] = $originalName;
                        $totalSize += $fileSize;
                    }
    
                    CoursesFile::create([
                        'user_login_id' => $userId,
                        'folder_name_id' => $folderName->folder_name_id,
                        'course_schedule_id' => null,
                        'files' => json_encode($fileData), 
                        'semester' => $request->semester,
                        'school_year' => $request->school_year,
                        'original_file_name' => json_encode($fileNames), 
                        'subject' => $subject,
                        'status' => 'To Review',
                        'file_size' => $totalSize, 
                        'is_archived' => false
                    ]);
                }
    
                \DB::commit();
                return back()->with('success', 'Files uploaded successfully');
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Transaction error: ' . $e->getMessage());
                return back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred while uploading files. Please try again.'])
                ->withInput();
        }
    }

    //store syllabus files
    public function storeSyllabus(Request $request)
    {
        try {
            $messages = [
                'semester.required' => 'Please select a semester.',
                'school_year.required' => 'Please select a school year.',
                'classroom_folder.required' => 'Please select a requirement folder.',
                'files.*.*.required' => 'Please select a file to upload.',
                'files.*.*.mimes' => 'Only PDF files are allowed.',
                'files.*.*.max' => 'File size should not exceed 1gb.'
            ];
            
            $validator = \Validator::make($request->all(), [
                'semester' => 'required|string|in:First Semester,Second Semester,Summer',
                'school_year' => 'required|string',
                'classroom_folder' => 'required|string|exists:folder_name,folder_name',
                'files' => 'required|array',
                'files.*.*' => 'required|mimes:pdf|max:1048576',
            ], $messages);
    
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
    
            $userId = auth()->id();
            
            $folderName = FolderName::where('folder_name', $request->classroom_folder)
                                   ->where('main_folder_name', 'Syllabus Preparation')
                                   ->first();
            
            if (!$folderName) {
                return back()
                    ->withErrors(['folder' => 'Please select a valid requirement folder'])
                    ->withInput();
            }
    
            \DB::beginTransaction();
            try {
                foreach ($request->file('files') as $subject => $fileArray) {
                    $files = is_array($fileArray) ? $fileArray : [$fileArray];
    
                    $fileData = [];
                    $fileNames = [];
                    $totalSize = 0;
    
                    foreach ($files as $file) {
                        if (!$file->isValid()) {
                            throw new \Exception('File upload failed for ' . $subject);
                        }
    
                        $originalName = $file->getClientOriginalName();
                        $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('course_files', $filename, 'public');
                        $fileSize = $file->getSize();
    
                        $fileData[] = [
                            'path' => $path,
                            'name' => $originalName,
                            'size' => $fileSize,
                        ];
                        $fileNames[] = $originalName;
                        $totalSize += $fileSize;
                    }
    
                    CoursesFile::create([
                        'user_login_id' => $userId,
                        'folder_name_id' => $folderName->folder_name_id,
                        'course_schedule_id' => null,
                        'files' => json_encode($fileData), 
                        'semester' => $request->semester,
                        'school_year' => $request->school_year,
                        'original_file_name' => json_encode($fileNames), 
                        'subject' => $subject,
                        'status' => 'To Review',
                        'file_size' => $totalSize, 
                        'is_archived' => false
                    ]);
                }
    
                \DB::commit();
                return back()->with('success', 'Files uploaded successfully');
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Transaction error: ' . $e->getMessage());
                return back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred while uploading files. Please try again.'])
                ->withInput();
        }
    }

    //update file
   public function updateFiles(Request $request)
    {
        try {
            $file = CoursesFile::findOrFail($request->fileId);
    
           $request->validate([
        'new_files.*' => 'file|mimes:pdf,jpg,png,jpeg',
    ], [
        'new_files.*.mimes' => 'Only PDF, JPG, PNG, and JPEG files are allowed.',
    ]);
    
    
            $currentFiles = json_decode($file->files, true) ?: [];
            $currentOriginalNames = json_decode($file->original_file_name, true) ?: [];
    
            $remainingFileIndices = json_decode($request->input('remaining_file_indices', '[]'), true);
    
            $filteredFiles = [];
            $filteredOriginalNames = [];
            foreach ($remainingFileIndices as $index) {
                if (isset($currentFiles[$index]) && isset($currentOriginalNames[$index])) {
                    $filteredFiles[] = $currentFiles[$index];
                    $filteredOriginalNames[] = $currentOriginalNames[$index];
                }
            }
    
            if ($request->hasFile('new_files')) {
                foreach ($request->file('new_files') as $newFile) {
                    $originalFileName = $newFile->getClientOriginalName();
                    $path = $newFile->store('course_files', 'public');
    
                    $filteredFiles[] = [
                        'path' => $path,
                        'name' => $originalFileName,
                    ];
                    $filteredOriginalNames[] = $originalFileName;
                }
            }
    
            if (empty($filteredFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one file must remain'
                ], 400);
            }
    
            $file->update([
                'files' => json_encode($filteredFiles),
                'original_file_name' => json_encode($filteredOriginalNames)
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Files updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => ' ' . $e->getMessage()
            ], 500);
        }
    }

        
   public function addNewFile(Request $request)
    {
        $request->validate([
             'files.*' => 'file|mimes:pdf,jpeg,jpg,png,gif',
            'reference_file_id' => 'required' 
        ]);
    
        try {
            $referenceFile = CoursesFile::findOrFail($request->reference_file_id);
            $userLoginId = auth()->user()->user_login_id;
    
            if ($request->hasFile('files')) {
                $newFiles = $request->file('files');
                $filePaths = [];
                $fileNames = [];
                $totalSize = 0;
    
                foreach ($newFiles as $uploadedFile) {
                    $path = $uploadedFile->store('courses_files', 'public');
                    $filePaths[] = $path;
                    $fileNames[] = $uploadedFile->getClientOriginalName();
                    $totalSize += Storage::disk('public')->size($path);
                }
    
                $newFile = new CoursesFile();
                $newFile->files = implode(',', $filePaths);
                $newFile->original_file_name = implode(',', $fileNames);
                $newFile->user_login_id = $userLoginId;
                $newFile->folder_name_id = $referenceFile->folder_name_id;
                $newFile->course_schedule_id = $referenceFile->course_schedule_id;
                $newFile->folder_input_id = $referenceFile->folder_input_id;
                $newFile->semester = $referenceFile->semester;
                $newFile->school_year = $referenceFile->school_year;
                $newFile->subject = $referenceFile->subject;
                $newFile->status = 'To Review';
                $newFile->file_size = $totalSize;
                $newFile->is_archived = false;
                $newFile->save();
    
                $folderName = FolderName::find($referenceFile->folder_name_id)->folder_name;
                $currentUser = UserLogin::findOrFail($userLoginId);
                $senderName = $currentUser->first_name . ' ' . $currentUser->surname;
    
                $adminUsers = UserLogin::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    Notification::create([
                        'courses_files_id' => $newFile->courses_files_id,
                        'user_login_id' => $admin->user_login_id,
                        'folder_name_id' => $referenceFile->folder_name_id,
                        'sender' => $senderName,
                        'sender_user_login_id' => $userLoginId,
                        'notification_message' => "has submitted additional files for the course {$referenceFile->subject} in {$folderName}.",
                        'is_read' => false,
                    ]);
                }
    
                return response()->json(['success' => true]);
            }
    
            return response()->json(['success' => false, 'message' => 'No files were uploaded.']);
        } catch (\Exception $e) {
            logger()->error('File creation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'File upload failed. Please try again.'], 500);
        }
    }

    //show view archive page
   public function showArchive()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $userId = auth()->id();
        $user = auth()->user();
        
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $firstName = $user->first_name;
        $surname = $user->surname;
        
        $folder = FolderName::first();
        
        if (!$folder) {
            return redirect()->back()->with('error', 'Folder not found.');
        }
    
        $folders = FolderName::all();
        $folderInputs = CoursesFile::where('folder_name_id', $folder->folder_name_id)->get();
    
        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())
                ->orderBy('created_at', 'desc') 
                ->get();
        $notificationCount = $notifications->count();
    
       $uploadedFiles = CoursesFile::where('user_login_id', $user->user_login_id)
        ->where('is_archived', 1)
        ->with(['userLogin', 'folderName', 'folderInput', 'courseSchedule'])
        ->get()
        ->map(function ($file) {
            // Decode the 'files' attribute if it's a JSON field
            $file->files = json_decode($file->files, true);
            return $file;
        });
    
        // Get course schedules from database only
        $courseSchedules = DB::table('course_schedules')
        ->where('user_login_id', $user->user_login_id) 
        ->get()
        ->map(function ($schedule) {
            return (object) [
                'course_schedule_id' => $schedule->course_schedule_id,
                'course_code' => $schedule->course_code,
                'course_subjects' => $schedule->course_subjects,
                'year_section' => $schedule->year_section,
                'program' => $schedule->program,
                'schedule' => $schedule->schedule,
                'semester' => $schedule->semester,
                'school_year' => $schedule->school_year
            ];
        });
    
        // Fetch semesters and school years for current faculty
        $availableSemesters = collect(['First Semester', 'Second Semester', 'Summer']);
        
        // Keep your existing school year code
        $availableSchoolYears = DB::table('course_schedules')
             ->where('user_login_id', $user->user_login_id) 
            ->select('school_year')
            ->distinct()
            ->whereNotNull('school_year')
            ->orderBy('school_year')
            ->pluck('school_year');
        
        if ($availableSchoolYears->isEmpty()) {
            // Current and next academic year
            $currentYear = now()->year;
            $availableSchoolYears = collect([
                ($currentYear-1).'-'.$currentYear,
                $currentYear.'-'.($currentYear+1)
            ]);
        }
    
        return view('faculty.view-archive', [
            'uploadedFiles' => $uploadedFiles,
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folderInputs' => $folderInputs,
            'firstName' => $firstName,
            'surname' => $surname,
            'folders' => $folders,
             'availableSemesters' => $availableSemesters,  // Changed comma to =>
    'availableSchoolYears' => $availableSchoolYears 
        ]);
    }

    //archive file
  public function archive(Request $request)
   {
    try {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Archive files within date range
        $filesArchived = CoursesFile::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->whereHas('folderName', function($query) {
                $query->where('main_folder_name', 'Classroom Management');
            })
            ->where('is_archived', 0)
            ->update([
                'is_archived' => 1,
                'archived_at' => Carbon::now()
            ]);

        return response()->json([
            'success' => true,
            'message' => $filesArchived . ' files have been archived successfully',
            'count' => $filesArchived
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error archiving files: ' . $e->getMessage()
        ], 500);
    }
   }


    //archive classroom management
    public function archiveByDate(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
    
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            Log::info('Date range being queried:', [
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString()
            ]);
    
            $classroomManagementFolderIds = FolderName::where('main_folder_name', 'Classroom Management')
                ->pluck('folder_name_id');
    
            $filesToArchive = CoursesFile::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('folder_name_id', $classroomManagementFolderIds)
                ->where('is_archived', 0)
                ->where('status', 'Approved')
                ->get();
    
            Log::info('Files found:', [
                'count' => $filesToArchive->count(),
                'files' => $filesToArchive->map(function($file) {
                    return [
                        'id' => $file->courses_files_id,
                        'created_at' => $file->created_at,
                        'folder_id' => $file->folder_name_id
                    ];
                })
            ]);
    
            if ($filesToArchive->count() > 0) {
                $filesArchived = CoursesFile::query()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('folder_name_id', $classroomManagementFolderIds)
                    ->where('is_archived', 0)
                    ->where('status', 'Approved')
                    ->update([
                        'is_archived' => 1,
                        'archived_at' => now()
                    ]);
    
                return response()->json([
                    'success' => true,
                    'message' => $filesArchived . ' files have been archived successfully',
                    'count' => $filesArchived,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => $filesToArchive->count()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No approved files found to archive in the selected date range',
                    'count' => 0,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => 0
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Archive Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving files: ' . $e->getMessage()
            ], 500);
        }
    }
    
     //archive test administration
    public function archiveByTestAdministration(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
    
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            Log::info('Date range being queried:', [
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString()
            ]);
    
            $classroomManagementFolderIds = FolderName::where('main_folder_name', 'Classroom Management')
                ->pluck('folder_name_id');
    
            $filesToArchive = CoursesFile::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('folder_name_id', $classroomManagementFolderIds)
                ->where('is_archived', 0)
                ->where('status', 'Approved')
                ->get();
    
            Log::info('Files found:', [
                'count' => $filesToArchive->count(),
                'files' => $filesToArchive->map(function($file) {
                    return [
                        'id' => $file->courses_files_id,
                        'created_at' => $file->created_at,
                        'folder_id' => $file->folder_name_id
                    ];
                })
            ]);
    
            if ($filesToArchive->count() > 0) {
                $filesArchived = CoursesFile::query()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('folder_name_id', $classroomManagementFolderIds)
                    ->where('is_archived', 0)
                    ->where('status', 'Approved')
                    ->update([
                        'is_archived' => 1,
                        'archived_at' => now()
                    ]);
    
                return response()->json([
                    'success' => true,
                    'message' => $filesArchived . ' files have been archived successfully',
                    'count' => $filesArchived,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => $filesToArchive->count()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No approved files found to archive in the selected date range',
                    'count' => 0,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => 0
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Archive Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving files: ' . $e->getMessage()
            ], 500);
        }
    }
    
    //archive syllabus
    public function archiveBySyllabus(Request $request)
    {
       try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
    
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            Log::info('Date range being queried:', [
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString()
            ]);
    
            $classroomManagementFolderIds = FolderName::where('main_folder_name', 'Classroom Management')
                ->pluck('folder_name_id');
    
            $filesToArchive = CoursesFile::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('folder_name_id', $classroomManagementFolderIds)
                ->where('is_archived', 0)
                ->where('status', 'Approved')
                ->get();
    
            Log::info('Files found:', [
                'count' => $filesToArchive->count(),
                'files' => $filesToArchive->map(function($file) {
                    return [
                        'id' => $file->courses_files_id,
                        'created_at' => $file->created_at,
                        'folder_id' => $file->folder_name_id
                    ];
                })
            ]);
    
            if ($filesToArchive->count() > 0) {
                $filesArchived = CoursesFile::query()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('folder_name_id', $classroomManagementFolderIds)
                    ->where('is_archived', 0)
                    ->where('status', 'Approved')
                    ->update([
                        'is_archived' => 1,
                        'archived_at' => now()
                    ]);
    
                return response()->json([
                    'success' => true,
                    'message' => $filesArchived . ' files have been archived successfully',
                    'count' => $filesArchived,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => $filesToArchive->count()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No approved files found to archive in the selected date range',
                    'count' => 0,
                    'debug' => [
                        'start_date' => $startDate->toDateTimeString(),
                        'end_date' => $endDate->toDateTimeString(),
                        'folder_ids' => $classroomManagementFolderIds,
                        'files_found' => 0
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Archive Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving files: ' . $e->getMessage()
            ], 500);
        }
    }

    //unarchive files
    public function unarchive($courses_files_id)
    {
        $file = CoursesFile::find($courses_files_id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if ($file->user_login_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $file->is_archived = false;
        $file->save();

        return redirect()->back()->with('success', 'File has been restored');
    }

    //archive all
    public function archiveAll(Request $request)
    {
        $fileIds = json_decode($request->input('file_ids', '[]'), true);
        \Log::info('Received file IDs:', $fileIds);
    
        if (!empty($fileIds)) {
            $query = CoursesFile::whereIn('courses_files_id', $fileIds)
                ->where('status', 'Approved');
            
            \Log::info('SQL query:', [$query->toSql()]);
            \Log::info('SQL bindings:', $query->getBindings());
    
            $updatedCount = $query->update(['is_archived' => true]);
    
            \Log::info('Updated count:', [$updatedCount]);
    
            if ($updatedCount > 0) {
                return redirect()->back()->with('success', "$updatedCount files have been archived.");
            } else {
                return redirect()->back()->with('error', 'No eligible files were found to archive.');
            }
        }
    
        return redirect()->back()->with('error', 'No files selected.');
    }

    //restore achive
    public function bulkUnarchive(Request $request)
    {
        $fileIds = $request->input('file_ids', []);
        
        if (!empty($fileIds)) {
            CoursesFile::whereIn('courses_files_id', $fileIds)->update(['is_archived' => false]);
            return redirect()->back()->with('success', count($fileIds) . ' files have been restored.');
        }

        return redirect()->back()->with('error', 'No files selected for restoration.');
    }
    
    public function destroyFacultyFile($fileId)
    {
        // Get the fileName from the request body
        $fileName = request()->input('fileName');
        // Decode the URL-encoded file name
        $decodedFileName = urldecode($fileName);
    
        // Find the file record by ID
        $file = CoursesFile::find($fileId);
    
        // Check if the file exists
        if ($file) {
            // Get all files in the 'files' column (assuming it's a comma-separated list)
            $files = explode(',', $file->files);
    
            // Check if the decoded fileName is in the files array
            if (($key = array_search($decodedFileName, $files)) !== false) {
                // Remove the file from the array
                unset($files[$key]);
    
                // Update the 'files' column in the database
                $file->files = implode(',', $files);
                $file->save();
    
                // Delete the file from storage
                if (Storage::delete($decodedFileName)) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => 'File deletion from storage failed']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'File not found in the list']);
            }
        }
    
        return response()->json(['success' => false, 'message' => 'File record not found']);
    }

    public function archiveOneFile($id)
    {
        $file = CoursesFile::find($id);

        if ($file) {
            $file->is_archived = true;
            $file->save();

            return redirect()->back()->with('success', 'File archived successfully!');
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    // In your controller file
    public function deleteSingleFile($fileId, $fileIndex)
    {
        try {
            $coursesFile = CoursesFile::findOrFail($fileId);
            
            // Check if user has permission to delete this file
            if ($coursesFile->user_login_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Check if file is approved
            if ($coursesFile->status === 'Approved') {
                return response()->json(['error' => 'Cannot delete approved files'], 403);
            }
    
            // Get current files array
            $files = json_decode($coursesFile->files, true);
            $originalNames = json_decode($coursesFile->original_file_name, true);
    
            // Check if index exists
            if (!isset($files[$fileIndex])) {
                return response()->json(['error' => 'File not found'], 404);
            }
    
            // Delete the actual file from storage
            Storage::disk('public')->delete($files[$fileIndex]['path']);
    
            // Remove the file from arrays
            unset($files[$fileIndex]);
            unset($originalNames[$fileIndex]);
    
            // Reindex arrays
            $files = array_values($files);
            $originalNames = array_values($originalNames);
    
            if (empty($files)) {
                // If no files left, delete the entire record
                $coursesFile->delete();
                return response()->json(['success' => true, 'reload' => true]);
            } else {
                // Update the record with remaining files
                $coursesFile->update([
                    'files' => json_encode($files),
                    'original_file_name' => json_encode($originalNames)
                ]);
            }
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }




}
