@extends('layouts.admin')

@section('title', 'Assessment History - TESDA')
@section('page-title', 'Assessment History')

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Completed Assessment Batches</h5>
        <a href="{{ route('admin.assessment-batches.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Batches
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-3">
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

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Batch Name</th>
                        <th>NC Program</th>
                        <th>Date</th>
                        <th>Assessor</th>
                        <th>Applicants</th>
                        <th>Pass</th>
                        <th>Fail</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        @php
                            $stat = $results[$batch->id] ?? null;
                        @endphp
                        <tr>
                            <td>{{ $batch->batch_name }}</td>
                            <td>{{ $batch->nc_program }}</td>
                            <td>{{ \Carbon\Carbon::parse($batch->assessment_date)->format('M d, Y') }}</td>
                            <td>{{ $batch->assessor_name ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $stat->total_applicants ?? 0 }}</span></td>
                            <td><span class="badge bg-success">{{ $stat->passed ?? 0 }}</span></td>
                            <td><span class="badge bg-danger">{{ $stat->failed ?? 0 }}</span></td>
                            <td>
                                <a href="{{ route('admin.assessment-batches.show', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No completed batches yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $batches->withQueryString()->links() }}
    </div>
</div>
@endsection