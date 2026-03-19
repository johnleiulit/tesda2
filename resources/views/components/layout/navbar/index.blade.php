<nav class="navbar navbar-expand-lg navbar-dark text-light fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold lh-sm" href="#">
            <span class="d-block d-lg-inline">SHC - TVET</span>
            <span class="d-block d-lg-inline">Training and Assessment Center</span>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse text-center" id="navMenu">
            <ul class="navbar-nav ms-auto mx-lg-auto ms-lg-auto mt-3 fw-bold gap-3">
                <li class="nav-item"><a class="nav-link text-white" href="{{ url('/#about')}}">About</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('programs') }}">Programs</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('benefits') }}">Benefits</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('howtoenroll') }}">How to Enroll</a>
                </li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('contact') }}">Contact</a></li>

                @if (Route::has('login'))
                    @auth
                        <li class="nav-item">
                            <a href="{{ url('/dashboard') }}" class="nav-link px-2 py-1.5">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item bg-warning rounded-pill">
                            <a href="{{ route('login') }}" class="nav-link px-3 py-1.5 text-dark no-hover">
                                Sign In
                            </a>
                        </li>
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>
<style>
    .navbar .nav-link:not(.no-hover):hover {
        color: #ffc107 !important;
    }

    .navbar .nav-link {
        transition: 0.3s ease;
    }
    
</style>
