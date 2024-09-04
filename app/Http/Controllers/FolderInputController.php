<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\FolderInput;
use App\Models\CoursesFile;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;


class FolderInputController extends Controller
{
    //store input field
    public function store(Request $request)
    {
        Log::info('Request Data: ' . json_encode($request->all()));
        try {
            $validated = $request->validate([
                'folder_name_id' => 'required|exists:folder_name,folder_name_id',
                'input_label' => 'required|string',
                'input_type' => 'required|in:text,file',
            ]);
            
            $input = FolderInput::create($validated);
            Log::info('Input Created: ' . json_encode($input->toArray()));
            
            session()->flash('success', 'Input field added successfully!');
    
            return response()->json(['input' => $input]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating input: ' . $e->getMessage());
            return response()->json(['error' => 'Error adding input field: ' . $e->getMessage()], 500);
        }
    }

    //delete input field
    public function destroy($id)
    {
        $input = FolderInput::findOrFail($id);
        $input->delete();
    
        return response()->json(['success' => 'Input field deleted successfully.']);
    }
    
    //update input field
    public function update(Request $request, $id)
    {
        $request->validate([
            'input_label' => 'required|string|max:255',
            'input_type' => 'required|string|in:text,file',
        ]);
    
        $folderInput = FolderInput::findOrFail($id);
        $folderInput->update($request->only(['input_label', 'input_type']));
    
        session()->flash('success', 'Input field updated successfully!');
    
        return response()->json([
            'success' => true,
            'message' => 'Folder input updated successfully'
        ]);
    }
    
    //show input field
    public function showInputs($folder_name_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'admin') {
            return redirect()->route('login');
        }

            
        $folders = FolderName::all();
        $folder = FolderName::find($folder_name_id);
        

        $inputs = FolderInput::where('folder_name_id', $folder_name_id)->get();
        
        $notifications = Notification::where('user_login_id', $user->user_login_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->where('is_read', 0)->count();
        $userDetails = $user->userDetails; 

        return view('admin.maintenance.view-file-input', [
            'inputs' => $inputs,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'userDetails' => $userDetails,
            'folders' => $folders,
            'folder' => $folder,
            
        ]);
    }

    //show edit
    public function show($id)
    {
        $folderInput = FolderInput::findOrFail($id);
        return response()->json($folderInput);
    }

}