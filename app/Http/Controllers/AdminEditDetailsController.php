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

class AdminEditDetailsController extends Controller
{
    public function adminAccountPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

           
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $notificationCount = $notifications->where('is_read', 0)->count();
        
        $folders = FolderName::all();

        return view('admin.admin-account', [
            'folders' => $folders,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }

}
