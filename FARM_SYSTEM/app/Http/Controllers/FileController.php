<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\Notification;
use App\Models\UserDetails;
use App\Models\Message;
use App\Exports\CoursesFilesExport;
use App\Exports\ExportNotPassed;
use App\Exports\GenerateAllReports;
use App\Exports\AllReportNotPassed;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    //approve files
    public function approve($courses_files_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'admin') {
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
    public function decline($courses_files_id, Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'admin') {
            return redirect()->route('login');
        }
    
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
    
            $targetFile->status = 'Declined';
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
    
            return redirect()->back()->with('success', 'Declined successfully and message sent!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the file.');
        }
    }



    //undo approval
    public function undoApproval($courses_files_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $user = auth()->user();
    
        if ($user->role !== 'admin') {
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
    public function undoDeclined($courses_files_id)
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

    //delete files
    public function destroy($courses_files_id)
    {
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
    
            Storage::delete('/' . $targetFile->files);
    
            $targetFile->delete();
    
            return response()->json(['success' => 'File deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting the file.'], 500);
        }
    }

    //generate reports
    public function generateAllReports($semester)
    {
        Log::info('Starting generateAllReports for semester: ' . $semester);
        
        $export = new GenerateAllReports($semester);
        
        Log::info('Created GenerateAllReports instance');
        
        $result = Excel::download($export, 'faculty_report.xlsx');
        
        Log::info('Excel::download completed');
        
        return $result;
    }

    public function deleteSelectedFiles(Request $request)
    {
        $ids = $request->input('ids');

        Notification::whereIn('courses_files_id', $ids)->delete();

        CoursesFile::whereIn('courses_files_id', $ids)->delete();

        return response()->json(['success' => true]);
    }

    public function deleteAllFiles(Request $request)
    {
        Notification::whereIn('courses_files_id', CoursesFile::pluck('courses_files_id'))->delete();

        CoursesFile::truncate();

        return response()->json(['success' => true]);
    }
}
