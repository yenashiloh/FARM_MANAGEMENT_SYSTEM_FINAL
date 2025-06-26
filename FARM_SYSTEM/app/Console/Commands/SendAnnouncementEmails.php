<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;
use App\Models\UserLogin;


class SendAnnouncementEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:announcement-emails {announcementId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send announcement emails to the specified recipients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $announcementId = $this->argument('announcementId');
        $announcement = Announcement::find($announcementId);

        if (!$announcement) {
            $this->error('Announcement not found.');
            return 1;
        }

        $recipientEmailsString = $announcement->type_of_recepient;

        if ($recipientEmailsString === 'All Faculty') {
            $recipients = User::where('role', 'faculty')->pluck('email')->toArray();
        } else {
            $recipients = explode(', ', $recipientEmailsString);
        }

        $this->sendAnnouncementEmails($announcement, $recipients);

        $this->info('Emails have been sent successfully.');
        return 0;
    }

    protected function sendAnnouncementEmail($announcement, $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Log::warning("Invalid email address: {$email}");
            return;
        }

        try {
            Mail::send('admin.emails.announcement', 
                ['announcement' => $announcement], 
                function ($message) use ($announcement, $email) {
                    $message->to($email)
                        ->subject($announcement->subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );
        } catch (\Exception $e) {
            \Log::error("Failed to send email to {$email}: " . $e->getMessage());
        }
    }
        
}
