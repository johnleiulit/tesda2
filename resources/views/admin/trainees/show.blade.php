@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.trainees.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Batches
                </a>
            </div>
        </div>

        <!-- Batch Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-1">
                            {{ $batch->nc_program }} - Batch {{ $batch->batch_number }}
                        </h4>
                        <span
                            class="badge 
                            @if ($batch->status === 'scheduled') bg-info
                            @elseif($batch->status === 'ongoing') bg-warning
                            @elseif($batch->status === 'completed') bg-secondary
                            @elseif($batch->is_full) bg-success
                            @else bg-primary @endif">
                            {{ $batch->is_full && $batch->status === 'enrolling' ? 'FULL' : strtoupper($batch->status) }}
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-light text-dark fs-6 me-2">
                            {{ $batch->applications->count() }}/{{ $batch->max_students }} Enrolled
                        </span>
                        <span class="badge bg-success me-1">{{ $completedCount }} Completed</span>
                        <span class="badge bg-danger">{{ $failedCount }} Failed</span>
                    </div>
                </div>
            </div> <!-- Closing card-header -->

            @if ($batch->trainingSchedule)
                <div class="card-body">
                    <div class="mb-0">
                        <strong><i class="bi bi-calendar-check"></i> Schedule:</strong>
                        {{ $batch->trainingSchedule->start_date->format('M d, Y') }} -
                        {{ $batch->trainingSchedule->end_date->format('M d, Y') }} |
                        {{ $batch->trainingSchedule->days }} |
                        {{ $batch->trainingSchedule->start_time->format('h:i A') }} -
                        {{ $batch->trainingSchedule->end_time->format('h:i A') }} |
                        <strong>Venue:</strong> {{ $batch->trainingSchedule->venue }} |
                        <strong>Instructor:</strong> {{ $batch->trainingSchedule->instructor }}
                    </div>
                </div>
            @endif
        </div> <!-- Closing card -->

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                @if (!$batch->is_full)
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
                        <i class="bi bi-person-plus"></i> Add Applicant
                    </button>
                @endif
                {{-- Check if batch has a training schedule and applications --}}
                @if ($batch->trainingSchedule && $batch->trainingSchedule->applications->count() > 0)
                    {{-- Check if notifications were already sent --}}
                    @if ($batch->trainingSchedule->schedule_notifications_sent_at)
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-check"></i> Schedule Notifications Sent
                        </button>
                    @else
                        {{-- Show normal send button when not sent yet --}}
                        <form action="{{ route('admin.training-schedules.send-schedule', $batch->trainingSchedule) }}"
                            method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Send training schedule notifications to all {{ $batch->trainingSchedule->applications->count() }} applicant(s) in this schedule?')">
                                <i class="fas fa-envelope"></i> Send Schedule Notifications
                            </button>
                        </form>
                    @endif
                @endif

                @if ($batch->is_full && !$batch->hasSchedule())
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="bi bi-calendar-plus"></i> Create Schedule
                    </button>
                @endif

                @if ($batch->status !== 'completed')
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeBatchModal">
                        <i class="bi bi-check-circle-fill"></i> Mark Batch as Done
                    </button>
                @endif
            </div>
        </div>

        <!-- Trainees List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-0">Enrolled Trainees</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <!-- Bulk Action Buttons -->
                        <div class="btn-group" id="bulkActions" style="display: none;">
                            <button type="button" class="btn btn-success btn-sm" onclick="bulkComplete()">
                                <i class="bi bi-check-circle"></i> Complete Selected
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkFail()">
                                <i class="bi bi-x-circle"></i> Fail Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div> <!-- Closing card-header -->

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Reference Number</th>
                                <th>Training Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->applications as $index => $application)
                                <tr>
                                    <td>
                                        @if ($application->training_status === 'ongoing')
                                            <input type="checkbox" class="trainee-checkbox" value="{{ $application->id }}"
                                                onchange="toggleBulkActions()">
                                        @endif
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $application->firstname }} {{ $application->surname }}</td>
                                    <td>{{ $application->user->email }}</td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        @if ($application->reference_number)
                                            <span class="badge bg-success">
                                                {{ $application->reference_number }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                Missing
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($application->training_status === 'enrolled') bg-primary
                                            @elseif($application->training_status === 'ongoing') bg-warning
                                            @elseif($application->training_status === 'completed') bg-success
                                            @elseif($application->training_status === 'failed') bg-danger @endif">
                                            {{ strtoupper($application->training_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.applications.show', $application) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
{{-- 
                                        @if ($application->training_status === 'ongoing')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                data-bs-target="#completeTrainingModal{{ $application->id }}">
                                                <i class="bi bi-check-circle"></i> Complete
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#failTrainingModal{{ $application->id }}">
                                                <i class="bi bi-x-circle"></i> Fail
                                            </button>
                                        @endif --}}

                                        @if (
                                            $batch->status !== 'completed' &&
                                                !in_array($application->training_status, ['completed', 'failed']) &&
                                                $application->training_status !== 'ongoing')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#removeApplicantModal{{ $application->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No trainees enrolled in this batch yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> <!-- Closing card-body -->
        </div> <!-- Closing card -->

        <!-- Create Schedule Modal -->
        @if ($batch->is_full && !$batch->hasSchedule())
            <div class="modal fade" id="createScheduleModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.schedules.store') }}">
                            @csrf
                            <input type="hidden" name="training_batch_id" value="{{ $batch->id }}">
                            <input type="hidden" name="nc_program" value="{{ $batch->nc_program }}">
                            <input type="hidden" name="max_students" value="{{ $batch->max_students }}">

                            <div class="modal-header">
                                <h5 class="modal-title">Create Schedule for Batch {{ $batch->batch_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Schedule Name</label>
                                        <input type="text" name="schedule_name" class="form-control"
                                            value="Batch {{ $batch->batch_number }} Schedule" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" name="start_time" class="form-control" value="08:00"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">End Time</label>
                                        <input type="time" name="end_time" class="form-control" value="17:00"
                                            required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Days</label>
                                        <input type="text" name="days" class="form-control"
                                            placeholder="e.g., Monday-Friday" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Venue</label>
                                        <input type="text" name="venue" class="form-control" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Instructor</label>
                                        <input type="text" name="instructor" class="form-control" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Description (Optional)</label>
                                        <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Create Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Complete Batch Modal -->
        <div class="modal fade" id="completeBatchModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.trainees.batch.complete', $batch) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Mark Batch as Done</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Important:</strong> All trainees must have a result (Completed or Failed) before
                                marking this batch as done.
                            </div>
                            <p>Are you sure you want to mark <strong>{{ $batch->nc_program }} - Batch
                                    {{ $batch->batch_number }}</strong> as completed?</p>
                            <p class="text-muted">This batch will be moved to Training History.</p>

                            <div class="mt-3">
                                <strong>Current Status:</strong>
                                <ul class="mt-2">
                                    <li>Total Trainees: {{ $batch->applications->count() }}</li>
                                    <li>Completed: <span class="badge bg-success">{{ $completedCount }}</span></li>
                                    <li>Failed: <span class="badge bg-danger">{{ $failedCount }}</span></li>
                                    <li>Pending: <span
                                            class="badge bg-warning">{{ $batch->applications->count() - ($completedCount + $failedCount) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, Mark as Done</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Applicant Modal -->
        <div class="modal fade" id="addApplicantModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.trainees.batch.add-applicant', $batch) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Applicant to Batch {{ $batch->batch_number }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if ($availableApplicants->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Select Applicant</label>
                                    <select name="application_id" class="form-select" required>
                                        <option value="">-- Select Applicant --</option>
                                        @foreach ($availableApplicants as $applicant)
                                            <option value="{{ $applicant->id }}">
                                                {{ $applicant->firstname }} {{ $applicant->surname }}
                                                ({{ $applicant->user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert alert-info">
                                    <small>
                                        <i class="bi bi-info-circle"></i>
                                        Only showing approved applicants for <strong>{{ $batch->nc_program }}</strong>
                                        who are not enrolled in any batch.
                                    </small>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No available applicants found for this program.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            @if ($availableApplicants->count() > 0)
                                <button type="submit" class="btn btn-primary">Add Applicant</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Individual Modals (Complete, Fail, Remove) -->
        @foreach ($batch->applications as $application)
            <!-- Complete Training Modal -->
            <div class="modal fade" id="completeTrainingModal{{ $application->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.applications.complete-training', $application) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Mark Training as Completed</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Mark training as completed for
                                    <strong>{{ $application->firstname }} {{ $application->surname }}</strong>?
                                </p>
                                <div class="mb-3">
                                    <label class="form-label">Remarks (Optional)</label>
                                    <textarea name="training_remarks" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Mark as Completed</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Fail Training Modal -->
            {{-- <div class="modal fade" id="failTrainingModal{{ $application->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.applications.fail-training', $application) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Mark Training as Failed</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Mark training as failed for
                                    <strong>{{ $application->firstname }} {{ $application->surname }}</strong>?
                                </p>
                                <div class="mb-3">
                                    <label class="form-label">Reason (Required)</label>
                                    <textarea name="training_remarks" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Mark as Failed</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> --}}

            <!-- Remove Applicant Modal -->
            <div class="modal fade" id="removeApplicantModal{{ $application->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST"
                            action="{{ route('admin.trainees.batch.remove-applicant', [$batch, $application]) }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Remove Applicant from Batch</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This action will remove the applicant from the batch.
                                </div>
                                <p>Are you sure you want to remove <strong>{{ $application->firstname }}
                                        {{ $application->surname }}</strong> from this batch?</p>
                                <p class="text-muted"><small>The applicant's training status will be reset and they can be
                                        enrolled in another batch later.</small></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Remove Applicant</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Quick Complete Modal -->
        <div class="modal fade" id="quickCompleteModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="quickCompleteForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h6 class="modal-title">Mark as Completed</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Mark <strong id="completeTraineeName"></strong> as completed?</p>
                            <div class="mb-2">
                                <label class="form-label small">Quick Remarks</label>
                                <select name="training_remarks" class="form-select form-select-sm">
                                    <option value="">No remarks</option>
                                    <option value="Successfully completed all requirements">Successfully completed all
                                        requirements</option>
                                    <option value="Excellent performance">Excellent performance</option>
                                    <option value="Good performance">Good performance</option>
                                    <option value="Satisfactory performance">Satisfactory performance</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer p-2">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success">Complete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Fail Modal -->
        <div class="modal fade" id="quickFailModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="quickFailForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h6 class="modal-title">Mark as Failed</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Mark <strong id="failTraineeName"></strong> as failed?</p>
                            <div class="mb-2">
                                <label class="form-label small">Reason <span class="text-danger">*</span></label>
                                <select name="training_remarks" class="form-select form-select-sm" required>
                                    <option value="">Select reason</option>
                                    <option value="Poor attendance">Poor attendance</option>
                                    <option value="Failed assessments">Failed assessments</option>
                                    <option value="Incomplete requirements">Incomplete requirements</option>
                                    <option value="Behavioral issues">Behavioral issues</option>
                                    <option value="Other">Other (specify below)</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <textarea name="additional_remarks" class="form-control form-control-sm" rows="2"
                                    placeholder="Additional details (optional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer p-2">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger">Mark as Failed</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Action Modal -->
        <div class="modal fade" id="bulkActionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="bulkActionForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkActionTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="bulkActionContent"></div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="bulk_remarks" class="form-control" rows="3"
                                    placeholder="Enter remarks for all selected trainees" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" id="bulkActionBtn"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- Closing container-fluid -->

    <!-- JavaScript -->
    <script>
        // Quick action functions
        function quickComplete(applicationId, traineeName) {
            document.getElementById('completeTraineeName').textContent = traineeName;
            document.getElementById('quickCompleteForm').action = '/admin/applications/' + applicationId +
                '/complete-training';
            new bootstrap.Modal(document.getElementById('quickCompleteModal')).show();
        }

        function quickFail(applicationId, traineeName) {
            document.getElementById('failTraineeName').textContent = traineeName;
            document.getElementById('quickFailForm').action = '/admin/applications/' + applicationId + '/fail-training';
            new bootstrap.Modal(document.getElementById('quickFailModal')).show();
        }

        // Bulk selection functions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.trainee-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            toggleBulkActions();
        }

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');

            if (checkedBoxes.length > 0) {
                bulkActions.style.display = 'inline-block';
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function bulkComplete() {
            const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
            const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (applicationIds.length === 0) {
                alert('Please select trainees to complete.');
                return;
            }

            document.getElementById('bulkActionTitle').textContent = 'Bulk Complete Training';
            document.getElementById('bulkActionContent').innerHTML =
                `<p>Mark <strong>${applicationIds.length}</strong> selected trainee(s) as completed?</p>`;
            document.getElementById('bulkActionBtn').textContent = 'Complete All';
            document.getElementById('bulkActionBtn').className = 'btn btn-success';

            // Set form action and hidden inputs
            document.getElementById('bulkActionForm').action = '{{ route('admin.applications.bulk-complete-training') }}';

            // Add hidden inputs for application IDs
            const form = document.getElementById('bulkActionForm');
            // Remove existing hidden inputs
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());

            applicationIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
        }

        function bulkFail() {
            const checkedBoxes = document.querySelectorAll('.trainee-checkbox:checked');
            const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (applicationIds.length === 0) {
                alert('Please select trainees to fail.');
                return;
            }

            document.getElementById('bulkActionTitle').textContent = 'Bulk Fail Training';
            document.getElementById('bulkActionContent').innerHTML =
                `<p>Mark <strong>${applicationIds.length}</strong> selected trainee(s) as failed?</p>`;
            document.getElementById('bulkActionBtn').textContent = 'Fail All';
            document.getElementById('bulkActionBtn').className = 'btn btn-danger';

            // Set form action and hidden inputs
            document.getElementById('bulkActionForm').action = '{{ route('admin.applications.bulk-fail-training') }}';

            // Add hidden inputs for application IDs
            const form = document.getElementById('bulkActionForm');
            // Remove existing hidden inputs
            form.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());

            applicationIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
        }

        // Auto-submit forms after selection (optional enhancement)
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit quick complete form when remarks are selected
            const quickCompleteSelect = document.querySelector(
                '#quickCompleteModal select[name="training_remarks"]');
            if (quickCompleteSelect) {
                quickCompleteSelect.addEventListener('change', function() {
                    if (this.value && this.value !== '') {
                        // Optional: Auto-submit after 1 second delay
                        setTimeout(() => {
                            if (confirm('Auto-complete with selected remarks?')) {
                                document.getElementById('quickCompleteForm').submit();
                            }
                        }, 1000);
                    }
                });
            }
        });
    </script>
@endsection
