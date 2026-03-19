@extends('layouts.admin')

@section('title', 'Create Assessment Batch - TESDA')
@section('page-title', 'Create Assessment Batch')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @error('nc_program')
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Cannot Create Batch:</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @enderror

    <div class="card">
        <div class="card-header">
            <h5>Create New Assessment Batch</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.assessment-batches.store') }}">
                @csrf

                <div class="row g-3">
                    <!-- NC Program Selection -->
                    <div class="col-md-6">
                        <label class="form-label">NC Program <span class="text-danger">*</span></label>
                        <select name="nc_program" id="nc_program" class="form-select" required>
                            <option value="">Select NC Program</option>
                            @foreach ($ncPrograms as $program)
                                @php $count = $eligibleCounts[$program] ?? 0; @endphp
                                @if ($count === 0)
                                    @continue
                                @endif

                                <option value="{{ $program }}">
                                    {{ $program }} ({{ $count }} eligible)
                                </option>
                            @endforeach
                        </select>
                        {{-- @error('nc_program')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <!-- Batch Name -->
                    <div class="col-md-6">
                        <label class="form-label">Batch Name <span class="text-danger">*</span></label>
                        <input type="text" name="batch_name" class="form-control"
                            placeholder="Leave empty to auto-generate" value="{{ old('batch_name') }}">
                        @error('batch_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Auto-generated if left empty</small>
                    </div>
                    <!-- Intensive Review Day 1 -->
                    <div class="col-12 mt-3">
                        <h6 class="text-primary">Intensive Review Day 1</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Review Day 1 Date</label>
                        <input type="date" name="intensive_review_day1" class="form-control"
                            value="{{ old('intensive_review_day1') }}" min="{{ now()->toDateString() }}" required>
                        @error('intensive_review_day1')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Day 1 Start Time</label>
                        <input type="time" name="intensive_review_day1_start" class="form-control"
                            value="{{ old('intensive_review_day1_start', '08:00') }}">
                        @error('intensive_review_day1_start')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Day 1 End Time</label>
                        <input type="time" name="intensive_review_day1_end" class="form-control"
                            value="{{ old('intensive_review_day1_end', '17:00') }}">
                        @error('intensive_review_day1_end')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Intensive Review Day 2 -->
                    <div class="col-12 mt-3">
                        <h6 class="text-primary">Intensive Review Day 2</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Review Day 2 Date</label>
                        <input type="date" name="intensive_review_day2" class="form-control"
                            value="{{ old('intensive_review_day2') }}" min="{{ now()->toDateString() }}" required>
                        @error('intensive_review_day2')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Day 2 Start Time</label>
                        <input type="time" name="intensive_review_day2_start" class="form-control"
                            value="{{ old('intensive_review_day2_start', '08:00') }}">
                        @error('intensive_review_day2_start')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Day 2 End Time</label>
                        <input type="time" name="intensive_review_day2_end" class="form-control"
                            value="{{ old('intensive_review_day2_end', '17:00') }}">
                        @error('intensive_review_day2_end')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Assessment Date -->
                    <div class="col-12 mt-3">
                        <h6 class="text-primary">Intensive Review Day 1</h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Assessment Date <span class="text-danger">*</span></label>
                        <input type="date" name="assessment_date" class="form-control"
                            value="{{ old('assessment_date') }}" required min="{{ date('Y-m-d') }}">
                        @error('assessment_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div class="col-md-4">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control"
                            value="{{ old('start_time', '08:00') }}" required>
                        @error('start_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div class="col-md-4">
                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control"
                            value="{{ old('end_time', '17:00') }}" required>
                        @error('end_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Venue -->
                    <div class="col-md-6">
                        <label class="form-label">Venue <span class="text-danger">*</span></label>
                        <input type="text" name="venue" class="form-control" placeholder="Assessment venue"
                            value="{{ old('venue') }}" required>
                        @error('venue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Assessor -->
                    <div class="col-md-6">
                        <label class="form-label">Assessor Name</label>
                        <input type="text" name="assessor_name" class="form-control"
                            placeholder="Enter assessor name" value="{{ old('assessor_name') }}">
                        @error('assessor_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Auto-Assignment:</strong> Eligible applicants from the selected NC program will be automatically
                    assigned to this batch based on their training completion date.
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.assessment-batches.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Assessment Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
