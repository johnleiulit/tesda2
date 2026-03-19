<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class PaymentVerifiedNotification extends Notification
{
    use Queueable;
    
    protected $application;
    protected $isReassessment;

    public function __construct(Application $application, $isReassessment = false)
    {
        $this->application = $application;
        $this->isReassessment = $isReassessment;
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
        
        // Count eligible applicants for the same NC program
        $eligibleCount = Application::where('title_of_assessment_applied_for', $ncProgram)
            ->where(function($query) {
                // NEW Assessment Only applicants
                $query->where('application_type', 'Assessment Only')
                    ->where('status', Application::STATUS_APPROVED)
                    ->where('payment_status', Application::PAYMENT_STATUS_VERIFIED)
                    ->whereNull('assessment_batch_id');
            })
            ->orWhere(function($query) use ($ncProgram) {
                // NEW TWSP applicants
                $query->where('title_of_assessment_applied_for', $ncProgram)
                    ->where('application_type', 'TWSP')
                    ->where('status', Application::STATUS_APPROVED)
                    ->where('training_status', Application::TRAINING_STATUS_COMPLETED)
                    ->whereNull('assessment_batch_id');
            })
            ->orWhere(function($query) use ($ncProgram) {
                // REASSESSMENT applicants
                $query->where('title_of_assessment_applied_for', $ncProgram)
                    ->whereHas('assessmentResult', function($q) {
                        $q->where('result', 'Not Yet Competent');
                    })
                    ->where('reassessment_payment_status', 'verified')
                    ->whereNull('assessment_batch_id');
            })
            ->count();
        
        $paymentType = $this->isReassessment ? 'reassessment payment' : 'payment';
        
        return (new MailMessage)
            ->subject('Payment Verified - ' . $ncProgram)
            ->greeting('Hello ' . $applicantName . ',')
            ->line('Your ' . $paymentType . ' has been **verified successfully**.')
            ->line('**Application Details:**')
            ->line('• NC Program: ' . $ncProgram)
            ->line('• Application Type: ' . $this->application->application_type)
            ->line('**Next Steps:**')
            ->line('Your application is now ready for assessment scheduling.')
            ->line('You will receive another email once your assessment schedule is confirmed.')
            ->line('**Assessment Queue Information:**')
            ->line('There are currently **' . $eligibleCount . ' eligible applicant(s)** for ' . $ncProgram . ' waiting for assessment scheduling.')
            ->line('You may visit your dashboard to see the current count of eligible applicants for this assessment.')
            ->action('View Dashboard', route('applicant.dashboard'))
            ->line('Thank you,')
            ->salutation('SHC-TVET Training and Assessment Centre');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'nc_program' => $this->application->title_of_assessment_applied_for,
            'is_reassessment' => $this->isReassessment,
            'message' => 'Your payment has been verified successfully.',
        ];
    }
}
