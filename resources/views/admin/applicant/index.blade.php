@extends('layouts.admin')

@section('title', 'Admin Dashboard - TESDA')
@section('page-title', 'Applicants Management')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Applications</h1>
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Resubmitted</h5>
                        <h2>{{ $resubmittedCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Payment </h5>
                        <h2>{{ $firstPaymentCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
        </br>
        <!-- Application Type Tabs -->
        <ul class="nav nav-tabs mb-3" id="applicationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="twsp-tab" data-bs-toggle="tab" data-bs-target="#twsp" type="button"
                    role="tab">
                    <i class="bi bi-mortarboard"></i> TWSP Applications
                    <span class="badge bg-primary">{{ $twspApps->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="assessment-tab" data-bs-toggle="tab" data-bs-target="#assessment"
                    type="button" role="tab">
                    <i class="bi bi-clipboard-check"></i> Assessment Only
                    <span class="badge bg-info">{{ $assessmentApps->total() }}</span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="applicationTabsContent">
            <!-- TWSP Tab -->
            <div class="tab-pane fade show active" id="twsp" role="tabpanel">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-primary text-light">
                                <tr>
                                    <th>Applicant</th>
                                    <th>Title of Assessment</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th style="width:180px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($twspApps as $app)
                                    <tr class="{{ $app->isUnviewed() ? 'table-warning' : '' }}">
                                        <td>
                                            @if ($app->isUnviewed())
                                                <span class="badge bg-danger me-1">NEW</span>
                                            @endif
                                            @if ($app->was_corrected)
                                                <span class="badge bg-danger me-1">
                                                    <i class="fas fa-redo"></i> RESUBMITTED
                                                </span>
                                            @endif
                                            @if ($app->application_type === 'Assessment Only' && in_array($app->payment_status, ['pending', 'submitted']))
                                                <span class="badge bg-warning me-1">
                                                    <i class="fas fa-money-bill-wave"></i>PAYMENT
                                                </span>
                                            @endif
                                            {{ $app->surname }}, {{ $app->firstname }}
                                            {{-- {{ $app->user?->name ?? '—' }} --}}
                                        </td>
                                        <td>{{ $app->title_of_assessment_applied_for }}</td>
                                        <td>
                                            @php
                                                $map = [
                                                    'pending' => 'secondary',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $map[$app->status] ?? 'secondary' }}">
                                                {{ ucfirst($app->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $app->created_at?->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.applications.show', $app) }}"
                                                    class="btn btn-sm btn-outline-primary">View</a>

                                                @if ($app->status === \App\Models\Application::STATUS_PENDING)
                                                    <form method="POST"
                                                        action="{{ route('admin.applications.approve', $app) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Approve?')">Approve</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('admin.applications.reject', $app) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Reject?')">Reject</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4">No TWSP applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($twspApps->hasPages())
                        <div class="card-footer">
                            {{ $twspApps->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assessment Tab -->
            <div class="tab-pane fade" id="assessment" role="tabpanel">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-primary text-light">
                                <tr>
                                    <th>Applicant</th>
                                    <th>Title of Assessment</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th style="width:180px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessmentApps as $app)
                                    <tr class="{{ $app->isUnviewed() ? 'table-warning' : '' }}">
                                        <td>
                                            @if ($app->isUnviewed())
                                                <span class="badge bg-danger me-1">NEW</span>
                                            @endif
                                            @if ($app->was_corrected)
                                                <span class="badge bg-danger me-1">
                                                    <i class="fas fa-redo"></i> RESUBMITTED
                                                </span>
                                            @endif
                                            @if ($app->application_type === 'Assessment Only' && $app->payment_status === 'submitted' && $app->payment_proof)
                                                <span class="badge bg-warning me-1">
                                                    <i class="fas fa-money-bill-wave"></i>PAYMENT
                                                </span>
                                            @endif
                                            {{ $app->surname }}, {{ $app->firstname }}
                                        </td>
                                        <td>{{ $app->title_of_assessment_applied_for }}</td>
                                        <td>
                                            @php
                                                $map = [
                                                    'pending' => 'secondary',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $map[$app->status] ?? 'secondary' }}">
                                                {{ ucfirst($app->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $app->created_at?->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.applications.show', $app) }}"
                                                    class="btn btn-sm btn-outline-primary">View</a>

                                                @if ($app->status === \App\Models\Application::STATUS_PENDING)
                                                    <form method="POST"
                                                        action="{{ route('admin.applications.approve', $app) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('Approve?')">Approve</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('admin.applications.reject', $app) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Reject?')">Reject</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4">No Assessment applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($assessmentApps->hasPages())
                        <div class="card-footer">
                            {{ $assessmentApps->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
