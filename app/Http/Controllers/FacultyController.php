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
    
        $semester = $courseSchedules->pluck('sem_academic_year')->unique()->first() ?? 'N/A';
    
        $files = CoursesFile::where('folder_name_id', $folder_name_id)
            ->where('user_login_id', auth()->id())
            ->get();
    
            $filesWithSubjects = $files->map(function ($file) use ($courseSchedules) {
                $courseSchedule = $courseSchedules->firstWhere('course_code', $file->subject);
            
                if ($courseSchedule) {
                    $file->subject_name = $file->subject;
                    $file->year = $courseSchedule->year_section ?? 'N/A';
                    $file->program = $courseSchedule->program ?? 'N/A';
                    $file->code = $courseSchedule->course_code ?? 'N/A';
                    $file->schedule = $courseSchedule->schedule ?? 'N/A';
                    $file->sem_academic_year = $courseSchedule->sem_academic_year ?? 'N/A'; 
                } else {
                    $file->subject_name = 'N/A';
                    $file->year = 'N/A';
                    $file->program = 'N/A';
                    $file->code = 'N/A';
                    $file->schedule = 'N/A';
                    $file->sem_academic_year = 'N/A'; 
                }
            
                return $file;
            });
            
        //progress
        $files = CoursesFile::where('user_login_id', auth()->id())
        ->where('status', 'approved')
        ->get();
    
        $approvedCounts = [];
        foreach ($files as $file) {
            $mainFolderName = $file->folderName->main_folder_name;
            if (!isset($approvedCounts[$mainFolderName])) {
                $approvedCounts[$mainFolderName] = 0;
            }
            $approvedCounts[$mainFolderName]++;
        }
        
        $folders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        
        $totalCounts = [];
        foreach ($folders as $folderName) {
            $totalSubfolders = FolderName::where('main_folder_name', $folderName)
                ->count();
            
            $totalCounts[$folderName] = $totalSubfolders;
        }
        
        $progress = [];
        foreach ($folders as $folderName) {
            $approvedCount = $approvedCounts[$folderName] ?? 0;
            $requiredCount = $totalCounts[$folderName] ?? 0;
            
            if ($requiredCount > 0) {
                $percentage = min(($approvedCount / $requiredCount) * 100, 100);
                $progress[$folderName] = round($percentage, 2); 
            } else {
                $progress[$folderName] = 0;
            }
        }
        
        Log::info('Progress Calculation Debug', [
            'approvedCounts' => $approvedCounts,
            'totalCounts' => $totalCounts,
            'progress' => $progress
        ]);


        $groupedFiles = $filesWithSubjects->groupBy('semester');
    
        $subjects = $courseSchedules->pluck('course_subjects')->unique();
    
        $folderInputs = FolderInput::where('folder_name_id', $folder->folder_name_id)->get();
    
       $notifications = \App\Models\Notification::where('user_login_id', auth()->id())
            ->orderBy('created_at', 'desc') 
            ->get();
        $notificationCount = $notifications->count();
    
        $folders = FolderName::all();
    
        $firstName = $user->first_name;
        $surname = $user->surname;
        $hasUploaded = $filesWithSubjects->isNotEmpty();
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
            'surname' => $surname,
            'folder_inputs' => $folderInputs,
            'isUploadOpen' => $isUploadOpen,
            'statusMessage' => $statusMessage,
            'formattedStartDate' => $formattedStartDate,
            'formattedEndDate' => $formattedEndDate,
            'courseSchedules' => $courseSchedules,
            'hasUploaded' => $hasUploaded,
            'progress' => $progress,
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

        $folders = FolderName::all();

       $notifications = \App\Models\Notification::where('user_login_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();
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

}
