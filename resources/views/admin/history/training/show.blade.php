@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.history.training') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Training History
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
                        <span class="badge bg-secondary">COMPLETED</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-light text-dark fs-6 me-2">
                            {{ $totalTrainees }} Total Trainees
                        </span>
                        <span class="badge bg-success me-1">{{ $completedCount }} Completed</span>
                        <span class="badge bg-danger me-1">{{ $failedCount }} Failed</span>
                        <span class="badge bg-info">{{ $passRate }}% Pass Rate</span>
                    </div>
                    <div class="col-md-8">
                        <p><strong><i class="bi bi-calendar-check"></i> Schedule: </strong>
                            {{ $batch->trainingSchedule->start_date->format('M d, Y') }} -  
                            {{ $batch->trainingSchedule->end_date->format('M d, Y') }}
                            ( {{ $batch->trainingSchedule->days }} )
                            {{ $batch->trainingSchedule->start_time->format('h:i A') }} - 
                            {{ $batch->trainingSchedule->end_time->format('h:i A') }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>
                            <strong>Venue: </strong>{{ $batch->trainingSchedule->venue }}<br>
                            <strong>Instructor: </strong>{{ $batch->trainingSchedule->instructor }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Total Trainees</h6>
                        <h3 class="fw-bold mb-0">{{ $totalTrainees }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-success">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Completed</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $completedCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Failed</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $failedCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-info">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Pass Rate</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $passRate }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trainees List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Training Results</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Result</th>
                                <th>Completed Date</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->applications as $index => $application)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $application->firstname }} {{ $application->surname }}</td>
                                    <td>{{ $application->user->email }}</td>
                                    <td>
                                        <span class="badge 
                                            @if ($application->training_status === 'completed') bg-success
                                            @elseif($application->training_status === 'failed') bg-danger
                                            @else bg-secondary @endif">
                                            {{ strtoupper($application->training_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($application->training_completed_at)
                                            {{ $application->training_completed_at->format('M d, Y') }}
                                        @elseif($application->trainingResult && $application->trainingResult->completed_at)
                                            {{ $application->trainingResult->completed_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($application->training_remarks)
                                            <small>{{ Str::limit($application->training_remarks, 50) }}</small>
                                        @elseif($application->trainingResult && $application->trainingResult->remarks)
                                            <small>{{ Str::limit($application->trainingResult->remarks, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.applications.show', $application) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No training results found.
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
