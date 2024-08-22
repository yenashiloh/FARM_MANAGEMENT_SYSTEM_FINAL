<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;

class DashboardController extends Controller
{
    public function adminDashboardPage()
    {
        $folders = FolderName::all();
        $folder = FolderName::first(); 
    
        // Count the number of faculty users
        $facultyCount = UserLogin::where('role', 'faculty')->count();
    
        // Count the total number of files submitted
        $filesCount = CoursesFile::count();
    
        // Count the number of files with the status "To Review"
        $toReviewCount = CoursesFile::where('status', 'To Review')->count();
    
        // Count the number of files with the status "Approved"
        $approvedCount = CoursesFile::where('status', 'Approved')->count();
    
        // Count the number of files with the status "Declined"
        $declinedCount = CoursesFile::where('status', 'Declined')->count();
    
        // Count the number of files with the status "Approved" or "Declined"
        $completedReviewsCount = CoursesFile::whereIn('status', ['Approved', 'Declined'])->count();
    
        // Get file counts per folder
        $folderCounts = FolderName::withCount('coursesFiles')->get();
    
        return view('admin.admin-dashboard', [
            'folders' => $folders,
            'folder' => $folder,
            'facultyCount' => $facultyCount,
            'filesCount' => $filesCount,
            'toReviewCount' => $toReviewCount,
            'completedReviewsCount' => $completedReviewsCount,
            'approvedCount' => $approvedCount,
            'declinedCount' => $declinedCount,
            'folderCounts' => $folderCounts, // Pass the folder counts to the view
        ]);
    }
    


    

}
