{{-- resources/views/admin/dashboard/employment/index.blade.php --}}
<div class="card analytics-card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-briefcase me-2"></i>Employment Feedback Analytics
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Employment Rate --}}
            <div class="col-md-3">
                <div class="text-center">
                    <div class="text-success">
                        <i class="bi bi-person-check fs-1"></i>
                        <div class="fw-bold fs-4">{{ $employment['employment_rate'] ?? 0 }}%</div>
                        <div class="text-muted">Employment Rate</div>
                    </div>
                </div>
            </div>
            
            {{-- Average Income --}}
            <div class="col-md-3">
                <div class="text-center">
                    <div class="text-primary">
                        <i class="bi bi-currency-dollar fs-1"></i>
                        <div class="fw-bold fs-4">₱{{ number_format($employment['avg_income'] ?? 0) }}</div>
                        <div class="text-muted">Avg. Monthly Income</div>
                    </div>
                </div>
            </div>
            
            {{-- Employment Sectors --}}
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Employment by Sector</h6>
                @if(isset($employment['sectors']) && count($employment['sectors']) > 0)
                    @foreach($employment['sectors'] as $sector)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">{{ $sector['name'] }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ $sector['percentage'] }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $sector['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted small">No employment data available</p>
                @endif
            </div>
        </div>
    </div>
</div>
