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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Paginator;

class FacultyController extends Controller
{
    //faculty logout
    public function facultyLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    //uploaded-file page
    public function showUploadedFiles($folder_name_id)
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
            return redirect()->route('faculty.faculty-accomplishment')
                ->with('error', 'Folder not found.');
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

        $courseSchedules = CourseSchedule::where('user_login_id', $user->user_login_id)->get();
       
        //filters
        $semesters = CoursesFile::distinct()->pluck('semester')->sort();
        $schoolYears = CoursesFile::distinct()->pluck('school_year');
        

        $files = CoursesFile::with('courseSchedule') 
        ->where('folder_name_id', $folder_name_id)
        ->where('user_login_id', auth()->id())
        ->where('is_archived', 0)
        ->orderBy('created_at', 'desc')
        ->get();
    

        $groupedFiles = $files->groupBy(function ($file) {
            $courseSchedule = $file->courseSchedule;
            return implode('|', [
                $courseSchedule ? $courseSchedule->sem_academic_year : 'N/A',
                $courseSchedule ? $courseSchedule->program : 'N/A',
                $courseSchedule ? $courseSchedule->course_subjects : 'N/A',
                $courseSchedule ? $courseSchedule->course_code : 'N/A',
                $courseSchedule ? $courseSchedule->year_section : 'N/A',
            ]);
        });

        $consolidatedFiles = $groupedFiles->map(function ($groupFiles, $key) {
            $firstFile = $groupFiles->first();
    
            $fileObject = new \stdClass();
            $fileObject->courses_files_id = $firstFile->courses_files_id;
            $fileObject->courses_files_ids = $groupFiles->pluck('courses_files_id')->toArray();
    
            $fileObject->semester = $firstFile->semester ?? 'N/A';
            $fileObject->school_year = $firstFile->school_year ?? 'N/A';
    
            $fileObject->courseSchedule = $firstFile->courseSchedule;
            if ($fileObject->courseSchedule) {
                $fileObject->program = $fileObject->courseSchedule->program;
                $fileObject->subject_name = $fileObject->courseSchedule->course_subjects;
                $fileObject->year = $fileObject->courseSchedule->year_section;
                $fileObject->course_code = $fileObject->courseSchedule->course_code;
                $fileObject->schedule = $fileObject->courseSchedule->schedule;
            } else {
                $fileObject->program = 'N/A';
                $fileObject->subject_name = 'N/A';
                $fileObject->year = 'N/A';
                $fileObject->course_code = 'N/A';
                $fileObject->schedule = 'N/A';
            }
    
            $fileObject->files = $groupFiles->map(function ($file) {
                return [
                    'id' => $file->courses_files_id,
                    'path' => $file->files,
                    'name' => $file->original_file_name,
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            })->toArray();
    
            $statuses = collect($fileObject->files)->pluck('status')->unique();
            if ($statuses->contains('To Review')) {
                $fileObject->status = 'To Review';
            } elseif ($statuses->contains('Declined')) {
                $fileObject->status = 'Declined';
            } elseif ($statuses->every(fn($status) => $status === 'Approved')) {
                $fileObject->status = 'Approved';
            } else {
                $fileObject->status = 'Mixed';
            }
    
            return json_decode(json_encode($fileObject), true);
        })->values();
        
        // progress tracking
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', auth()->id())
                    ->count();
                
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', auth()->id())
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

        // department Progress
        $departments = Department::all();
        $departmentProgress = [];
        
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)
                ->pluck('user_login_id');
            $totalApprovedFiles = 0;
            $totalFiles = 0;
        
            foreach ($mainFolders as $mainFolder) {
                $subFolders = FolderName::where('main_folder_name', $mainFolder)
                    ->pluck('folder_name_id');
                
                $totalFiles += CoursesFile::whereIn('user_login_id', $userIds)
                    ->whereIn('folder_name_id', $subFolders)
                    ->count();
        
                $totalApprovedFiles += CoursesFile::whereIn('user_login_id', $userIds)
                    ->whereIn('folder_name_id', $subFolders)
                    ->where('status', 'Approved')
                    ->count();
            }
        
            $departmentOverallProgress = ($totalFiles > 0) ? 
                ($totalApprovedFiles / $totalFiles) * 100 : 0;
            $departmentProgress[$department->name] = $departmentOverallProgress;
        }

        $folderInputs = FolderInput::where('folder_name_id', $folder->folder_name_id)->get();
        
        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();

        $folders = FolderName::all();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $hasUploaded = $consolidatedFiles->isNotEmpty(); 

        //school year
        $currentYear = date('Y');

        return view('faculty.accomplishment.uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'semesters' => $semesters,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folders' => $folders,
            'firstName' => $firstName,
            'surname' => $surname,
            'folder_inputs' => $folderInputs,
            'isUploadOpen' => $isUploadOpen,
            'statusMessage' => $statusMessage,
            'formattedStartDate' => $formattedStartDate,
            'formattedEndDate' => $formattedEndDate,
            'courseSchedules' => $courseSchedules,
            'hasUploaded' => $hasUploaded,
            'overallProgress' => $overallProgress,
            'folderProgress' => $folderProgress,
            'folderStatus' => $folderStatus,
            'folderNameId' => $folder->folder_name_id,
            'departmentProgress' => $departmentProgress,
            'consolidatedFiles' => $consolidatedFiles,
            'currentYear' => $currentYear,
            'schoolYears' => $schoolYears, 
        ]);
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
    public function requestAccess(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        RequestUploadAccess::create([
            'user_login_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Your request has been submitted successfully.');
    }

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
        ]);
    
        return redirect()->back()->with('success', 'Your request has been submitted successfully.');
    }


    

}
