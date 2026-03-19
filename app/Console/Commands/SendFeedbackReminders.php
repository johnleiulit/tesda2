<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Notifications\FeedbackReminderNotification;

class SendFeedbackReminders extends Command
{
    protected $signature = 'feedback:send-reminders';
    protected $description = 'Send feedback reminder emails to scholars who haven\'t submitted employment feedback';

    public function handle()
    {
        $this->info('Starting feedback reminder process...');

        // Find ALL scholars who completed assessment (Competent OR Not Yet Competent) 
        // but haven't submitted employment feedback
        $eligibleApplications = Application::whereNotNull('scholarship_type')
            ->whereHas('assessmentResult', function ($query) {
                $query->whereIn('result', ['Competent', 'Not Yet Competent']);
            })
            ->whereDoesntHave('employmentRecord')
            ->get();

        $this->info("Found {$eligibleApplications->count()} eligible applications");

        $sentCount = 0;
        foreach ($eligibleApplications as $application) {
            try {
                $user = $application->user;
                $user->notify(new FeedbackReminderNotification($application));
                $sentCount++;
                $this->info("Reminder sent to: {$application->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$application->email}: " . $e->getMessage());
            }
        }

        $this->info("Completed. Sent {$sentCount} reminders.");
        return Command::SUCCESS;
    }
}
