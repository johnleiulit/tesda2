<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class FeedbackReminderNotification extends Notification
{
    use Queueable;

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = trim($this->application->firstname . ' ' . $this->application->surname);
        $ncProgram = $this->application->title_of_assessment_applied_for;
        
        return (new MailMessage)
            ->subject('Employment Feedback Required - ' . $ncProgram)
            ->greeting('Hello ' . $applicantName . ',')
            ->line('I hope you are doing well.')
            ->line('We noticed you haven\'t submitted your employment feedback yet.')
            ->line('As a scholarship recipient, please share your current employment status. This helps us improve the TWSP program.')
            ->action('Submit Feedback Now', url('/applicant/dashboard'))
            ->line('Thank you for your cooperation.')
            ->salutation('Best regards, SHC-TVET');
    }
}
