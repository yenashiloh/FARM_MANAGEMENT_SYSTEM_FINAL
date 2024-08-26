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
            Notification::where('id', $notificationId)
                        ->where('user_login_id', Auth::id())
                        ->update(['is_read' => true]);
        } else {
            Notification::where('user_login_id', Auth::id())
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
        }

        return response()->json(['status' => 'success']);
    }

    //get faculty notification
    public function getNotifications()
    {
        $notifications = Notification::where('user_login_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->take(10)
                                    ->get()
                                    ->map(function ($notification) {
                                        return [
                                            'id' => $notification->id,
                                            'sender' => $notification->sender,
                                            'message' => $notification->notification_message,
                                            'created_at' => $notification->created_at->format('F j, Y, g:ia'),
                                            'is_read' => $notification->is_read,
                                            'url' => route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $notification->folder_name_id])
                                        ];
                                    });

        return response()->json(['notifications' => $notifications]);
    }

    //get admin notification
    public function getAdminNotifications()
    {
        $notifications = Notification::where('user_login_id', auth()->user()->user_login_id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                $user_login_id = $notification->user_login_id; 
                $folder_name_id = $notification->folder_name_id; 
    
                return [
                    'id' => $notification->id,
                    'sender' => $notification->sender,
                    'message' => $notification->notification_message,
                    'created_at' => $notification->created_at->format('F j, Y, g:ia'),
                    'is_read' => $notification->is_read,
                    'url' => route('admin.accomplishment.view-accomplishment', [
                        'user_login_id' => $user_login_id,
                        'folder_name_id' => $folder_name_id
                    ])
                ];
            });
    
        return response()->json(['notifications' => $notifications]);
    }

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
    
    //mark as read
    public function markAsRead(Request $request)
    {
        Log::info('Mark as read request received', ['request' => $request->all()]);

        $validatedData = $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id',
        ]);
    
        $updated = Notification::whereIn('id', $validatedData['notification_ids'])
            ->where('user_login_id', auth()->user()->user_login_id)
            ->update(['is_read' => true]);
    
        if ($updated) {
            Log::info('Notifications marked as read', ['notification_ids' => $validatedData['notification_ids']]);
            return response()->json(['message' => 'Notifications marked as read successfully.']);
        } else {
            Log::warning('No notifications were updated', ['notification_ids' => $validatedData['notification_ids']]);
            return response()->json(['message' => 'No notifications were updated.'], 400);
        }
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
