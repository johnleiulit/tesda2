<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class CorrectionRequestedNotification extends Notification
{
    use Queueable;
    protected $application;
    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
         $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Application Correction Required - TESDA')
                    ->greeting('Hello ' . $this->application->firstname . '!')
                    ->line('Your application for **' . $this->application->title_of_assessment_applied_for . '** requires some corrections.')
                    ->line('**Admin\'s Feedback:**')
                    ->line($this->application->correction_message)
                    ->line('**What to do next:**')
                    ->line('1. Log in to your dashboard')
                    ->line('2. Click "Edit Application" button')
                    ->line('3. Make the necessary corrections')
                    ->line('4. Resubmit your application')
                    ->action('Edit Application', route('applicant.applications.edit', $this->application))
                    ->line('If you have any questions, please contact our office.')
                    ->salutation('Best regards, TESDA Assessment Center');
    
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'message' => $this->application->correction_message,
            'requested_at' => $this->application->correction_requested_at,
        ];
    }
}
