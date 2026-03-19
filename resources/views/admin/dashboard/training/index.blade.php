{{-- resources/views/admin/dashboard/training/index.blade.php --}}
<div class="card analytics-card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="bi bi-mortarboard me-2"></i>Training Analytics
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Left: Pie Chart - Completed vs Failed --}}
            <div class="col-md-4">
                <h6 class="text-muted mb-3 text-center">Training Completion Rate</h6>
                
                @php
                    $completedCount = $training['completed_count'] ?? 0;
                    $failedCount = $training['failed_count'] ?? 0;
                    $total = $training['total_assessed'] ?? 0;
                    
                    $completedPercentage = $training['completed_percentage'] ?? 0;
                    $failedPercentage = $training['failed_percentage'] ?? 0;
                @endphp
                
                {{-- Pie Chart Container --}}
                <div class="d-flex justify-content-center mb-3">
                    <div class="position-relative" style="width: 200px; height: 200px;">
                        <canvas id="trainingPieChart"></canvas>
                    </div>
                </div>
                
                {{-- Legend --}}
                <div class="text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <span class="badge bg-success me-2" style="width: 15px; height: 15px;"></span>
                        <span class="small">Completed: <strong>{{ $completedCount }}</strong> ({{ $completedPercentage }}%)</span>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="badge bg-danger me-2" style="width: 15px; height: 15px;"></span>
                        <span class="small">Failed: <strong>{{ $failedCount }}</strong> ({{ $failedPercentage }}%)</span>
                    </div>
                    <div class="mt-3">
                        <div class="text-muted small">Total Assessed</div>
                        <div class="fw-bold fs-4 text-primary">{{ $total }}</div>
                    </div>
                </div>
            </div>
            
            {{-- Right: Batch Performance & Metrics --}}
            <div class="col-md-8">
                
                {{-- Batch Performance --}}
                <div>
                    <h6 class="text-muted mb-3">Batch Performance</h6>
                    @if(isset($training['batches']) && count($training['batches']) > 0)
                        @foreach($training['batches'] as $batch)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold">Batch {{ $batch['batch_number'] }}</small>
                                    <small class="text-muted">{{ $batch['success_rate'] }}% Success</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $batch['success_rate'] }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <span>{{ $batch['completed'] }} Completed</span>
                                    <span>{{ $batch['failed'] }} Failed</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted small">No active batches</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Script --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trainingPieChart');
    
    if (ctx) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Failed'],
                datasets: [{
                    data: [{{ $completedCount }}, {{ $failedCount }}],
                    backgroundColor: [
                        '#198754', // Green for completed
                        '#dc3545'  // Red for failed
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = {{ $total }};
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
