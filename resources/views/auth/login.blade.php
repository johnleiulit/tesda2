@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <hr>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('auth.google') }}"
                                class="border border-primary p-2 d-inline-flex align-items-center text-decoration-none">

                                <!-- Google Official SVG Icon -->
                                <svg width="20" height="20" viewBox="0 0 48 48" class="me-2">
                                    <path fill="#FFC107"
                                        d="M43.6 20.5H42V20H24v8h11.3C33.9 32.9 29.4 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.5 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.3-.4-3.5z" />
                                    <path fill="#FF3D00"
                                        d="M6.3 14.7l6.6 4.8C14.5 16.1 18.9 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.1 6.5 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z" />
                                    <path fill="#4CAF50"
                                        d="M24 44c5.3 0 10.1-2 13.8-5.3l-6.4-5.4C29.5 34.5 26.9 36 24 36c-5.3 0-9.8-3.1-11.3-7.6l-6.5 5C9.6 39.8 16.2 44 24 44z" />
                                    <path fill="#1976D2"
                                        d="M43.6 20.5H42V20H24v8h11.3c-1 2.9-3.1 5.3-5.9 6.7l6.4 5.4C39.6 36.6 44 30.8 44 24c0-1.3-.1-2.3-.4-3.5z" />
                                </svg>

                                SIGN IN WITH GOOGLE
                            </a>
                        </div>
                        <div class="d-flex justify-content-center">
                            <p class="mt-3">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
