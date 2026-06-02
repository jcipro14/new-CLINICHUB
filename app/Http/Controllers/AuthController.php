<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\WelcomeMail;

class AuthController extends Controller
{
    // Max login attempts before lockout
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_SECONDS = 60;

    // ── Show login page ────────────────────────────────────────
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        $inactiveLogout = $request->query('inactive', false);
        
        // FIXED: Pointing to the correct auth subdirectory view
        return view('auth.login', compact('inactiveLogout'));
    }

    // ── Show register page ─────────────────────────────────────
    public function showRegister()
    {
        // FIXED: Pointing to the correct auth subdirectory view
        return view('auth.login');
    }

    // ── Handle login ───────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role'     => 'required|string|in:student,staff,sta,superadmin',
        ]);

        // Brute-force protection using session
        $attempts  = session('login_attempts', 0);
        $lockUntil = session('lock_until', 0);

        if ($lockUntil && now()->timestamp < $lockUntil) {
            $remaining = $lockUntil - now()->timestamp;
            return back()->withErrors(['login' => "Too many failed attempts. Try again in {$remaining} seconds."]);
        }

        $username = trim($request->username);
        $role     = $request->role;

        // Find user by id_number and role
        $user = User::where('id_number', $username)->where('role', $role)->first();

        $isValid = false;

        if ($user) {
            // Standard bcrypt check
            if (Hash::check($request->password, $user->password)) {
                $isValid = true;
            }
            // Legacy MD5 fallback for superadmin
            if ($role === 'superadmin' && md5($request->password) === $user->password) {
                $isValid = true;
                // Upgrade to bcrypt on success
                $user->password = Hash::make($request->password);
                $user->save();
            }
        }

        if ($isValid) {
            session()->forget(['login_attempts', 'lock_until']);
            Auth::login($user);
            $request->session()->regenerate();

            // Audit log
            DB::table('audit_logs')->insert([
                'user_id'    => $user->id_number,
                'role'       => $user->role,
                'action'     => 'Login',
                'details'    => 'User logged in successfully',
                'ip_address' => $request->ip(),
                'timestamp'  => now(),
            ]);

            return $this->redirectByRole($user->role);
        }

        // Failed attempt
        $attempts++;
        if ($attempts >= self::MAX_ATTEMPTS) {
            session(['lock_until' => now()->timestamp + self::LOCKOUT_SECONDS, 'login_attempts' => 0]);
            return back()->withErrors(['login' => 'Too many failed attempts. Login locked for 1 minute.']);
        }
        session(['login_attempts' => $attempts]);

        return back()->withErrors(['login' => 'Invalid ID number, password, or role.']);
    }

    // ── Handle registration ────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:50',
            'last_name'    => 'required|string|max:50',
            'reg_id'       => 'required|string|max:20|unique:users,id_number',
            'email'        => 'required|email|max:100',
            'course'       => 'nullable|string|in:BSIT,BSCS,BSCpE,BSEE,BSECE,DEE',
            'reg_password' => [
                'required', 'string', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            ],
        ], [
            'reg_id.unique'      => 'ID number already exists.',
            'reg_password.regex' => 'Password must have 1 uppercase, 1 lowercase, a number, and a special character.',
        ]);

        $user = User::create([
            'first_name' => trim($request->first_name),
            'last_name'  => trim($request->last_name),
            'id_number'  => trim($request->reg_id),
            'email'      => trim($request->email),
            'password'   => Hash::make($request->reg_password),
            'role'       => 'student',
            'course'     => $request->course,
        ]);

        // Send welcome email
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // Non-fatal — log but don't block registration
            logger()->warning('Welcome email failed: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Registration successful. You may now login.');
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Helper: redirect based on role ───────────────────────
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'student'    => redirect()->route('student.dashboard'),
            'staff'      => redirect()->route('staff.dashboard'),
            'sta'        => redirect()->route('staff.dashboard'),
            'superadmin' => redirect()->route('admin.dashboard'),
            default      => redirect()->route('login'),
        };
    }
}