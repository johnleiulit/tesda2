<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TrainingSchedule;
use App\Models\Application;

class TrainingScheduleNotification extends Notification
{
    use Queueable;
    
    protected $trainingSchedule;
    protected $application;

    public function __construct(TrainingSchedule $trainingSchedule, Application $application)
    {
        $this->trainingSchedule = $trainingSchedule;
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = trim($this->application->firstname . ' ' . $this->application->surname);
        $ncProgram = $this->trainingSchedule->nc_program;
        
        $message = (new MailMessage)
            ->subject('Training Schedule Confirmed - ' . $ncProgram)
            ->greeting('Hello ' . $applicantName . ',')
            ->line('Your training schedule has been confirmed!')
            ->line('**Training Details:**')
            ->line('• NC Program: ' . $ncProgram)
            ->line('• Schedule: ' . $this->trainingSchedule->schedule_name)
            ->line('• Start Date: ' . $this->trainingSchedule->start_date->format('F d, Y'))
            ->line('• End Date: ' . $this->trainingSchedule->end_date->format('F d, Y'))
            ->line('• Time: ' . $this->trainingSchedule->start_time->format('g:i A') . ' - ' . $this->trainingSchedule->end_time->format('g:i A'))
            ->line('• Days: ' . $this->trainingSchedule->days)
            ->line('• Venue: ' . $this->trainingSchedule->venue);

        // Add instructor if available
        if ($this->trainingSchedule->instructor) {
            $message->line('• Instructor: ' . $this->trainingSchedule->instructor);
        }

        $message->line('**Important Reminders:**')
            ->line('• Please arrive 15 minutes before the scheduled time')
            ->line('• Bring necessary materials and valid ID')
            ->line('• Attendance is mandatory for all training sessions')
            ->line('• Contact us if you have any concerns')
            ->action('View Dashboard', route('applicant.dashboard'))
            ->line('We look forward to seeing you in training!')
            ->salutation('SHC-TVET Training and Assessment Centre');
            
        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'training_schedule_id' => $this->trainingSchedule->id,
            'application_id' => $this->application->id,
            'schedule_name' => $this->trainingSchedule->schedule_name,
            'start_date' => $this->trainingSchedule->start_date,
            'message' => 'Your training schedule has been confirmed.',
        ];
    }
}
