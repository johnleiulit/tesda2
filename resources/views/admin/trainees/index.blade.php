@extends('layouts.admin')
@section('title', 'Training Batches - TESDA')
@section('page-title', 'Training Batches Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <p class="text-muted">Overview of all training batches</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Batch</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_batches'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Enrolled</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_enrolled'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Full Batch</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['full_batches'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-warning">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Backouts</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['backout_count'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backout Applicants Section -->
        @if ($backoutApplicants->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning bg-opacity-10 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Backout Applicants ({{ $backoutApplicants->count() }})
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#backoutTable">
                            <i class="bi bi-chevron-down"></i> Toggle
                        </button>
                    </div>
                </div>
                <div class="collapse show" id="backoutTable">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            These applicants were removed from their batches and are available for re-enrollment.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NC Program</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($backoutApplicants as $index => $applicant)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $applicant->firstname }} {{ $applicant->surname }}</td>
                                            <td>{{ $applicant->user->email }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $applicant->title_of_assessment_applied_for }}
                                                </span>
                                            </td>
                                            <td>{{ $applicant->updated_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <a href="{{ route('admin.applications.show', $applicant) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Batches Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">All Training Batches</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="text-light bg-primary">
                            <tr>
                                <th>#</th>
                                <th>NC Program</th>
                                <th>Batch</th>
                                <th>Status</th>
                                <th>Enrolled</th>
                                <th>Completed</th>
                                <th>Failed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $index => $batch)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $batch->nc_program }}</td>
                                    <td>Batch {{ $batch->batch_number }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($batch->status === 'scheduled') bg-info
                                            @elseif($batch->status === 'ongoing') bg-warning
                                            @elseif($batch->status === 'completed') bg-secondary
                                            @elseif($batch->is_full) bg-success
                                            @else bg-primary @endif">
                                            {{ $batch->is_full && $batch->status === 'enrolling' ? 'FULL' : strtoupper($batch->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $batch->enrolled_count }}/{{ $batch->max_students }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $batch->completed_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $batch->failed_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.trainees.batch.show', $batch) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No training batches found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
