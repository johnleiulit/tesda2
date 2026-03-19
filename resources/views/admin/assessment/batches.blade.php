@extends('layouts.admin')

@section('title', 'Assessment Batches - TESDA')
@section('page-title', 'Assessment Batches')

@section('content')

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card border-secondary h-100 ">
                <div class="card-body text-center ">
                    <h5>Total Eligible</h5>
                    <h2>{{ $eligibleGrouped->flatten()->count() }}</h2>
                    <small class="text-muted">All NC Programs</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <h6>EVENTS MANAGEMENT SERVICES NC III</h6>
                    <h2 class="text-success">
                        {{ $eligibleGrouped->get('EVENTS MANAGEMENT SERVICES NC III', collect())->count() }}
                    </h2>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <h6>TOURISM PROMOTION SERVICES NC II</h6>
                    <h2 class="text-info">
                        {{ $eligibleGrouped->get('TOURISM PROMOTION SERVICES NC II', collect())->count() }}
                    </h2>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <h6>BOOKKEEPING NC III</h6>
                    <h2 class="text-primary">
                        {{ $eligibleGrouped->get('BOOKKEEPING NC III', collect())->count() }}
                    </h2>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <h6>PHARMACY SERVICES NC III</h6>
                    <h2 class="text-warning">
                        {{ $eligibleGrouped->get('PHARMACY SERVICES NC III', collect())->count() }}
                    </h2>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>
         <div class="col-md-4">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <h6>VISUAL GRAPHIC DESIGN NC III</h6>
                    <h2 class="text-danger">
                        {{ $eligibleGrouped->get('VISUAL GRAPHIC DESIGN NC III', collect())->count() }}
                    </h2>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>

    </div>


    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Assessment Batches</h5>
            <a href="{{ route('admin.assessment-batches.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Batch
            </a>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-4">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                        </option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="nc_program" class="form-select" onchange="this.form.submit()">
                        <option value="">All NC Programs</option>
                        @foreach ($availablePrograms as $program)
                            <option value="{{ $program }}" {{ request('nc_program') == $program ? 'selected' : '' }}>
                                {{ $program }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Batch Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>NC Program</th>
                            <th>Date</th>
                            <th>Assessor</th>
                            <th>Status</th>
                            <th>Applicants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>{{ $batch->batch_name }}</td>
                                <td>{{ $batch->nc_program }}</td>
                                <td>{{ \Carbon\Carbon::parse($batch->assessment_date)->format('M d, Y') }}</td>
                                <td>{{ $batch->assessor_name ?? 'N/A' }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $batch->status == 'completed'
                                            ? 'success'
                                            : ($batch->status == 'ongoing'
                                                ? 'warning'
                                                : ($batch->status == 'cancelled'
                                                    ? 'danger'
                                                    : 'primary')) }}">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-info">{{ $batch->applications_count }}/{{ $batch->max_applicants }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.assessment-batches.show', $batch->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.assessment-batches.destroy', $batch->id) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this batch?')"
                                            {{ $batch->applications_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No assessment batches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
@endsection