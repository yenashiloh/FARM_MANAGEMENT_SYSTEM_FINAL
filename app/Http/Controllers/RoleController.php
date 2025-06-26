<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLogin;
use App\Models\Announcement;
use App\Models\FolderName;
use App\Models\LogoutLog;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Str;
use App\Services\FLSSApiService;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\DB;


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
        
            if (in_array($user->role, ['faculty', 'faculty-coordinator'])) {
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
                case 'faculty-coordinator':
                    return redirect()->route('faculty.faculty-dashboard'); 
                case 'director':
                    return redirect()->route('director.director-dashboard');
                default:
                    return redirect()->back()->with('error', 'Invalid role.');
            }
        }
    
        $state = Str::random(40);
        session(['oauth_state' => $state]);
    
        $announcement = Announcement::where('type_of_recepient', 'All Faculty')
                                    ->where('published', 1)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        
        $oauthUrl = 'https://pup-hris.site/auth/oauth?response_type=code&client_id=' . env('FARMS_CLIENT_ID') .
                    '&redirect_uri=' . urlencode(env('FARMS_REDIRECT_URI')) .
                    '&state=' . $state;
    
        return view('welcome', [
            'announcement' => $announcement,  
            'oauthUrl' => $oauthUrl,
        ]);
    }


  //API - HRIS
    public function handleProviderCallback(Request $request)
    {
        try {
            if ($request->state !== session('oauth_state')) {
                throw new Exception('Invalid state parameter');
            }
    
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post('https://pup-hris.site/api/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'redirect_uri' => env('FARMS_REDIRECT_URI'),
                'client_id' => env('FARMS_CLIENT_ID'),
                'client_secret' => env('FARMS_CLIENT_SECRET')
            ]);
    
            if (!$response->successful()) {
                throw new Exception('Failed to get access token: ' . $response->status());
            }
    
            $tokenData = $response->json();
    
            if (!isset($tokenData['faculty_data'])) {
                throw new Exception('Faculty data not found in response');
            }
    
            $facultyData = $tokenData['faculty_data'];
    
            \Log::info('Faculty data before database insert:', [
                'faculty_code' => $facultyData['faculty_code'] ?? null,
                'faculty_id' => $facultyData['UserID'] ?? null,
            ]);
    
            \DB::beginTransaction();
            try {
                $user = UserLogin::updateOrCreate(
                    ['email' => $facultyData['Email']],
                    [
                        'user_id' => $facultyData['UserID'],
                        'faculty_id' => $facultyData['UserID'],
                        'faculty_code' => $facultyData['faculty_code'] ?? null,
                        'first_name' => $facultyData['first_name'],
                        'middle_name' => $facultyData['middle_name'],
                        'surname' => $facultyData['last_name'],
                        'name_extension' => $facultyData['name_extension'],
                        'employment_type' => $facultyData['faculty_type'] ?? null,
                        'is_active' => $facultyData['status'] === 'Active',
                        'role' => 'faculty',
                        'password' => Hash::make(Str::random(16))
                    ]
                );
    
                \Log::info('User after database operation:', [
                    'user_id' => $user->user_login_id,
                    'faculty_code' => $user->faculty_code,
                    'faculty_id' => $user->faculty_id,
                ]);
    
                // Create an instance of FlssApiService
                $flssApiService = app(FlssApiService::class);
    
                // Fetch course schedules from FLSS API
                try {
                    $courseSchedules = $flssApiService->getCourseSchedules();
                    $courseFiles = $flssApiService->getCourseFiles();
    
                    \Log::info('API Responses:', [
                        'schedules_count' => count($courseSchedules['course_schedules'] ?? $courseSchedules),
                        'files_count' => count($courseFiles['courses_files'] ?? $courseFiles)
                    ]);
    
                    // Create a lookup map of semester and school year by course_schedule_id
                    $semesterData = [];
                    foreach ($courseFiles['courses_files'] ?? $courseFiles as $courseFile) {
                        $scheduleId = $courseFile['course_schedule_id'];
                        $semesterData[$scheduleId] = [
                            'semester' => $courseFile['semester'] ?? '',
                            'school_year' => $courseFile['school_year'] ?? '',
                            'subject' => $courseFile['subject'] ?? ''
                        ];
                    }
    
                    // Filter schedules for the current faculty
                    $scheduleItems = $courseSchedules['course_schedules'] ?? $courseSchedules;
                    $facultySchedules = collect($scheduleItems)->filter(function ($schedule) use ($user) {
                        return (string)$schedule['user_login_id'] === (string)$user->faculty_id;
                    });
    
                    \Log::info('Found faculty schedules:', [
                        'faculty_id' => $user->faculty_id,
                        'schedule_count' => $facultySchedules->count()
                    ]);
    
                    // Insert or update course schedules in the database
                    foreach ($facultySchedules as $schedule) {
                        $scheduleId = $schedule['course_schedule_id'];
                        $semInfo = $semesterData[$scheduleId] ?? [
                            'semester' => '',
                            'school_year' => '',
                            'subject' => ''
                        ];
    
                        \Log::info('Combined schedule data:', [
                            'schedule_id' => $scheduleId,
                            'schedule' => $schedule,
                            'semester_info' => $semInfo
                        ]);
    
                        try {
                            $existingSchedule = CourseSchedule::where('course_schedule_id', $scheduleId)
                                ->where('user_login_id', $user->user_login_id)
                                ->first();
    
                            if ($existingSchedule) {
                                \Log::info('Updating existing record:', ['id' => $existingSchedule->id]);
    
                                $existingSchedule->semester = $semInfo['semester'];
                                $existingSchedule->school_year = $semInfo['school_year'];
                                $existingSchedule->course_subjects = $schedule['course_subjects'] ?? $semInfo['subject'] ?? '';
                                $existingSchedule->course_code = $schedule['course_code'] ?? '';
                                $existingSchedule->year_section = $schedule['year_section'] ?? '';
                                $existingSchedule->program = $schedule['program'] ?? '';
                                $existingSchedule->schedule = $schedule['schedule'] ?? '';
    
                                $existingSchedule->save();
                            } else {
                                \Log::info('Creating new record');
    
                                \DB::table('course_schedules')->insert([
                                    'course_schedule_id' => $scheduleId,
                                    'user_login_id' => $user->user_login_id,
                                    'semester' => $semInfo['semester'],
                                    'school_year' => $semInfo['school_year'],
                                    'course_subjects' => $schedule['course_subjects'] ?? $semInfo['subject'] ?? '',
                                    'course_code' => $schedule['course_code'] ?? '',
                                    'year_section' => $schedule['year_section'] ?? '',
                                    'program' => $schedule['program'] ?? '',
                                    'schedule' => $schedule['schedule'] ?? '',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error saving course schedule: ' . $e->getMessage(), [
                                'scheduleId' => $scheduleId,
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error fetching course schedules: ' . $e->getMessage());
                }
    
                $sessionData = [
                    'access_token' => $tokenData['access_token'],
                    'expires_in' => $tokenData['expires_in'],
                    'token_type' => $tokenData['token_type'] ?? 'Bearer',
                    'faculty_data' => $facultyData,
                    'user_id' => $user->user_login_id,
                    'user_role' => 'faculty'
                ];
    
                \DB::commit();
    
                $request->session()->regenerate(true);
                Auth::login($user, true);
                session($sessionData);
                session()->save();
    
                \Log::info('Authentication successful', [
                    'user_id' => $user->user_login_id,
                    'faculty_id' => $facultyData['UserID'],
                    'faculty_code' => $user->faculty_code,
                    'session_id' => session()->getId(),
                    'is_authenticated' => Auth::check(),
                    'response_status' => $response->status()
                ]);
                    
                    $loginMessage = $user->first_name . ' ' . $user->surname . ' has logged in';
                DB::table('login_logs')->insert([
                    'user_login_id' => $user->user_login_id,
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_message' => $loginMessage,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                \Log::info('Login logged', [
                    'user_id' => $user->user_login_id,
                    'login_message' => $loginMessage
                ]);
                return redirect()
                    ->route('faculty.faculty-dashboard')
                    ->withHeaders([
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0'
                    ]);
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            \Log::error('OAuth callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'code' => $request->code,
                'state' => $request->state,
                'response' => isset($response) ? $response->json() : null
            ]);
    
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }
    
    
    public function getCourseFiles()
    {
        return $this->makeApiRequest('GET', '/course-files');
    }
}
