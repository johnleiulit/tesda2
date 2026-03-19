<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('feedback:send-reminders', function () {
    return $this->call(\App\Console\Commands\SendFeedbackReminders::class);
})->purpose('Send feedback reminder emails to scholars');

// Schedule the command
if (config('app.env') !== 'production') {
    // For testing: every 5 minutes
    Schedule::command('feedback:send-reminders')->everyFiveMinutes();
} else {
    // For production: twice daily at 9 AM and 9 PM
    Schedule::command('feedback:send-reminders')->twiceDaily(9, 21);
}
