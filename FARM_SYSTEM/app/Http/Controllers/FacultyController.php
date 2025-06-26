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
use App\Models\CourseSchedule;
use App\Models\RequestUploadAccess;
use App\Models\Department;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Paginator;
use App\Services\FLSSApiService;
use Illuminate\Support\Facades\DB;



class FacultyController extends Controller
{
    protected $flssApiService;

    public function __construct(FLSSApiService $flssApiService)
    {
        $this->flssApiService = $flssApiService;
    }
    
    //faculty logout
    public function facultyLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    //uploaded-file page
    public function showUploadedFiles()
    {
        // authentication check
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
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
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
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

       $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->where('courses_files.is_archived', 0)
        ->where('folder_name.main_folder_name', 'Classroom Management')
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();

    
    
        // Process files with subjects
       $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });
    
    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();

    //requirements that already upload 
   $uploadedFiles = CoursesFile::where('user_login_id', $userId)
        ->select('folder_name_id', 'semester', 'school_year')
        ->get()
        ->groupBy(function($file) {
            return $file->semester . '_' . $file->school_year;
        });

    // Get all folders with Classroom Management
    $allFolders = FolderName::where('main_folder_name', 'Classroom Management')->get();
    
    // Structure the data for JavaScript
    $uploadedFoldersByPeriod = [];
    foreach ($uploadedFiles as $period => $files) {
        list($semester, $schoolYear) = explode('_', $period);
        $uploadedFoldersByPeriod[$period] = $files->pluck('folder_name_id')->toArray();
    }

    
    $hasUploaded = $consolidatedFiles->isNotEmpty(); 
        return view('faculty.accomplishment.uploaded-files', compact(
            'folderStatus', 'folderProgress', 'overallProgress',
            'courseSchedules', 'consolidatedFiles', 'isUploadOpen',
            'statusMessage', 'remainingTime', 'formattedStartDate',
            'formattedEndDate', 'departmentProgress', 'notifications',
            'firstName',  'surname', 'folders', 'hasUploaded',  'messages', 'allFolders', 'uploadedFoldersByPeriod', 'availableSemesters', 'availableSchoolYears' 
        ));
    }

    //show uploaded files page
    public function viewUploadedFiles($user_login_id, $folder_name_id, $semester = null)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $userId = auth()->id();
        $user = auth()->user();
        
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->back()->with('error', 'Folder not found.');
        }
    
        $folders = FolderName::all();
        $folderInputs = FolderInput::where('folder_name_id', $folder->folder_name_id)->get();
    
        $notifications = \App\Models\Notification::where('user_login_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();
    
        $notificationCount = $notifications->count();
    
        $uploadedFilesQuery = CoursesFile::where('courses_files.user_login_id', $user_login_id)
            ->where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.is_archived', false)
            ->with(['userLogin', 'folderName', 'folderInput', 'courseSchedule']);
    
        if ($semester) {
            $uploadedFilesQuery->whereHas('courseSchedule', function ($query) use ($semester) {
                $query->where('sem_academic_year', $semester);
            });
        }
    
        $uploadedFiles = $uploadedFilesQuery->get();
    
        $semesters = CoursesFile::where('courses_files.user_login_id', $user_login_id)
            ->where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.is_archived', false)
            ->join('course_schedules', 'courses_files.course_schedule_id', '=', 'course_schedules.course_schedule_id')
            ->select('course_schedules.sem_academic_year')
            ->distinct()
            ->pluck('course_schedules.sem_academic_year');
    
     
        return view('faculty.accomplishment.view-uploaded-files', [
            'uploadedFiles' => $uploadedFiles,
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folderInputs' => $folderInputs,
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'folders' => $folders,
            'semesters' => $semesters,
            'selectedSemester' => $semester,
        ]);
    }

    //show announcement page
    public function announcementPage() 
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user(); 
    
        $userEmail = $user->email;
        $firstName = $user->first_name;
        $surname = $user->surname;
        $userDepartmentId = $user->department_id;
    
        $folders = FolderName::all();
    
        $notifications = \App\Models\Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
    
        $announcements = \App\Models\Announcement::where(function ($query) use ($userEmail, $userDepartmentId) {
                $query->where('type_of_recepient', 'All Faculty')
                    ->orWhere('type_of_recepient', $userEmail)
                    ->orWhere('department_id', $userDepartmentId); 
            })
            ->where('published', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(5); 
    

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
    
        return view('faculty.announcement', [
            'folders' => $folders,
            'folder' => $folder,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'announcements' => $announcements,
            'firstName' => $firstName,
            'surname' => $surname,
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

        $file->is_archived = true;
        $file->save();

        $userId = auth()->id();
        $totalStorageUsed = \App\Models\CoursesFile::where('user_login_id', $userId)->sum('file_size');
        $file->user->total_storage_used = $totalStorageUsed - $file->file_size;
        $file->user->save();

        return back()->with('success', 'File archived successfully!');
    }

    //request access the upload files
    public function requestUploadAccess(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'user_login_id' => 'required|exists:user_login,user_login_id',
        ]);
    
        \App\Models\RequestUploadAccess::create([
            'user_login_id' => $request->user_login_id,
            'reason' => $request->reason,
            'status' => 'unread', 
            'status_request' => 'Pending',
        ]);
    
        return redirect()->route('faculty.request-upload-access')->with('success', 'Your request has been submitted successfully!');
    }

    //show test administration page
    public function showTestAdministration()
    {
        // authentication check
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
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
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
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

       $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->where('courses_files.is_archived', 0)
        ->where('folder_name.main_folder_name', 'Test Administration')
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();

    
    
        // Process files with subjects
       $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });
    
    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();

    //requirements that already upload 
   $uploadedFiles = CoursesFile::where('user_login_id', $userId)
        ->select('folder_name_id', 'semester', 'school_year')
        ->get()
        ->groupBy(function($file) {
            return $file->semester . '_' . $file->school_year;
        });

    // Get all folders with Test Administration
    $allFolders = FolderName::where('main_folder_name', 'Test Administration')->get();
    
    // Structure the data for JavaScript
    $uploadedFoldersByPeriod = [];
    foreach ($uploadedFiles as $period => $files) {
        list($semester, $schoolYear) = explode('_', $period);
        $uploadedFoldersByPeriod[$period] = $files->pluck('folder_name_id')->toArray();
    }

    
    $hasUploaded = $consolidatedFiles->isNotEmpty(); 
          return view('faculty.accomplishment.test-administration', compact(
            'folderStatus', 'folderProgress', 'overallProgress',
            'courseSchedules', 'consolidatedFiles', 'isUploadOpen',
            'statusMessage', 'remainingTime', 'formattedStartDate',
            'formattedEndDate', 'departmentProgress', 'notifications',
            'firstName',  'surname', 'folders', 'hasUploaded',  'messages', 'allFolders', 'uploadedFoldersByPeriod', 'availableSemesters', 'availableSchoolYears' 
        ));
    }


    //show syllabus preparation page
    public function showSyllabusPreparation()
    {
         // authentication check
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
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
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
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

       $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->where('courses_files.is_archived', 0)
        ->where('folder_name.main_folder_name', 'Syllabus Preparation')
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();

    
    
        // Process files with subjects
       $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });
    
    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();

    //requirements that already upload 
   $uploadedFiles = CoursesFile::where('user_login_id', $userId)
        ->select('folder_name_id', 'semester', 'school_year')
        ->get()
        ->groupBy(function($file) {
            return $file->semester . '_' . $file->school_year;
        });

    // Get all folders with Syllabus Preparation
    $allFolders = FolderName::where('main_folder_name', 'Syllabus Preparation')->get();
    
    // Structure the data for JavaScript
    $uploadedFoldersByPeriod = [];
    foreach ($uploadedFiles as $period => $files) {
        list($semester, $schoolYear) = explode('_', $period);
        $uploadedFoldersByPeriod[$period] = $files->pluck('folder_name_id')->toArray();
    }

    
    $hasUploaded = $consolidatedFiles->isNotEmpty(); 
          return view('faculty.accomplishment.syllabus-preparation', compact(
            'folderStatus', 'folderProgress', 'overallProgress',
            'courseSchedules', 'consolidatedFiles', 'isUploadOpen',
            'statusMessage', 'remainingTime', 'formattedStartDate',
            'formattedEndDate', 'departmentProgress', 'notifications',
            'firstName',  'surname', 'folders', 'hasUploaded',  'messages', 'allFolders', 'uploadedFoldersByPeriod', 'availableSemesters', 'availableSchoolYears' 
        ));
    }
    
    //show request upload page
    public function showRequestUploadFaculty() 
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user(); 
    
        $userEmail = $user->email;
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $requestUploads = \App\Models\RequestUploadAccess::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $folders = FolderName::all();
        $notifications = \App\Models\Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
    
        $folder = $folders->first();
    
        return view('faculty.request-upload-access', [
            'folders' => $folders,
            'folder' => $folder,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'requestUploads' => $requestUploads 
        ]);
    }
     
    //show all approved files page
    public function showApprovedFiles()
    {
    if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
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
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
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
    
     $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->where('courses_files.status', 'Approved') // Filter only approved files
        ->whereIn('folder_name.main_folder_name', ['Classroom Management', 'Syllabus Preparation', 'Test Administration'])
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();

    
    
        // Process files with subjects
    $filesWithSubjects = $files->map(function ($file, $index) {
    $courseSchedule = $file->courseSchedule;
    $fileObject = new \stdClass();
    $fileObject->index = $index + 1;
    $fileObject->courses_files_id = $file->courses_files_id;
    $fileObject->semester = $file->semester;
    $fileObject->school_year = $file->school_year;
    $fileObject->subject = $file->subject;
    $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
    $fileObject->main_folder_name = $file->folderName ? $file->folderName->main_folder_name : 'N/A';
    $fileObject->created_at = $file->created_at;
    $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
    $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
    $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
    $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
    $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
    $fileObject->files = $file->files;
    $fileObject->original_file_name = $file->original_file_name;
    $fileObject->status = $file->status;
    $fileObject->declined_reason = $file->declined_reason;
    $fileObject->is_archived = $file->is_archived; // Add this line
    return $fileObject;
});

   $consolidatedFiles = $filesWithSubjects->map(function ($file) {
    $fileObject = new \stdClass();
    $fileObject->courses_files_id = $file->courses_files_id;
    $fileObject->semester = $file->semester ?? 'N/A';
    $fileObject->school_year = $file->school_year ?? 'N/A';
    $fileObject->folder_name = $file->folder_name;  
    $fileObject->program = $file->program;
    $fileObject->subject_name = $file->subject_name;
    $fileObject->year = $file->year;
    $fileObject->subject = $file->subject;
    $fileObject->course_code = $file->code;
    $fileObject->schedule = $file->schedule;
    $fileObject->is_archived = $file->is_archived; // Add this line
    $fileObject->files = [];

    $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
    $originalNames = is_string($file->original_file_name) ? 
        json_decode($file->original_file_name, true) : 
        $file->original_file_name;

    if (is_array($filesData)) {
        foreach ($filesData as $index => $fileData) {
            $fileObject->files[] = [
                'id' => $file->courses_files_id,
                'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                'status' => $file->status,
                'declined_reason' => $file->declined_reason,
                'created_at' => $file->created_at,
                'is_archived' => $file->is_archived, // Add this line
            ];
        }
    }

    $fileObject->status = $file->status;
    return json_decode(json_encode($fileObject), true);
})->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();
        
       $hasUploaded = $consolidatedFiles->isNotEmpty(); 
        return view('faculty.accomplishment.approved-files', compact(
            'folderStatus', 'consolidatedFiles', 'notifications',
            'firstName', 'surname', 'folders', 'hasUploaded',  'availableSemesters', 'availableSchoolYears',
        ));
    }
    
    //show declined files page
    public function showDeclinedFiles()
    {
       if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
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
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
        $courseSchedules = collect($this->flssApiService->getCourseSchedules());
    
        $courseSchedules = $courseSchedules
            ->filter(function ($schedule) use ($user) {
                Log::info('Comparing IDs:', [
                    'faculty_id' => $user->faculty_id,
                    'user_login_id' => $schedule['user_login_id']
                ]);
                return (string)$schedule['user_login_id'] === (string)$user->faculty_id;
            })
            ->map(function ($schedule) {
                return (object) [
                    'course_schedule_id' => $schedule['course_schedule_id'],
                    'course_code' => $schedule['course_code'],
                    'course_subjects' => $schedule['course_subjects'],
                    'year_section' => $schedule['year_section'],
                    'program' => $schedule['program'],
                    'schedule' => $schedule['schedule']
                ];
            });
    
        $courseFiles = collect($this->flssApiService->getCourseFiles())
            ->filter(function ($file) use ($user) {
                return (string)$file['user_login_id'] === (string)$user->faculty_id;
            });
    
        $semesters = $courseFiles->pluck('semester')
            ->unique()
            ->filter()
            ->sort()
            ->values();
    
        $schoolYears = $courseFiles->pluck('school_year')
            ->unique()
            ->filter()
            ->sort()
            ->values();
    
       $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->whereIn('folder_name.main_folder_name', ['Classroom Management', 'Test Administration', 'Syllabus Preparation'])
        ->where('courses_files.status', 'Declined') 
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();
    
    
       $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });
    
    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();
    
    
       $hasUploaded = $consolidatedFiles->isNotEmpty(); 
        return view('faculty.accomplishment.declined-files', compact(
            'folderStatus', 'consolidatedFiles', 'semesters', 'schoolYears', 'notifications',
            'firstName', 'surname', 'folders', 'hasUploaded', 'messages'
        ));
    }
    
    //show pending review page
    public function showPendingFiles()
    {
        // authentication check
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
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

      $files = CoursesFile::with('courseSchedule')
    ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
    ->where('courses_files.user_login_id', $userId)
    ->where('courses_files.is_archived', 0)
    ->whereIn('folder_name.main_folder_name', ['Classroom Management', 'Syllabus Preparation', 'Test Administration'])
    ->orderBy('courses_files.created_at', 'desc')
    ->select('courses_files.*') 
    ->get();

    
    
        // Process files with subjects
        $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->main_folder_name = $file->folderName ? $file->folderName->main_folder_name : 'N/A'; // Add this line
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });

    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();


        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
       $hasUploaded = $consolidatedFiles->isNotEmpty(); 
        return view('faculty.accomplishment.pending-files', compact(
            'folderStatus', 'consolidatedFiles', 'notifications',  'availableSemesters', 'availableSchoolYears',
            'firstName', 'surname', 'folders', 'hasUploaded'
        ));
    }
    
    //send a message to the admin
    public function sendMessageClassroomManagement(Request $request)
    {
        try {
            $validated = $request->validate([
                'courses_files_id' => 'required|exists:courses_files,courses_files_id',
                'message_body' => 'required|string|max:1000'
            ]);
    
            \Log::info('Incoming message data:', [
                'courses_files_id' => $request->input('courses_files_id'),
                'message_body' => $request->input('message_body')
            ]);
    
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Unauthorized'
                ], 401);
            }
    
            $message = Message::create([
                'user_login_id' => $userId,
                'courses_files_id' => $validated['courses_files_id'],
                'message_body' => $validated['message_body']
            ]);
    
            $admin = UserLogin::where('role', 'admin')->first();
    
            if ($admin) {
                $sender = UserLogin::findOrFail($userId);
                $senderName = $sender->first_name . ' ' . $sender->surname;
    
                Notification::create([
                    'courses_files_id' => $validated['courses_files_id'],
                    'user_login_id' => $admin->user_login_id, 
                    'folder_name_id' => CoursesFile::findOrFail($validated['courses_files_id'])->folder_name_id,
                    'sender' => $senderName,
                    'notification_message' => ' sent a new message regarding the documents.',
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Message Send Error: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
