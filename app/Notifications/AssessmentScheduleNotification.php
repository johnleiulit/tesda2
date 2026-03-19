<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AssessmentBatch;
use App\Models\Application;

class AssessmentScheduleNotification extends Notification
{
    use Queueable;
    
    protected $assessmentBatch;
    protected $application;

    public function __construct(AssessmentBatch $assessmentBatch, Application $application)
    {
        $this->assessmentBatch = $assessmentBatch;
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = trim($this->application->firstname . ' ' . $this->application->surname);
        $ncProgram = $this->assessmentBatch->nc_program;
        
        $message = (new MailMessage)
            ->subject('Assessment Schedule - ' . $ncProgram)
            ->greeting('Hello ' . $applicantName . ',')
            ->line('Your assessment schedule has been confirmed!')
            ->line('**Assessment Details:**')
            ->line('• NC Program: ' . $ncProgram)
            ->line('• Batch: ' . $this->assessmentBatch->batch_name)
            ->line('• Assessment Date: ' . $this->assessmentBatch->assessment_date->format('F d, Y'))
            ->line('• Time: ' . $this->assessmentBatch->start_time->format('g:i A') . ' - ' . $this->assessmentBatch->end_time->format('g:i A'))
            ->line('• Venue: ' . $this->assessmentBatch->venue);

        // Add intensive review days if available
        if ($this->assessmentBatch->intensive_review_day1) {
            $message->line('**Intensive Review Schedule:**')
                ->line('• Day 1: ' . $this->assessmentBatch->intensive_review_day1->format('F d, Y') . 
                       ' (' . $this->assessmentBatch->intensive_review_day1_start->format('g:i A') . 
                       ' - ' . $this->assessmentBatch->intensive_review_day1_end->format('g:i A') . ')');
        }
        
        if ($this->assessmentBatch->intensive_review_day2) {
            $message->line('• Day 2: ' . $this->assessmentBatch->intensive_review_day2->format('F d, Y') . 
                           ' (' . $this->assessmentBatch->intensive_review_day2_start->format('g:i A') . 
                           ' - ' . $this->assessmentBatch->intensive_review_day2_end->format('g:i A') . ')');
        }

        $message->line('**Important Reminders:**')
            ->line('• Please arrive 30 minutes before the scheduled time')
            ->line('• Bring valid ID and required documents')
            ->line('• Contact us if you have any concerns')
            ->action('View Dashboard', route('applicant.dashboard'))
            ->line('Good luck with your assessment!')
            ->salutation('SHC-TVET Training and Assessment Centre');
            
        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'assessment_batch_id' => $this->assessmentBatch->id,
            'application_id' => $this->application->id,
            'batch_name' => $this->assessmentBatch->batch_name,
            'assessment_date' => $this->assessmentBatch->assessment_date,
            'message' => 'Your assessment schedule has been confirmed.',
        ];
    }
}
