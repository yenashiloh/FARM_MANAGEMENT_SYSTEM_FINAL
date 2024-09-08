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

class CoursesFileController extends Controller
{
    //store files
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480', 
            'folder_name_id' => 'required|exists:folder_name,folder_name_id',
        ]);
    
        try {
            $userLoginId = auth()->user()->user_login_id;
    
            $folder_name_id = $request->input('folder_name_id');
    
            $courseSchedules = CourseSchedule::where('user_login_id', $userLoginId)->get();
    
            if ($courseSchedules->isEmpty()) {
                return redirect()->back()->with('error', 'No course schedules found for this user.');
            }

            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('courses_files', 'public');
                $courseSchedule = $courseSchedules[$index % $courseSchedules->count()]; 
                $subject_name = $courseSchedule->course_subjects ?? 'N/A';
                $semester = $courseSchedule->sem_academic_year ?? 'N/A';
                $fileSize = $file->getSize(); 
                $course_schedule_id = $courseSchedule->course_schedule_id; 
    
                CoursesFile::create([
                    'files' => $path, 
                    'original_file_name' => $file->getClientOriginalName(), 
                    'user_login_id' => $userLoginId, 
                    'folder_name_id' => $folder_name_id, 
                    'course_schedule_id' => $course_schedule_id,
                    'semester' => $semester, 
                    'subject' => $subject_name, 
                    'file_size' => $fileSize, 
                ]);
            }
    
            return redirect()->back()->with('success', 'Files uploaded successfully!');
        } catch (\Exception $e) {
            logger()->error('File upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'File upload failed. Please try again.');
        }
    }
    
    //update file
    public function updateFile(Request $request, $id)
    {
        $request->validate([
            'files' => 'nullable|file|mimes:pdf', 
        ]);
    
        try {
            $file = CoursesFile::findOrFail($id);
    
            $userLoginId = auth()->user()->user_login_id;
            $folder_name_id = $file->folder_name_id; 
            $semester = $file->semester;
            $originalStatus = $file->status;
    
            // Get the current user's details
            $currentUser = UserLogin::findOrFail($userLoginId);
            $senderName = $currentUser->first_name . ' ' . $currentUser->surname;
    
            if ($request->hasFile('files')) {
                if (Storage::disk('public')->exists($file->files)) {
                    Storage::disk('public')->delete($file->files);
                }
    
                $path = $request->file('files')->store('courses_files', 'public');
    
                $file->update([
                    'files' => $path,
                    'original_file_name' => $request->file('files')->getClientOriginalName(),
                    'user_login_id' => $userLoginId,
                    'file_size' => $request->file('files')->getSize(), 
                    'status' => ($originalStatus === 'Declined') ? 'To Review' : $originalStatus, 
                ]);
    
                if ($originalStatus === 'Declined') {
                    $folderName = FolderName::find($folder_name_id)->folder_name;
                    $subject = $file->subject;
    
                    $adminUsers = UserLogin::where('role', 'admin')->get();
                    foreach ($adminUsers as $admin) {
                        Notification::create([
                            'courses_files_id' => $file->courses_files_id,
                            'user_login_id' => $admin->user_login_id,
                            'folder_name_id' => $folder_name_id,
                            'sender' => $senderName, 
                            'notification_message' => "has re-uploaded the previously declined course {$subject} in {$folderName}.",
                            'is_read' => false,
                        ]);
                    }
                }
    
                session()->flash('success', 'File updated successfully!');
    
                return response()->json(['success' => true]);
            }
    
            return response()->json(['success' => false, 'message' => 'No file has been selected. Please select a file'], 400);
    
        } catch (\Exception $e) {
            logger()->error('File update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'File update failed. Please try again.'], 500);
        }
    }
    
    //show view archive page
    public function showArchive()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'faculty') {
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
    
        $notifications = \App\Models\Notification::where('user_login_id', auth()->id())->get();
        $notificationCount = $notifications->count();
    
        $uploadedFiles = CoursesFile::where('user_login_id', $user->user_login_id)
            ->where('folder_name_id', $folder->folder_name_id)
            ->where('is_archived', true)
            ->with(['userLogin', 'folderName', 'folderInput', 'courseSchedule'])
            ->get();
    
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
        ]);
    }
    
    //archive file
    public function archive($id)
    {
        $file = CoursesFile::find($id);

        if ($file) {
            $file->is_archived = true;
            $file->save();

            return redirect()->back()->with('success', 'File archived successfully!');
        }

        return redirect()->back()->with('error', 'File not found.');
    }

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


    public function bulkUnarchive(Request $request)
{
    $fileIds = $request->input('file_ids', []);
    
    if (!empty($fileIds)) {
        CoursesFile::whereIn('courses_files_id', $fileIds)->update(['is_archived' => false]);
        return redirect()->back()->with('success', count($fileIds) . ' files have been restored.');
    }

    return redirect()->back()->with('error', 'No files selected for restoration.');
}
}
