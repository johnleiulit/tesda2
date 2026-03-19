<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)){
            $user = Auth::user();

            // Redirect based on role
            return match($user->role){
                'admin' => redirect()->route('admin.dashboard'),
                'assessor' => redirect()->route('assessor.dashboard'),
                'applicant' => redirect()->route('applicant.dashboard'),
                default => redirect()->route('home'),
            };
        }
        
        return back()->withErrors([
            'email' => 'Invalid email or password.'
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ],[
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'applicant', // Default role
        ]);

        Auth::login($user);

        return redirect()->route('applicant.dashboard');
    }

    // Google OAuth methods
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::where('email', $googleUser->email)->first();

        if (!$user){
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(uniqid()), // Random password
                'role' => 'applicant', // Default role
                'email_verified_at' => now(),
            ]);
        }
        
        Auth::login($user);

        return redirect()->route('applicant.dashboard');

        }catch (\Exception $e){
        return redirect()->route('login')->withErrors([
            'error' => 'Google login failed. Please try again'
        ]);
    }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
