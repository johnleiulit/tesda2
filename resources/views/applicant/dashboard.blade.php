{{-- resources/views/applicant/dashboard.blade.php --}}
@extends('layouts.app')

@push('styles')
    <style>
        /* Step Indicator Styles */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            padding: 20px 0;
            overflow-x: auto;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 35px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e9ecef;
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
            min-width: 100px;
            z-index: 1;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .step-label {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            max-width: 100px;
            line-height: 1.3;
            font-weight: 500;
        }

        .step.active .step-icon {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
            animation: pulse 2s infinite;
        }

        .step.active .step-label {
            color: #0d6efd;
            font-weight: 600;
        }

        .step.completed .step-icon {
            background: #198754;
            color: white;
            border-color: #198754;
        }

        .step.completed .step-label {
            color: #198754;
            font-weight: 600;
        }

        .step.completed.failed-result .step-icon {
            background: #dc3545 !important;
            color: white !important;
            border-color: #dc3545 !important;
        }

        .step.completed.failed-result .step-label {
            color: #dc3545 !important;
            font-weight: 600;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 0 0 0 rgba(13, 110, 253, 0.7);
            }

            50% {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 0 0 10px rgba(13, 110, 253, 0);
            }
        }

        .application-card {
            transition: transform 0.2s ease;
            border-radius: 12px;
        }

        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .application-type-card {
            transition: all 0.3s ease;
        }

        .application-type-card:hover:not(.opacity-50) {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .step-indicator {
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .step {
                min-width: 80px;
            }

            .step-icon {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }

            .step-label {
                font-size: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Applicant Dashboard</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applicationTypeModal">
                <i class="bi bi-plus-circle"></i> Apply
            </button>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($twspApps->isEmpty() && $assessmentApps->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>You have no submitted applications yet. Click the "Apply" button to
                get started.
            </div>
        @else
            {{-- Correction Needed Alert --}}
            @php
                $needsCorrection = $twspApps
                    ->where('correction_requested', true)
                    ->merge($assessmentApps->where('correction_requested', true));
            @endphp

            @if ($needsCorrection->isNotEmpty())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Application Correction Required</h5>
                    <p class="mb-3">You have <strong>{{ $needsCorrection->count() }}</strong> application(s) that need
                        corrections. Please review the admin's feedback and update your application.</p>

                    @foreach ($needsCorrection as $app)
                        <div class="card mb-3 border-danger">
                            <div class="card-header bg-danger text-white">
                                <strong>{{ $app->title_of_assessment_applied_for }}</strong>
                                <small class="float-end">
                                    <i class="bi bi-clock"></i> {{ $app->correction_requested_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><strong>Admin's Message:</strong></p>
                                <div class="bg-light p-3 rounded" style="white-space: pre-line;">
                                    {{ $app->correction_message }}</div>
                                <a href="{{ route('applicant.applications.edit', $app->id) }}" class="btn btn-warning mt-2">
                                    <i class="fas fa-edit"></i> Edit & Resubmit Application
                                </a>
                            </div>
                        </div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Payment Reminder Alert --}}
            @php
                $pendingPayments = $assessmentApps->where('payment_status', 'pending');
            @endphp
            @if ($pendingPayments->isNotEmpty())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Payment Required</h5>
                    <p class="mb-0">You have {{ $pendingPayments->count() }} application(s) pending payment. Please submit
                        your payment proof via <strong>GCash</strong> or visit our office for <strong>walk-in
                            payment</strong>.</p>
                    <hr>
                    <p class="mb-0 small"><strong>GCash Number:</strong> 0912-345-6789 | <strong>Account Name:</strong>
                        TESDA Assessment Center</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Application Type Tabs -->
            <ul class="nav nav-tabs mb-3" id="applicationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="twsp-tab" data-bs-toggle="tab" data-bs-target="#twsp"
                        type="button" role="tab">
                        <i class="bi bi-mortarboard"></i> TWSP Applications
                        <span class="badge bg-primary ms-1">{{ $twspApps->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assessment-tab" data-bs-toggle="tab" data-bs-target="#assessment"
                        type="button" role="tab">
                        <i class="bi bi-clipboard-check"></i> Assessment Only
                        <span class="badge bg-success ms-1">{{ $assessmentApps->count() }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="applicationTabsContent">
                <!-- TWSP Tab -->
                <div class="tab-pane fade show active" id="twsp" role="tabpanel">
                    @forelse ($twspApps as $app)
                        <div class="card application-card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-0">
                                            <i
                                                class="bi bi-mortarboard me-2"></i>{{ $app->title_of_assessment_applied_for }}
                                        </h5>
                                        <small>{{ $app->firstname }} {{ $app->middlename }} {{ $app->surname }}
                                            {{ $app->name_extension }}</small>
                                    </div>
                                    @php
                                        $failedCount = $app
                                            ->assessmentResults()
                                            ->where('result', 'Not Yet Competent')
                                            ->count();
                                    @endphp
                                    @if ($failedCount >= 3)
                                        <div class="text-end ms-3">
                                            <span class="badge bg-danger d-block mb-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>Maximum Assessment Attempts
                                                Reached
                                            </span>
                                            <small class="d-block" style="font-size: 0.75rem; line-height: 1.2;">
                                                Please contact the admin for further assistance.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Define assessment result variables --}}
                                @php
                                    $firstAssessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->first();
                                    $reassessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->skip(1)
                                        ->first();
                                    $secondReassessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->skip(2)
                                        ->first();
                                @endphp
                                {{-- Employment Feedback Section - TWSP ONLY --}}
                                @if ($firstAssessmentResult || $reassessmentResult || $secondReassessmentResult)
                                    @php
                                        // Use existing variables from the dashboard
                                        $latestResult =
                                            $secondReassessmentResult ??
                                            ($reassessmentResult ?? $firstAssessmentResult);
                                        $isCompetent = $latestResult && $latestResult->result === 'Competent';
                                        $daysSinceAssessment = $latestResult
                                            ? $latestResult->assessed_at->diffInDays(now())
                                            : 0;
                                    @endphp

                                    {{-- Employment Feedback Section --}}
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">
                                                <i class="bi bi-briefcase"></i> Employment Feedback
                                                <span class="badge bg-light text-success ms-2">
                                                    {{ $isCompetent ? 'COMPETENT' : 'NOT YET COMPETENT' }}
                                                </span>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if ($app->employmentRecord)
                                                {{-- Show existing employment record --}}
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h6><i class="bi bi-check-circle text-success"></i> Employment
                                                            Status Updated, Thank you for your cooperation</h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>Position:</strong>
                                                                    {{ $app->employmentRecord->occupation }}
                                                                </p>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>Company:</strong>
                                                                    {{ $app->employmentRecord->employer_name }}
                                                                </p>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>Monthly Income:</strong>
                                                                    ₱{{ number_format($app->employmentRecord->monthly_income, 2) }}
                                                                </p>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>Date Employed:</strong>
                                                                    {{ $app->employmentRecord->date_employed->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <button class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#employmentModal{{ $app->id }}">
                                                            <i class="bi bi-pencil"></i> Update Details
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Show feedback request --}}
                                                <div class="text-center">
                                                    <i class="bi bi-briefcase fs-1 text-info"></i>
                                                    <h6 class="mt-2">Employment Feedback</h6>
                                                    <p class="text-muted">Please share your current employment status to
                                                        help us track TWSP program outcomes.</p>
                                                    <button class="btn btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#employmentModal{{ $app->id }}">
                                                        <i class="bi bi-plus-circle"></i> Update Employment Status
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Progress Indicator for TWSP (14 steps) --}}
                                <div class="step-indicator mb-4">
                                    {{-- Step 1: Application Submitted --}}
                                    <div class="step completed">
                                        <div class="step-icon"><i class="bi bi-check"></i></div>
                                        <div class="step-label">Application Submitted</div>
                                    </div>

                                    {{-- Step 2: Admin Review --}}
                                    <div
                                        class="step {{ $app->status === 'approved' ? 'completed' : ($app->status === 'pending' ? 'active' : '') }}">
                                        <div class="step-icon">
                                            @if ($app->status === 'approved')
                                                <i class="bi bi-check fs-4"></i>
                                            @else
                                                2
                                            @endif
                                        </div>
                                        <div class="step-label">Admin Review</div>
                                    </div>

                                    {{-- Step 3: Scheduled for Training --}}
                                    <div
                                        class="step {{ $app->trainingBatch && $app->trainingBatch->hasSchedule() ? 'completed' : ($app->status === 'approved' ? 'active' : '') }}">
                                        <div class="step-icon">
                                            @if ($app->trainingBatch && $app->trainingBatch->hasSchedule())
                                                <i class="bi bi-check fs-4"></i>
                                            @else
                                                3
                                            @endif
                                        </div>
                                        <div class="step-label">
                                            Scheduled for Training
                                            @if ($app->training_batch_id)
                                                @php
                                                    $enrolledCount = \App\Models\Application::where(
                                                        'training_batch_id',
                                                        $app->training_batch_id,
                                                    )
                                                        ->where('application_type', 'TWSP')
                                                        ->count();
                                                    $hasSchedule =
                                                        $app->trainingBatch && $app->trainingBatch->hasSchedule();
                                                @endphp

                                                {{-- Show View Schedule button only if schedule exists AND training not completed --}}
                                                @if ($hasSchedule && $app->training_status !== 'completed')
                                                    <br>
                                                    <button class="badge bg-info mt-1 border-0" style="cursor: pointer;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#trainingScheduleModal{{ $app->id }}">
                                                        <i class="bi bi-calendar-event"></i> View Schedule
                                                    </button>
                                                @elseif(!$hasSchedule)
                                                    {{-- Show enrolled count only if no schedule yet --}}
                                                    <br>
                                                    <small class="badge bg-success mt-1">{{ $enrolledCount }}
                                                        Enrolled</small>
                                                @endif
                                            @elseif($app->status === 'approved')
                                                {{-- Show eligible count while waiting to be scheduled --}}
                                                @php
                                                    $count =
                                                        $eligibleCountsByProgram[$app->title_of_assessment_applied_for][
                                                            'training'
                                                        ] ?? 0;
                                                @endphp
                                                <br><small class="badge bg-warning mt-1">{{ $count }}
                                                    Eligible</small>
                                            @endif
                                        </div>
                                    </div>



                                    {{-- Step 4: Training Result --}}
                                    <div
                                        class="step {{ $app->training_status === 'completed' ? 'completed' : ($app->trainingBatch && $app->trainingBatch->hasSchedule() ? 'active' : '') }}">
                                        <div class="step-icon">
                                            @if ($app->training_status === 'completed')
                                                <i class="bi bi-check"></i>
                                            @else
                                                4
                                            @endif
                                        </div>
                                        <div class="step-label">Training Result</div>
                                    </div>


                                    {{-- Step 5: Scheduled for Assessment --}}
                                    <div
                                        class="step {{ $firstAssessmentResult ? 'completed' : ($app->assessment_batch_id && $app->assessmentBatch && $app->assessmentBatch->assessment_date ? 'completed' : ($app->training_status === 'completed' ? 'active' : '')) }}">
                                        <div class="step-icon">
                                            @if ($firstAssessmentResult || ($app->assessmentBatch && $app->assessmentBatch->assessment_date))
                                                <i class="bi bi-check"></i>
                                            @else
                                                5
                                            @endif
                                        </div>
                                        <div class="step-label">
                                            Scheduled for Assessment
                                            {{-- Only show count/schedule if NO assessment result yet (not in reassessment flow) --}}
                                            @if (!$firstAssessmentResult)
                                                @if ($app->assessment_batch_id)
                                                    {{-- Assigned to batch - show count and schedule logic --}}
                                                    @php
                                                        $enrolledCount = \App\Models\Application::where(
                                                            'assessment_batch_id',
                                                            $app->assessment_batch_id,
                                                        )
                                                            ->where('application_type', 'TWSP')
                                                            ->count();
                                                        $hasAssessmentSchedule =
                                                            $app->assessmentBatch &&
                                                            $app->assessmentBatch->assessment_date;
                                                    @endphp

                                                    @if ($hasAssessmentSchedule)
                                                        {{-- Schedule exists - show View Schedule button only --}}
                                                        <br>
                                                        <button class="badge bg-info mt-1 border-0"
                                                            style="cursor: pointer;" data-bs-toggle="modal"
                                                            data-bs-target="#scheduleModal{{ $app->id }}">
                                                            <i class="bi bi-calendar-event"></i> View Schedule
                                                        </button>
                                                    @else
                                                        {{-- No schedule yet - show enrolled count --}}
                                                        <br>
                                                        <small class="badge bg-success mt-1">{{ $enrolledCount }}
                                                            Enrolled</small>
                                                    @endif
                                                @elseif($app->training_status === 'completed')
                                                    {{-- Waiting to be scheduled - show eligible count --}}
                                                    @php
                                                        $count =
                                                            $eligibleCountsByProgram[
                                                                $app->title_of_assessment_applied_for
                                                            ]['assessment'] ?? 0;
                                                    @endphp
                                                    <br><small class="badge bg-warning mt-1">{{ $count }}
                                                        Eligible</small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>




                                    {{-- Step 6: Assessment Result (with NYC badge) --}}
                                    <div
                                        class="step {{ $firstAssessmentResult ? 'completed' : ($app->assessment_batch_id ? 'active' : '') }} {{ $firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent' ? 'failed-result' : '' }}">
                                        <div class="step-icon">
                                            @if ($firstAssessmentResult)
                                                @if ($firstAssessmentResult->result === 'Not Yet Competent')
                                                    <span class="badge bg-danger"
                                                        style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                @else
                                                    <i class="bi bi-check fs-4"></i>
                                                @endif
                                            @else
                                                6
                                            @endif
                                        </div>
                                        <div class="step-label">Assessment Result</div>
                                    </div>

                                    {{-- Step 7: Reassessment Pay --}}
                                    @if ($firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent')
                                        <div
                                            class="step {{ !$app->reassessment_payment_status || $app->reassessment_payment_status === 'rejected' ? 'active' : 'completed' }}">
                                            <div class="step-icon">
                                                @if ($app->reassessment_payment_status === 'verified')
                                                    <i class="bi bi-check"></i>
                                                @else
                                                    7
                                                @endif
                                            </div>
                                            <div class="step-label">Reassessment Pay</div>
                                        </div>

                                        {{-- Step 8: Admin Review (Payment Verification) --}}
                                        <div
                                            class="step {{ $app->reassessment_payment_status === 'verified' ? 'completed' : ($app->reassessment_payment_status === 'submitted' || $app->reassessment_payment_status === 'pending' ? 'active' : '') }}">
                                            <div class="step-icon">
                                                @if ($app->reassessment_payment_status === 'verified')
                                                    <i class="bi bi-check"></i>
                                                @else
                                                    8
                                                @endif
                                            </div>
                                            <div class="step-label">Admin Review</div>
                                        </div>

                                        {{-- Step 9: Reassessment Scheduled --}}
                                        <div
                                            class="step {{ $reassessmentResult || ($app->assessment_batch_id && $app->reassessment_payment_status === 'verified') ? 'completed' : ($app->reassessment_payment_status === 'verified' ? 'active' : '') }}">
                                            <div class="step-icon">
                                                @if ($reassessmentResult || ($app->assessment_batch_id && $app->reassessment_payment_status === 'verified'))
                                                    <i class="bi bi-check fs-4"></i>
                                                @else
                                                    9
                                                @endif
                                            </div>
                                            <div class="step-label">
                                                Reassessment Scheduled
                                                @if ($app->reassessment_payment_status === 'verified')
                                                    @php
                                                        $hasReassessmentSchedule =
                                                            $app->assessment_batch_id &&
                                                            $app->assessmentBatch &&
                                                            $app->assessmentBatch->assessment_date;
                                                        $count =
                                                            $eligibleCountsByProgram[
                                                                $app->title_of_assessment_applied_for
                                                            ]['assessment'] ?? 0;
                                                    @endphp

                                                    @if ($hasReassessmentSchedule && !$reassessmentResult)
                                                        {{-- Schedule exists - show View Schedule button only --}}
                                                        <br>
                                                        <button class="badge bg-info mt-1 border-0"
                                                            style="cursor: pointer;" data-bs-toggle="modal"
                                                            data-bs-target="#scheduleModal{{ $app->id }}">
                                                            <i class="bi bi-calendar-event"></i> View Schedule
                                                        </button>
                                                    @elseif(!$reassessmentResult)
                                                        {{-- No schedule yet - show eligible count --}}
                                                        <br>
                                                        <small class="badge bg-warning mt-1">{{ $count }}
                                                            Eligible</small>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>


                                        {{-- Step 10: Reassessment Result (with NYC badge) --}}
                                        <div
                                            class="step {{ $reassessmentResult ? 'completed' : ($app->reassessment_payment_status === 'verified' && $app->assessment_batch_id ? 'active' : '') }} {{ $reassessmentResult && $reassessmentResult->result === 'Not Yet Competent' ? 'failed-result' : '' }}">
                                            <div class="step-icon">
                                                @if ($reassessmentResult)
                                                    @if ($reassessmentResult->result === 'Not Yet Competent')
                                                        <span class="badge bg-danger"
                                                            style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                    @else
                                                        <i class="bi bi-check"></i>
                                                    @endif
                                                @else
                                                    10
                                                @endif
                                            </div>
                                            <div class="step-label">Reassessment Result</div>
                                        </div>

                                        {{-- Step 11: Reassessment Pay (2nd) --}}
                                        @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent')
                                            <div
                                                class="step {{ $app->second_reassessment_payment_status === 'submitted' || $app->second_reassessment_payment_status === 'pending' || $app->second_reassessment_payment_status === 'verified' ? 'completed' : (!$app->hasFailedTwice() ? 'active' : '') }}">
                                                <div class="step-icon">
                                                    @if (
                                                        $app->second_reassessment_payment_status === 'verified' ||
                                                            $app->second_reassessment_payment_status === 'submitted' ||
                                                            $app->second_reassessment_payment_status === 'pending')
                                                        <i class="bi bi-check"></i>
                                                    @else
                                                        11
                                                    @endif
                                                </div>
                                                <div class="step-label">Reassessment Pay (2nd)</div>
                                            </div>

                                            {{-- Step 12: Admin Review (2nd Payment) --}}
                                            <div
                                                class="step {{ $app->second_reassessment_payment_status === 'verified' ? 'completed' : ($app->second_reassessment_payment_status === 'submitted' || $app->second_reassessment_payment_status === 'pending' ? 'active' : '') }}">
                                                <div class="step-icon">
                                                    @if ($app->second_reassessment_payment_status === 'verified')
                                                        <i class="bi bi-check"></i>
                                                    @else
                                                        12
                                                    @endif
                                                </div>
                                                <div class="step-label">Admin Review</div>
                                            </div>

                                            {{-- Step 13: 2nd Reassessment Scheduled --}}
                                            @if (!$app->hasFailedTwice())
                                                <div
                                                    class="step {{ $secondReassessmentResult || ($app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified') ? 'completed' : ($app->second_reassessment_payment_status === 'verified' ? 'active' : '') }}">
                                                    <div class="step-icon">
                                                        @if ($secondReassessmentResult || ($app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified'))
                                                            <i class="bi bi-check fs-4"></i>
                                                        @else
                                                            13
                                                        @endif
                                                    </div>
                                                    <div class="step-label">
                                                        2nd Reassessment Scheduled
                                                        @if ($app->second_reassessment_payment_status === 'verified')
                                                            @php
                                                                $hasSecondReassessmentSchedule =
                                                                    $app->assessment_batch_id &&
                                                                    $app->assessmentBatch &&
                                                                    $app->assessmentBatch->assessment_date;
                                                                $count =
                                                                    $eligibleCountsByProgram[
                                                                        $app->title_of_assessment_applied_for
                                                                    ]['assessment'] ?? 0;
                                                            @endphp

                                                            @if ($hasSecondReassessmentSchedule && !$secondReassessmentResult)
                                                                {{-- Schedule exists - show View Schedule button only --}}
                                                                <br>
                                                                <button class="badge bg-info mt-1 border-0"
                                                                    style="cursor: pointer;" data-bs-toggle="modal"
                                                                    data-bs-target="#scheduleModal{{ $app->id }}">
                                                                    <i class="bi bi-calendar-event"></i> View Schedule
                                                                </button>
                                                            @elseif(!$secondReassessmentResult)
                                                                {{-- No schedule yet - show eligible count --}}
                                                                <br>
                                                                <small class="badge bg-warning mt-1">{{ $count }}
                                                                    Eligible</small>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Step 14: 2nd Reassessment Result (with NYC badge) --}}
                                            <div
                                                class="step {{ $secondReassessmentResult ? 'completed' : ($app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified' ? 'active' : '') }} {{ $secondReassessmentResult && $secondReassessmentResult->result === 'Not Yet Competent' ? 'failed-result' : '' }}">
                                                <div class="step-icon">
                                                    @if ($secondReassessmentResult)
                                                        @if ($secondReassessmentResult->result === 'Not Yet Competent')
                                                            <span class="badge bg-danger"
                                                                style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                        @else
                                                            <i class="bi bi-check"></i>
                                                        @endif
                                                    @else
                                                        14
                                                    @endif
                                                </div>
                                                <div class="step-label">2nd Reassessment Result</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                {{-- Status Info --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Application Status:</strong>
                                            @php
                                                $statusMap = [
                                                    'pending' => ['badge' => 'secondary', 'text' => 'Pending'],
                                                    'approved' => ['badge' => 'success', 'text' => 'Approved'],
                                                    'rejected' => ['badge' => 'danger', 'text' => 'Rejected'],
                                                ];
                                                $status = $statusMap[$app->status] ?? [
                                                    'badge' => 'secondary',
                                                    'text' => 'Unknown',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $status['badge'] }}">{{ $status['text'] }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Training Status:</strong>
                                            @if ($app->training_status === 'completed')
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i>
                                                    Completed</span>
                                            @elseif($app->training_status === 'failed')
                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>
                                            @elseif($app->training_status === 'ongoing')
                                                <span class="badge bg-info"><i class="bi bi-hourglass-split"></i>
                                                    Ongoing</span>
                                            @elseif($app->status === 'approved')
                                                <span class="badge bg-info"><i class="bi bi-book"></i> Enrolled</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Assessment Result:</strong>
                                            @if ($firstAssessmentResult)
                                                @if ($firstAssessmentResult->result === 'Competent')
                                                    <span class="badge bg-success">COMPETENT</span>
                                                @else
                                                    <span class="badge bg-danger">NOT YET COMPETENT</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Not Assessed</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>


                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('applicant.applications.show', $app->id) }}"
                                        class="btn btn-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                    @if ($app->correction_requested)
                                        <a href="{{ route('applicant.applications.edit', $app->id) }}"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Application
                                        </a>
                                    @endif

                                    @if (
                                        $firstAssessmentResult &&
                                            $firstAssessmentResult->result === 'Not Yet Competent' &&
                                            (!$app->reassessment_payment_status || $app->reassessment_payment_status === 'rejected'))
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#reassessmentModal{{ $app->id }}">
                                            <i class="bi bi-credit-card"></i> Pay for Reassessment
                                        </button>
                                    @endif

                                    @php
                                        $reassessmentResult =
                                            $app->assessmentResults->count() > 1
                                                ? $app->assessmentResults->last()
                                                : null;
                                    @endphp
                                    @if (
                                        $reassessmentResult &&
                                            $reassessmentResult->result === 'Not Yet Competent' &&
                                            !$app->hasFailedTwice() &&
                                            (!$app->second_reassessment_payment_status || $app->second_reassessment_payment_status === 'rejected'))
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#reassessmentModal2{{ $app->id }}">
                                            <i class="bi bi-credit-card"></i> Pay for 2nd Reassessment
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($firstAssessmentResult || $reassessmentResult || $secondReassessmentResult)
                            <div class="modal fade" id="employmentModal{{ $app->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST"
                                            action="{{ route('applicant.employment-feedback.store', $app->id) }}">
                                            @csrf
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-briefcase"></i> TWSP Employment Feedback
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> Please provide your current
                                                    employment
                                                    information. This helps us track the effectiveness of our TWSP programs.
                                                </div>

                                                <div class="row">

                                                    {{-- Date Employed --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date Employed <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date" name="date_employed" class="form-control"
                                                            value="{{ $app->employmentRecord ? $app->employmentRecord->date_employed->format('Y-m-d') : '' }}"
                                                            required>
                                                    </div>

                                                    {{-- Occupation --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Occupation <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="occupation" class="form-control"
                                                            value="{{ $app->employmentRecord->occupation ?? '' }}"
                                                            placeholder="e.g., Bookkeeper, Event Coordinator, Tourism Assistant"
                                                            required>
                                                    </div>

                                                    {{-- Name of Employer --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Name of Employer <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="employer_name" class="form-control"
                                                            value="{{ $app->employmentRecord->employer_name ?? '' }}"
                                                            placeholder="Company/Organization Name" required>
                                                    </div>

                                                    {{-- Classification of Employer --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Classification of Employer <span
                                                                class="text-danger">*</span></label>
                                                        <select name="employer_classification" class="form-select"
                                                            required>
                                                            <option value="">Select Classification</option>
                                                            <option value="Private"
                                                                {{ ($app->employmentRecord->employer_classification ?? '') == 'Private' ? 'selected' : '' }}>
                                                                Private
                                                            </option>
                                                            <option value="Government"
                                                                {{ ($app->employmentRecord->employer_classification ?? '') == 'Government' ? 'selected' : '' }}>
                                                                Government
                                                            </option>
                                                            <option value="NGO"
                                                                {{ ($app->employmentRecord->employer_classification ?? '') == 'NGO' ? 'selected' : '' }}>
                                                                NGO
                                                            </option>
                                                            <option value="Self-Employed"
                                                                {{ ($app->employmentRecord->employer_classification ?? '') == 'Self-Employed' ? 'selected' : '' }}>
                                                                Self-Employed
                                                            </option>
                                                        </select>
                                                    </div>

                                                    {{-- Address of Employer --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Address of Employer <span
                                                                class="text-danger">*</span></label>
                                                        <textarea name="employer_address" class="form-control" rows="3" placeholder="Complete address of employer"
                                                            required>{{ $app->employmentRecord->employer_address ?? '' }}</textarea>
                                                    </div>

                                                    {{-- Monthly Income --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Monthly Income/Salary <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₱</span>
                                                            <input type="number" name="monthly_income"
                                                                class="form-control" step="0.01" min="0"
                                                                value="{{ $app->employmentRecord->monthly_income ?? '' }}"
                                                                placeholder="0.00" required>
                                                        </div>
                                                        <small class="text-muted">Enter your gross monthly income</small>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-info">
                                                    <i class="bi bi-check-circle"></i>
                                                    {{ $app->employmentRecord ? 'Update' : 'Submit' }} Employment Details
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-muted p-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p>No TWSP applications yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Assessment Only Tab -->
                <div class="tab-pane fade" id="assessment" role="tabpanel">
                    @forelse ($assessmentApps as $app)
                        <div class="card application-card mb-4 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-0">
                                            <i
                                                class="bi bi-clipboard-check me-2"></i>{{ $app->title_of_assessment_applied_for }}
                                        </h5>
                                        <small>{{ $app->firstname }} {{ $app->middlename }} {{ $app->surname }}
                                            {{ $app->name_extension }}</small>
                                    </div>
                                    @php
                                        $failedCount = $app
                                            ->assessmentResults()
                                            ->where('result', 'Not Yet Competent')
                                            ->count();
                                    @endphp
                                    @if ($failedCount >= 3)
                                        <div class="text-end ms-3">
                                            <span class="badge bg-danger d-block mb-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>Maximum Assessment Attempts
                                                Reached
                                            </span>
                                            <small class="d-block" style="font-size: 0.75rem; line-height: 1.2;">
                                                Please contact the admin for further assistance.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Get the FIRST assessment result (not the latest) --}}
                                @php
                                    $firstAssessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->first();
                                    $reassessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->skip(1)
                                        ->first();
                                    $secondReassessmentResult = $app
                                        ->assessmentResults()
                                        ->orderBy('assessed_at', 'asc')
                                        ->skip(2)
                                        ->first();
                                @endphp


                                {{-- Progress Indicator for Assessment Only (6 steps) --}}
                                <div class="step-indicator mb-4">
                                    {{-- Step 1: Application Submitted --}}
                                    <div class="step completed">
                                        <div class="step-icon"><i class="bi bi-check fs-4"></i></div>
                                        <div class="step-label">Application Submitted</div>
                                    </div>

                                    {{-- Step 2: Payment --}}
                                    <div
                                        class="step {{ $app->payment_status === 'verified' ? 'completed' : ($app->payment_status === 'submitted' ? 'active' : '') }}">
                                        <div class="step-icon">
                                            @if ($app->payment_status === 'verified')
                                                <i class="bi bi-check"></i>
                                            @else
                                                2
                                            @endif
                                        </div>
                                        <div class="step-label">Payment</div>
                                    </div>

                                    {{-- Step 3: Admin Review --}}
                                    <div
                                        class="step {{ $app->status === 'approved' ? 'completed' : ($app->payment_status === 'verified' ? 'active' : '') }}">
                                        <div class="step-icon">
                                            @if ($app->status === 'approved')
                                                <i class="bi bi-check"></i>
                                            @else
                                                3
                                            @endif
                                        </div>
                                        <div class="step-label">Admin Review</div>
                                    </div>
                                    {{-- Step 4: Scheduled for Assessment --}}
                                    <div
                                        class="step {{ $app->assessmentResult ? 'completed' : ($app->assessment_batch_id ? 'completed' : ($app->status === 'approved' && $app->payment_status === 'verified' ? 'active' : '')) }}">
                                        <div class="step-icon">
                                            @if ($app->assessment_batch_id || $app->assessmentResult)
                                                <i class="bi bi-check fs-4"></i>
                                            @else
                                                4
                                            @endif
                                        </div>
                                        <div class="step-label">
                                            Scheduled for Assessment
                                            @if ($app->assessment_batch_id && !($firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent'))
                                                {{-- Show View Schedule only if scheduled AND not in reassessment --}}
                                                <br>
                                                <button class="badge bg-success mt-1 border-0" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#scheduleModal{{ $app->id }}">
                                                    <i class="bi bi-calendar-event"></i> View Schedule
                                                </button>
                                            @elseif($app->status === 'approved' && $app->payment_status === 'verified' && !$firstAssessmentResult)
                                                {{-- Waiting to be scheduled, show eligible count --}}
                                                @php
                                                    $count =
                                                        $eligibleCountsByProgram[$app->title_of_assessment_applied_for][
                                                            'assessment'
                                                        ] ?? 0;
                                                @endphp
                                                <br>
                                                <small class="badge bg-warning mt-1">{{ $count }} Eligible</small>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Step 5: Assessment Result --}}
                                    <div
                                        class="step {{ $firstAssessmentResult ? 'completed' : ($app->assessment_batch_id ? 'active' : '') }} {{ $firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent' ? 'failed-result' : '' }}">
                                        <div class="step-icon">
                                            @if ($firstAssessmentResult)
                                                @if ($firstAssessmentResult->result === 'Not Yet Competent')
                                                    <span class="badge bg-danger"
                                                        style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                @else
                                                    <i class="bi bi-check fs-4"></i>
                                                @endif
                                            @else
                                                5
                                            @endif
                                        </div>
                                        <div class="step-label">Assessment Result</div>
                                    </div>

                                    {{-- Step 6: Reassessment Pay --}}
                                    @if ($firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent')
                                        <div
                                            class="step {{ !$app->reassessment_payment_status || $app->reassessment_payment_status === 'rejected' ? 'active' : 'completed' }}">
                                            <div class="step-icon">
                                                @if ($app->reassessment_payment_status === 'verified')
                                                    <i class="bi bi-check"></i>
                                                @else
                                                    6
                                                @endif
                                            </div>
                                            <div class="step-label">Reassessment Pay</div>
                                        </div>

                                        {{-- Step 7: Admin Review (Payment Verification) --}}
                                        <div
                                            class="step {{ $app->reassessment_payment_status === 'verified' ? 'completed' : ($app->reassessment_payment_status === 'submitted' || $app->reassessment_payment_status === 'pending' ? 'active' : '') }}">
                                            <div class="step-icon">
                                                @if ($app->reassessment_payment_status === 'verified')
                                                    <i class="bi bi-check"></i>
                                                @else
                                                    7
                                                @endif
                                            </div>
                                            <div class="step-label">Admin Review</div>
                                        </div>
                                        {{-- Step 8: Reassessment Scheduled --}}
                                        @if ($firstAssessmentResult && $firstAssessmentResult->result === 'Not Yet Competent')
                                            <div
                                                class="step {{ $reassessmentResult || ($app->assessment_batch_id && $app->reassessment_payment_status === 'verified') ? 'completed' : ($app->reassessment_payment_status === 'verified' ? 'active' : '') }}">
                                                <div class="step-icon">
                                                    @if ($reassessmentResult || ($app->assessment_batch_id && $app->reassessment_payment_status === 'verified'))
                                                        <i class="bi bi-check fs-4"></i>
                                                    @else
                                                        8
                                                    @endif
                                                </div>
                                                <div class="step-label">
                                                    Reassessment Scheduled
                                                    @if (
                                                        $app->assessment_batch_id &&
                                                            $app->reassessment_payment_status === 'verified' &&
                                                            !($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent') &&
                                                            !$secondReassessmentResult)
                                                        {{-- Show View Schedule only if scheduled AND reassessment payment verified AND not in 2nd reassessment AND no 2nd reassessment result yet --}}
                                                        <br>
                                                        <button class="badge bg-success mt-1 border-0"
                                                            style="cursor: pointer;" data-bs-toggle="modal"
                                                            data-bs-target="#reassessmentScheduleModal{{ $app->id }}">
                                                            <i class="bi bi-calendar-event"></i> View Schedule
                                                        </button>
                                                    @elseif($app->reassessment_payment_status === 'verified' && !$app->assessment_batch_id && !$reassessmentResult)
                                                        {{-- Waiting to be scheduled, show eligible count --}}
                                                        @php
                                                            $count =
                                                                $eligibleCountsByProgram[
                                                                    $app->title_of_assessment_applied_for
                                                                ]['assessment'] ?? 0;
                                                        @endphp
                                                        <br>
                                                        <small class="badge bg-warning mt-1">{{ $count }}
                                                            Eligible</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Step 9: Reassessment Result --}}
                                        <div
                                            class="step {{ $reassessmentResult ? 'completed' : ($app->reassessment_payment_status === 'verified' && $app->assessment_batch_id ? 'active' : '') }} {{ $reassessmentResult && $reassessmentResult->result === 'Not Yet Competent' ? 'failed-result' : '' }}">
                                            <div class="step-icon">
                                                @if ($reassessmentResult)
                                                    @if ($reassessmentResult->result === 'Not Yet Competent')
                                                        <span class="badge bg-danger"
                                                            style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                    @else
                                                        <i class="bi bi-check fs-4"></i>
                                                    @endif
                                                @else
                                                    9
                                                @endif
                                            </div>
                                            <div class="step-label">Reassessment Result</div>
                                        </div>

                                        {{-- Step 10: Reassessment Pay (2nd) --}}
                                        @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent')
                                            <div
                                                class="step {{ $app->second_reassessment_payment_status === 'submitted' || $app->second_reassessment_payment_status === 'pending' || $app->second_reassessment_payment_status === 'verified' ? 'completed' : (!$app->hasFailedTwice() ? 'active' : '') }}">

                                                <div class="step-icon">
                                                    @if (
                                                        $app->second_reassessment_payment_status === 'verified' ||
                                                            $app->second_reassessment_payment_status === 'submitted' ||
                                                            $app->second_reassessment_payment_status === 'pending')
                                                        <i class="bi bi-check"></i>
                                                    @else
                                                        10
                                                    @endif
                                                </div>
                                                <div class="step-label">Reassessment Pay (2nd)</div>
                                            </div>
                                        @endif
                                        {{-- Step 11 --}}
                                        @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent')
                                            <div
                                                class="step {{ $app->second_reassessment_payment_status === 'verified' ? 'completed' : ($app->second_reassessment_payment_status === 'submitted' || $app->second_reassessment_payment_status === 'pending' ? 'active' : '') }}">

                                                <div class="step-icon">
                                                    @if ($app->second_reassessment_payment_status === 'verified')
                                                        <i class="bi bi-check"></i>
                                                    @else
                                                        11
                                                    @endif
                                                </div>
                                                <div class="step-label">Admin Review</div>
                                            </div>
                                        @endif
                                    @endif
                                    {{-- Step 12: 2nd Reassessment Scheduled --}}
                                    @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent' && !$app->hasFailedTwice())
                                        <div
                                            class="step {{ $app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified' ? 'completed' : ($app->second_reassessment_payment_status === 'verified' ? 'active' : '') }}">



                                            <div class="step-icon">
                                                @if ($app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified')
                                                    <i class="bi bi-check fs-4"></i>
                                                @else
                                                    12
                                                @endif
                                            </div>
                                            <div class="step-label">
                                                2nd Reassessment Scheduled
                                                @if (
                                                    $app->assessment_batch_id &&
                                                        $app->second_reassessment_payment_status === 'verified' &&
                                                        !($secondReassessmentResult && $secondReassessmentResult->result === 'Not Yet Competent'))
                                                    <br>
                                                    <button class="badge bg-success mt-1 border-0"
                                                        style="cursor: pointer;" data-bs-toggle="modal"
                                                        data-bs-target="#reassessmentScheduleModal{{ $app->id }}">
                                                        <i class="bi bi-calendar-event"></i> View Schedule
                                                    </button>
                                                @elseif($app->second_reassessment_payment_status === 'verified' && !$secondReassessmentResult)
                                                    @php
                                                        $count =
                                                            $eligibleCountsByProgram[
                                                                $app->title_of_assessment_applied_for
                                                            ]['assessment'] ?? 0;
                                                    @endphp
                                                    <br>
                                                    <small class="badge bg-warning mt-1">{{ $count }}
                                                        Eligible</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Step 13: 2nd Reassessment Result (Always visible) --}}
                                    @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent')
                                        <div
                                            class="step {{ $secondReassessmentResult ? 'completed' : ($app->assessment_batch_id && $app->second_reassessment_payment_status === 'verified' ? 'active' : '') }}">


                                            <div class="step-icon">
                                                @if ($secondReassessmentResult)
                                                    @if ($secondReassessmentResult->result === 'Not Yet Competent')
                                                        <span class="badge bg-danger"
                                                            style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">NYC</span>
                                                    @else
                                                        <i class="bi bi-check"></i>
                                                    @endif
                                                @else
                                                    13
                                                @endif
                                            </div>
                                            <div class="step-label">2nd Reassessment Result</div>
                                        </div>
                                    @endif
                                </div>


                                {{-- Status Info --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Application Status:</strong>
                                            @php
                                                $statusMap = [
                                                    'pending' => ['badge' => 'secondary', 'text' => 'Pending'],
                                                    'approved' => ['badge' => 'success', 'text' => 'Approved'],
                                                    'rejected' => ['badge' => 'danger', 'text' => 'Rejected'],
                                                ];
                                                $status = $statusMap[$app->status] ?? [
                                                    'badge' => 'secondary',
                                                    'text' => 'Unknown',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $status['badge'] }}">{{ $status['text'] }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Payment Status:</strong>
                                            @php
                                                $paymentMap = [
                                                    'pending' => ['badge' => 'warning', 'text' => 'Payment Required'],
                                                    'submitted' => ['badge' => 'info', 'text' => 'Under Review'],
                                                    'verified' => ['badge' => 'success', 'text' => 'Verified'],
                                                    'rejected' => ['badge' => 'danger', 'text' => 'Rejected'],
                                                ];
                                                $payment = $paymentMap[$app->payment_status] ?? [
                                                    'badge' => 'secondary',
                                                    'text' => 'N/A',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $payment['badge'] }}">{{ $payment['text'] }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2"><strong>Assessment Result:</strong>
                                            @if ($app->assessmentResult)
                                                @if ($app->assessmentResult->result === 'Competent')
                                                    <span class="badge bg-success">COMPETENT</span>
                                                @else
                                                    <span class="badge bg-danger">NOT YET COMPETENT</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Not Assessed</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('applicant.applications.show', $app->id) }}"
                                        class="btn btn-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>

                                    @if ($app->correction_requested)
                                        <a href="{{ route('applicant.applications.edit', $app->id) }}"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Application
                                        </a>
                                    @endif

                                    @if ($app->payment_status === 'pending' || $app->payment_status === 'rejected')
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#paymentModal{{ $app->id }}">
                                            <i class="fas fa-upload"></i> Upload Payment
                                        </button>
                                    @endif

                                    @if (
                                        $app->assessmentResult &&
                                            $app->assessmentResult->result === 'Not Yet Competent' &&
                                            (!$app->reassessment_payment_status || $app->reassessment_payment_status === 'rejected'))
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#reassessmentModal{{ $app->id }}">
                                            <i class="bi bi-credit-card"></i> Pay for Reassessment
                                        </button>
                                    @endif

                                    @if (
                                        $reassessmentResult &&
                                            $reassessmentResult->result === 'Not Yet Competent' &&
                                            !$app->hasFailedTwice() &&
                                            (!$app->second_reassessment_payment_status || $app->second_reassessment_payment_status === 'rejected'))
                                        <button class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#reassessmentModal2{{ $app->id }}">
                                            <i class="bi bi-credit-card"></i> Pay for 2nd Reassessment
                                        </button>
                                    @endif


                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted p-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p>No Assessment Only applications yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    {{-- Application Type Modal --}}
    <div class="modal fade" id="applicationTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Select Application Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        @php
                            $twspAnnouncement = \App\Models\TwspAnnouncement::getActive();
                            $twspAvailable = $twspAnnouncement && $twspAnnouncement->hasAvailableSlots();
                        @endphp

                        <!-- TWSP Option -->
                        <div class="card border-success application-type-card {{ !$twspAvailable ? 'opacity-50' : '' }}"
                            style="cursor: {{ $twspAvailable ? 'pointer' : 'not-allowed' }};"
                            @if ($twspAvailable) onclick="selectApplicationType('TWSP')" @endif>
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Training For Work Scholarship Program (TWSP)
                                    @if (!$twspAvailable)
                                        <span class="badge bg-danger ms-2">Unavailable</span>
                                    @else
                                        <span
                                            class="badge bg-success ms-2">{{ $twspAnnouncement->getRemainingSlots() }}/{{ $twspAnnouncement->total_slots }}
                                            Slots</span>
                                    @endif
                                </h5>
                                <p class="card-text text-muted mb-0">
                                    Complete the full training program before taking the assessment.
                                    Includes comprehensive training and certification.
                                </p>
                                @if (!$twspAvailable)
                                    <small class="text-danger d-block mt-2">
                                        <i class="fas fa-info-circle"></i> TWSP applications are currently closed. Please
                                        check back later.
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Assessment Only Option -->
                        <div class="card border-primary application-type-card" style="cursor: pointer;"
                            onclick="selectApplicationType('Assessment Only')">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    Assessment Only
                                </h5>
                                <p class="card-text text-muted mb-0">
                                    Skip training and proceed directly to competency assessment.
                                    Suitable for those with prior experience.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Upload Modals --}}
    @foreach ($assessmentApps as $app)
        <div class="modal fade" id="paymentModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('applicant.payment.upload', $app->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Upload Payment Proof</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Payment Instructions:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>GCash Number: <strong>0912-345-6789</strong></li>
                                    <li>Account Name: <strong>TESDA Assessment Center</strong></li>
                                    <li>Amount: <strong>₱500.00</strong></li>
                                </ul>
                            </div>

                            @if ($app->payment_status === 'rejected' && $app->payment_remarks)
                                <div class="alert alert-danger">
                                    <strong>Previous submission was rejected:</strong><br>
                                    {{ $app->payment_remarks }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="payment_proof{{ $app->id }}" class="form-label">
                                    Upload GCash Screenshot or Receipt <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="payment_proof{{ $app->id }}"
                                    name="payment_proof" accept="image/*,.pdf" required>
                                <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Submit Payment Proof</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    {{-- Assessment Schedule Modals --}}
    @foreach ($applications as $app)
        @if ($app->assessment_batch_id && $app->assessmentBatch)
            <div class="modal fade" id="scheduleModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-calendar-check"></i> Assessment Schedule
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="text-muted small">Program</label>
                                <h6 class="mb-0">{{ $app->title_of_assessment_applied_for }}</h6>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Batch Name</label>
                                <h6 class="mb-0">{{ $app->assessmentBatch->batch_name }}</h6>
                            </div>

                            {{-- Intensive Review Training Schedule --}}
                            @if ($app->assessmentBatch->intensive_review_day1 || $app->assessmentBatch->intensive_review_day2)
                                <div class="card bg-light mb-3">
                                    <div class="card-header bg-warning">
                                        <strong><i class="bi bi-book"></i> Intensive Review Training</strong>
                                    </div>
                                    <div class="card-body">
                                        @if ($app->assessmentBatch->intensive_review_day1)
                                            <div class="mb-2">
                                                <div class="row align-items-center">

                                                    {{-- Column 1 : Date --}}
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Day 1</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('F d, Y') }}
                                                            <span class="text-muted">
                                                                ({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('l') }})
                                                            </span>
                                                        </h6>
                                                    </div>

                                                    {{-- Column 2 : Time --}}
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Time</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $app->assessmentBatch->intensive_review_day1_start->format('g:i a') }}
                                                            -
                                                            {{ $app->assessmentBatch->intensive_review_day1_end->format('g:i a') }}
                                                        </h6>
                                                    </div>

                                                </div>
                                            </div>
                                        @endif
                                        @if ($app->assessmentBatch->intensive_review_day2)
                                            <div class="mb-2">
                                                <div class="row align-items-center">
                                                    {{-- Column 1 : Date --}}
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Day 2</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('F d, Y') }}
                                                            <span class="text-muted">
                                                                ({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('l') }})
                                                            </span>
                                                        </h6>
                                                    </div>
                                                    {{-- Column 2 : Time --}}
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Time</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $app->assessmentBatch->intensive_review_day2_start->format('g:i a') }}
                                                            -
                                                            {{ $app->assessmentBatch->intensive_review_day2_end->format('g:i a') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Assessment Date --}}
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-success text-white">
                                    <strong><i class="bi bi-clipboard-check"></i> Assessment Day</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Date</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar3"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->assessment_date)->format('F d, Y') }}
                                            </h6>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Time</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-clock"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->start_time)->format('g:i a') }}
                                                -
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->end_time)->format('g:i a') }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <label class="text-muted small">Venue</label>
                                        <h6 class="mb-0">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $app->assessmentBatch->venue ?? 'TESDA Assessment Center' }}
                                        </h6>
                                    </div>

                                    @if ($app->assessmentBatch->assessor_name)
                                        <div class="mt-2">
                                            <label class="text-muted small">Assessor</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-person-badge"></i>
                                                {{ $app->assessmentBatch->assessor_name }}
                                            </h6>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($app->assessmentBatch->remarks)
                                <div class="alert alert-warning">
                                    <strong><i class="bi bi-info-circle"></i> Important Notes:</strong><br>
                                    {{ $app->assessmentBatch->remarks }}
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Reassessment Schedule Modal (Different from first assessment) --}}
    @foreach ($applications as $app)
        @if ($app->reassessment_payment_status === 'verified' && $app->assessmentBatch)
            <div class="modal fade" id="reassessmentScheduleModal{{ $app->id }}" tabindex="-1"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title"><i class="bi bi-calendar-check"></i> Reassessment Schedule</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>This is your reassessment schedule.</strong> You will retake all COCs.
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Program</label>
                                <h6 class="mb-0">{{ $app->title_of_assessment_applied_for }}</h6>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Batch Name</label>
                                <h6 class="mb-0">{{ $app->assessmentBatch->batch_name }}</h6>
                            </div>

                            {{-- Intensive Review Training Schedule --}}
                            @if ($app->assessmentBatch->intensive_review_day1 || $app->assessmentBatch->intensive_review_day2)
                                <div class="card bg-light mb-3">
                                    <div class="card-header bg-info text-white">
                                        <strong><i class="bi bi-book"></i> Intensive Review Training</strong>
                                    </div>
                                    <div class="card-body">
                                        @if ($app->assessmentBatch->intensive_review_day1)
                                            <div class="mb-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Day 1</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('F d, Y') }}
                                                            <span
                                                                class="text-muted">({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day1)->format('l') }})</span>
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Time</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $app->assessmentBatch->intensive_review_day1_start->format('g:i a') }}-{{ $app->assessmentBatch->intensive_review_day1_end->format('g:i a') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($app->assessmentBatch->intensive_review_day2)
                                            <div class="mb-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Day 2</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('F d, Y') }}
                                                            <span
                                                                class="text-muted">({{ \Carbon\Carbon::parse($app->assessmentBatch->intensive_review_day2)->format('l') }})</span>
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">Time</label>
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $app->assessmentBatch->intensive_review_day2_start->format('g:i a') }}-{{ $app->assessmentBatch->intensive_review_day2_end->format('g:i a') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Assessment Date --}}
                            <div class="card bg-light mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <strong><i class="bi bi-clipboard-check"></i> Reassessment Day</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Date</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-calendar3"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->assessment_date)->format('F d, Y') }}
                                            </h6>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Time</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-clock"></i>
                                                {{ \Carbon\Carbon::parse($app->assessmentBatch->start_time)->format('g:i a') }}-{{ \Carbon\Carbon::parse($app->assessmentBatch->end_time)->format('g:i a') }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="text-muted small">Venue</label>
                                        <h6 class="mb-0">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $app->assessmentBatch->venue ?? 'TESDA Assessment Center' }}
                                        </h6>
                                    </div>
                                    @if ($app->assessmentBatch->assessor_name)
                                        <div class="mt-2">
                                            <label class="text-muted small">Assessor</label>
                                            <h6 class="mb-0">
                                                <i class="bi bi-person-badge"></i>
                                                {{ $app->assessmentBatch->assessor_name }}
                                            </h6>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($app->assessmentBatch->remarks)
                                <div class="alert alert-warning">
                                    <strong><i class="bi bi-info-circle"></i> Important Notes:</strong><br>
                                    {{ $app->assessmentBatch->remarks }}
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    </div>



    {{-- Reassessment Payment Modals --}}
    @foreach ($applications as $app)
        @php
            $assessmentResult = $app->latestAssessmentResult;
        @endphp
        @if ($assessmentResult && $assessmentResult->result === 'Not Yet Competent')
            <div class="modal fade" id="reassessmentModal{{ $app->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('applicant.reassessment.submit', $app->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">Pay for Reassessment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Program:</strong> {{ $app->title_of_assessment_applied_for }}<br>
                                    <strong>Reassessment Fee:</strong> ₱500.00
                                </div>

                                <h6>Previous Assessment Results:</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>COC Code</th>
                                                <th>COC Title</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($assessmentResult->cocResults as $coc)
                                                <tr>
                                                    <td><strong>{{ $coc->coc_code }}</strong></td>
                                                    <td>{{ $coc->coc_title }}</td>
                                                    <td>
                                                        @if ($coc->result === 'competent')
                                                            <span class="badge bg-success">COMPETENT</span>
                                                        @else
                                                            <span class="badge bg-danger">NOT YET COMPETENT</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Note:</strong> Reassessment requires you to retake ALL COCs, not just the NYC
                                    ones.
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label">Upload Payment Proof <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="payment_proof" class="form-control"
                                        accept="image/*,.pdf" required>
                                    <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max 2MB)</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Reference Number (Optional)</label>
                                    <input type="text" name="payment_reference" class="form-control"
                                        placeholder="e.g., GCash Ref #, Bank Transaction #">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning">Submit Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    {{-- 2nd Reassessment Payment Modal --}}
    @foreach ($applications as $app)
        @php
            // Get all failed results
            $failedResults = $app->assessmentResults->where('result', 'Not Yet Competent')->values();

            // 1st reassessment is the 2nd failed result (index 1)
            $reassessmentResult = $failedResults->count() > 1 ? $failedResults->get(1) : null;

            // 2nd reassessment is the 3rd failed result (index 2)
            $secondReassessmentResult = $failedResults->count() > 2 ? $failedResults->get(2) : null;
        @endphp

        @if ($reassessmentResult && $reassessmentResult->result === 'Not Yet Competent' && !$app->hasFailedTwice())
            <div class="modal fade" id="reassessmentModal2{{ $app->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('applicant.reassessment.submit', $app->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">Pay for 2nd Reassessment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Program:</strong> {{ $app->title_of_assessment_applied_for }}<br>
                                    <strong>2nd Reassessment Fee:</strong> ₱500.00
                                </div>
                                <h6>Previous Reassessment Results:</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>COC Code</th>
                                                <th>COC Title</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reassessmentResult->cocResults as $coc)
                                                <tr>
                                                    <td><strong>{{ $coc->coc_code }}</strong></td>
                                                    <td>{{ $coc->coc_title }}</td>
                                                    <td>
                                                        @if ($coc->result === 'competent')
                                                            <span class="badge bg-success">COMPETENT</span>
                                                        @else
                                                            <span class="badge bg-danger">NOT YET COMPETENT</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-warning">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Note:</strong> This is your 2nd reassessment. You will retake ALL COCs.
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Upload Payment Proof <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="payment_proof" class="form-control"
                                        accept="image/*,.pdf" required>
                                    <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max 2MB)</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Reference Number (Optional)</label>
                                    <input type="text" name="payment_reference" class="form-control"
                                        placeholder="e.g., GCash Ref #, Bank Transaction #">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning">Submit Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    {{-- Training Schedule Modals --}}
    @foreach ($twspApps as $app)
        @if ($app->training_batch_id && $app->trainingBatch && $app->trainingBatch->hasSchedule())
            <div class="modal fade" id="trainingScheduleModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-calendar-check"></i> Training Schedule
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="text-muted small">Program</label>
                                <h6 class="mb-0">{{ $app->title_of_assessment_applied_for }}</h6>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Batch Name</label>
                                <h6 class="mb-0">{{ $app->trainingBatch->batch_name }}</h6>
                            </div>

                            @if ($app->trainingBatch->trainingSchedule)
                                <div class="card bg-light mb-3">
                                    <div class="card-header bg-info text-white">
                                        <strong><i class="bi bi-book"></i> Training Schedule</strong>
                                    </div>
                                    <div class="card-body">
                                        {{-- Date Row --}}
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="text-muted small">Start Date</label>
                                                <h6 class="mb-0">
                                                    <i class="bi bi-calendar3"></i>
                                                    {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->start_date)->format('F d, Y') }}
                                                </h6>

                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">End Date</label>
                                                <h6 class="mb-0">
                                                    <i class="bi bi-calendar3"></i>
                                                    {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_date)->format('F d, Y') }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_date)->format('l') }}
                                                </small>
                                            </div>
                                        </div>

                                        {{-- Time Row --}}
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="text-muted small">Venue</label>
                                                @if ($app->trainingBatch->trainingSchedule->venue)
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-geo-alt"></i>
                                                        {{ $app->trainingBatch->trainingSchedule->venue }}
                                                    </h6>
                                                @else
                                                    <h6 class="mb-0 text-muted">-</h6>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Time</label>
                                                @if ($app->trainingBatch->trainingSchedule->end_time)
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-clock"></i>
                                                        {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->start_time)->format('h:i A') }}

                                                        -
                                                        {{ \Carbon\Carbon::parse($app->trainingBatch->trainingSchedule->end_time)->format('h:i A') }}
                                                    </h6>
                                                @else
                                                    <h6 class="mb-0 text-muted">-</h6>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach




@endsection

@push('scripts')
    <script>
         // ========== TAB PERSISTENCE ==========
        document.addEventListener('DOMContentLoaded', function() {
            const twspTab = document.getElementById('twsp-tab');
            const assessmentTab = document.getElementById('assessment-tab');
            const twspPane = document.getElementById('twsp');
            const assessmentPane = document.getElementById('assessment');

            // Get saved tab from localStorage
            const savedTab = localStorage.getItem('activeApplicationTab');

            // Restore the saved tab on page load
            if (savedTab === 'assessment') {
                // Remove active from TWSP
                twspTab.classList.remove('active');
                twspPane.classList.remove('show', 'active');
                
                // Add active to Assessment
                assessmentTab.classList.add('active');
                assessmentPane.classList.add('show', 'active');
            }
            // If savedTab is 'twsp' or null, TWSP tab is already active by default

            // Save tab selection when clicked
            twspTab.addEventListener('click', function() {
                localStorage.setItem('activeApplicationTab', 'twsp');
            });

            assessmentTab.addEventListener('click', function() {
                localStorage.setItem('activeApplicationTab', 'assessment');
            });
        });

        // ========== EXISTING CODE ==========
        function selectApplicationType(type) {
            sessionStorage.setItem('application_type', type);
            sessionStorage.setItem('program_name', 'BOOKKEEPING NC III');
            window.location.href = "{{ route('applicant.apply.create') }}";
        }

        // Hover effects for application type cards
        document.querySelectorAll('.application-type-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('opacity-50')) {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s';
                }
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
        function selectApplicationType(type) {
            sessionStorage.setItem('application_type', type);
            sessionStorage.setItem('program_name', 'BOOKKEEPING NC III');
            window.location.href = "{{ route('applicant.apply.create') }}";
        }

        // Hover effects for application type cards
        document.querySelectorAll('.application-type-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('opacity-50')) {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s';
                }
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
@endpush
