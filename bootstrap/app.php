<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleAuthenticate;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\DirectorAuthenticate;
use App\Console\Commands\SendAnnouncementEmails;
use App\Http\Middleware\CheckHrisSession;
use App\Console\Commands\SendUploadReminders;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\UploadSchedule;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(PreventBackHistory::class);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleAuthenticate::class,
            'director' => \App\Http\Middleware\DirectorAuthenticate::class,
            'prevent-back-history' => PreventBackHistory::class,
            'hris.session' => \App\Http\Middleware\CheckHrisSession::class,
        ]);
    })
    ->withCommands([
        SendAnnouncementEmails::class,
        SendUploadReminders::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        $uploadSchedule = UploadSchedule::first();
        
        if ($uploadSchedule) {
            $schedule->command('reminders:upload')
                 ->dailyAt('23:59')  
                ->timezone('Asia/Manila');
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->create();