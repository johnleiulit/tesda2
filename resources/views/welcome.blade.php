<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
</head>

<body class="d-flex flex-column min-vh-100">

    @include('components.layout.navbar.index')
    <main class="flex-fill">
        @include('components.sections.hero.index')

        {{-- TWSP Announcement Section --}}
        @php
            $twspAnnouncement = \App\Models\TwspAnnouncement::getActive();
        @endphp

        @if ($twspAnnouncement && $twspAnnouncement->hasAvailableSlots())
            <section class="twsp-announcement py-5 bg-light">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <div class="alert alert-success">
                                <h3>🎓 SHC - TVET Training and Assessment Center</h3>
                                <h4>Now Offering: {{ $twspAnnouncement->program_name }}</h4>
                                <p class="mb-0">
                                    <strong>Available Slots:</strong>
                                    {{ $twspAnnouncement->getRemainingSlots() }} / {{ $twspAnnouncement->total_slots }}
                                </p>
                                <a href="{{ route('applicant.dashboard') }}" class="btn btn-primary btn-lg mt-3">
                                    Apply for TWSP Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @include('components.sections.about.index')
        @include('components.sections.faq.index')
    </main>

    @include('components.layout.footer.index');

    {{-- @if (Route::has('login'))
        <div class="d-none d-lg-block" style="height: 3.5rem;"></div>
    @endif --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
