<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\CoursesFile;
use App\Models\UserDetails;
use App\Models\Notification;
use Illuminate\Support\Facades\Log; 

class NotificationController extends Controller
{
    //get notification count 
    public function getNotificationCount()
    {
        $count = Notification::where('user_login_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
        return response()->json(['count' => $count]);
    }

    public function markNotificationsAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
    
        if ($notificationId) {
            // Mark specific notification as read
            Notification::where('id', $notificationId)
                        ->where('user_login_id', Auth::id())
                        ->update(['is_read' => true]);
        } else {
            // Mark all notifications as read
            Notification::where('user_login_id', Auth::id())
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
        }
    
        return response()->json(['status' => 'success']);
    }

    public function getNotifications(Request $request)
    {
        $lastId = $request->input('last_id', 0);
    
        $notifications = Notification::where('user_login_id', Auth::id())
                                    ->where('id', '>', $lastId)
                                    ->orderBy('created_at', 'desc')
                                    ->orderBy('id', 'desc')
                                    ->take(10)
                                    ->get()
                                    ->map(function ($notification) {
                                        return [
                                            'id' => $notification->id,
                                            'sender' => $notification->sender ? $notification->sender->first_name . ' ' . $notification->sender->surname : 'Unknown',
                                            'message' => $notification->notification_message,
                                            'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                                            'created_at_formatted' => $notification->created_at->format('F j, Y, g:ia'),
                                            'is_read' => (bool)$notification->is_read,
                                            'url' => route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $notification->folder_name_id])
                                        ];
                                    });
    
        return response()->json(['notifications' => $notifications]);
    }
        

    //get admin notification
    public function getAdminNotifications()
    {
        $notifications = Notification::where('user_login_id', auth()->user()->user_login_id)
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                $user_login_id = $notification->user_login_id; 
                $folder_name_id = $notification->folder_name_id; 
                $sender = $notification->sender ? $notification->sender->first_name . ' ' . $notification->sender->surname : 'Unknown';

                return [
                    'id' => $notification->id,
                    'sender' => $sender,
                    'message' => $notification->notification_message,
                    'created_at' => $notification->created_at->format('F j, Y, g:ia'),
                    'is_read' => (bool)$notification->is_read,
                    'url' => route('admin.accomplishment.view-accomplishment', [
                        'user_login_id' => $user_login_id,
                        'folder_name_id' => $folder_name_id
                    ])
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    //get the admin notification count
    public function getAdminNotificationCount()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['count' => 0]);
        }
    
        $count = Notification::where('user_login_id', auth()->id())
                            ->where('is_read', 0)
                            ->count();
    
        return response()->json(['count' => $count]);
    }
    
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
        
        Notification::where('id', $notificationId)
                   ->where('user_login_id', auth()->id())
                   ->update(['is_read' => true]);
        
        return response()->json(['status' => 'success']);
    }

    public function markAllAsRead()
    {
        Notification::where('user_login_id', auth()->id())
                   ->where('is_read', 0)
                   ->update(['is_read' => true]);
        
        return response()->json(['status' => 'success']);
    }
    
    //log click
    public function logClick(Request $request)
    {
    
        $request->validate([
            'notification_id' => 'required|integer|exists:notifications,id',
        ]);

        Log::info('Notification clicked', ['notification_id' => $request->notification_id, 'user_id' => auth()->user()->user_login_id]);

        return response()->json(['message' => 'Click logged successfully.']);
    }
}
