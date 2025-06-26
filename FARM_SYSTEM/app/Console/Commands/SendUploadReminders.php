<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserLogin;
use App\Models\FolderName;
use App\Models\UploadSchedule;
use App\Models\CoursesFile;
use App\Models\ReminderTracking;
use App\Notifications\DocumentUploadReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendUploadReminders extends Command
{
    protected $signature = 'reminders:upload';
    protected $description = 'Send reminder emails to faculty members who haven\'t uploaded required documents';

    public function handle()
    {
        DB::beginTransaction();
        try {
            $schedule = UploadSchedule::first();
            
            if (!$schedule) {
                $this->error('No upload schedule found in database.');
                Log::error('No upload schedule found when trying to send reminders');
                return;
            }

            $now = Carbon::now('Asia/Manila');
            $today = $now->format('Y-m-d');
            
            $remindersSentToday = ReminderTracking::whereDate('sent_date', $today)->count();
            if ($remindersSentToday > 0) {
                $this->info('Reminders have already been sent today.');
                Log::info('Reminders already sent today. Skipping.');
                DB::commit();
                return;
            }

            $deadlineDateTime = Carbon::parse($schedule->end_date)
                ->setTimeFromTimeString($schedule->stop_time)
                ->setTimezone('Asia/Manila');
                
            $oneWeekBeforeDeadline = $deadlineDateTime->copy()->subWeek();

            if ($now->lt($oneWeekBeforeDeadline) || $now->gt($deadlineDateTime)) {
                $this->info('Outside of reminder period. No reminders needed.');
                Log::info('Reminder check: Outside of reminder period');
                DB::commit();
                return;
            }

            $requiredFolders = FolderName::all();
            if ($requiredFolders->isEmpty()) {
                $this->error('No required folders found.');
                Log::error('No required folders found when trying to send reminders');
                DB::commit();
                return;
            }

            $faculty = UserLogin::whereIn('role', ['faculty', 'faculty-coordinator'])
                ->whereNotIn('user_login_id', function($query) use ($today) {
                    $query->select('user_login_id')
                        ->from('reminder_tracking')
                        ->whereDate('sent_date', $today);
                })
                ->lockForUpdate()  
                ->get();

            if ($faculty->isEmpty()) {
                $this->info('No faculty members need reminders today.');
                Log::info('No faculty members need reminders');
                DB::commit();
                return;
            }

            $remindersSent = 0;
            
            foreach ($faculty as $member) {
                $alreadySent = ReminderTracking::where('user_login_id', $member->user_login_id)
                    ->whereDate('sent_date', $today)
                    ->exists();
                
                if ($alreadySent) {
                    continue;
                }

                $missingFolders = [];
                
                foreach ($requiredFolders as $folder) {
                    $hasUploaded = CoursesFile::where('user_login_id', $member->user_login_id)
                        ->where('folder_name_id', $folder->folder_name_id)
                        ->exists();
                    
                    if (!$hasUploaded) {
                        $missingFolders[] = $folder->folder_name;
                    }
                }

                if (!empty($missingFolders)) {
                    try {
                        $daysUntilDeadline = $now->diffInDays($deadlineDateTime, false);
                        
                        ReminderTracking::create([
                            'user_login_id' => $member->user_login_id,
                            'sent_date' => $today
                        ]);

                        $member->notify(new DocumentUploadReminder(
                            $schedule->end_date,
                            $schedule->stop_time,
                            $missingFolders,
                            $daysUntilDeadline
                        ));
                        
                        $remindersSent++;
                        Log::info("Sent reminder to faculty member", [
                            'email' => $member->email,
                            'missing_folders_count' => count($missingFolders),
                            'user_login_id' => $member->user_login_id
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Failed to send reminder", [
                            'email' => $member->email,
                            'error' => $e->getMessage(),
                            'user_login_id' => $member->user_login_id
                        ]);
                        throw $e;
                    }
                }
            }
            
            DB::commit();
            $this->info("Reminder process completed. Sent {$remindersSent} reminders.");
            Log::info("Reminder process completed", ['reminders_sent' => $remindersSent]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Process failed: {$e->getMessage()}");
            Log::error("Reminder process failed", ['error' => $e->getMessage()]);
        }
    }
}