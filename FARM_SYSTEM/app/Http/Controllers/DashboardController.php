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
use App\Models\Announcement;
use Illuminate\Support\Facades\Log;


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
        
       $announcements = [];
        if (!session()->has('announcements_shown')) {
            $announcements = Announcement::where('published', 1)
                ->where(function($query) use ($user) {
                    $query->where('type_of_recepient', $user->role)
                          ->orWhere('type_of_recepient', 'All Faculty')
                          ->orWhere('type_of_recepient', 'like', '%' . $user->email . '%')
                          ->orWhere(function($q) use ($user) {
                              $q->where('department_id', $user->department_id)
                                ->whereNotNull('department_id');
                          })
                          ->orWhere(function($q) use ($user) {
                              $q->where('user_login_id', $user->id)
                                ->whereNotNull('user_login_id');
                          });
                })
                ->orderBy('created_at', 'desc')
                ->get();
        
            session(['announcements_shown' => true]);
        }

        // Get current academic year
        $currentAcademicYear = date('Y') . '-' . (date('Y') + 1);
        
        // Get faculty course count for the current academic year
        $facultyCourseCount = CourseSchedule::where('user_login_id', $userId)
            ->where('sem_academic_year', $currentAcademicYear)
            ->count();
    
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $folders = FolderName::all();
        $folder = FolderName::first();
    
        // Get notifications
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
    
        // Get file counts
        $totalFilesSubmitted = CoursesFile::where('user_login_id', $userId)->count();
        $toReviewCount = CoursesFile::where('user_login_id', $userId)
            ->where('status', 'To Review')
            ->count();
        $approvedCount = CoursesFile::where('user_login_id', $userId)
            ->where('status', 'Approved')
            ->count();
        $declinedCount = CoursesFile::where('user_login_id', $userId)
            ->where('status', 'Declined')
            ->count();
        
        // Calculate storage usage
        $totalStorageUsed = CoursesFile::where('user_login_id', $userId)
            ->where('is_archived', false)
            ->sum('file_size');
        $formattedTotalStorageUsed = $this->formatBytes($totalStorageUsed);
        $totalStorageLimit = 15 * 1024 * 1024 * 1024; // 30GB in bytes
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
    
        // Process main folders data
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
            'currentAcademicYear' => $currentAcademicYear,
              'announcements' => $announcements
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
