{{-- resources/views/admin/dashboard/assessment/index.blade.php --}}
<div class="card analytics-card">
    <div class="card-header bg-success text-light">
        <h5 class="mb-0">
            <i class="bi bi-clipboard-check me-2"></i>Assessment Analytics
        </h5>
    </div>
    <div class="card-body">
        {{-- Assessment Metrics --}}
        <div class="row text-center mb-4">
            <div class="col-6">
                <div class="text-success">
                    <i class="bi bi-trophy fs-4"></i>
                    <div class="small">Competent</div>
                    <div class="fw-bold">{{ $assessment['competent_count'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-danger">
                    <i class="bi bi-x-circle fs-4"></i>
                    <div class="small">Not Yet Competent</div>
                    <div class="fw-bold">{{ $assessment['nyc_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Program Overview (Clickable) --}}
        <div class="mb-4">
            <h6 class="text-muted mb-3">Program Performance Overview</h6>
            @if (isset($assessment['programs']) && count($assessment['programs']) > 0)
                @foreach ($assessment['programs'] as $program)
                    <div class="mb-3">
                        <div class="card border-0 bg-light program-card" style="cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#cocModal{{ $loop->index }}">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-dark">{{ $program['name'] }}</strong>
                                        <small class="text-muted d-block">{{ count($program['coc_breakdown']) }}
                                            COCs</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">{{ $program['overall_competent_rate'] }}%
                                        </div>
                                        <small class="text-muted">{{ $program['total_assessments'] }} assessed</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $program['overall_competent_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted small">No assessment data available</p>
            @endif
        </div>

        {{-- Reassessment Analysis --}}
        <div>
            <h6 class="text-muted mb-3">Reassessment Analysis</h6>
            <div class="row text-center">
                <div class="col-4">
                    <div class="text-warning">
                        <div class="fw-bold">{{ $assessment['reassessment']['first'] ?? 0 }}</div>
                        <div class="small">1st Reassess</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-danger">
                        <div class="fw-bold">{{ $assessment['reassessment']['second'] ?? 0 }}</div>
                        <div class="small">2nd Reassess</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-success">
                        <div class="fw-bold">{{ $assessment['reassessment']['success_rate'] ?? 0 }}%</div>
                        <div class="small">Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced COC Detail Modals with Charts --}}
@if (isset($assessment['programs']) && count($assessment['programs']) > 0)
    @foreach ($assessment['programs'] as $program)
        <div class="modal fade" id="cocModal{{ $loop->index }}" tabindex="-1"
            aria-labelledby="cocModalLabel{{ $loop->index }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="cocModalLabel{{ $loop->index }}">
                            <i class="bi bi-clipboard-data me-2"></i>{{ $program['name'] }} - COC Performance Analysis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Program Summary --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-primary">
                                        <i class="bi bi-people fs-2"></i>
                                        <div class="fw-bold fs-4">{{ $program['total_assessments'] }}</div>
                                        <div class="small text-muted">Total Assessed</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-success">
                                        <i class="bi bi-trophy fs-2"></i>
                                        <div class="fw-bold fs-4">{{ $program['overall_competent_rate'] }}%</div>
                                        <div class="small text-muted">Overall Competent</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-danger">
                                        <i class="bi bi-x-circle fs-2"></i>
                                        <div class="fw-bold fs-4">{{ $program['overall_nyc_rate'] }}%</div>
                                        <div class="small text-muted">Overall NYC</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-info">
                                        <i class="bi bi-list-check fs-2"></i>
                                        <div class="fw-bold fs-4">{{ count($program['coc_breakdown']) }}</div>
                                        <div class="small text-muted">Total COCs</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Charts Section --}}
                        <div class="row mb-4">

                            {{-- LEFT: Performance Insights --}}
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Performance Insights</h6>

                                @php
                                    $bestCoc = collect($program['coc_breakdown'])
                                        ->sortByDesc('competent_rate')
                                        ->first();
                                    $worstCoc = collect($program['coc_breakdown'])->sortBy('competent_rate')->first();
                                @endphp

                                {{-- Best Performing --}}
                                <div class="card border-success mb-3">
                                    <div class="card-body text-center">
                                        <i class="bi bi-trophy text-success fs-3"></i>
                                        <h6 class="text-success mt-2">Best Performing COC</h6>
                                        <div class="fw-bold">{{ $bestCoc['coc_code'] ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            {{ $bestCoc['competent_rate'] ?? 0 }}% Success Rate
                                        </div>
                                    </div>
                                </div>

                                {{-- Needs Improvement --}}
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                                        <h6 class="text-warning mt-2">Needs Improvement</h6>
                                        <div class="fw-bold">{{ $worstCoc['coc_code'] ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            {{ $worstCoc['competent_rate'] ?? 0 }}% Success Rate
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: Donut Chart --}}
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="bi bi-pie-chart me-2"></i>Overall Success Rate
                                        </h6>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <canvas id="donutChart{{ $loop->index }}"
                                            style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Bar Chart - Competent vs NYC per COC --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Competent vs NYC
                                            Comparison</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="barChart{{ $loop->index }}" style="max-height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart.js Scripts for this program --}}
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Prepare data for {{ $program['name'] }}
                    const cocLabels{{ $loop->index }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'coc_code')) !!};
                    const competentData{{ $loop->index }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'competent_count')) !!};
                    const nycData{{ $loop->index }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'nyc_count')) !!};
                    const competentRates{{ $loop->index }} = {!! json_encode(array_column($program['coc_breakdown']->toArray(), 'competent_rate')) !!};

                    // 1. Radar Chart - COC Performance
                    const radarCtx{{ $loop->index }} = document.getElementById('radarChart{{ $loop->index }}');
                    if (radarCtx{{ $loop->index }}) {
                        new Chart(radarCtx{{ $loop->index }}, {
                            type: 'radar',
                            data: {
                                labels: cocLabels{{ $loop->index }},
                                datasets: [{
                                        label: 'Competent',
                                        data: competentData{{ $loop->index }},
                                        backgroundColor: 'rgba(25, 135, 84, 0.2)',
                                        borderColor: 'rgba(25, 135, 84, 1)',
                                        borderWidth: 2,
                                        pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                                        pointBorderColor: '#fff',
                                        pointHoverBackgroundColor: '#fff',
                                        pointHoverBorderColor: 'rgba(25, 135, 84, 1)'
                                    },
                                    {
                                        label: 'Not Yet Competent',
                                        data: nycData{{ $loop->index }},
                                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                                        borderColor: 'rgba(220, 53, 69, 1)',
                                        borderWidth: 2,
                                        pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                                        pointBorderColor: '#fff',
                                        pointHoverBackgroundColor: '#fff',
                                        pointHoverBorderColor: 'rgba(220, 53, 69, 1)'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Competent vs NYC per COC'
                                    }
                                }
                            }
                        });
                    }

                    // 2. Donut Chart - Overall Success Rate
                    const donutCtx{{ $loop->index }} = document.getElementById('donutChart{{ $loop->index }}');
                    if (donutCtx{{ $loop->index }}) {
                        new Chart(donutCtx{{ $loop->index }}, {
                            type: 'doughnut',
                            data: {
                                labels: ['Competent', 'Not Yet Competent'],
                                datasets: [{
                                    data: [{{ $program['overall_competent_rate'] }},
                                        {{ $program['overall_nyc_rate'] }}
                                    ],
                                    backgroundColor: [
                                        'rgba(25, 135, 84, 0.8)',
                                        'rgba(220, 53, 69, 0.8)'
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
                                        position: 'bottom',
                                    },
                                    title: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.label + ': ' + context.parsed + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // 3. Bar Chart - Competent vs NYC Comparison
                    const barCtx{{ $loop->index }} = document.getElementById('barChart{{ $loop->index }}');
                    if (barCtx{{ $loop->index }}) {
                        new Chart(barCtx{{ $loop->index }}, {
                            type: 'bar',
                            data: {
                                labels: cocLabels{{ $loop->index }},
                                datasets: [{
                                        label: 'Competent',
                                        data: competentData{{ $loop->index }},
                                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                                        borderColor: 'rgba(25, 135, 84, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Not Yet Competent',
                                        data: nycData{{ $loop->index }},
                                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                        borderColor: 'rgba(220, 53, 69, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: false
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
        @endpush
    @endforeach
@endif
