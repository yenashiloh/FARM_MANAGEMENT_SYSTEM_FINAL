<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogoutLog;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function logLogout(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                LogoutLog::create([
                    'user_login_id' => $user->user_login_id,
                    'logout_time' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logout_message' => $user->first_name . ' ' . $user->surname . ' is logged out'
                ]);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
             $request->session()->forget('announcements_shown');
            
            return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            
            return response()->json(['status' => 'error', 'message' => 'An error occurred during logout'], 500);
        }
    }
}