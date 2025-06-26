<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckHrisSession
{
    public function handle($request, Closure $next)
    {
        \Log::info('CheckHrisSession Middleware', [
            'is_authenticated' => Auth::check(),
            'has_access_token' => session()->has('access_token'),
            'session_id' => session()->getId(),
            'intended_url' => url()->current()
        ]);

        if (!Auth::check() || !session()->has('access_token')) {
            \Log::warning('Session check failed', [
                'auth_check' => Auth::check(),
                'has_token' => session()->has('access_token')
            ]);
            
            Auth::logout();
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        return $next($request);
    }
}