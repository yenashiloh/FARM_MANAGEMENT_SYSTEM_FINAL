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
            
            $relatedFiles = CoursesFile::where('folder_name_id', $targetFile->folder_name_id)
                ->where('user_login_id', $targetFile->user_login_id)
                ->where('course_schedule_id', $targetFile->course_schedule_id)
                ->where('semester', $targetFile->semester)
                ->where('school_year', $targetFile->school_year)
                ->get();

            foreach ($relatedFiles as $file) {
                $file->status = 'Approved';
                $file->save();
            }

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

            return redirect()->back()->with('success', 'Approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while approving the files.');
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
            
            $relatedFiles = CoursesFile::where('folder_name_id', $targetFile->folder_name_id)
                ->where('user_login_id', $targetFile->user_login_id)
                ->where('course_schedule_id', $targetFile->course_schedule_id)
                ->where('semester', $targetFile->semester)
                ->where('school_year', $targetFile->school_year)
                ->get();
    
            foreach ($relatedFiles as $file) {
                $file->status = 'Declined';
                $file->declined_reason = $request->input('declineReason');
                $file->save();
            }
    
            $folder = FolderName::find($targetFile->folder_name_id);
            $userDetails = UserLogin::where('user_login_id', $user->user_login_id)->first();
            $senderName = $userDetails ? $userDetails->first_name . ' ' . $userDetails->surname : 'Unknown Sender';
    
            Notification::create([
                'courses_files_id' => $targetFile->courses_files_id,
                'user_login_id' => $targetFile->user_login_id,
                'folder_name_id' => $targetFile->folder_name_id,
                'sender' => $senderName,
                'notification_message' => 'declined the course ' . $targetFile->subject . ' in ' . 
                    ($folder ? $folder->folder_name : 'Unknown Folder'),
            ]);
    
            return redirect()->route('admin.accomplishment.view-accomplishment', [
                'user_login_id' => $targetFile->user_login_id,
                'folder_name_id' => $targetFile->folder_name_id 
            ])->with('success', 'Declined successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the files.');
        }
    }
    
    //delete files
    public function destroy($courses_files_id)
    {
        try {
            $targetFile = CoursesFile::findOrFail($courses_files_id);
            
            $relatedFiles = CoursesFile::where('folder_name_id', $targetFile->folder_name_id)
                ->where('user_login_id', $targetFile->user_login_id)
                ->where('course_schedule_id', $targetFile->course_schedule_id)
                ->where('semester', $targetFile->semester)
                ->where('school_year', $targetFile->school_year)
                ->get();
    
            foreach ($relatedFiles as $file) {
                Storage::delete('/' . $file->files);
                $file->delete();
            }
    
            return response()->json(['success' => 'All related files deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting files.'], 500);
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
