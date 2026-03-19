<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;
use App\Models\AssessmentResult;

class AssessmentResultNotification extends Notification
{
    use Queueable;
    
    protected $application;
    protected $assessmentResult;

    public function __construct(Application $application, AssessmentResult $assessmentResult)
    {
        $this->application = $application;
        $this->assessmentResult = $assessmentResult;
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
        $result = $this->assessmentResult->result;
        $isPassed = $result === AssessmentResult::RESULT_PASS;
        
        $message = (new MailMessage)
            ->subject('Assessment Result - ' . $ncProgram)
            ->greeting('Hello ' . $applicantName . ',');
        
        if ($isPassed) {
            // PASSED
            $message->line('Congratulations! You have **PASSED** your assessment for ' . $ncProgram . '.')
                ->line('**Assessment Details:**')
                ->line('• NC Program: ' . $ncProgram)
                ->line('• Result: **COMPETENT**')
                ->line('• Assessment Date: ' . $this->assessmentResult->assessed_at->format('F d, Y'))
                ->line('**Next Steps:**')
                ->line('Your Certificate of Competency (COC) will be processed.')
                ->line('You will receive an email from TESDA once your Certificate of Competency is ready for claiming.')
                ->action('View Result', route('applicant.dashboard'))
                ->line('Congratulations once again on your achievement!')
                ->line('Thank you,')
                ->salutation('SHC-TVET Training and Assessment Centre');
        } else {
            // FAILED
            $message->line('Your assessment result for ' . $ncProgram . ' is now available.')
                ->line('**Assessment Details:**')
                ->line('• NC Program: ' . $ncProgram)
                ->line('• Result: **NOT YET COMPETENT (NYC)**')
                ->line('• Assessment Date: ' . $this->assessmentResult->assessed_at->format('F d, Y'));
            
            // Show which COCs failed if available
            $nycCocs = $this->assessmentResult->getNycCocs();
            if ($nycCocs->isNotEmpty()) {
                $message->line('**Competencies that need improvement:**');
                foreach ($nycCocs as $coc) {
                    $message->line('• ' . $coc->coc_title);
                }
            }
            
            $message->line('**Next Steps:**')
                ->line('You may apply for reassessment to improve your competencies.')
                ->line('Please proceed with the reassessment payment and upload your proof of payment in your dashboard.')
                ->action('View Dashboard', route('applicant.dashboard'))
                ->line('Don\'t be discouraged! Use this as an opportunity to improve your skills.')
                ->line('Thank you,')
                ->salutation('SHC-TVET Training and Assessment Centre');
        }
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'assessment_result_id' => $this->assessmentResult->id,
            'nc_program' => $this->application->title_of_assessment_applied_for,
            'result' => $this->assessmentResult->result,
            'message' => 'Your assessment result is now available.',
        ];
    }
}
