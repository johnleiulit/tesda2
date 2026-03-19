<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssessmentBatch;
use App\Models\Application;
use App\Models\User;
use App\Models\AssessmentResult;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\AssessmentResultNotification;
use App\Notifications\AssessmentScheduleNotification;



use Illuminate\Http\Request;

class AssessmentBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // List batches with filtering
    public function index(Request $request)
    {
        $status = $request->query('status', 'scheduled');
        $ncProgram = $request->query('nc_program');
    
        $batches = AssessmentBatch::query()
            ->with(['applications'])
            ->withCount('applications')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($ncProgram, fn($q) => $q->where('nc_program', $ncProgram))
            ->orderBy('assessment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Eligible pool (approved + training completed + not yet assigned/completed)
        $eligibleQuery = Application::where('status', Application::STATUS_APPROVED)
            ->where(function ($q) {
            // For BOOKKEEPING: must complete training
            $q->where(function ($bookkeeping) {
                $bookkeeping->where('title_of_assessment_applied_for', 'BOOKKEEPING NC III')
                        ->where('training_status', Application::TRAINING_STATUS_COMPLETED);
            })
            // For Other NCs: skip training check
            ->orWhere(function ($otherNcs) {
                $otherNcs->where('title_of_assessment_applied_for', '!=', 'BOOKKEEPING NC III');
            });
        })
        ->where(function ($q) {
            $q->whereNull('assessment_status')
            ->orWhere('assessment_status', Application::ASSESSMENT_STATUS_PENDING);
        })
        ->orderBy('title_of_assessment_applied_for');

        if ($ncProgram) {
            $eligibleQuery->where('title_of_assessment_applied_for', $ncProgram);
        }

        $eligibleApplicants = $eligibleQuery->get();

        // Group for display
        $eligibleGrouped = $eligibleApplicants->groupBy('title_of_assessment_applied_for');

        // Programs for dropdown
        $availablePrograms = Application::where('status', Application::STATUS_APPROVED)
            ->where(function ($q) {
            // BOOKKEEPING: must complete training
            $q->where(function ($bookkeeping) {
                $bookkeeping->where('title_of_assessment_applied_for', 'BOOKKEEPING NC III')
                        ->where('training_status', Application::TRAINING_STATUS_COMPLETED);
            })
            // Other NCs: just need approval
            ->orWhere('title_of_assessment_applied_for', '!=', 'BOOKKEEPING NC III');
        })
        ->distinct()
        ->pluck('title_of_assessment_applied_for')
        ->sort()
        ->values();

        // Stats for cards (same shape as your enrollmentList stats)
        $eligibleStats = [
            'total_eligible' => $eligibleApplicants->count(),
            'programs_count' => $eligibleGrouped->count(),
            'program_stats' => $eligibleGrouped->map(function ($apps, $program) {
                return [
                    'program' => $program,
                    'count' => $apps->count(),
                    'applicants' => $apps,  // available if you want to show popovers/details
                ];
            })->values(),
        ];

        return view('admin.assessment.batches', compact('batches', 'status', 'ncProgram', 'availablePrograms','eligibleApplicants', 'eligibleGrouped', 'eligibleStats'));
        }

    /**
     * Show the form for creating a new resource.
     */
    // Show form to create assessment batch
    public function create()
    {
        $ncPrograms = Application::where('status', Application::STATUS_APPROVED)
        ->where(function ($q) {
            // BOOKKEEPING: must complete training
            $q->where(function ($bookkeeping) {
                $bookkeeping->where('title_of_assessment_applied_for', 'BOOKKEEPING NC III')
                        ->where('training_status', Application::TRAINING_STATUS_COMPLETED);
            })
            // Other NCs: just need approval
            ->orWhere('title_of_assessment_applied_for', '!=', 'BOOKKEEPING NC III');
        })
        ->distinct()
        ->pluck('title_of_assessment_applied_for')
        ->sort();

        $eligibleCounts = [];
        foreach ($ncPrograms as $program) {
            $eligibleCounts[$program] = Application::where('title_of_assessment_applied_for', $program)
                ->where('training_status', Application::TRAINING_STATUS_COMPLETED)
                ->where(function($q) {
                    $q->whereNull('assessment_status')
                    ->orWhere('assessment_status', Application::ASSESSMENT_STATUS_PENDING);
                })
                ->count();
        }

        return view('admin.assessment.create', compact('ncPrograms', 'eligibleCounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // Store new assessment batch
    public function store(Request $request)
    {
        $validated = $request->validate([
        'nc_program' => 'required|string|max:255',
        'batch_name' => 'nullable|string|max:255|unique:assessment_batches,batch_name',
        'venue' => 'required|string|max:255',
        'assessor_name' => 'nullable|string|max:255',   
        'intensive_review_day1' => 'required|date|after_or_equal:today',
        'intensive_review_day1_start' => 'required',
        'intensive_review_day1_end' => 'required|after:intensive_review_day1_start',
        'intensive_review_day2' => 'required|date|after:intensive_review_day1',
        'intensive_review_day2_start' => 'required',
        'intensive_review_day2_end' => 'required|after:intensive_review_day2_start',
        'assessment_date' => 'required|date|after:intensive_review_day2',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        ]);
        // Check if there are exactly 10 eligible applicants
        $eligibleCount = Application::where('title_of_assessment_applied_for', $validated['nc_program'])
            ->where('training_status', Application::TRAINING_STATUS_COMPLETED)
            ->where(function($q) {
                $q->whereNull('assessment_status')
                ->orWhere('assessment_status', Application::ASSESSMENT_STATUS_PENDING);
            })
            ->count();

        if ($eligibleCount < 10) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nc_program' => "Cannot create batch. Only {$eligibleCount} eligible applicants available. Need exactly 10 applicants."]);
        }
        $validated['max_applicants'] = 10;

        // Generate batch name if not provided
        if (empty($validated['batch_name'])) {
            $validated['batch_name'] = $this->generateBatchName(
                $validated['nc_program'],
                $validated['assessment_date']);
        }

        $batch = AssessmentBatch::create($validated);

        // Auto-assign eligible applicants
        $this->assignApplicantsToBatch($batch);

        return redirect()->route('admin.assessment-batches.index')->with('success', 'Assessment batch created and applicants assigned successfully.');
    }

    /**
     * Display the specified resource.
     */
    // Show batch details
    // Show batch details
    public function show(AssessmentBatch $assessment_batch)
    {
    
        
        // For completed batches (history), load applicants through assessment_results
        if ($assessment_batch->status === AssessmentBatch::STATUS_COMPLETED) {
            // Get application IDs from assessment results (preserves history)
            $applicationIds = AssessmentResult::where('assessment_batch_id', $assessment_batch->id)
                ->pluck('application_id');
            
            // Load those applications with their results for THIS batch
            $assignedApplications = Application::whereIn('id', $applicationIds)
                ->with(['user'])
                ->get();
            
            // Attach the assessment result for THIS batch to each application
            foreach ($assignedApplications as $app) {
                $app->setRelation('assessmentResult', 
                    $app->assessmentResults()
                        ->where('assessment_batch_id', $assessment_batch->id)
                        ->with('cocResults')
                        ->first()
                );
            }
            
            // Replace the applications collection
            $assessment_batch->setRelation('applications', $assignedApplications);
        } else {
            // For active batches, use normal relationship
            $assessment_batch->load(['applications.user', 'applications.assessmentResults']);
            
            // Attach the assessment result for THIS batch to each application
            foreach ($assessment_batch->applications as $app) {
                $app->setRelation('assessmentResult', 
                    $app->assessmentResults()
                        ->where('assessment_batch_id', $assessment_batch->id)
                        ->with('cocResults')
                        ->first()
                );
            }
        }

        // Eligible applicants (includes both first-time and reassessment)
        $eligibleQuery = Application::where('title_of_assessment_applied_for', $assessment_batch->nc_program)
            ->where('status', Application::STATUS_APPROVED)
            ->where(function ($q) use ($assessment_batch) {
                // For BOOKKEEPING: must complete training (first-time only)
                if ($assessment_batch->nc_program === 'BOOKKEEPING NC III') {
                    $q->where(function($subQ) {
                        // First-time: completed training
                        $subQ->where('training_status', Application::TRAINING_STATUS_COMPLETED)
                            ->whereNull('reassessment_payment_status');
                    })->orWhere(function($subQ) {
                        // Reassessment: verified payment (no training requirement)
                        $subQ->where('reassessment_payment_status', 'verified');
                    });
                } else {
                    // For Other NCs: no training check needed
                    // Just approved status is enough
                }
            })
            ->where(function($q) {
                $q->whereNull('assessment_status')
                ->orWhere('assessment_status', Application::ASSESSMENT_STATUS_PENDING);
            })
            // CRITICAL: Exclude applicants already in OTHER batches
            ->where(function($q) use ($assessment_batch) {
                $q->whereNull('assessment_batch_id')
                ->orWhere('assessment_batch_id', $assessment_batch->id);
            })
            ->with(['user', 'assessmentResults.cocResults']);

        // Order by: reassessment applicants first, then by date
        $eligibleQuery->orderByRaw('CASE WHEN reassessment_payment_status = "verified" THEN 0 ELSE 1 END')
            ->orderBy('reassessment_payment_date', 'asc')
            ->orderBy('training_completed_at', 'asc')
            ->orderBy('reviewed_at', 'asc');

        $eligibleApplicants = $eligibleQuery->get();

        return view('admin.assessment.show', compact('assessment_batch', 'eligibleApplicants'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //Update assessment batch
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
        'batch_name' => 'required|string|max:255|unique:assessment_batches,batch_name,' . $batch->id,
        'assessment_date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'venue' => 'required|string|max:255',
        'assessor_name' => 'nullable|string|max:255',
        'max_applicants' => 'required|integer|min:1|max:10',
        'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        'remarks' => 'nullable|string',
    ]);

        $batch->update($validated);

        return redirect()->back()->with('success', 'Assessment batch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    // Delete batch (only if no applicants assigned)
    public function destroy(AssessmentBatch $assessment_batch)
    {
        $batch = $assessment_batch;

        if ($batch->applications()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete batch with assigned applicants.');
        }

        $batch->delete();

        return redirect()->route('admin.assessment-batches.index')
            ->with('success', 'Assessment batch deleted successfully.');
    }

    // Add applicants to batch
    public function addApplicants(Request $request, AssessmentBatch $assessment_batch)
    {
        // Validate that batch is not completed
        if ($assessment_batch->status === AssessmentBatch::STATUS_COMPLETED) {
            return back()->with('error', 'Cannot add applicants to a completed batch.');
        }

        // Validate that batch is not full
        $currentCount = $assessment_batch->applications()->count();
        if ($currentCount >= $assessment_batch->max_applicants) {
            return back()->with('error', 'This batch is already at full capacity.');
        }
        $request->validate([
            'application_id' => 'required|exists:applications,id'
        ]);

        $application = Application::findOrFail($request->application_id);

        // Check if applicant is already assigned to another batch
        if ($application->assessment_batch_id) {
            return back()->with('error', 'This applicant is already assigned to another batch.');
        }

         // Check if applicant is eligible
        if ($application->status !== Application::STATUS_APPROVED) {
            return back()->with('error', 'Only approved applicants can be assigned.');
        }
        $isReassessment = $application->reassessment_payment_status === 'verified';
         // For BOOKKEEPING, check if training is completed
         if (!$isReassessment && 
            $assessment_batch->nc_program === 'BOOKKEEPING NC III' && 
            $application->training_status !== Application::TRAINING_STATUS_COMPLETED) {
            return back()->with('error', 'Applicant must complete training before assessment.');
        }
        // Add applicant to batch
        $application->update([
            'assessment_batch_id' => $assessment_batch->id,
            'assessment_status' => Application::ASSESSMENT_STATUS_ASSIGNED,
        ]);

        try {
            $application->user->notify(new AssessmentScheduleNotification($assessment_batch, $application));
            $message = 'Applicant added to batch successfully and schedule notification sent.';
            
        } catch (\Exception $e) {
            \Log::error('Failed to send schedule notification to applicant ID: ' . $application->id . '. Error: ' . $e->getMessage());
            $message = 'Applicant added to batch successfully, but failed to send notification email.';
        }

        return back()->with('success', $message);
        }
    // Remove an applicant from a batch
    public function unassignApplicant(AssessmentBatch $assessment_batch, Application $application)
    {
        // Validate that batch is not completed
        if ($assessment_batch->status === AssessmentBatch::STATUS_COMPLETED) {
            return back()->with('error', 'Cannot remove applicants from a completed batch.');
        }

        // Check if application belongs to this batch
        if ($application->assessment_batch_id !== $assessment_batch->id) {
            return back()->with('error', 'This applicant is not in this batch.');
        }

        // Check if applicant has assessment result
        if ($application->assessmentResult) {
            return back()->with('error', 'Cannot remove applicant with assessment result.');
        }

        // Remove from batch
        $application->update([
            'assessment_batch_id' => null,
            'assessment_status' => Application::ASSESSMENT_STATUS_PENDING,
        ]);

        return back()->with('success', 'Applicant removed from batch successfully.');
    }

    // Save assessment result for a single applicant
    public function markAssessmentCompleted(Request $request,AssessmentBatch $assessment_batch,Application $application) 
    {
       // Must belong to this batch
        if ($application->assessment_batch_id !== $assessment_batch->id) {
            abort(404);
        }

        if (in_array($assessment_batch->status, [AssessmentBatch::STATUS_COMPLETED, AssessmentBatch::STATUS_CANCELLED], true)) {
            return back()->with('error', 'Batch already finalized.');
        }

        $data = $request->validate([
            'result' => ['required', Rule::in([
                AssessmentResult::RESULT_PASS,
                AssessmentResult::RESULT_FAIL,
                AssessmentResult::RESULT_INCOMPLETE,
            ])],
            'score' => ['nullable', 'numeric', 'between:0,100'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'coc_results' => ['nullable', 'array'],
            'coc_results.*.code' => ['required_with:coc_results', 'string'],
            'coc_results.*.title' => ['required_with:coc_results', 'string'],
            'coc_results.*.result' => ['required_with:coc_results', 'in:competent,not_yet_competent'],
        ]);

        // Upsert the assessment result
        $result = AssessmentResult::updateOrCreate(
            [
                'application_id' => $application->id,
                'assessment_batch_id' => $assessment_batch->id,
            ],
            [
                'result' => $data['result'],
                'score' => $data['score'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'assessed_by' => $request->user()->id,
                'assessed_at' => now(),
            ]
        );
        if ($result->wasRecentlyCreated) {
            $application->increment('reassessment_attempt');
        }

        if ($result->wasRecentlyCreated) {
            // First time creating this result
            $application->update([
                'reassessment_attempt' => $application->reassessment_attempt + 1,
            ]);
        }

        // Save COC results if provided (for fail/NYC results)
        if (isset($data['coc_results']) && is_array($data['coc_results'])) {
            // Delete existing COC results for this assessment
            $result->cocResults()->delete();

            // Create new COC results
            foreach ($data['coc_results'] as $cocData) {
                \App\Models\AssessmentCocResult::create([
                    'assessment_result_id' => $result->id,
                    'application_id' => $application->id,
                    'coc_code' => $cocData['code'],
                    'coc_title' => $cocData['title'],
                    'result' => $cocData['result'],
                ]);
            }
        }

        return back()->with('success', 'Assessment result saved successfully.');
    }
    // Close assessment batch
    public function close(Request $request, AssessmentBatch $assessment_batch)
    {
        if (in_array($assessment_batch->status, [AssessmentBatch::STATUS_COMPLETED, AssessmentBatch::STATUS_CANCELLED], true)) {
        return back()->with('error', 'Batch already finalized.');
    }

        $assessment_batch->load(['applications', 'results']);
        // Ensure every assigned application has a result
        $appIdsWithResult = $assessment_batch->results->pluck('application_id')->all();
        $missing = $assessment_batch->applications->reject(fn($app) => in_array($app->id, $appIdsWithResult, true));

        if ($missing->isNotEmpty()) {
            return back()->with('error', 'Finalize results for all assigned applicants before closing.');
        }

        DB::transaction(function () use ($assessment_batch) {
            // Update each application outcome
            $resultsByApp = $assessment_batch->results->keyBy('application_id');

            foreach ($assessment_batch->applications as $application) {
                $res = $resultsByApp[$application->id] ?? null;
                if (!$res) {
                    continue;
                }

                if ($res->result === AssessmentResult::RESULT_PASS) {
                    $application->assessment_status = Application::ASSESSMENT_STATUS_COMPLETED;
                } elseif ($res->result === AssessmentResult::RESULT_FAIL) {
                    $application->assessment_status = Application::ASSESSMENT_STATUS_FAILED;
                } else {
                    // Policy choice: keep as assigned, or mark failed.
                    // $application->assessment_status = Application::ASSESSMENT_STATUS_ASSIGNED;
                    $application->assessment_status = Application::ASSESSMENT_STATUS_ASSIGNED;
                }

                $application->assessment_date = now();
                $application->save();

                $application->user->notify(new AssessmentResultNotification($application, $res));
            }

            $assessment_batch->status = AssessmentBatch::STATUS_COMPLETED;
            $assessment_batch->save();
        });

        return redirect()->route('admin.assessment-batches.show', $assessment_batch)->with('success', 'Batch closed and outcomes recorded.');
    }
    //Auto-assign eligible applicants to batch
    private function assignApplicantsToBatch(AssessmentBatch $batch)
    {
        $eligibleApplicants = Application::where('title_of_assessment_applied_for', $batch->nc_program)
            ->where('training_status', Application::TRAINING_STATUS_COMPLETED)
            ->where(function($q) {
                $q->whereNull('assessment_status')
                ->orWhere('assessment_status', Application::ASSESSMENT_STATUS_PENDING);
            })
            ->orderBy('training_completed_at', 'asc') 
            ->limit($batch->max_applicants)
            ->get();

        $assignedCount = 0;

        // Assign only up to max_applicants
        foreach ($eligibleApplicants as $applicant) {
            if ($assignedCount >= $batch->max_applicants) {
                break; // Stop if we've reached the limit
            }
            
            $applicant->update([
                'assessment_batch_id' => $batch->id,
                'assessment_status' => Application::ASSESSMENT_STATUS_ASSIGNED,
            ]);
            
            $assignedCount++;
        }

        return $assignedCount;
    }

    //Generate batch name automatically
    private function generateBatchName($ncProgram, $assessmentDate)
    {
        $programCode = strtoupper(substr(str_replace(' ', '', $ncProgram), 0, 4));

        $date = \Carbon\Carbon::parse($assessmentDate);
        $year = $date->format('Y');
        $month = $date->format('m');

        // Get the last batch for this program in the current month
        $lastBatch = AssessmentBatch::where('nc_program', $ncProgram)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastBatch && preg_match('/BATCH-(\d+)$/', $lastBatch->batch_name, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return "{$programCode}-{$year}{$month}-BATCH-{$sequence}";
    }
        // History of assessment batches
    public function history(Request $request)
    {
        $ncProgram = $request->query('nc_program');

        $batches = AssessmentBatch::query()
            ->completed()
            ->when($ncProgram, fn($q) => $q->where('nc_program', $ncProgram))
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);

        $batchIds = $batches->pluck('id');

        $results = AssessmentResult::selectRaw(
                'assessment_batch_id,
                COUNT(*) as total_applicants,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN result = ? THEN 1 ELSE 0 END) as failed',
                [AssessmentResult::RESULT_PASS, AssessmentResult::RESULT_FAIL]
            )
            ->whereIn('assessment_batch_id', $batchIds)
            ->groupBy('assessment_batch_id')
            ->get()
            ->keyBy('assessment_batch_id');

        $availablePrograms = Application::where('training_status', Application::TRAINING_STATUS_COMPLETED)
            ->distinct()
            ->pluck('title_of_assessment_applied_for')
            ->sort();

        return view('admin.history.assessment', compact(
            'batches', 'results', 'availablePrograms', 'ncProgram'
        ));
    }

    public function sendScheduleNotifications(AssessmentBatch $assessment_batch)
    {
        // Get all assigned applicants in this batch
        $applications = $assessment_batch->applications()->with('user')->get();
        
        if ($applications->isEmpty()) {
            return back()->with('error', 'No applicants assigned to this batch.');
        }
        
        $sentCount = 0;
        
        foreach ($applications as $application) {
            try {
                // Send notification to each applicant
                $application->user->notify(new AssessmentScheduleNotification($assessment_batch, $application));
                $sentCount++;
            } catch (\Exception $e) {
                // Log error but continue with other notifications
                \Log::error('Failed to send schedule notification to applicant ID: ' . $application->id . '. Error: ' . $e->getMessage());
            }
        }
        // Mark notifications as sent
        $assessment_batch->update([
            'schedule_notifications_sent_at' => now()
        ]);
        
        return back()->with('success', "Schedule notifications sent to {$sentCount} applicant(s).");
    }
}
