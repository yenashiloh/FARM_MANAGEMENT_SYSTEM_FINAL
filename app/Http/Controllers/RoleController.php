<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;
use App\Models\Announcement;
use App\Models\FolderName;

class RoleController extends Controller
{
    //view login form
    public function showLoginForm()
    {
        return view('login'); 
    }

    //login post
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = UserLogin::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        Auth::login($user);

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.admin-dashboard');
            case 'faculty':
                return redirect()->route('faculty.faculty-dashboard');
            case 'director':
                return redirect()->route('director.director-dashboard');
            default:
                return redirect()->back()->with('error', 'Invalid role.');
        }
    }
    
    //landing page
    public function showLandingPage()
    {
        $announcement = Announcement::where('type_of_recepient', 'All Faculty')
                                    ->where('published', 1) 
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        
        return view('welcome', ['announcement' => $announcement]);
    }
}
