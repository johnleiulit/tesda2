@extends('layouts.admin')

@section('title', 'Employment Feedback - TESDA')
@section('page-title', 'Employment Feedback')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>TWSP Training Batches - Employment Tracking</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>NC Program</th>
                            <th>Batch Number</th>
                            <th>Completed Applicants</th>
                            <th>With Employment</th>
                            <th>Without Employment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            @php
                                $stats = $batchStats[$batch->id] ?? [
                                    'total' => 0,
                                    'with_employment' => 0,
                                    'without_employment' => 0,
                                ];
                            @endphp
                            <tr>
                                <td>
                                    {{ $batch->nc_program }} - Batch {{ $batch->batch_number }}
                                    @if ($batchStats[$batch->id]['new_employment'] > 0)
                                        <span class="badge bg-danger ms-2">
                                            {{ $batchStats[$batch->id]['new_employment'] }} NEW
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $batch->nc_program }}</td>
                                <td>Batch {{ $batch->batch_number }}</td>
                                <td><span class="badge bg-info">{{ $stats['total'] }}</span></td>
                                <td><span class="badge bg-success">{{ $stats['with_employment'] }}</span></td>
                                <td><span class="badge bg-warning">{{ $stats['without_employment'] }}</span></td>
                                <td>
                                    <a href="{{ route('admin.employment-feedback.show', $batch->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No completed TWSP training batches found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $batches->links() }}
        </div>
    </div>
@endsection
