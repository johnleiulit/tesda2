<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Notifications\EmploymentFeedbackSubmittedNotification;
use App\Models\User;


class ApplicantEmploymentController extends Controller
{
    public function store(Request $request, Application $application)
    {
        // Ensure the application belongs to the authenticated user
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        $validated = $request->validate([
            'date_employed' => 'required|date',
            'occupation' => 'required|string|max:255',
            'employer_name' => 'required|string|max:255',
            'employer_address' => 'required|string',
            'employer_classification' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
        ]);

        // Create or update employment record
        $employmentRecord = $application->employmentRecord()->updateOrCreate(
            ['application_id' => $application->id],
            $validated
        );
        User::where('role', 'admin')->get()->each(function ($admin) use ($employmentRecord, $application) {
            $admin->notify(new EmploymentFeedbackSubmittedNotification($employmentRecord, $application));
        });

        return redirect()->back()->with('success', 'Employment details submitted successfully!');
    }
}
