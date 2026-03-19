<nav class="sidebar bg-light">
    <div class="p-3">
        <!-- Logo/Brand -->
        <div class="text-center mb-4 text-primary">
            <h5 class="text-primary mb-0">
                <i class="bi bi-gear-fill"></i> SHC-TVET Admin
            </h5>
            <small class="text-primary">Management System</small>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav nav-pills flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} "
                    href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}"
                    href="{{ route('admin.calendar.index') }}">
                    <i class="bi bi-calendar-event"></i>
                    Calendar
                </a>
            </li>

            <!-- User Management Section -->
            <li class="nav-item mt-3">
                <small class="text-dark text-uppercase fw-bold px-3">User Management</small>
            </li>

            <!-- TWSP Announcements -->
            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.twsp.*') ? 'active' : '' }}"
                    href="{{ route('admin.twsp.index') }}">
                    <i class="bi bi-megaphone"></i> TWSP
                </a>
            </li>

            <!-- Contact Messages -->
            <li class="nav-item">
                <a class="nav-link text-primary d-flex align-items-center justify-content-between {{ request()->routeIs('admin.contact.messages') ? 'active' : '' }}"
                    href="{{ route('admin.contact.messages') }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope"></i>
                        <span>Messages</span>
                    </div>
                    @if (isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">
                            {{ $unreadMessagesCount }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- Applicants -->
            <li class="nav-item">
                <a class="nav-link text-primary d-flex align-items-center justify-content-between {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}"
                    href="{{ route('admin.applicants.index') }}">
                    <div class="d-flex align-items-center ">
                        <i class="bi bi-people"></i>
                        <span>Applicants</span>
                    </div>
                    @if (isset($unviewedApplicationsCount) && $unviewedApplicationsCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">
                            {{ $unviewedApplicationsCount }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- List of Enrollment -->
            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.trainees.*') ? 'active' : '' }}"
                    href="{{ route('admin.trainees.index') }}">
                    <i class="bi bi-person-badge"></i>
                    Trainees List
                </a>
            </li>
            <!-- Assessment Batches -->
            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.assessment-batches.*') && !request()->routeIs('admin.assessment-batches.history') ? 'active' : '' }}"
                    href="{{ route('admin.assessment-batches.index') }}">
                    <i class="bi bi-clipboard-check"></i>
                    Assessment
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.reassessment.*') ? 'active' : '' }}"
                    href="{{ route('admin.reassessment.index') }}">
                    <i class="bi bi-credit-card"></i> Reassessment
                    <div class="d-flex justify-content-center align-items-center">
                        Payments
                        @php
                            $pendingCount = \App\Models\Application::where(function ($query) {
                                $query
                                    ->where('reassessment_payment_status', 'pending')
                                    ->orWhere('second_reassessment_payment_status', 'pending');
                            })->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="badge bg-danger rounded-pill text-light ms-2">{{ $pendingCount }}</span>
                        @endif
                    </div>
                </a>
            </li>
            <!-- In sidebar.blade.php -->
            <li class="nav-item">
                <a class="nav-link text-primary d-flex align-items-center justify-content-between {{ request()->routeIs('admin.employment-feedback.*') ? 'active' : '' }}"
                    href="{{ route('admin.employment-feedback.index') }}">
                    <div class="d-flex align-items-center">
                        <i class="nav-icon bi bi-briefcase"></i>
                        <span>Feedback</span>
                    </div>
                    @php
                        $newEmploymentCount = \App\Models\EmploymentRecord::whereNull('viewed_at')->count();
                    @endphp
                    @if ($newEmploymentCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">
                            {{ $newEmploymentCount }}
                        </span>
                    @endif
                </a>
            </li>


            <!-- Divider -->
            <hr class="text-secondary my-3">

            <!-- History -->
            <li class="nav-item ">
                <small class="text-muted text-uppercase fw-bold px-3">History</small>
            </li>

            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.history.index*') ? 'active' : '' }}"
                    href="{{ route('admin.history.index') }}">
                    <i class="bi bi-person-badge"></i>
                    Application History
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.history.training*') ? 'active' : '' }}"
                    href="{{ route('admin.history.training') }}">
                    <i class="bi bi-archive"></i>
                    <span>Training History</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-primary {{ request()->routeIs('admin.assessment-batches.history') ? 'active' : '' }}"
                    href="{{ route('admin.assessment-batches.history') }}">
                    <i class="bi bi-archive"></i> Assessment History
                </a>
            </li>

            <!-- Divider -->
            <hr class="text-secondary my-3">

        </ul>
    </div>

    <!-- Footer -->
    <div class="mt-auto p-3">
        <div class="text-center">
            <small class="text-muted">
                <i class="bi bi-shield-check"></i>
                Admin Panel
            </small>
        </div>
    </div>
</nav>

<style>
    /* Additional sidebar styling */
    .sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar .nav-link {
        transition: all 0.2s ease;
        border-radius: 0.375rem;
        margin: 0.125rem 0.5rem;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(2px);
    }

    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: white !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* Sub-menu styling */
    .sidebar .nav .nav .nav-link {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        margin: 0.125rem 0;
    }

    /* Scrollbar styling for webkit browsers */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #343a40;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 2px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }
</style>
