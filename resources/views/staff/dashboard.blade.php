@extends('layouts.portal')
@section('title','Staff Dashboard – UM Clinic')
@section('page_title','Staff Dashboard')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
    @php
        $stats = [
            ['Pending',          $pendingCount,     'bg-amber-50',  'text-amber-600',  '🕐'],
            ['Upcoming',         $upcomingCount,    'bg-green-50',  'text-green-600',  '📅'],
            ['Done (Month)',      $completedCount,   'bg-blue-50',   'text-blue-600',   '✅'],
            ['Total Students',   $totalStudents,    'bg-purple-50', 'text-purple-600', '👥'],
            ['Total Records',    $totalRecords,     'bg-red-50',    'text-red-600',    '📋'],
            ['Visits (Month)',   $monthlyVisits,    'bg-teal-50',   'text-teal-600',   '🏥'],
            ['Patients (Month)', $newPatientsMonth, 'bg-pink-50',   'text-pink-600',   '🆕'],
        ];
    @endphp
    @foreach($stats as [$label, $val, $bg, $color, $icon])
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:-translate-y-0.5 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <span class="text-slate-500 text-xs font-semibold uppercase tracking-wide leading-tight">{{ $label }}</span>
            <div class="w-8 h-8 {{ $bg }} rounded-xl flex items-center justify-center text-sm shrink-0">{{ $icon }}</div>
        </div>
        <div class="text-2xl font-extrabold {{ $color }}">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Charts Row (staff only — STA sees operational data only) --}}
@if(Auth::user()->role === 'staff')
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    {{-- Sickness Types Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">🩺</span>
                Sickness Frequency
            </h3>
            <div class="flex items-center gap-2">
                <select id="chartMonth" class="text-xs border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:border-red-400">
                    <option value="0">All Months</option>
                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mname)
                    <option value="{{ $mi+1 }}" {{ ($mi+1) == date('n') ? 'selected' : '' }}>{{ $mname }}</option>
                    @endforeach
                </select>
                <select id="chartYear" class="text-xs border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:border-red-400">
                    @for($y = date('Y'); $y >= date('Y')-3; $y--)
                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button onclick="loadCharts()" class="bg-red-700 hover:bg-red-800 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">Filter</button>
            </div>
        </div>
        <div class="p-4" style="height:260px;position:relative;">
            <canvas id="reasonChart"></canvas>
            <div id="reasonEmpty" class="hidden absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No data for selected period.</div>
        </div>
    </div>

    {{-- Medicine Usage Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">💊</span>
                Medicine Dispensed
                <span class="text-xs text-slate-400 font-normal ml-1">(same filter)</span>
            </h3>
        </div>
        <div class="p-4" style="height:260px;position:relative;">
            <canvas id="medicineChart"></canvas>
            <div id="medicineEmpty" class="hidden absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No data for selected period.</div>
        </div>
    </div>

</div>
@endif

{{-- Main Grid --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Today's Appointments --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">📅</span>
                Today's Appointments
                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $todayAppts->count() }}</span>
            </h3>
            <a href="{{ route('staff.appointments') }}" class="text-xs text-red-700 hover:underline font-semibold">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead><tr><th>Student</th><th>ID</th><th>Doctor</th><th>Reason</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($todayAppts as $appt)
                    <tr>
                        <td class="font-medium">{{ $appt->name }}</td>
                        <td class="text-slate-400 text-xs">{{ $appt->student_id }}</td>
                        <td>{{ $appt->doctor ?: 'TBA' }}</td>
                        <td class="max-w-[140px] truncate">{{ $appt->reason ?: '—' }}</td>
                        <td><span class="badge-status {{ strtolower($appt->status) }}">{{ $appt->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-slate-400 text-sm">No appointments today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Side column --}}
    <div class="flex flex-col gap-5">

        {{-- Inventory Alerts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">⚠️</span>
                    Inventory Alerts
                </h3>
                <a href="{{ route('staff.inventory') }}" class="text-xs text-red-700 hover:underline font-semibold">Manage →</a>
            </div>
            <div class="p-4 space-y-2">
                @if($lowStockItems->isNotEmpty())
                <p class="text-xs font-bold text-red-600 uppercase tracking-wide">Low Stock</p>
                @foreach($lowStockItems as $item)
                <div class="flex justify-between items-center text-sm py-1.5 border-b border-slate-50">
                    <span class="text-slate-700 truncate">{{ $item->medicine_name }}</span>
                    <span class="text-xs bg-red-100 text-red-700 font-semibold px-2 py-0.5 rounded-full shrink-0 ml-2">{{ $item->remaining_quantity }} left</span>
                </div>
                @endforeach
                @endif
                @if($expiringItems->isNotEmpty())
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wide mt-2">Expiring Soon</p>
                @foreach($expiringItems as $item)
                <div class="flex justify-between items-center text-sm py-1.5 border-b border-slate-50">
                    <span class="text-slate-700 truncate">{{ $item->medicine_name }}</span>
                    <span class="text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full shrink-0 ml-2">{{ $item->expiry_date->format('M d') }}</span>
                </div>
                @endforeach
                @endif
                @if($lowStockItems->isEmpty() && $expiringItems->isEmpty())
                <div class="text-center py-4">
                    <span class="text-2xl">✅</span>
                    <p class="text-sm text-slate-400 mt-1">No alerts</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Activity (staff only — STA has no logs page) --}}
        @if(Auth::user()->role === 'staff')
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex-1">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">📝</span>
                    Recent Activity
                </h3>
                <a href="{{ route('staff.logs') }}" class="text-xs text-red-700 hover:underline font-semibold">All logs →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentLogs->take(5) as $log)
                <div class="px-4 py-3">
                    <div class="text-xs text-slate-400 mb-0.5">{{ $log->timestamp->format('M d, H:i') }}</div>
                    <div class="text-sm text-slate-700 leading-snug">{{ Str::limit($log->action, 60) }}</div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-slate-400 text-sm">No recent activity.</div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Student Feedback (staff only) --}}
@if(Auth::user()->role === 'staff')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <span class="w-7 h-7 bg-yellow-100 rounded-lg flex items-center justify-center">⭐</span>
            Recent Student Feedback
        </h3>
        <a href="{{ route('staff.feedback') }}" class="text-xs text-red-700 hover:underline font-semibold">View all →</a>
    </div>
    @if($feedbacks->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead><tr><th>Student</th><th>Rating</th><th>Message</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($feedbacks as $fb)
                <tr>
                    <td class="font-medium">{{ $fb->name ?? $fb->student_id }}</td>
                    <td>
                        @if($fb->rating)
                        <span class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $fb->rating ? 'text-yellow-400' : 'text-slate-200' }} text-base">★</span>
                            @endfor
                        </span>
                        @else
                        <span class="text-slate-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="max-w-[320px] truncate text-slate-600">{{ $fb->message }}</td>
                    <td class="text-xs text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($fb->created_at)->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
        <span class="text-3xl mb-2">💬</span>
        <p class="text-sm">No feedback submitted yet.</p>
    </div>
    @endif
</div>
@endif

@endsection

@section('scripts')
@if(Auth::user()->role === 'staff')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let reasonChart, medicineChart;

function buildReasonChart(labels, data) {
    const ctx   = document.getElementById('reasonChart');
    const empty = document.getElementById('reasonEmpty');
    if (!ctx) return;
    if (reasonChart) reasonChart.destroy();
    empty.classList.toggle('hidden', labels.length > 0);
    if (!labels.length) return;
    reasonChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Cases', data, backgroundColor: 'rgba(153,27,27,0.75)', borderColor: '#991b1b', borderWidth: 1.5, borderRadius: 6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.raw} case(s)` } } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }, x: { ticks: { font: { size: 10 }, maxRotation: 35 } } }
        }
    });
}

function buildMedicineChart(labels, data) {
    const ctx   = document.getElementById('medicineChart');
    const empty = document.getElementById('medicineEmpty');
    if (!ctx) return;
    if (medicineChart) medicineChart.destroy();
    empty.classList.toggle('hidden', labels.length > 0);
    if (!labels.length) return;
    medicineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Units', data, backgroundColor: 'rgba(37,99,235,0.75)', borderColor: '#1d4ed8', borderWidth: 1.5, borderRadius: 6 }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.raw} unit(s)` } } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }, y: { ticks: { font: { size: 11 } } } }
        }
    });
}

function loadCharts() {
    const month = document.getElementById('chartMonth').value;
    const year  = document.getElementById('chartYear').value;
    fetch(`/api/dashboard-data?month=${month}&year=${year}`)
        .then(r => r.json())
        .then(d => {
            const rLabels = Object.keys(d.reasons  || {}).map(k => k.charAt(0).toUpperCase() + k.slice(1));
            const rData   = Object.values(d.reasons  || {});
            const mLabels = Object.keys(d.medicines || {}).map(k => k.charAt(0).toUpperCase() + k.slice(1));
            const mData   = Object.values(d.medicines || {});
            buildReasonChart(rLabels, rData);
            buildMedicineChart(mLabels, mData);
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', loadCharts);
</script>
@endif
@endsection
