<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: middleware('role:staff,sta')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Flatten comma-separated roles passed as single string (e.g. 'staff,sta')
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $single) {
                $allowed[] = trim($single);
            }
        }

        if (!in_array($user->role, $allowed)) {
            // Redirect to their correct dashboard
            return match ($user->role) {
                'student'    => redirect()->route('student.dashboard'),
                'staff'      => redirect()->route('staff.dashboard'),
                'sta'        => redirect()->route('staff.dashboard'),
                'superadmin' => redirect()->route('admin.dashboard'),
                default      => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
