<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Console\ClosureCommand;
use App\Models\UploadSchedule;  // Add this import

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('reminders:upload')
    ->dailyAt(UploadSchedule::first()?->start_time ?? '00:00')
    ->timezone('Asia/Manila');