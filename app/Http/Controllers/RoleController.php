<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;
use App\Models\Announcement;
use App\Models\FolderName;
use App\Models\LogoutLog;

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
    
        if ($user->role === 'faculty') {
            \App\Models\LoginLog::create([
                'user_login_id' => $user->user_login_id,
                'login_time' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'login_message' => $user->first_name . ' ' . $user->surname . ' has logged in',
            ]);
        }
    
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
        if (Auth::check()) {
            $user = Auth::user();
    
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
    
        $announcement = Announcement::where('type_of_recepient', 'All Faculty')
                                    ->where('published', 1)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        
        return view('welcome', ['announcement' => $announcement]);
    }
}
