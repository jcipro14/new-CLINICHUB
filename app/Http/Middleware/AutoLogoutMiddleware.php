<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;

class AutoLogoutMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $limitMinutes = SystemSetting::current()->auto_logout ?? 5;
        $limitSeconds = $limitMinutes * 60;

        $lastActivity = session('last_activity');

        if ($lastActivity && (time() - $lastActivity) > $limitSeconds) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login', ['inactive' => 1]);
        }

        session(['last_activity' => time()]);

        return $next($request);
    }
}
