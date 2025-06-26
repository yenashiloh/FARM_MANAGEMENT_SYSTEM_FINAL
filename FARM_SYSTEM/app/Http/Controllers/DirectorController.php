<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Message;
use App\Exports\GenerateAllReports;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;


class DirectorController extends Controller
{
    //show faculty uploaded files
    public function showDirectorUploadedFiles($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $folder = FolderName::findOrFail($folder_name_id);

        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $notificationCount = $notifications->where('is_read', 0)->count();

        $allSemesters = CoursesFile::where('folder_name_id', $folder_name_id)
            ->select('semester')
            ->distinct()
        ->pluck('semester');

        $selectedSemester = request('semester');

        $filesQuery = CoursesFile::where('folder_name_id', $folder_name_id)
            ->with(['userLogin', 'courseSchedule']);
        
        if ($selectedSemester) {
            $filesQuery->where('semester', $selectedSemester);
        }

        $files = $filesQuery->get();

        $groupedFiles = $files->groupBy(function ($file) {
            return $file->user_login_id . '-' . $file->semester;
        })->map(function ($group) {
            $firstFile = $group->first();
            return [
                'user_name' => $firstFile->userLogin->first_name . ' ' . $firstFile->userLogin->surname,
                'user_login_id' => $firstFile->user_login_id,
                'semester' => $firstFile->semester,
                'created_at' => $group->max('created_at'),
                'file_count' => $group->count(),
            ];
        })->values();

        $folders = FolderName::all();


        return view('director.accomplishment.director-uploaded-files', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'groupedFiles' => $groupedFiles,
            'folder_name_id' => $folder_name_id,
            'folders' => $folders,
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'allSemesters' => $allSemesters,
            'selectedSemester' => $selectedSemester, 
            'user' => $user, 
            
        ]);
    }

    //director dashboard
    public function directorDashboardPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

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

        return view('director.director-dashboard', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyCount' => $facultyCount,
            'filesCount' => $filesCount,
            'toReviewCount' => $toReviewCount,
            'completedReviewsCount' => $completedReviewsCount,
            'approvedCount' => $approvedCount,
            'declinedCount' => $declinedCount,
            'folderCounts' => $folderCounts,
            'user' => $user, 
            'chartData' => $chartData,
            'semesters' => $semesters,
        ]);
    }

    //view faculty accomplishment 
    public function viewFacultyAccomplishment($user_login_id, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
        $folder = FolderName::find($folder_name_id);
    
        if (!$folder) {
            return redirect()->route('login')->with('error', 'Folder not found.');
        }
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $files = CoursesFile::where('courses_files.folder_name_id', $folder_name_id)
            ->where('courses_files.user_login_id', $user_login_id)
            ->with(['userLogin', 'courseSchedule', 'folderName'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        $semesters = CoursesFile::distinct()->pluck('semester')->sort();
        $schoolYears = CoursesFile::distinct()->pluck('school_year');
    
        $folders = FolderName::all();
        $viewedUser = UserLogin::find($user_login_id);
        $faculty = UserLogin::findOrFail($user_login_id);
        $department = Department::find($faculty->department_id);
        $departmentName = $department ? $department->name : '';
    
        $currentFolder = $folders->firstWhere('main_folder_name', $folder->main_folder_name);
    
       $messages = Message::whereIn('courses_files_id', $files->pluck('courses_files_id'))
        ->with('userLogin')
        ->orderBy('created_at', 'asc')
        ->get();
    
        return view('director.accomplishment.view-faculty-accomplishment', [
            'folder' => $folder,
            'folderName' => $folder->folder_name,
            'files' => $files, // Pass files directly without grouping
            'folders' => $folders,
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'folder_name_id' => $folder_name_id,
            'semesters' => $semesters,
            'schoolYears' => $schoolYears,
            'departmentName' => $departmentName, 
            'faculty' => $faculty,  
             'currentFolder' => $currentFolder,
            'user_login_id' => $user_login_id,
            'folder_name_id' => $folder_name_id,
            'semesters' => $semesters,
            'schoolYears' => $schoolYears, 
            'messages' => $messages,
            
        ]);
    }

    
    //show the director department page
    public function showDirectorDepartmentPage($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

       try {
        $folder = FolderName::findOrFail($folder_name_id);
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->where('is_read', 0)->count();
        $departments = Department::orderBy('name', 'asc')->get();
        $folders = FolderName::all();
        
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $departmentProgress = [];
        
        foreach ($departments as $department) {
            // Get all faculty users in this department
            $facultyUsers = UserLogin::where('department_id', $department->department_id)
                ->whereIn('role', ['faculty', 'faculty-coordinator'])
                ->get();
            
            $subFolders = FolderName::whereIn('main_folder_name', $mainFolders)
                ->pluck('folder_name_id');
            
            $totalRequiredSubmissions = count($facultyUsers) * count($subFolders);
            $approvedSubmissions = 0;
            $totalSubmissions = 0;
            
            // Check if there are any submissions
            $hasSubmissions = CoursesFile::whereIn('user_login_id', $facultyUsers->pluck('user_login_id'))
                ->whereIn('folder_name_id', $subFolders)
                ->where('is_archived', 0)
                ->exists();
            
            if ($hasSubmissions) {
                foreach ($facultyUsers as $faculty) {
                    // Count approved submissions for this faculty
                    $facultyApproved = CoursesFile::where('user_login_id', $faculty->user_login_id)
                        ->whereIn('folder_name_id', $subFolders)
                        ->where('status', 'Approved')
                        ->where('is_archived', 0)
                        ->distinct()
                        ->count('courses_files_id');
                    
                    // Count total submissions for this faculty
                    $facultyTotal = CoursesFile::where('user_login_id', $faculty->user_login_id)
                        ->whereIn('folder_name_id', $subFolders)
                        ->where('is_archived', 0)
                        ->distinct()
                        ->count('courses_files_id');
                        
                    $approvedSubmissions += $facultyApproved;
                    $totalSubmissions += $facultyTotal;
                }
            }
            
            // Calculate progress based on total required submissions for all faculty
            $progress = $totalRequiredSubmissions > 0 ? 
                ($approvedSubmissions / $totalRequiredSubmissions) * 100 : 0;
            
            $departmentProgress[$department->name] = [
                'progress' => $progress,
                'approved' => $approvedSubmissions,
                'total' => $totalSubmissions,
                'faculty_count' => count($facultyUsers),
                'required_total' => $totalRequiredSubmissions
            ];
        }
            
            return view('director.department', [
                'folders' => $folders,
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
                'firstName' => $user->first_name,
                'surname' => $user->surname,
                'user' => $user,
                'departments' => $departments,
                'folder_name_id' => $folder_name_id,
                'folderName' => $folder->folder_name,
                'folder' => $folder,
                'departmentProgress' => $departmentProgress,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Folder not found.');
        }
    }

    //show the faculty members in dept
    public function showDirectorAccomplishmentDepartment($department, $folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $firstName = $user->first_name;
        $surname = $user->surname;
    
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
    
        $folder = FolderName::findOrFail($folder_name_id);
        $folders = FolderName::all();
    
        $decodedDepartment = urldecode($department);
        
        $departmentRecord = Department::where('name', $decodedDepartment)->first();
        
        if (!$departmentRecord) {
            return redirect()->back()->withErrors(['Department not found']);
        }
    
        $facultyUsers = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator'])
            ->where('department_id', $departmentRecord->department_id)
            ->get();
    
        return view('director.accomplishment.view-accomplishment-faculty', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'firstName' => $firstName,
            'surname' => $surname,
            'user' => $user,
            'user_login_id' => $user->user_login_id, 
            'department' => $departmentRecord,  
            'facultyUsers' => $facultyUsers,
            'folder_name_id' => $folder_name_id,
            'folder' => $folder,
            'folderName' => $folder->folder_name
        ]);
    }

    //show director account
    public function directorAccountPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
     
        $user = auth()->user();
     
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
     
        $folders = FolderName::all();
     
        return view('director.director-account', [
            'folders' => $folders,
            'user' => $user,
        ]);
    }
     
    //update the director account
    public function updateDirectorAccount(Request $request)
    {
        $user = auth()->user();
     
        $request->validate([
            'first-name' => 'required|string|max:255',
            'last-name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'recent-password' => 'required_with:new-password|current_password',
            'new-password' => 'nullable|confirmed|min:6',
            'confirm-password' => 'nullable|same:new-password',
        ]);
     
         $user->update([
            'email' => $request->input('email'),
            'password' => $request->input('new-password') ? bcrypt($request->input('new-password')) : $user->password,
            'first_name' => $request->input('first-name'),
            'surname' => $request->input('last-name'),

        ]);
     
        return redirect()->route('director.director-account')->with('success', 'Account details updated successfully!');
     }
     
    //director logout
    public function directorLogout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
     }
    
    //generate reports
    public function generateAllReportsDirector($semester)
    {
        Log::info('Starting generateAllReportsDirector for semester: ' . $semester);

        $export = new GenerateAllReports($semester);

        Log::info('Created GenerateAllReports instance');

        $result = Excel::download($export, 'faculty_report_director.xlsx');

        Log::info('Excel::download completed');

        return $result;
    }
    
     //approve files
    public function approveDirector($courses_files_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
    
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
            
            $targetFile->status = 'Approved';
            $targetFile->save();
    
            $folder = FolderName::find($targetFile->folder_name_id);
            $userDetails = UserLogin::where('user_login_id', $user->user_login_id)->first();
            $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->surname : 'Unknown Sender';
    
            Notification::create([
                'courses_files_id' => $targetFile->courses_files_id,
                'user_login_id' => $targetFile->user_login_id,
                'folder_name_id' => $targetFile->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'approved the course ' . $targetFile->subject . ' in ' . 
                    ($folder ? $folder->folder_name : 'Unknown Folder') . '.',
            ]);
    
            return redirect()->back()->with('success', 'File has been approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while approving the file.');
        }
    }

    //decline files   
    public function declineDirector($courses_files_id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
    
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
    
            $targetFile->status = 'Declined';
            $targetFile->save();
    
            $targetFile->declined_reason = $request->input('declineReason');
            $targetFile->save();
    
            $folder = FolderName::find($targetFile->folder_name_id);
    
            $adminDetails = UserLogin::where('user_login_id', $user->user_login_id)->first();
            $senderName = $adminDetails ? $adminDetails->first_name . ' ' . $adminDetails->surname : 'Unknown Sender';
    
            Notification::create([
                'courses_files_id' => $targetFile->courses_files_id,
                'user_login_id' => $targetFile->user_login_id, 
                'folder_name_id' => $targetFile->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'declined the course ' . $targetFile->subject . ' in ' . 
                    ($folder ? $folder->folder_name : 'Unknown Folder'),
            ]);
    
            Message::create([
                'user_login_id' => $user->user_login_id, 
                'courses_files_id' => $targetFile->courses_files_id,
                'folder_name_id' => $targetFile->folder_name_id,
                'message_body' => $request->input('declineReason'),
            ]);
    
            return redirect()->route('director.accomplishment.view-faculty-accomplishment', [
                'user_login_id' => $targetFile->user_login_id,
                'folder_name_id' => $targetFile->folder_name_id 
            ])->with('success', 'Declined successfully and message sent!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the file.');
        }
    }
    
    //undo approval
    public function undoApprovalDirector($courses_files_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'director') {
            return redirect()->route('login');
        }
    
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
    
            if ($targetFile->status !== 'Approved') {
                return redirect()->back()->with('error', 'This file cannot be undone as it is not approved.');
            }
    
            if ($targetFile->is_archived == 1) {
                return redirect()->back()->with('error', 'This file is archived and needs to be unarchived first. Kindly message the faculty to request unarchiving');
            }
    
            $targetFile->status = 'To Review';
            $targetFile->save();
    
            $folder = FolderName::find($targetFile->folder_name_id);
            $userDetails = UserLogin::where('user_login_id', $user->user_login_id)->first();
            $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->surname : 'Unknown Sender';
    
            // Send a notification
            Notification::create([
                'courses_files_id' => $targetFile->courses_files_id,
                'user_login_id' => $targetFile->user_login_id,
                'folder_name_id' => $targetFile->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'reverted the approval for the course ' . $targetFile->subject . ' in ' . 
                    ($folder ? $folder->folder_name : 'Unknown Folder'),
            ]);
    
            return redirect()->back()->with('success', 'File has been reverted to "To Review"!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while undoing the approval.');
        }
    }
    
    //undo declined
    public function undoDeclinedDirector($courses_files_id)
    {
        try {
            $file = CoursesFile::findOrFail($courses_files_id);
    
            if ($file->is_archived == 1) {
                return redirect()->back()->with('error', 'This file is archived and needs to be unarchived first. Kindly message the faculty to request unarchiving.');
            }
    
            if ($file->status !== 'Declined') {
                return redirect()->back()->with('error', 'This file is not declined.');
            }
    
            $file->status = 'To Review';
            $file->save();
    
            $folder = FolderName::find($file->folder_name_id);
            $userDetails = UserLogin::where('user_login_id', auth()->user()->user_login_id)->first();
            $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->surname : 'Unknown Sender';
    
            Notification::create([
                'courses_files_id' => $file->courses_files_id,
                'user_login_id' => $file->user_login_id,
                'folder_name_id' => $file->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'reverted the decline for the course ' . $file->subject . ' in ' . 
                    ($folder ? $folder->folder_name : 'Unknown Folder'),
            ]);
    
            return redirect()->back()->with('success', 'File has been reverted to "To Review"!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while undoing the decline.');
        }
    }
    
    //send message 
    public function sendMessageDirector(Request $request)
    {
        Log::info('sendMessageDirector called', ['request' => $request->all()]);
    
        try {
            $message = Message::create([
                'user_login_id' => auth()->id(), 
                'courses_files_id' => $request->courses_files_id,
                'folder_name_id' => $request->folder_name_id,
                'message_body' => $request->message_body,
            ]);
    
            Log::info('Message created successfully', ['message' => $message]);
    
            $message->load('userLogin');
            Log::info('User login relationship loaded', ['userLogin' => $message->userLogin]);
    
            $coursesFile = CoursesFile::findOrFail($request->courses_files_id);
            $facultyUserLoginId = $coursesFile->user_login_id;
            Log::info('Faculty user login ID retrieved', ['facultyUserLoginId' => $facultyUserLoginId]);
    
            $directorDetails = UserLogin::find(auth()->id()); 
            $senderName = $directorDetails ? $directorDetails->first_name . ' ' . $directorDetails->surname : 'Unknown Sender';
            Log::info('Sender name determined', ['senderName' => $senderName]);
    
            Notification::create([
                'courses_files_id' => $request->courses_files_id,
                'user_login_id' => $facultyUserLoginId, 
                'folder_name_id' => $request->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'sent a message regarding your documents.',
            ]);
    
            Log::info('Notification created successfully');
    
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in sendMessageDirector', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the message.'
            ], 500);
        }
    }
}
