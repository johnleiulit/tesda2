<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingBatch;
use App\Models\TrainingSchedule;
use App\Models\TrainingResult;
use App\Models\AssessmentBatch;
use App\Models\AssessmentResult;
use App\Notifications\ApplicationApprovedNotification;
use App\Models\EnrollmentArchive;
use Illuminate\Validation\Rule;
use App\Models\EmploymentRecord;
use App\Models\AssessmentCocResult;
use App\Notifications\TrainingScheduleNotification;

class AdminController extends Controller
{
    // Admin Dashboard
   // Replace the existing dashboard() method in app/Http/Controllers/AdminController.php

    public function dashboard()
    {
        $data = [
            'overview' => $this->getOverviewData(),
            'applicant' => $this->getApplicantAnalytics(),
            'training' => $this->getTrainingAnalytics(),
            'assessment' => $this->getAssessmentAnalytics(),
            'employment' => $this->getEmploymentAnalytics(),
        ];
        
        return view('admin.dashboard', $data);
    }

    private function getOverviewData()
    {
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', 'pending')->count();
        $activeTrainingBatches = TrainingBatch::whereIn('status', ['active', 'ongoing', 'scheduled'])->count();
        
        // Calculate overall competency rate
        $totalAssessments = AssessmentResult::count();
        $competentCount = AssessmentResult::where('result', 'Competent')->count();
        $competencyRate = $totalAssessments > 0 ? round(($competentCount / $totalAssessments) * 100, 1) : 0;
        
        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'active_training_batches' => $activeTrainingBatches,
            'competency_rate' => $competencyRate,
        ];
    }

    private function getApplicantAnalytics()
    {
        // Define the 5 specific programs
        $programs = [
            'EVENTS MANAGEMENT SERVICES NC III',
            'TOURISM PROMOTION SERVICES NC II',
            'BOOKKEEPING NC III',
            'PHARMACY SERVICES NC III',
            'VISUAL GRAPHIC DESIGN NC III'
        ];
        
        // Get application counts for each program
        $programData = [];
        $totalApplications = 0;
        
        foreach ($programs as $program) {
            $count = Application::where('title_of_assessment_applied_for', $program)->count();
            $programData[] = [
                'name' => $program,
                'count' => $count
            ];
            $totalApplications += $count;
        }
        
        // Sort by count (highest to lowest)
        usort($programData, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Calculate percentages for bar width
        foreach ($programData as &$program) {
            $program['percentage'] = $totalApplications > 0 
                ? round(($program['count'] / $totalApplications) * 100, 1) 
                : 0;
        }
        
        // Get counts by application type
        $assessmentCount = Application::where('application_type', 'Assessment Only')->count();
        $twspCount = Application::where('application_type', 'TWSP')->count();
        
        return [
            'programs' => $programData,
            'total_applications' => $totalApplications,
            'assessment_count' => $assessmentCount,
            'twsp_count' => $twspCount,
        ];
    }

    private function getTrainingAnalytics()
    {
        $completedCount = Application::where('training_status', 'completed')->count();
        $failedCount = Application::where('training_status', 'failed')->count();
        
        // Batch performance
        $batches = TrainingBatch::with(['applications'])
            ->whereIn('status', ['active', 'ongoing', 'scheduled', 'completed'])
            ->get()
            ->map(function ($batch) {
                $completed = $batch->applications->where('training_status', 'completed')->count();
                $failed = $batch->applications->where('training_status', 'failed')->count();
                $total = $completed + $failed;
                
                return [
                    'batch_number' => $batch->batch_number,
                    'completed' => $completed,
                    'failed' => $failed,
                    'success_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                ];
            });
        
        // Calculate percentages for pie chart
        $totalAssessed = $completedCount + $failedCount;
        $completedPercentage = $totalAssessed > 0 ? round(($completedCount / $totalAssessed) * 100, 1) : 0;
        $failedPercentage = $totalAssessed > 0 ? round(($failedCount / $totalAssessed) * 100, 1) : 0;
        
        return [
            'completed_count' => $completedCount,
            'failed_count' => $failedCount,
            'batches' => $batches,
            
            // Pie chart data
            'total_assessed' => $totalAssessed,
            'completed_percentage' => $completedPercentage,
            'failed_percentage' => $failedPercentage,
        ];
    }


    private function getAssessmentAnalytics()
    {
        $competentCount = AssessmentResult::where('result', 'Competent')->count();
        $nycCount = AssessmentResult::where('result', 'Not Yet Competent')->count();
        
        // Detailed COC performance by program
        $programs = Application::select('title_of_assessment_applied_for as name')
            ->distinct()
            ->whereHas('assessmentResults')
            ->get()
            ->map(function ($program) {
                $programName = $program->name;
                
                // Get all COC results for this program
                $cocResults = AssessmentCocResult::whereHas('application', function ($q) use ($programName) {
                    $q->where('title_of_assessment_applied_for', $programName);
                })->get();
                
                // Group COC results by COC code
                $cocBreakdown = $cocResults->groupBy('coc_code')->map(function ($cocs, $cocCode) {
                    // ✅ FIX: Count unique applicants, not records
                    $totalApplicants = $cocs->unique('application_id')->count();
                    $competentCocs = $cocs->where('result', 'competent')->count();
                    $nycCocs = $cocs->where('result', 'not_yet_competent')->count();
                    
                    return [
                        'coc_code' => $cocCode,
                        'coc_title' => $cocs->first()->coc_title ?? 'Unknown',
                        'total_assessments' => $totalApplicants,  // ✅ Now shows correct count
                        'competent_count' => $competentCocs,
                        'nyc_count' => $nycCocs,
                        'competent_rate' => $totalApplicants > 0 ? round(($competentCocs / $totalApplicants) * 100, 1) : 0,
                        'nyc_rate' => $totalApplicants > 0 ? round(($nycCocs / $totalApplicants) * 100, 1) : 0,
                    ];
                })->values();
                
                // Overall program statistics
                $totalProgramResults = AssessmentResult::whereHas('application', function ($q) use ($programName) {
                    $q->where('title_of_assessment_applied_for', $programName);
                })->count();
                
                $competentProgramResults = AssessmentResult::where('result', 'Competent')
                    ->whereHas('application', function ($q) use ($programName) {
                        $q->where('title_of_assessment_applied_for', $programName);
                    })->count();
                
                return [
                    'name' => $this->getProgramShortName($programName),
                    'full_name' => $programName,
                    'total_assessments' => $totalProgramResults,
                    'overall_competent_rate' => $totalProgramResults > 0 ? round(($competentProgramResults / $totalProgramResults) * 100, 1) : 0,
                    'overall_nyc_rate' => $totalProgramResults > 0 ? round((($totalProgramResults - $competentProgramResults) / $totalProgramResults) * 100, 1) : 0,
                    'coc_breakdown' => $cocBreakdown,
                ];
            })->filter(function ($program) {
                return $program['total_assessments'] > 0;
            });
        
        // Reassessment analysis
        $firstReassessment = Application::where('reassessment_payment_status', 'verified')->count();
        $secondReassessment = Application::where('second_reassessment_payment_status', 'verified')->count();
        $totalReassessments = $firstReassessment + $secondReassessment;
        
        // Calculate success rate after reassessment
        $successAfterReassessment = AssessmentResult::where('result', 'Competent')
            ->whereHas('application', function ($q) {
                $q->where('reassessment_payment_status', 'verified');
            })->count();
        
        $reassessmentSuccessRate = $totalReassessments > 0 ? round(($successAfterReassessment / $totalReassessments) * 100, 1) : 0;
        
        return [
            'competent_count' => $competentCount,
            'nyc_count' => $nycCount,
            'programs' => $programs,
            'reassessment' => [
                'first' => $firstReassessment,
                'second' => $secondReassessment,
                'success_rate' => $reassessmentSuccessRate,
            ],
        ];
    }

    private function getEmploymentAnalytics()
    {
        $totalGraduates = Application::whereHas('assessmentResults', function ($q) {
            $q->where('result', 'Competent');
        })->count();
        
        $employedCount = \App\Models\EmploymentRecord::count();
        $employmentRate = $totalGraduates > 0 ? round(($employedCount / $totalGraduates) * 100, 1) : 0;
        
        // Average income
        $avgIncome = \App\Models\EmploymentRecord::avg('monthly_income') ?? 0;
        
        // Employment by sector
        $sectors = \App\Models\EmploymentRecord::select('employer_classification as name', DB::raw('count(*) as count'))
            ->groupBy('employer_classification')
            ->get()
            ->map(function ($sector) use ($employedCount) {
                return [
                    'name' => $sector->name,
                    'count' => $sector->count,
                    'percentage' => $employedCount > 0 ? round(($sector->count / $employedCount) * 100, 1) : 0,
                ];
            });
        
        return [
            'employment_rate' => $employmentRate,
            'avg_income' => $avgIncome,
            'sectors' => $sectors,
        ];
    }

    private function getCOCCountForProgram($programName)
    {
        // Define COC counts for each program
        $cocCounts = [
            'Bookkeeping NC III' => 3,
            'Visual Graphic Design NC III' => 4,
            'Tourism Promotion Services NC II' => 3,
            'Events Management Services NC III' => 4,
            'Pharmacy Services NC II' => 3,
        ];
        
        // Find matching program (case-insensitive)
        foreach ($cocCounts as $program => $count) {
            if (stripos($programName, $program) !== false || stripos($program, $programName) !== false) {
                return $count;
            }
        }
        
        // Default COC count
        return 3;
    }


    private function getProgramShortName($program)
    {
        // Normalize the program name for comparison (case-insensitive)
        $programLower = strtolower(trim($program));
        
        // Check for bookkeeping variations (with or without NC levels)
        if (str_contains($programLower, 'bookkeeping') || str_contains($programLower, 'book keeping')) {
            return 'BKP';
        }
        
        // Check for other common programs
        if (str_contains($programLower, 'visual graphic design')) {
            return 'VGD';
        }
        
        if (str_contains($programLower, 'tourism promotion')) {
            return 'TPS';
        }
        
        if (str_contains($programLower, 'events management')) {
            return 'EMS';
        }
        
        if (str_contains($programLower, 'pharmacy')) {
            return 'PMS';
        }
        
        // Fallback: take first 3 letters
        return strtoupper(substr($program, 0, 3));
    }
    private function extractBatchNumber($batchName)
    {
        // Handle formats like "BOOK-202603-BATCH-2" or "Batch 2"
        if (preg_match('/BATCH-(\d+)$/i', $batchName, $matches)) {
            return 'B' . $matches[1];
        }
        
        if (preg_match('/Batch\s+(\d+)/i', $batchName, $matches)) {
            return 'B' . $matches[1];
        }
        
        // Fallback: return the original batch name
        return $batchName;
    }


    public function indexApplicants()
    {
        return redirect()->route('admin.applications.index');
    }

    public function traineesList(Request $request)
    {
        $query = TrainingBatch::with([
            'applications' => function($q) {
                $q->where('application_type', 'TWSP');
        },
            'applications.user',
            'trainingSchedule'
        ])
        ->withCount([
            'applications as enrolled_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_ENROLLED)
                  ->where('application_type', 'TWSP');
            },
            'applications as completed_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_COMPLETED)
                  ->where('application_type', 'TWSP');
            },
            'applications as failed_count' => function ($q) {
                $q->where('training_status', Application::TRAINING_STATUS_FAILED)
                  ->where('application_type', 'TWSP');
            },
        ])
        ->where('status', '!=', TrainingBatch::STATUS_COMPLETED)
        ->orderBy('nc_program')
        ->orderBy('batch_number');

        $batches = $query->get();

        $backoutApplicants = Application::where('status', Application::STATUS_APPROVED)
            ->whereNull('training_batch_id')
            ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
            ->where('application_type', 'TWSP') 
            ->with('user')
            ->orderBy('updated_at', 'desc') // Most recent first
            ->get();
        $stats = [
            'total_enrolled' => Application::where('status', Application::STATUS_APPROVED)
                ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
                ->where('application_type', 'TWSP') 
                ->count(),
            'total_batches' => $batches->count(),
            'full_batches' => $batches->where('is_full', true)->count(),
            'backout_count' => $backoutApplicants->count(),
        ];

        return view('admin.trainees.index', compact('batches', 'stats', 'backoutApplicants'));
    }
// Add this method after traineesList() method
public function showBatch(TrainingBatch $batch)
{
    $batch->load([
        'applications' => function($q) {
            $q->where('application_type', 'TWSP');
        },
        'applications.user',
        'applications.trainingResult',
        'trainingSchedule'
    ]);

    $completedCount = $batch->applications->filter(function($application) {
        return $application->training_status === Application::TRAINING_STATUS_COMPLETED;
    })->count();

    $failedCount = $batch->applications->filter(function($application) {
        return $application->training_status === Application::TRAINING_STATUS_FAILED;
    })->count();
    // Get available applicants (approved but not enrolled in any batch)
    $availableApplicants = Application::where('status', Application::STATUS_APPROVED)
        ->where('application_type', 'TWSP')
        ->whereNull('training_batch_id')
        ->where('title_of_assessment_applied_for', $batch->nc_program)
        ->with('user')
        ->orderBy('surname')
        ->orderBy('firstname')
        ->get();

    return view('admin.trainees.show', compact('batch', 'completedCount', 'failedCount', 'availableApplicants'));
}
// Add this method after showBatch() method
public function completeBatch(TrainingBatch $batch)
{
    // Check if all trainees have results (completed or failed)
    $totalTrainees = $batch->applications()->count();
    $completedOrFailed = $batch->applications()
        ->whereIn('training_status', [
            Application::TRAINING_STATUS_COMPLETED,
            Application::TRAINING_STATUS_FAILED
        ])
        ->count();

    if ($totalTrainees !== $completedOrFailed) {
        return redirect()->back()->with('error', 'All trainees must have a result (Completed or Failed) before marking batch as done.');
    }

    // Update batch status to completed
    $batch->update(['status' => TrainingBatch::STATUS_COMPLETED]);

    return redirect()->route('admin.trainees.index')->with('success', 'Batch ' . $batch->batch_number . ' has been marked as completed and moved to history.');
}
// Add applicant to training batch
public function addApplicantToBatch(Request $request, TrainingBatch $batch)
{
    // Validate that batch is not completed
    if ($batch->status === TrainingBatch::STATUS_COMPLETED) {
        return back()->with('error', 'Cannot add applicants to a completed batch.');
    }

    // Validate that batch is not full
    if ($batch->is_full) {
        return back()->with('error', 'This batch is already full.');
    }

    $request->validate([
        'application_id' => 'required|exists:applications,id'
    ]);

    $application = Application::findOrFail($request->application_id);

    // Check if applicant is already enrolled in another batch
    if ($application->training_batch_id) {
        return back()->with('error', 'This applicant is already enrolled in another batch.');
    }

    // Check if applicant is approved
    if ($application->status !== Application::STATUS_APPROVED) {
        return back()->with('error', 'Only approved applicants can be enrolled.');
    }

    // Add applicant to batch
    $application->update([
        'training_batch_id' => $batch->id,
        'training_status' => Application::TRAINING_STATUS_ENROLLED,
        'training_schedule_id' => $batch->trainingSchedule ? $batch->trainingSchedule->id : null,
    ]);

    // If batch has schedule, set status to ongoing
    if ($batch->trainingSchedule) {
        $application->update([
            'training_status' => Application::TRAINING_STATUS_ONGOING
        ]);
    }

    // Create training result record
    TrainingResult::create([
        'application_id' => $application->id,
        'training_batch_id' => $batch->id,
        'training_schedule_id' => $batch->trainingSchedule ? $batch->trainingSchedule->id : null,
        'result' => TrainingResult::RESULT_ONGOING, 
    ]);


    return back()->with('success', 'Applicant added to batch successfully.');
}

// Remove applicant from training batch
public function removeApplicantFromBatch(TrainingBatch $batch, Application $application)
{
    // Validate that batch is not completed
    if ($batch->status === TrainingBatch::STATUS_COMPLETED) {
        return back()->with('error', 'Cannot remove applicants from a completed batch.');
    }

    // Check if application belongs to this batch
    if ($application->training_batch_id !== $batch->id) {
        return back()->with('error', 'This applicant is not in this batch.');
    }

    // Remove from batch
    $application->update([
        'training_batch_id' => null,
        'training_schedule_id' => null,
        'training_completed_at' => null,
        'training_remarks' => null,
    ]);
    $application->training_status = 'enrolled';
    // Delete training result record
    $application->trainingResult()->delete();

    // Update batch status if it was full
    if ($batch->is_full) {
        $batch->update(['status' => TrainingBatch::STATUS_ENROLLING]);
    }

    return back()->with('success', 'Applicant removed from batch successfully.');
}



    public function manageSchedules(Request $request)
    {
    $ncProgram = $request->query('nc_program');
    
    $schedules = TrainingSchedule::query()
        ->when($ncProgram, fn($q) => $q->where('nc_program', $ncProgram))
        ->orderBy('nc_program')
        ->orderBy('start_date')
        ->get();

    $availablePrograms = TrainingSchedule::distinct()->pluck('nc_program')->sort();
    
    return view('admin.schedules.index', compact('schedules', 'availablePrograms', 'ncProgram'));
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'training_batch_id' => 'required|exists:training_batches,id',
            'nc_program' => 'required|string|max:255',
            'schedule_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'days' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1|max:100',
            'venue' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = TrainingSchedule::STATUS_ACTIVE;

        // Create the schedule
        $schedule = TrainingSchedule::create($validated);

        // Validate end_time is after start_time manually
        if (strtotime($validated['end_time']) <= strtotime($validated['start_time'])) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        // Update training batch status to 'scheduled'
        $batch = TrainingBatch::find($validated['training_batch_id']);
        $batch->update(['status' => TrainingBatch::STATUS_SCHEDULED]);

        // Update all applications in this batch to link with the schedule AND set status to 'ongoing'
        Application::where('training_batch_id', $batch->id)
            ->update([
                'training_schedule_id' => $schedule->id,
                'training_status' => Application::TRAINING_STATUS_ONGOING
            ]);

        return redirect()->route('admin.trainees.index')->with('success', 'Training schedule created successfully for Batch ' . $batch->batch_number);
    }

    public function updateSchedule(Request $request, TrainingSchedule $schedule)
    {
    $validated = $request->validate([
        'schedule_name' => 'required|string|max:255',
        'schedule_type' => 'required|in:regular,weekend',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'days' => 'required|string|max:255',
        'max_students' => 'required|integer|min:1|max:100',
        'venue' => 'required|string|max:255',
        'instructor' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,completed,cancelled',
    ]);

    $schedule->update($validated);

    return redirect()->back()->with('success', 'Training schedule updated successfully.');
    }

    public function deleteSchedule(TrainingSchedule $schedule)
    {
    $schedule->delete();
    
    return redirect()->back()->with('success', 'Training schedule deleted successfully.');
    }  

    public function editSchedule(TrainingSchedule $schedule)
    {   
    return response()->json($schedule);
    }

    // Mark training as completed
    public function markTrainingCompleted(Application $application, Request $request)
    {
    $request->validate([
        'training_remarks' => 'nullable|string|max:500',
    ]);

    $application->update([
        'training_status' => Application::TRAINING_STATUS_COMPLETED,
        'training_completed_at' => now(),
        'training_remarks' => $request->input('training_remarks'),
    ]);
    // Update training result
    $application->trainingResult()->update([
        'result' => TrainingResult::RESULT_COMPLETED,
        'completed_at' => now(),
        'remarks' => $request->input('training_remarks'),
        'evaluated_by' => auth()->id(),
    ]);

    return redirect()->back()->with('success', 'Training marked as completed successfully.');
    }

    // Mark training as failed
    public function markTrainingFailed(Application $application, Request $request)
    {
        $request->validate([
            'training_remarks' => 'required|string|max:500',
        ]);

        $application->update([
            'training_status' => Application::TRAINING_STATUS_FAILED,
            'training_remarks' => $request->input('training_remarks'),
        ]);
        // Update training result
        $application->trainingResult()->update([
            'result' => TrainingResult::RESULT_FAILED,
            'completed_at' => now(),
            'remarks' => $request->input('training_remarks'),
            'evaluated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Training marked as failed.');
    }

    // View training progress
    public function trainingProgress(Request $request)
    {
    $status = $request->query('status', 'enrolled');
    
    $applications = Application::where('status', Application::STATUS_APPROVED)
        ->where('training_status', $status)
        ->with(['user:id,name,email', 'trainingSchedule'])
        ->orderBy('training_completed_at', 'desc')
        ->paginate(15);

    return view('admin.training.progress', compact('applications', 'status'));
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

    public function indexApplicationHistory()
    {
        return view('admin.history.index');
    }

    public function archiveEnrollmentSection(Request $request)
    {
    $data = $request->validate([
        'program' => ['required','string','max:255'],
    ]);

    $hasEnrolled = Application::where('status', Application::STATUS_APPROVED)
        ->where('title_of_assessment_applied_for', $data['program'])
        ->where('training_status', Application::TRAINING_STATUS_ENROLLED)
        ->exists();

    if ($hasEnrolled) {
        return back()->with('error', 'There are still enrolled trainees in this section. Complete/Fail all first.');
    }

    EnrollmentArchive::updateOrCreate(
        ['program' => $data['program'], 'schedule_type' => $data['schedule_type']],
        ['archived_by' => $request->user()->id, 'archived_at' => now()]
    );

    return back()->with('success', 'Section archived. Trainees are now available in Training History.');
    }

    public function listApplicationsHistory(Request $request)
    {
        $search = $request->query('q');
        $status = $request->query('status');
        $apps = Application::query()
            ->with('user:id,name') // optional
            ->where('status', '<>', Application::STATUS_PENDING)
            ->when($status, fn($q) => $q->where('status', $status))
             ->when($search, function ($q) use ($search) {
            $q->where(function ($w) use ($search) {
            $w->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
              ->orWhere('title_of_assessment_applied_for', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

            if ($request->ajax()) {
        return view('admin.history.index', compact('apps'))->render();
        }

        return view('admin.history.index', compact('apps','status'));
    }
    public function trainingHistory(Request $request)
    {
        $ncProgram = $request->query('nc_program');
        $status = $request->query('status'); // 'completed' | 'failed' | null

        $query = TrainingBatch::where('status', TrainingBatch::STATUS_COMPLETED)
            ->with([
                'applications.user',
                'trainingSchedule'
            ])
            ->withCount([
                'applications as completed_count' => function ($q) {
                    $q->where('training_status', Application::TRAINING_STATUS_COMPLETED);
                },
                'applications as failed_count' => function ($q) {
                    $q->where('training_status', Application::TRAINING_STATUS_FAILED);
                },
            ])
            ->orderBy('nc_program')
            ->orderBy('batch_number');

        if ($ncProgram) {
            $query->where('nc_program', $ncProgram);
        }

        $batches = $query->get();

        $availablePrograms = TrainingBatch::where('status', TrainingBatch::STATUS_COMPLETED)
            ->distinct()
            ->pluck('nc_program')
            ->sort()
            ->values();

        return view('admin.history.training.index', compact('batches', 'availablePrograms', 'ncProgram', 'status'));
    }
    // Add this method after trainingHistory() method
    public function trainingHistoryBatch(TrainingBatch $batch)
    {
        // Only show completed batches
        if ($batch->status !== TrainingBatch::STATUS_COMPLETED) {
            return redirect()->route('admin.history.training')
                ->with('error', 'This batch is not yet completed.');
        }

        $batch->load([
            'applications.user',
            'applications.trainingResult',
            'trainingSchedule'
        ]);

        // Calculate statistics
        $completedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_COMPLETED;
        })->count();

        $failedCount = $batch->applications->filter(function($application) {
            return $application->training_status === Application::TRAINING_STATUS_FAILED;
        })->count();

        $totalTrainees = $batch->applications->count();
        $passRate = $totalTrainees > 0 ? round(($completedCount / $totalTrainees) * 100, 2) : 0;

        return view('admin.history.training.show', compact(
            'batch', 
            'completedCount', 
            'failedCount', 
            'totalTrainees', 
            'passRate'
        ));
    }

    public function calendar()
    {
        // Fetch calendar data
        $trainingSchedules = TrainingSchedule::with('trainingBatch')
            ->whereIn('status', ['active', 'ongoing', 'scheduled'])
            ->get();
        
        $assessmentBatches = AssessmentBatch::whereIn('status', ['scheduled', 'ongoing'])
            ->get();
        
        // Format events for calendar
        $events = [];
        
        // Add training schedules
        foreach ($trainingSchedules as $schedule) {
            $programShort = $this->getProgramShortName($schedule->nc_program);
            $batchShort = 'B' . $schedule->trainingBatch->batch_number;
            
            $events[] = [
            'title' => "{$programShort} ({$batchShort}) - Start",
            'start' => $schedule->start_date->format('Y-m-d'),
            'type' => 'training',
            'color' => '#28a745',
            'extendedProps' => [
                'venue' => $schedule->venue,
                'instructor' => $schedule->instructor,
                'event_type' => 'training_start',
                'start_date' => $schedule->start_date->format('M d, Y'),
                'end_date' => $schedule->end_date->format('M d, Y'),
            ]
        ];
        
        // Training END event (only if different from start date)
        if (!$schedule->start_date->isSameDay($schedule->end_date)) {
            $events[] = [
                'title' => "{$programShort} ({$batchShort}) - End",
                'start' => $schedule->end_date->format('Y-m-d'),
                'type' => 'training',
                'color' => '#74c476', // Slightly different green shade for end
                'extendedProps' => [
                    'venue' => $schedule->venue,
                    'instructor' => $schedule->instructor,
                    'event_type' => 'training_end',
                    'start_date' => $schedule->start_date->format('M d, Y'),
                    'end_date' => $schedule->end_date->format('M d, Y'),
                ]
            ];
        }
        }
        
        // Add assessment batches
        foreach ($assessmentBatches as $batch) {
            $programShort = $this->getProgramShortName($batch->nc_program);
            $batchName = $this->extractBatchNumber($batch->batch_name);
            
            // Assessment date
            $events[] = [
                'title' => "{$programShort} ({$batchName}) - Assessment",
                'start' => $batch->assessment_date->format('Y-m-d'),
                'type' => 'assessment',
                'color' => '#0d6efd',
                'extendedProps' => [
                    'venue' => $batch->venue,
                    'assessor' => $batch->assessor_name ?? 'TBA',
                ]
            ];
            
            // Intensive Review Day 1
            if ($batch->intensive_review_day1) {
                $events[] = [
                    'title' => "{$programShort} ({$batchName}) - IR Day 1",
                    'start' => $batch->intensive_review_day1->format('Y-m-d'),
                    'type' => 'intensive_review',
                    'color' => '#ffc107',
                    'extendedProps' => [
                        'venue' => $batch->venue,
                    ]
                ];
            }
            
            // Intensive Review Day 2
            if ($batch->intensive_review_day2) {
                $events[] = [
                    'title' => "{$programShort} ({$batchName}) - IR Day 2",
                    'start' => $batch->intensive_review_day2->format('Y-m-d'),
                    'type' => 'intensive_review',
                    'color' => '#ffc107',
                    'extendedProps' => [
                        'venue' => $batch->venue,
                    ]
                ];
            }
        }
        return view('admin.calendar.index', compact('events'));
    }

    // In app/Http/Controllers/AdminController.php

    public function sendTrainingScheduleNotifications(TrainingSchedule $trainingSchedule)
    {
        // Get all assigned applicants in this training schedule
        $applications = $trainingSchedule->applications()->with('user')->get();
        
        if ($applications->isEmpty()) {
            return back()->with('error', 'No applicants assigned to this training schedule.');
        }

        // Check if notifications were already sent
        if ($trainingSchedule->schedule_notifications_sent_at) {
            return back()->with('warning', 'Schedule notifications have already been sent on ' . 
                $trainingSchedule->schedule_notifications_sent_at->format('F d, Y g:i A') . 
                '. Are you sure you want to send them again?');
        }
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($applications as $application) {
            try {
                // Send notification to each applicant
                $application->user->notify(new TrainingScheduleNotification($trainingSchedule, $application));
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                // Log error but continue with other notifications
                \Log::error('Failed to send training schedule notification to applicant ID: ' . $application->id . '. Error: ' . $e->getMessage());
            }
        }
        
        // Mark notifications as sent
        $trainingSchedule->update([
            'schedule_notifications_sent_at' => now()
        ]);
        
        $message = "Training schedule notifications sent to {$sentCount} applicant(s).";
        if ($failedCount > 0) {
            $message .= " {$failedCount} notification(s) failed to send.";
        }
        
        return back()->with('success', $message);
    }
    public function bulkCompleteTraining(Request $request)
{
    $request->validate([
        'application_ids' => 'required|array',
        'application_ids.*' => 'exists:applications,id',
        'bulk_remarks' => 'nullable|string|max:500'
    ]);

    $completedCount = 0;
    
    foreach ($request->application_ids as $applicationId) {
        $application = Application::findOrFail($applicationId);
        
        if ($application->training_status === 'ongoing') {
            $application->update([
                'training_status' => 'completed',
                'training_remarks' => $request->bulk_remarks,
                'training_completed_at' => now()
            ]);
            $completedCount++;
        }
    }
    
    return back()->with('success', "Successfully marked {$completedCount} trainee(s) as completed.");
}

    public function bulkFailTraining(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'bulk_remarks' => 'required|string|max:500'
        ]);

        $failedCount = 0;
        
        foreach ($request->application_ids as $applicationId) {
            $application = Application::findOrFail($applicationId);
            
            if ($application->training_status === 'ongoing') {
                $application->update([
                    'training_status' => 'failed',
                    'training_remarks' => $request->bulk_remarks,
                    'training_completed_at' => now()
                ]);
                $failedCount++;
            }
        }
        
        return back()->with('success', "Successfully marked {$failedCount} trainee(s) as failed.");
    }


}
