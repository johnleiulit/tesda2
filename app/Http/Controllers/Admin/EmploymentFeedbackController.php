<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\TrainingBatch;  // Changed from AssessmentBatch
use App\Models\EmploymentRecord;
use Illuminate\Http\Request;

class EmploymentFeedbackController extends Controller
{
    // First page: List of training batches
    public function index()
    {
        $batches = TrainingBatch::where('status', TrainingBatch::STATUS_COMPLETED)
            ->with(['trainingSchedule', 'applications'])
            ->withCount([
                'applications as twsp_count' => function ($q) {
                    $q->where('application_type', 'twsp')
                      ->where('training_status', 'completed');
                },
                'applications as employed_count' => function ($q) {
                    $q->where('application_type', 'twsp')
                      ->where('training_status', 'completed')
                      ->whereHas('employmentRecord');
                },
                'applications as new_employment_count' => function ($q) {
                    $q->where('application_type', 'twsp')
                    ->where('training_status', 'completed')
                    ->whereHas('employmentRecord', function($er) {
                        $er->whereNull('viewed_at');
                    });
                },
            ])
            ->orderBy('nc_program')
            ->orderBy('batch_number', 'desc')
            ->paginate(15);

        // Get statistics for each batch
        $batchStats = [];
        foreach ($batches as $batch) {
            $batchStats[$batch->id] = [
                'total' => $batch->twsp_count,
                'with_employment' => $batch->employed_count,
                'without_employment' => $batch->twsp_count - $batch->employed_count,
                'new_employment' => $batch->new_employment_count,
            ];
        }

        return view('admin.feedback.index', compact('batches', 'batchStats'));
    }

    // Second page: Show applicants in a specific training batch
    public function show(Request $request,TrainingBatch $batch)
    {
        $search = $request->query('q');

        $applications = $batch->applications()
            ->with(['employmentRecord', 'user', 'assessmentResult', 'trainingResult'])
            ->where('application_type', 'twsp')
            ->where('training_status', 'completed')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('firstname', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('middlename', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.feedback.show', compact('batch', 'applications'))->render();
        }

        return view('admin.feedback.show', compact('batch', 'applications', 'search'));
    }

    public function store(Request $request, Application $application)
    {
        $validated = $request->validate([
            'date_employed' => 'required|date',
            'occupation' => 'required|string|max:255',
            'employer_name' => 'required|string|max:255',
            'employer_address' => 'required|string',
            'employer_classification' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
        ]);

        $application->employmentRecord()->create($validated);

        return redirect()->back()->with('success', 'Employment record added successfully.');
    }

    public function update(Request $request, EmploymentRecord $employmentRecord)
    {
        $validated = $request->validate([
            'date_employed' => 'required|date',
            'occupation' => 'required|string|max:255',
            'employer_name' => 'required|string|max:255',
            'employer_address' => 'required|string',
            'employer_classification' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
        ]);

        $employmentRecord->update($validated);

        return redirect()->back()->with('success', 'Employment record updated successfully.');
    }
    
    public function markViewed(EmploymentRecord $employmentRecord)
    {
        $employmentRecord->markAsViewed();
        return response()->json(['success' => true]);
    }
}
