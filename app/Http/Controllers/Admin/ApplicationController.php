<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Notifications\ApplicationApprovedNotification;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingBatch;
use App\Models\TrainingResult;
use App\Models\ApplicationView;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CorrectionRequestedNotification;
use App\Notifications\PaymentVerifiedNotification;


class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $twspApps = Application::query()
            ->with('user:id,name')
            ->where('application_type', 'TWSP')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10, ['*'], 'twsp_page'); 

        $assessmentApps = Application::query()
            ->with('user:id,name')
            ->where('application_type', 'Assessment Only')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10, ['*'], 'assessment_page');

        $resubmittedCount = Application::where('was_corrected', true)
            ->where('status', 'pending')
            ->count();
        
        $firstPaymentCount = Application::where('application_type', 'Assessment Only')
            ->where('status', 'pending')
            ->where('payment_status', Application::PAYMENT_STATUS_SUBMITTED) // Only submitted, not pending
            ->whereNotNull('payment_proof') // Must have uploaded proof
            ->count();

        return view('admin.applicant.index', compact('twspApps', 'assessmentApps', 'status', 'resubmittedCount', 'firstPaymentCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        // Record the view (only once per admin per application)
        ApplicationView::firstOrCreate(
            [
                'application_id' => $application->id,
                'user_id' => auth()->id(),
            ],
            [
                'viewed_at' => now(),
                'view_type' => 'detail',
            ]
        );
        
        // Load relationships
        $application->load([
            'user',
            'workExperiences',
            'trainings',
            'licensureExams',
            'competencyAssessments',
            'twspDocument',
        ]);
        
        return view('admin.applicant.view', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      
            

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function updateReference(Request $request, Application $application)
    {
        $request->validate([
            'reference_number' => 'nullable|string|max:15|regex:/^[0-9]*$/',
        ], [
            'reference_number.regex' => 'Reference number must contain only digits.',
            'reference_number.max' => 'Reference number cannot exceed 15 digits.',
        ]);

        $application->update([
            'reference_number' => $request->reference_number,
        ]);

        return back()->with('success', 'Reference number saved successfully.');
    }
    public function verifyPayment(Application $application)
    {
        $application->update([
            'payment_status' => Application::PAYMENT_STATUS_VERIFIED,
        ]);
        // Send notification to applicant
        $application->user->notify(new PaymentVerifiedNotification($application));
        return back()->with('success', 'Payment verified successfully.');
    }

    public function rejectPayment(Request $request, Application $application)
    {
        $request->validate([
            'payment_remarks' => 'required|string|max:500',
        ]);
        
        $application->update([
            'payment_status' => Application::PAYMENT_STATUS_REJECTED,
            'payment_remarks' => $request->payment_remarks,
        ]);
        
        return back()->with('success', 'Payment rejected. Applicant will be notified.');
    }
    
    public function approveApplication(Application $application, Request $request)
    {
        if ($application->status !== Application::STATUS_PENDING) {
            return back()->with('warning', 'Only pending applications can be approved.');
        }
        DB::transaction(function () use ($application, $request) {
            if ($application->title_of_assessment_applied_for === 'BOOKKEEPING NC III' 
                && $application->application_type === 'TWSP') {
                // BOOKKEEPING + TWSP: Goes to training
                $trainingStatus = Application::TRAINING_STATUS_ENROLLED;
            } else {
                // BOOKKEEPING + Assessment Only OR Other NCs: Skip training
                $trainingStatus = Application::TRAINING_STATUS_COMPLETED;
            }

            $application->update([
                'status' => Application::STATUS_APPROVED,
                'training_status' => $trainingStatus,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_remarks' => $request->input('remarks'),
            ]);
            
         if ($application->application_type === 'TWSP') {
            $announcement = \App\Models\TwspAnnouncement::getActive();
            if ($announcement) {
                $announcement->incrementFilledSlots();
            }
        }
        // Auto-assign to training batch if BOOKKEEPING NC III
        if ($application->title_of_assessment_applied_for === 'BOOKKEEPING NC III' 
            && $application->application_type === 'TWSP') {
            $this->assignToTrainingBatch($application);
        }
        // Send email notification
        // $application->user->notify(new ApplicationApprovedNotification($application));
    });

    return redirect()->route('admin.applications.show', $application)->with('success', 'Application approved.');
    }

    public function rejectApplication(Application $application, Request $request)
    {
        if ($application->status !== Application::STATUS_PENDING) {
            return back()->with('warning', 'Only pending applications can be rejected.');
        }

        $request->validate(['remarks' => ['nullable','string','max:500']]);

        DB::transaction(function () use ($application, $request) {
            $application->update([
                'status' => Application::STATUS_REJECTED,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_remarks' => $request->input('remarks'),
            ]);
        });

        return redirect()->route('admin.applications.show', $application)->with('success', 'Application rejected.');
    }
    private function assignToTrainingBatch(Application $application)
    {
        $ncProgram = $application->title_of_assessment_applied_for;

        // Find the current open batch (enrolling or full but not scheduled)
        $currentBatch = \App\Models\TrainingBatch::where('nc_program', $ncProgram)
            ->whereIn('status', [\App\Models\TrainingBatch::STATUS_ENROLLING, \App\Models\TrainingBatch::STATUS_FULL])
            ->whereDoesntHave('trainingSchedule') // Batch without schedule yet
            ->orderBy('batch_number', 'desc')
            ->first();

         // Check if current batch has space
        if ($currentBatch && $currentBatch->enrolled_count < $currentBatch->max_students) {
            // Assign to current batch
            $application->update(['training_batch_id' => $currentBatch->id]);

            // Refresh the batch to get updated enrolled_count
            $enrolledCount = \App\Models\Application::where('training_batch_id', $currentBatch->id)->count();
            if ($enrolledCount >= $currentBatch->max_students) {
                        $currentBatch->update(['status' => \App\Models\TrainingBatch::STATUS_FULL]);
                    }
            } else {
                    // Create new batch
                    $nextBatchNumber = \App\Models\TrainingBatch::where('nc_program', $ncProgram)
                        ->max('batch_number') + 1;

                if (!$nextBatchNumber) {
                    $nextBatchNumber = 1; // First batch
                }

                $newBatch = \App\Models\TrainingBatch::create([
                    'nc_program' => $ncProgram,
                    'batch_number' => $nextBatchNumber,
                    'max_students' => 25,
                    'status' => \App\Models\TrainingBatch::STATUS_ENROLLING,
                ]);

                // Assign to new batch
                $application->update(['training_batch_id' => $newBatch->id]);
            }

            // Create training result record
            \App\Models\TrainingResult::create([
                'application_id' => $application->id,
                'training_batch_id' => $application->training_batch_id,
                'result' => \App\Models\TrainingResult::RESULT_ONGOING,
            ]);
    }
    public function requestCorrection(Request $request, Application $application)
    {
        $request->validate([
            'correction_message' => 'required|string|max:1000',
        ]);
        
        // Update application
        $application->update([
            'correction_requested' => true,
            'correction_message' => $request->correction_message,
            'correction_requested_at' => now(),
        ]);
        
        // Send email to applicant
        $application->user->notify(new CorrectionRequestedNotification($application));
        
        return redirect()->back()->with('success', 'Correction request sent to applicant.');
    }

    // When admin approves a TWSP application
    public function approve($id)
    {
        $application = Application::findOrFail($id);
        
        // Check if it's a TWSP application
        if ($application->application_type === 'twsp') {
            $announcement = TwspAnnouncement::getActive();
            
            if ($announcement && $announcement->hasAvailableSlots()) {
                $announcement->incrementFilledSlots();
            }
        }
        
        $application->status = 'approved';
        $application->save();
        
        return redirect()->back()->with('success', 'Application approved!');
    }

}
