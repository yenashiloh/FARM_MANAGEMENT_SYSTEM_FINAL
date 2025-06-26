<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentUploadReminder extends Notification
{
    use Queueable;
    protected $endDate;
    protected $stopTime;
    protected $missingFolders;
    protected $daysRemaining;

    public function __construct($endDate, $stopTime, $missingFolders, $daysRemaining)
    {
        $this->endDate = $endDate;
        $this->stopTime = $stopTime;
        $this->missingFolders = $missingFolders;
        $this->daysRemaining = $daysRemaining;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $endDateTime = \Carbon\Carbon::parse($this->endDate)->format('l, j F Y') . 
            ', ' . \Carbon\Carbon::parse($this->stopTime)->format('h:i A');
        
        return (new MailMessage)
            ->subject('Document Upload Reminder')
            ->view('emails.document-reminder', [
                'name' => $notifiable->first_name ? 'Mr/Ms. ' . $notifiable->first_name : 'Faculty Member',
                'deadline' => $endDateTime,
                'missingFolders' => $this->missingFolders,
                'daysRemaining' => $this->daysRemaining,
                'uploadUrl' => 'https://pupt-farm.com/'
            ]);
    }
}