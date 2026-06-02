<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Log;
use App\Models\SystemSetting;

class StaffController extends Controller
{
    // ── Staff dashboard ──────────────────────────────────────
    public function dashboard()
    {
        $user     = Auth::user();
        $settings = SystemSetting::current();

        $todayAppts = Appointment::whereIn('status', ['Upcoming', 'Pending'])
            ->whereDate('next_consultation', today())
            ->orderBy('next_consultation')
            ->get();

        $pendingCount   = Appointment::where('status', 'Pending')->count();
        $upcomingCount  = Appointment::where('status', 'Upcoming')->count();
        $completedCount = Appointment::where('status', 'Completed')
            ->whereMonth('next_consultation', now()->month)->count();

        $totalStudents  = User::where('role', 'student')->count();
        $totalRecords   = MedicalRecord::count();

        // Monthly stats
        $monthlyVisits    = MedicalRecord::whereMonth('date_consulted', now()->month)
            ->whereYear('date_consulted', now()->year)->count();
        $newPatientsMonth = MedicalRecord::whereMonth('date_consulted', now()->month)
            ->whereYear('date_consulted', now()->year)
            ->distinct('student_id')->count('student_id');

        $lowStockItems  = Inventory::lowStock(10)->get();
        $expiringItems  = Inventory::expiringSoon(7)->get();

        $recentLogs = Log::orderByDesc('timestamp')->limit(10)->get();

        // Recent student feedback
        $feedbacks = DB::table('feedback')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'user', 'settings',
            'todayAppts', 'pendingCount', 'upcomingCount', 'completedCount',
            'totalStudents', 'totalRecords', 'monthlyVisits', 'newPatientsMonth',
            'lowStockItems', 'expiringItems', 'recentLogs', 'feedbacks'
        ));
    }

    // ── Patients list ────────────────────────────────────────
    public function patients(Request $request)
    {
        $search   = $request->query('search');
        $patients = User::where('role', 'student')
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name',  'like', "%{$search}%")
                   ->orWhere('id_number',  'like', "%{$search}%");
            }))
            ->withCount('medicalRecords')
            ->with(['medicalRecords' => fn($q) => $q->orderByDesc('date_consulted')->limit(1)])
            ->orderBy('first_name')
            ->get();

        return view('staff.patients', compact('patients', 'search'));
    }

    // ── Feedback management ──────────────────────────────────
    public function feedback(Request $request)
    {
        $search    = $request->query('search');
        $feedbacks = DB::table('feedback')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('message', 'like', "%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate(25);

        $avgRating = DB::table('feedback')->whereNotNull('rating')->avg('rating');
        $totalFeedback = DB::table('feedback')->count();

        return view('staff.feedback', compact('feedbacks', 'search', 'avgRating', 'totalFeedback'));
    }

    // ── Activity logs ────────────────────────────────────────
    public function logs(Request $request)
    {
        $search = $request->query('search');
        $logs   = Log::when($search, fn($q) => $q->where('action', 'like', "%{$search}%")
                            ->orWhere('user_name', 'like', "%{$search}%"))
            ->orderByDesc('timestamp')
            ->paginate(50);

        return view('staff.logs', compact('logs', 'search'));
    }
}
