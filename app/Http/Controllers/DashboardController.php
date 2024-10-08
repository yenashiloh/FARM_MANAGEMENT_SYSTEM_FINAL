<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\CourseSchedule;
use App\Models\UserDetails;
use App\Models\Notification;

class DashboardController extends Controller
{
    //show admin dashboard
    public function adminDashboardPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        if ($user->role !== 'admin') {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $folders = FolderName::all();
        $folder = FolderName::first(); 
    
    
        $facultyCount = UserLogin::where('role', 'faculty')->count();
    
        $filesCount = CoursesFile::count();
        $toReviewCount = CoursesFile::where('status', 'To Review')->count();
        $approvedCount = CoursesFile::where('status', 'Approved')->count();
        $declinedCount = CoursesFile::where('status', 'Declined')->count();
        $completedReviewsCount = CoursesFile::whereIn('status', ['Approved', 'Declined'])->count();
        $folderCounts = FolderName::withCount('coursesFiles')->get();
    
        $folderStatusCounts = FolderName::withCount([
            'coursesFiles as to_review_count' => function ($query) {
                $query->where('status', 'To Review');
            },
            'coursesFiles as approved_count' => function ($query) {
                $query->where('status', 'Approved');
            },
            'coursesFiles as declined_count' => function ($query) {
                $query->where('status', 'Declined');
            },
        ])->get();
    
        $chartData = $folderStatusCounts->map(function ($folder) {
            return [
                'folder_name' => $folder->folder_name,
                'to_review_count' => $folder->to_review_count,
                'approved_count' => $folder->approved_count,
                'declined_count' => $folder->declined_count,
            ];
        });
    
        $semesters = CoursesFile::select('semester')->distinct()->get();

        return view('admin.admin-dashboard', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyCount' => $facultyCount,
            'filesCount' => $filesCount,
            'toReviewCount' => $toReviewCount,
            'completedReviewsCount' => $completedReviewsCount,
            'approvedCount' => $approvedCount,
            'declinedCount' => $declinedCount,
            'folderCounts' => $folderCounts,
            'firstName' => $firstName,
            'surname' => $surname,
            'chartData' => $chartData,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'semesters' => $semesters,
        ]);
    }
    
    //show faculty dashboard
    public function facultyDashboardPage()
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
    
        $folders = FolderName::all();
        $folder = FolderName::first();
    
        // Get notifications
        $notifications = \App\Models\Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
    
        // Get file counts
        $totalFilesSubmitted = \App\Models\CoursesFile::where('user_login_id', $userId)->count();
        $toReviewCount = \App\Models\CoursesFile::where('user_login_id', $userId)
            ->where('status', 'To Review')
            ->count();
        $approvedCount = \App\Models\CoursesFile::where('user_login_id', $userId)
            ->where('status', 'Approved')
            ->count();
        $declinedCount = \App\Models\CoursesFile::where('user_login_id', $userId)
            ->where('status', 'Declined')
            ->count();
    
        // Calculate storage
        $totalStorageUsed = \App\Models\CoursesFile::where('user_login_id', $userId)
            ->where('is_archived', false)
            ->sum('file_size');
        $formattedTotalStorageUsed = $this->formatBytes($totalStorageUsed);
        $totalStorageLimit = 30 * 1024 * 1024 * 1024;
        $storageAvailable = $totalStorageLimit - $totalStorageUsed;
        $formattedStorageAvailable = $this->formatBytes($storageAvailable);
    
        // Get folder status counts
        $folderStatusCounts = FolderName::withCount([
            'coursesFiles as approved_count' => function ($query) use ($userId) {
                $query->where('status', 'Approved')->where('user_login_id', $userId);
            },
            'coursesFiles as declined_count' => function ($query) use ($userId) {
                $query->where('status', 'Declined')->where('user_login_id', $userId);
            },
            'coursesFiles as to_review_count' => function ($query) use ($userId) {
                $query->where('status', 'To Review')->where('user_login_id', $userId);
            }
        ])->get();
    
        // Create chart data
        $chartData = $folderStatusCounts->map(function ($folder) {
            return [
                'folder_name' => $folder->folder_name,
                'approved' => $folder->approved_count,
                'declined' => $folder->declined_count,
                'to_review' => $folder->to_review_count,
            ];
        });
    
        // Get current academic year
        $currentAcademicYear = CourseSchedule::where('user_login_id', $userId)
            ->distinct()
            ->pluck('sem_academic_year')
            ->first();
    
        if (!$currentAcademicYear) {
            return redirect()->back()->with('error', 'No courses found for the current user.');
        }
    
        // Get faculty course count
        $facultyCourseCount = CourseSchedule::where('user_login_id', $userId)
            ->where('sem_academic_year', $currentAcademicYear)
            ->count();
    
        // Generate folder chart data
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderChartData = [];
    
        foreach ($mainFolders as $mainFolder) {
            $folderNames = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderData = [
                'name' => $mainFolder,
                'subfolders' => []
            ];
    
            foreach ($folderNames as $folder) {
                $userFilesCount = $folder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->whereHas('courseSchedule', function($query) use ($userId, $currentAcademicYear) {
                        $query->where('user_login_id', $userId)
                              ->where('sem_academic_year', $currentAcademicYear);
                    })
                    ->count();
    
                $percentage = $facultyCourseCount > 0 ? ($userFilesCount / $facultyCourseCount) * 100 : 0;
    
                $mainFolderData['subfolders'][] = [
                    'name' => $folder->folder_name,
                    'percentage' => round($percentage, 2),
                    'user_files_count' => $userFilesCount,
                    'total_files_count' => $facultyCourseCount,
                    'academic_year' => $currentAcademicYear
                ];
            }
    
            $folderChartData[] = $mainFolderData;
        }
    
        \Log::info('Faculty Course Count:', [
            'user_id' => $userId,
            'academic_year' => $currentAcademicYear,
            'course_count' => $facultyCourseCount
        ]);
        \Log::info('Folder Chart Data:', $folderChartData);
    
        return view('faculty.faculty-dashboard', [
            'folders' => $folders,
            'folder' => $folder,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'totalFilesSubmitted' => $totalFilesSubmitted,
            'toReviewCount' => $toReviewCount,
            'approvedCount' => $approvedCount,
            'declinedCount' => $declinedCount,
            'folderStatusCounts' => $folderStatusCounts,
            'chartData' => $chartData,
            'totalStorageUsed' => $totalStorageUsed,
            'formattedTotalStorageUsed' => $formattedTotalStorageUsed, 
            'storageAvailable' => $storageAvailable,
            'formattedStorageAvailable' => $formattedStorageAvailable,
            'firstName' => $firstName,
            'surname' => $surname,
            'folderChartData' => $folderChartData,
            'currentAcademicYear' => $currentAcademicYear
        ]);
    }

    //format bytes
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
