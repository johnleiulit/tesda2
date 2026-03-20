<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SHC Tesda Institution')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/applicantDashboard/style.css') }}">
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <span class="d-block d-lg-inline">SHC - TVET</span>
                <span class="d-block d-lg-inline">Training and Assessment Center</span>
            </a>

            @auth
                <div class="ms-lg-auto w-100 w-lg-auto mt-3 mt-lg-0">
                    <div class="d-flex justify-content-between justify-content-lg-end align-items-center w-100">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline order-2 order-lg-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                Logout
                            </button>
                        </form>
                        <span class="navbar-text text-light order-1 order-lg-1 me-lg-3">
                            Welcome, {{ Auth::user()->name }} ({{ Auth::user()->role }})
                        </span>

                    </div>
                </div>
            @endauth

        </div>
    </nav>


    <main class="container mt-4">
        {{-- Global Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success flash-message">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger flash-message">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning flash-message">
                {{ session('warning') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-10px)';
                    setTimeout(() => message.remove(), 500); // remove after fade out
                }, 5000); // ⏱️ 5 seconds
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
<style>
    .form-step {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-indicator {
    flex: 1;
    text-align: center;
}

.step-indicator .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

</style>
