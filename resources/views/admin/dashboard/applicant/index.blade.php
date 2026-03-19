{{-- resources/views/admin/dashboard/applicant/index.blade.php --}}
<div class="card analytics-card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>Applicant Analytics
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Total Applications Summary with Breakdown --}}
            <div class="col-md-2">
                <div class="text-center mb-3 fade-in">
                    <div class="text-primary">
                        <i class="bi bi-file-earmark-text fs-1 icon-bounce"></i>
                        <div class="fw-bold fs-4 counter" data-target="{{ $applicant['total_applications'] ?? 0 }}">0
                        </div>
                        <div class="text-muted small">Total Applications</div>
                    </div>
                </div>

                {{-- Application Type Breakdown --}}
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 px-2 slide-in-left"
                        style="animation-delay: 0.2s;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clipboard-check text-info me-2"></i>
                            <small class="text-muted">For Assessment</small>
                        </div>
                        <span class="badge bg-info counter"
                            data-target="{{ $applicant['assessment_count'] ?? 0 }}">0</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center px-2 slide-in-left"
                        style="animation-delay: 0.3s;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-mortarboard text-success me-2"></i>
                            <small class="text-muted">For TWSP</small>
                        </div>
                        <span class="badge bg-success counter"
                            data-target="{{ $applicant['twsp_count'] ?? 0 }}">0</span>
                    </div>
                </div>
            </div>

            {{-- Program Applications Bar Graph --}}
            <div class="col-md-10">
                <h6 class="text-muted mb-3 fade-in">Applications by Program (Highest to Lowest)</h6>
                @if (isset($applicant['programs']) && count($applicant['programs']) > 0)
                    @php
                        $colors = ['bg-primary', 'bg-primary', 'bg-primary', 'bg-primary', 'bg-primary'];
                    @endphp

                    @foreach ($applicant['programs'] as $index => $program)
                        <div class="mb-3 slide-in-right" style="animation-delay: {{ 0.1 * ($index + 1) }}s;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="badge border border-primary text-primary me-2">{{ $index + 1 }}</span>
                                    <span class="small fw-bold">{{ $program['name'] }}</span>
                                </div>
                                <span class="small text-muted">
                                    <strong class="counter" data-target="{{ $program['count'] }}">0</strong> applicants
                                    <span class="text-primary">({{ $program['percentage'] }}%)</span>
                                </span>
                            </div>
                            <div class="progress" style="height: 25px; background-color: #e9ecef;">
                                @if ($program['count'] > 0)
                                    <div class="progress-bar {{ $colors[$index] ?? 'bg-secondary' }} progress-bar-animated"
                                        style="width: 0%;" data-width="{{ $program['percentage'] }}" role="progressbar"
                                        aria-valuenow="{{ $program['percentage'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <span class="fw-bold text-white counter"
                                            data-target="{{ $program['count'] }}">0</span>
                                    </div>
                                @else
                                    <div class="progress-bar bg-secondary" style="width: 100%; opacity: 0.3;"
                                        role="progressbar">
                                        <span class="text-muted small">0 applicants</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info mb-0 fade-in">
                        <i class="bi bi-info-circle me-2"></i>No application data available
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Animation Styles and Scripts --}}
@push('styles')
    <style>
        /* Fade In Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        /* Slide In from Left */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease-out forwards;
            opacity: 0;
        }

        /* Slide In from Right */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in-right {
            animation: slideInRight 0.6s ease-out forwards;
            opacity: 0;
        }

        /* Icon Bounce */
        @keyframes iconBounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .icon-bounce {
            animation: iconBounce 2s ease-in-out infinite;
        }

        /* Progress Bar Animation */
        .progress-bar-animated {
            transition: width 1.5s ease-out;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Counter Animation
            function animateCounter(element) {
                const target = parseInt(element.getAttribute('data-target'));
                const duration = 1500; // 1.5 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 16);
            }

            // Animate all counters
            const counters = document.querySelectorAll('.counter');
            counters.forEach((counter, index) => {
                setTimeout(() => {
                    animateCounter(counter);
                }, index * 100); // Stagger the animations
            });

            // Animate progress bars
            const progressBars = document.querySelectorAll('.progress-bar-animated');
            progressBars.forEach((bar, index) => {
                const targetWidth = bar.getAttribute('data-width');
                setTimeout(() => {
                    bar.style.width = targetWidth + '%';
                }, 300 + (index * 150)); // Stagger the animations
            });
        });
    </script>
@endpush
