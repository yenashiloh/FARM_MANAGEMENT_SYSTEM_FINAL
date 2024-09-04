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
use App\Models\UploadSchedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UploadScheduleController extends Controller
{
    public function showUploadSchedule(Request $request)
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
        $userDetails = $user->userDetails; 
        $folders = FolderName::all();
        $folder = FolderName::first(); 
    
        // Use UploadSchedule::first() to get the first record
        $uploadSchedule = UploadSchedule::first();
    
        // Format the dates and times for the form
        if ($uploadSchedule) {
            $startDate = $uploadSchedule->start_date ? (is_string($uploadSchedule->start_date) ? $uploadSchedule->start_date : $uploadSchedule->start_date->format('Y-m-d')) : '';
            $startTime = $uploadSchedule->start_time;
            $endDate = $uploadSchedule->end_date ? (is_string($uploadSchedule->end_date) ? $uploadSchedule->end_date : $uploadSchedule->end_date->format('Y-m-d')) : '';
            $stopTime = $uploadSchedule->stop_time;
        } else {
            $startDate = $startTime = $endDate = $stopTime = '';
        }
    
        return view('admin.maintenance.upload-schedule', [
            'folders' => $folders,
            'folder' => $folder,
            'userDetails' => $userDetails,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'uploadSchedule' => [
                'start_date' => $startDate,
                'start_time' => $startTime,
                'end_date' => $endDate,
                'stop_time' => $stopTime,
            ],
        ]);
    }
    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|string',
            'stop_time' => 'nullable|string',
        ]);
     
        $timezone = 'Asia/Manila';
        $schedule = UploadSchedule::firstOrNew();
        $schedule->start_date = $validatedData['start_date'];
        $schedule->end_date = $validatedData['end_date'];
    
        $parseTime = function($time) use ($timezone) {
            $formats = ['H:i', 'H:i:s'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $time, $timezone)->format('H:i:s');
                } catch (\Exception $e) {
                    continue;
                }
            }
            return null;
        };
        if (!empty($validatedData['start_time']) && $validatedData['start_time'] !== $schedule->start_time) {
            $parsedTime = $parseTime($validatedData['start_time']);
            if ($parsedTime) {
                $schedule->start_time = $parsedTime;
            }
        }
    
        if (!empty($validatedData['stop_time']) && $validatedData['stop_time'] !== $schedule->stop_time) {
            $parsedTime = $parseTime($validatedData['stop_time']);
            if ($parsedTime) {
                $schedule->stop_time = $parsedTime;
            }
        }
    
        $schedule->save();
        return redirect()->route('admin.maintenance.upload-schedule')->with('success', 'Schedule updated successfully!');
    }

    
    // public function edit(UploadSchedule $uploadSchedule)
    // {
    //     // Format the dates and times before passing them to the view
    //     $uploadSchedule->start_date = \Carbon\Carbon::parse($uploadSchedule->start_date)->format('Y-m-d');
    //     $uploadSchedule->end_date = \Carbon\Carbon::parse($uploadSchedule->end_date)->format('Y-m-d');
    //     $uploadSchedule->start_time = \Carbon\Carbon::parse($uploadSchedule->start_time)->format('H:i');
    //     $uploadSchedule->stop_time = \Carbon\Carbon::parse($uploadSchedule->stop_time)->format('H:i');
    
    //     // Pass the formatted data to the view
    //     return view('upload-schedules.edit', compact('uploadSchedule'));
    // }
    

    
    

  

    // public function update(Request $request, UploadSchedule $uploadSchedule)
    // {
    //     $validatedData = $request->validate([
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //         'start_time' => 'required',
    //         'stop_time' => 'required|after:start_time',
    //     ]);

    //     $uploadSchedule->update($validatedData);

    //     return redirect()->route('admin.maintenance.upload-schedule')->with('success', 'Upload schedule updated successfully.');
    // }
}
