@extends('layouts.admin')

@section('title', 'Employment Feedback - TESDA')
@section('page-title', 'Employment Feedback - ' . $batch->nc_program . ' Batch ' . $batch->batch_number)

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5>{{ $batch->nc_program }} - Batch {{ $batch->batch_number }}</h5>
                <small class="text-muted">Completed Training Applicants</small>
            </div>
            <a href="{{ route('admin.employment-feedback.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Batches
            </a>
        </div>

        <!-- Search Bar -->
        <div class="card-header bg-light ">
            <input type="text" id="searchInput" value="{{ request('q') }}" class="form-control"
                placeholder="Search by applicant name...">
        </div>

        <!-- Applicants Block (for AJAX replacement) -->
        <div id="applicantsBlock">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Training Result</th>
                                <th>Assessment Result</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                @php
                                    $fullName = trim(
                                        $application->firstname .
                                            ' ' .
                                            ($application->middlename ? $application->middlename . ' ' : '') .
                                            $application->surname .
                                            ' ' .
                                            ($application->name_extension ?? ''),
                                    );

                                    // Get assessment result and map to Competent/Not Yet Competent
                                    $assessmentResult = $application->assessmentResult;
                                    if ($assessmentResult) {
                                        if ($assessmentResult->result === 'Competent') {
                                            $assessmentStatus = 'Competent';
                                            $assessmentBadgeClass = 'success';
                                        } elseif ($assessmentResult->result === 'Not Yet Competent') {
                                            $assessmentStatus = 'Not Yet Competent';
                                            $assessmentBadgeClass = 'danger';
                                        } else {
                                            $assessmentStatus = $assessmentResult->result;
                                            $assessmentBadgeClass = 'secondary';
                                        }
                                    } else {
                                        $assessmentStatus = 'N/A';
                                        $assessmentBadgeClass = 'secondary';
                                    }

                                    // Get training result
                                    $trainingResult = $application->trainingResult;
                                    $trainingStatus = $application->training_status
                                        ? ucfirst($application->training_status)
                                        : 'N/A';

                                    // Check if employment record exists
                                    $hasEmployment = $application->employmentRecord !== null;
                                @endphp
                                <tr>
                                    <td>
                                        {{ ($applications->currentPage() - 1) * $applications->perPage() + $loop->iteration }}
                                        @if ($hasEmployment && $application->employmentRecord->isNew())
                                            <span class="badge bg-danger ms-2">NEW</span>
                                        @endif
                                    </td>

                                    <td>{{ $fullName }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $application->training_status === 'completed' ? 'success' : 'secondary' }}">
                                            {{ $trainingStatus }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessmentBadgeClass }}">
                                            {{ $assessmentStatus }}
                                        </span>

                                        @if ($hasEmployment)
                                            <span class="badge bg-primary ms-1">Employed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($hasEmployment)
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewEmploymentModal{{ $application->id }}"
                                                data-employment-id="{{ $application->employmentRecord->id }}">
                                                <i class="bi bi-eye"></i> View/Edit
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addEmploymentModal{{ $application->id }}">
                                                <i class="bi bi-briefcase"></i> Employment
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Add Employment Modal -->
                                @if (!$hasEmployment)
                                    <div class="modal fade" id="addEmploymentModal{{ $application->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST"
                                                    action="{{ route('admin.employment-feedback.store', $application->id) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Add Employment Record</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Applicant Name</label>
                                                            <input type="text" class="form-control-plaintext"
                                                                value="{{ $fullName }}" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Date Employed <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="date_employed" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Occupation <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="occupation" class="form-control"
                                                                placeholder="e.g., Bookkeeper" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Name of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="employer_name" class="form-control"
                                                                placeholder="e.g., ABC Corporation" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Address of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea name="employer_address" class="form-control" rows="2" required></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Classification of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="employer_classification" class="form-select"
                                                                required>
                                                                <option value="">Select Classification</option>
                                                                <option value="Private">Private</option>
                                                                <option value="Government">Government</option>
                                                                <option value="NGO">NGO</option>
                                                                <option value="Self-Employed">Self-Employed</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Monthly Income/Salary <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="monthly_income" class="form-control"
                                                                step="0.01" min="0" placeholder="0.00" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- View/Edit Employment Modal -->
                                @if ($hasEmployment)
                                    <div class="modal fade" id="viewEmploymentModal{{ $application->id }}"
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST"
                                                    action="{{ route('admin.employment-feedback.update', $application->employmentRecord->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Employment Record</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Applicant Name</label>
                                                            <input type="text" class="form-control-plaintext"
                                                                value="{{ $fullName }}" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Date Employed <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="date_employed"
                                                                class="form-control"
                                                                value="{{ $application->employmentRecord->date_employed ? $application->employmentRecord->date_employed->format('Y-m-d') : '' }}"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Occupation <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="occupation" class="form-control"
                                                                value="{{ $application->employmentRecord->occupation ?? '' }}"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Name of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="employer_name"
                                                                class="form-control"
                                                                value="{{ $application->employmentRecord->employer_name ?? '' }}"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Address of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea name="employer_address" class="form-control" rows="2" required>{{ $application->employmentRecord->employer_address ?? '' }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Classification of Employer <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="employer_classification" class="form-select"
                                                                required>
                                                                <option value="">Select Classification</option>
                                                                <option value="Private"
                                                                    {{ ($application->employmentRecord->employer_classification ?? '') == 'Private' ? 'selected' : '' }}>
                                                                    Private</option>
                                                                <option value="Government"
                                                                    {{ ($application->employmentRecord->employer_classification ?? '') == 'Government' ? 'selected' : '' }}>
                                                                    Government</option>
                                                                <option value="NGO"
                                                                    {{ ($application->employmentRecord->employer_classification ?? '') == 'NGO' ? 'selected' : '' }}>
                                                                    NGO</option>
                                                                <option value="Self-Employed"
                                                                    {{ ($application->employmentRecord->employer_classification ?? '') == 'Self-Employed' ? 'selected' : '' }}>
                                                                    Self-Employed</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Monthly Income/Salary <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="monthly_income"
                                                                class="form-control" step="0.01" min="0"
                                                                value="{{ $application->employmentRecord->monthly_income ?? '' }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No completed applicants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3 mb-3" id="applicantsPagination">
                {{ $applications->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let timer;

        // Debounced live search
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                let query = this.value;
                let url =
                    `{{ route('admin.employment-feedback.show', $batch->id) }}?q=${encodeURIComponent(query)}`;

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        document.getElementById('applicantsBlock').innerHTML =
                            doc.querySelector('#applicantsBlock').innerHTML;

                        // Re-attach event listeners after AJAX reload
                        attachModalListeners();
                    })
                    .catch(error => console.error('Error fetching search results:', error));
            }, 400);
        });

        // Function to attach modal listeners
        function attachModalListeners() {
            document.querySelectorAll('[data-employment-id]').forEach(button => {
                button.addEventListener('click', function() {
                    const employmentId = this.getAttribute('data-employment-id');

                    // Mark as viewed when modal is opened
                    fetch(`{{ url('/admin/employment-feedback') }}/${employmentId}/mark-viewed`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove NEW badge from this row
                                const row = this.closest('tr');
                                const newBadge = row.querySelector('.badge.bg-danger');
                                if (newBadge && newBadge.textContent === 'NEW') {
                                    newBadge.remove();
                                }

                                // Update sidebar count (optional - will update on page refresh)
                                updateSidebarCount();
                            }
                        })
                        .catch(error => console.error('Error marking as viewed:', error));
                });
            });
        }

        // Function to update sidebar count
        function updateSidebarCount() {
            fetch('{{ route('admin.employment-feedback.index') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newCount = doc.querySelector('.sidebar .badge');
                    const currentBadge = document.querySelector('.sidebar .badge');

                    if (newCount && currentBadge) {
                        currentBadge.textContent = newCount.textContent;
                        if (newCount.textContent === '0') {
                            currentBadge.style.display = 'none';
                        }
                    }
                });
        }

        // Initial attachment of listeners
        document.addEventListener('DOMContentLoaded', function() {
            attachModalListeners();
        });
    </script>
@endsection
