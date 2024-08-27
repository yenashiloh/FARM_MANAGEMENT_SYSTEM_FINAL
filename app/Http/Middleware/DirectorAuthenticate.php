<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\FolderName;

class DirectorAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== 'director') {
            return redirect()->route('login')->with('error', 'Access denied. You must be a director to access this area.');
        }

        // Check if the director has a folder
        $folder = FolderName::firstOrCreate(
            ['user_login_id' => $user->user_login_id],
            [
                'folder_name' => 'Director ' . $user->last_name . ' Folder',
                'main_folder_name' => 'Director Folder'
            ]
        );

        // Add the folder_name_id to the request for use in the controller
        $request->merge(['folder_name_id' => $folder->folder_name_id]);

        return $next($request);
    }
}