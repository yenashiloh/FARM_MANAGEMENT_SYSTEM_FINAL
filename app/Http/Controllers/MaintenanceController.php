<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\Notification;

class MaintenanceController extends Controller
{
    //show maintenance page
     public function folderMaintenancePage()
     {
         if (!auth()->check()) {
             return redirect()->route('login');
         }
     
         $user = auth()->user();
         $firstName = $user->first_name;
         $surname = $user->surname;
     
         $notifications = Notification::where('user_login_id', $user->user_login_id)
             ->orderBy('created_at', 'desc')
             ->get();
     
         $notificationCount = $notifications->where('is_read', 0)->count();
     
         $folders = FolderName::all();
         $folder = FolderName::first();
     
         return view('admin.maintenance.create-folder', [
             'folders' => $folders,
             'folder' => $folder,
             'firstName' => $firstName,
             'surname' => $surname,
             'notifications' => $notifications,
             'notificationCount' => $notificationCount,
         ]);
     }

    //store main folder
    public function storeFolder(Request $request)
    {
        $request->validate([
            'folder_name' => [
                'required',
                'string',
                'max:255',
                // Custom validation to check unique folder name within the same main folder
                function ($attribute, $value, $fail) use ($request) {
                    if (FolderName::where('folder_name', $value)
                        ->where('main_folder_name', $request->main_folder_name)
                        ->exists()) {
                        $fail("The folder name '{$value}' already exists in {$request->main_folder_name}.");
                    }
                },
            ],
            'main_folder_name' => 'required|string',
        ]);

        FolderName::create([
            'user_login_id' => Auth::id(),
            'folder_name' => $request->folder_name,
            'main_folder_name' => $request->main_folder_name,
        ]);

        return redirect()->route('admin.maintenance.create-folder')->with('success', 'Folder added successfully!');
    }

    
    
    //update folder
    public function updateFolder(Request $request, $folder_name_id)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'main_folder_name' => 'required|string',
        ]);

        $folder = FolderName::findOrFail($folder_name_id);
        $folder->folder_name = $request->folder_name;
        $folder->main_folder_name = $request->main_folder_name;
        $folder->save();

        return redirect()->route('admin.maintenance.create-folder')
                        ->with('success', 'Folder updated successfully!')
                        ->with('updated_folder_id', $folder_name_id);
    }

    //delete folder
    public function deleteFolder($folder_name_id)
    {
        $deleted = FolderName::destroy($folder_name_id);
    
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Folder deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete the folder.'], 500);
        }
    }
}
