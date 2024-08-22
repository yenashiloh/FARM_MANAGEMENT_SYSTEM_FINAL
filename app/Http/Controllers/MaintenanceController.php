<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLogin;
use App\Models\FolderName;


class MaintenanceController extends Controller
{
     //show folder maintenance page
    public function folderMaintenancePage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $folders = FolderName::all();

        return view('admin.maintenance.create-folder', [
            'folders' => $folders
        ]);
    }

    //store main folder
    public function storeFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'main_folder_name' => 'required|string',
        ]);
    
        FolderName::create([
            'user_login_id' => Auth::id(), 
            'folder_name' => $request->folder_name,
            'main_folder_name' => $request->main_folder_name,
        ]);
    
        return redirect()->route('admin.maintenance.create-folder')->with('success', 'Folder added successfully!');
    }
    
    //update main folder
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

    //delete main folder
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
