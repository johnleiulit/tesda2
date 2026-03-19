@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">Training History</h2>
                <p class="text-muted">Completed training batches</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by NC Program</label>
                        <select name="nc_program" class="form-select" onchange="this.form.submit()">
                            <option value="">All Programs</option>
                            @foreach($availablePrograms as $program)
                                <option value="{{ $program }}" {{ request('nc_program') == $program ? 'selected' : '' }}>
                                    {{ $program }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request('nc_program'))
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('admin.history.training') }}" class="btn btn-secondary w-100">Clear Filter</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Completed Batches Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Completed Training Batches</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>NC Program</th>
                                <th>Batch</th>
                                <th>Instructor</th>
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
                                        @if($batch->trainingSchedule)
                                            {{ $batch->trainingSchedule->instructor }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $batch->completed_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $batch->failed_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.history.training.batch', $batch) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No completed training batches found.
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
