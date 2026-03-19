<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // ✅ add this import
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        
        if (!Auth::check()) {
            return redirect('login');
        }
        //Get the logged-in user's role
        $userRole = Auth::user()->role;

        // Check if the user's role matches any allowed role
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
