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

        // get course schedules and files with proper relationships
        $courseSchedules = CourseSchedule::where('user_login_id', $user->user_login_id)->get();
        $semester = $courseSchedules->pluck('sem_academic_year')->unique()->first() ?? 'N/A';

        $files = CoursesFile::with('courseSchedule')
        ->where('folder_name_id', $folder_name_id)
        ->where('user_login_id', auth()->id())
        ->where('is_archived', 0) 
        ->orderBy('created_at', 'desc')
        ->get();

        $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;

        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->sem_academic_year = $courseSchedule ? $courseSchedule->sem_academic_year : 'N/A';
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
        $hasUploaded = $filesWithSubjects->isNotEmpty();

        //filter select 
        

        return view('faculty.accomplishment.uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'semester' => $semester,
            'filesWithSubjects' => $filesWithSubjects,
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
        $userDepartmentId = $user->department_id; // Get the department ID of the logged-in user

        $folders = FolderName::all();

        $notifications = \App\Models\Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();

        // Retrieve announcements where type_of_recepient matches user's email or is 'All Faculty'
        $announcements = \App\Models\Announcement::where(function ($query) use ($userEmail, $userDepartmentId) {
            $query->where('type_of_recepient', 'All Faculty')
                ->orWhere('type_of_recepient', $userEmail)
                ->orWhere('department_id', $userDepartmentId); // Filter by department ID
        })
        ->where('published', 1)
        ->orderBy('created_at', 'desc')
        ->get();

        // Prepare emails for display
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

    
    public function requestAccess(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Save the request to the database
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
            'status' => 'unread', // Set default status to 'unread'
        ]);
    
        return redirect()->back()->with('success', 'Your request has been submitted successfully.');
    }


    

}
