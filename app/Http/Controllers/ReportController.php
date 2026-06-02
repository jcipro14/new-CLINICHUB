<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Models\Inventory;

class ReportController extends Controller
{
    // ── Monthly report ───────────────────────────────────────
    public function monthly(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year',  now()->year);

        $records = MedicalRecord::when($month, fn($q) => $q->whereMonth('date_consulted', $month))
            ->whereYear('date_consulted', $year)
            ->orderBy('date_consulted')
            ->get();

        // Reason breakdown
        $reasonMap = [];
        foreach ($records as $r) {
            $key = strtolower(trim($r->reason));
            if (!$key) continue;
            if (!isset($reasonMap[$key])) $reasonMap[$key] = ['label' => ucwords($key), 'count' => 0];
            $reasonMap[$key]['count']++;
        }
        arsort($reasonMap);

        // Medicine breakdown
        $medicineMap = [];
        foreach ($records as $r) {
            if (!$r->medicine) continue;
            $key = strtolower(trim($r->medicine));
            if (!isset($medicineMap[$key])) $medicineMap[$key] = ['label' => ucwords($key), 'count' => 0];
            $medicineMap[$key]['count']++;
        }
        arsort($medicineMap);

        $totalConsultations = $records->count();

        return view('staff.report_monthly', compact(
            'records', 'reasonMap', 'medicineMap',
            'totalConsultations', 'month', 'year'
        ));
    }

    // ── Inventory report ─────────────────────────────────────
    public function inventory()
    {
        $inventory    = Inventory::orderBy('medicine_name')->orderBy('expiry_date')->get();
        $expiring     = Inventory::expiringSoon(30)->get();
        $lowStock     = Inventory::lowStock(10)->get();
        $totalValue   = Inventory::sum('remaining_quantity');

        return view('staff.report_inventory', compact('inventory', 'expiring', 'lowStock', 'totalValue'));
    }

    // ── AJAX: dashboard chart data (staff) ───────────────────
    public function dashboardData(Request $request)
    {
        $month = (int) $request->query('month', 0);
        $year  = (int) $request->query('year',  now()->year);

        $reasons = MedicalRecord::selectRaw('reason, count(*) as count')
            ->whereYear('date_consulted', $year)
            ->when($month, fn($q) => $q->whereMonth('date_consulted', $month))
            ->whereNotNull('reason')->where('reason', '!=', '')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'reason');

        $medicines = MedicalRecord::selectRaw('medicine, SUM(quantity) as total')
            ->whereYear('date_consulted', $year)
            ->when($month, fn($q) => $q->whereMonth('date_consulted', $month))
            ->whereNotNull('medicine')->where('medicine', '!=', '')
            ->groupBy('medicine')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'medicine');

        $apptByMonth = Appointment::selectRaw('MONTH(next_consultation) as month, count(*) as count')
            ->whereYear('next_consultation', $year)
            ->whereNotNull('next_consultation')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return response()->json([
            'reasons'     => $reasons,
            'medicines'   => $medicines,
            'apptByMonth' => $apptByMonth,
        ]);
    }

    // ── AJAX: admin dashboard chart data ─────────────────────
    public function adminDashboardData(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        $apptByMonth = Appointment::selectRaw('MONTH(next_consultation) as month, count(*) as count')
            ->whereYear('next_consultation', $year)
            ->whereNotNull('next_consultation')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $topInventory = Inventory::selectRaw('medicine_name, SUM(remaining_quantity) as total')
            ->groupBy('medicine_name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'medicine_name');

        $usersByMonth = \App\Models\User::selectRaw('MONTH(created_at) as month, count(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return response()->json([
            'apptByMonth'  => $apptByMonth,
            'topInventory' => $topInventory,
            'usersByMonth' => $usersByMonth,
        ]);
    }
}
