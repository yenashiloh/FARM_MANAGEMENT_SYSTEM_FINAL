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



class CoursesFileController extends Controller
{
    //store files
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|array',
            'files.*.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'folder_name_id' => 'required|exists:folder_name,folder_name_id',
            'semester' => 'required|string',
            'school_year' => 'required|string',
        ]);
    
        try {
            $userLoginId = auth()->user()->user_login_id;
            $folder_name_id = $request->input('folder_name_id');
            $semester = $request->input('semester');
            $schoolYear = $request->input('school_year'); 
    
            foreach ($request->file('files') as $courseScheduleId => $courseFiles) {
                $courseSchedule = CourseSchedule::find($courseScheduleId);
                if (!$courseSchedule) {
                    continue;
                }
    
                foreach ($courseFiles as $file) {
                    $path = $file->store('courses_files', 'public');
                    $fileSize = $file->getSize();
    
                    CoursesFile::create([
                        'files' => $path,
                        'original_file_name' => $file->getClientOriginalName(),
                        'user_login_id' => $userLoginId,
                        'folder_name_id' => $folder_name_id,
                        'course_schedule_id' => $courseSchedule->course_schedule_id,
                        'semester' => $semester, 
                        'school_year' => $schoolYear, 
                        'subject' => $courseSchedule->course_subjects,
                        'file_size' => $fileSize,
                    ]);
                }
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
            'files.*' => 'nullable|file|mimes:pdf',
            'removed_files' => 'nullable|array',
            'removed_files.*' => 'required|string'
        ]);
    
        try {
            $file = CoursesFile::findOrFail($id);
            $userLoginId = auth()->user()->user_login_id;
            $folder_name_id = $file->folder_name_id;
            $originalStatus = $file->status;
    
            $currentUser = UserLogin::findOrFail($userLoginId);
            $senderName = $currentUser->first_name . ' ' . $currentUser->surname;
            $senderUserLoginId = $currentUser->user_login_id;
    
            // Get existing files
            $existingFiles = explode(',', $file->files);
            $existingFileNames = explode(',', $file->original_file_name);
    
            // Handle file removals
            if ($request->has('removed_files')) {
                $removedFiles = $request->input('removed_files'); // No need for json_decode
                foreach ($removedFiles as $removedFile) {
                    $index = array_search($removedFile, $existingFiles);
                    if ($index !== false) {
                        // Remove from storage
                        if (Storage::disk('public')->exists($removedFile)) {
                            Storage::disk('public')->delete($removedFile);
                        }
                        // Remove from arrays
                        unset($existingFiles[$index]);
                        unset($existingFileNames[$index]);
                    }
                }
            }
    
            // Handle new file uploads
            if ($request->hasFile('files')) {
                $newFiles = $request->file('files');
                foreach ($newFiles as $uploadedFile) {
                    $path = $uploadedFile->store('courses_files', 'public');
                    $existingFiles[] = $path;
                    $existingFileNames[] = $uploadedFile->getClientOriginalName();
                }
            }
    
            // Calculate total size of remaining and new files
            $totalSize = 0;
            foreach ($existingFiles as $existingFile) {
                if (Storage::disk('public')->exists($existingFile)) {
                    $totalSize += Storage::disk('public')->size($existingFile);
                }
            }
    
            // Update the database
            $file->update([
                'files' => implode(',', array_values($existingFiles)),
                'original_file_name' => implode(',', array_values($existingFileNames)),
                'user_login_id' => $userLoginId,
                'file_size' => $totalSize,
                'status' => ($originalStatus === 'Declined') ? 'To Review' : $originalStatus,
            ]);
    
            // Create notifications if status was Declined
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
                        'sender_user_login_id' => $senderUserLoginId,
                        'notification_message' => "has re-uploaded the previously declined course {$subject} in {$folderName}.",
                        'is_read' => false,
                    ]);
                }
            }
    
            return response()->json(['success' => true]);
    
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

    public function archiveByDateRange(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);
    
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();
    
        try {
            DB::beginTransaction();
    
            $files = CoursesFile::whereBetween('created_at', [$fromDate, $toDate])
                ->where('status', 'Approved')
                ->where('is_archived', false)  
                ->get();
    
            if ($files->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No approved files found within the specified date range.');
            }
    
            $count = 0;
            foreach ($files as $file) {
                $file->is_archived = 1;  
                $file->save();
                $count++;
            }
    
            DB::commit();
    
            return redirect()->back()
                ->with('success', $count . ' files have been archived successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Archive error: ' . $e->getMessage());  
            return redirect()->back()
                ->with('error', 'An error occurred while archiving the files.');
        }
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
}
