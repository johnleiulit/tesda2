<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EmploymentRecord;
use App\Models\Application;

class EmploymentFeedbackSubmittedNotification extends Notification
{
    use Queueable;
    
    protected $employmentRecord;
    protected $application;

    public function __construct(EmploymentRecord $employmentRecord, Application $application)
    {
        $this->employmentRecord = $employmentRecord;
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
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
        $applicantName = trim($this->application->firstname . ' ' . $this->application->surname);
        $ncProgram = $this->application->title_of_assessment_applied_for;
        
        return (new MailMessage)
            ->subject('New Employment Feedback Submitted - ' . $applicantName)
            ->greeting('Hello Admin,')
            ->line('An applicant has submitted their employment feedback.')
            ->line('**Applicant Information:**')
            ->line('• Name: ' . $applicantName)
            ->line('• Email: ' . $this->application->email)
            ->line('• NC Program: ' . $ncProgram)
            ->line('**Employment Details:**')
            ->line('• Position: ' . $this->employmentRecord->occupation)
            ->line('• Company: ' . $this->employmentRecord->employer_name)
            ->line('• Classification: ' . $this->employmentRecord->employer_classification)
            ->line('• Monthly Income: ₱' . number_format($this->employmentRecord->monthly_income, 2))
            ->line('• Date Employed: ' . $this->employmentRecord->date_employed->format('F d, Y'))
            ->line('• Submitted: ' . $this->employmentRecord->created_at->format('F d, Y g:i A'))
            ->action('View Employment Feedback', url('/admin/employment-feedback/' . $this->employmentRecord->id))
            ->line('Please review this feedback to track TWSP program outcomes.')
            ->salutation('SHC-TVET Training and Assessment Centre System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employment_record_id' => $this->employmentRecord->id,
            'application_id' => $this->application->id,
            'applicant_name' => trim($this->application->firstname . ' ' . $this->application->surname),
            'occupation' => $this->employmentRecord->occupation,
            'message' => 'New employment feedback submitted.',
        ];
    }
}
