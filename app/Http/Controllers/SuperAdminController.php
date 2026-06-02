<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Log;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Inventory;
use App\Models\Announcement;

class SuperAdminController extends Controller
{
    // ── Dashboard ────────────────────────────────────────────
    public function dashboard()
    {
        $settings = SystemSetting::current();

        $studentsCount  = User::where('role', 'student')->count();
        $staffCount     = User::where('role', 'staff')->count();
        $staCount       = User::where('role', 'sta')->count();
        $totalStudents  = $studentsCount;
        $totalStaff     = $staffCount + $staCount;
        $totalRecords   = MedicalRecord::count();
        $totalInventory = Inventory::sum('remaining_quantity');

        $pendingAppts    = Appointment::where('status', 'Pending')->count();
        $weeklyReg       = User::where('created_at', '>=', now()->subDays(7))->count();
        $monthlyVisits   = MedicalRecord::whereMonth('date_consulted', now()->month)
            ->whereYear('date_consulted', now()->year)->count();

        $apptStats = Appointment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentUsers         = User::orderByDesc('id')->limit(5)->get();
        $recentAnnouncements = Announcement::with('poster')->orderByDesc('created_at')->limit(5)->get();

        return view('superadmin.dashboard', compact(
            'settings',
            'totalStudents', 'totalStaff', 'studentsCount', 'staffCount', 'staCount',
            'totalRecords', 'totalInventory',
            'pendingAppts', 'weeklyReg', 'monthlyVisits',
            'apptStats', 'recentUsers', 'recentAnnouncements'
        ));
    }

    // ── Logs ─────────────────────────────────────────────────
    public function logs(Request $request)
    {
        $search = $request->query('search');
        $logs = Log::when($search, fn($q) => $q->where('action', 'like', "%{$search}%")
                          ->orWhere('user_name', 'like', "%{$search}%"))
            ->orderByDesc('timestamp')
            ->paginate(100);

        return view('superadmin.logs', compact('logs', 'search'));
    }

    // ── Audit logs ───────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $search = $request->query('search');
        $logs = AuditLog::when($search, fn($q) => $q->where('action', 'like', "%{$search}%")
                               ->orWhere('user_id', 'like', "%{$search}%"))
            ->orderByDesc('timestamp')
            ->paginate(100);

        return view('superadmin.audit_logs', compact('logs', 'search'));
    }

}
