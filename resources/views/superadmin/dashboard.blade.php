@extends('layouts.portal')
@section('title','Admin Dashboard – UM Clinic')
@section('page_title','Superadmin Dashboard')

@section('content')

{{-- Stats Row 1: Role counts --}}
<div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
    @php $statsA = [
        ['Students',       $studentsCount,  'bg-blue-50',   'text-blue-700',   '🎓'],
        ['Staff',          $staffCount,     'bg-green-50',  'text-green-700',  '🧑‍⚕️'],
        ['STA',            $staCount,       'bg-yellow-50', 'text-yellow-700', '👨‍🏫'],
        ['Med. Records',   $totalRecords,   'bg-purple-50', 'text-purple-700', '📋'],
        ['Inventory',      $totalInventory, 'bg-amber-50',  'text-amber-700',  '📦'],
        ['Pending Appts',  $pendingAppts,   'bg-red-50',    'text-red-700',    '🕐'],
        ['New (7 days)',   $weeklyReg,      'bg-teal-50',   'text-teal-700',   '🆕'],
    ]; @endphp
    @foreach($statsA as [$label, $val, $bg, $col, $icon])
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:-translate-y-0.5 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <span class="text-slate-500 text-xs font-semibold uppercase tracking-wide leading-tight">{{ $label }}</span>
            <div class="w-8 h-8 {{ $bg }} rounded-xl flex items-center justify-center text-sm shrink-0">{{ $icon }}</div>
        </div>
        <div class="text-2xl font-extrabold {{ $col }}">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    {{-- Appointments by Month --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">📅</span>
                Appointments by Month
            </h3>
            <div class="flex items-center gap-2">
                <select id="adminChartYear" class="text-xs border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:border-red-400">
                    @for($y = date('Y'); $y >= date('Y')-3; $y--)
                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button onclick="loadAdminCharts()" class="bg-red-700 hover:bg-red-800 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">Refresh</button>
            </div>
        </div>
        <div class="p-4" style="height:240px;position:relative;">
            <canvas id="apptChart"></canvas>
        </div>
    </div>

    {{-- Top Inventory --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">📦</span>
                Top 5 Inventory Stock
                <span class="text-xs text-slate-400 font-normal ml-1">(remaining units)</span>
            </h3>
        </div>
        <div class="p-4" style="height:240px;position:relative;">
            <canvas id="inventoryChart"></canvas>
            <div id="inventoryEmpty" class="hidden absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No inventory data.</div>
        </div>
    </div>

</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

    {{-- Appointment Summary --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">📅</span>
                Appointment Summary
            </h3>
            <a href="{{ route('staff.appointments') }}" class="text-xs text-red-700 hover:underline font-semibold">Manage →</a>
        </div>
        <div class="p-4 space-y-2">
            @foreach(['Pending','Upcoming','Completed','Cancelled'] as $status)
            <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                <span class="badge-status {{ strtolower($status) }}">{{ $status }}</span>
                <span class="font-bold text-slate-800 text-lg">{{ $apptStats[$status] ?? 0 }}</span>
            </div>
            @endforeach
            <div class="pt-1">
                <div class="text-xs text-slate-400">Total visits this month:
                    <span class="font-bold text-slate-700">{{ $monthlyVisits }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-yellow-100 rounded-lg flex items-center justify-center">⚡</span>
                Quick Actions
            </h3>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2">
            <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-red-50 hover:border-red-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">➕</span>
                <span class="text-xs font-semibold text-slate-700">Add User</span>
            </a>
            <a href="{{ route('staff.inventory') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-blue-50 hover:border-blue-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">📦</span>
                <span class="text-xs font-semibold text-slate-700">Inventory</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-green-50 hover:border-green-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">⚙️</span>
                <span class="text-xs font-semibold text-slate-700">Settings</span>
            </a>
            <a href="{{ route('admin.backup') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">💾</span>
                <span class="text-xs font-semibold text-slate-700">Backup</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">👥</span>
                <span class="text-xs font-semibold text-slate-700">Manage Users</span>
            </a>
            <a href="{{ route('staff.announcements') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-green-50 hover:border-green-200 border border-transparent rounded-xl transition text-center">
                <span class="text-xl">🏥</span>
                <span class="text-xs font-semibold text-slate-700">Health Alerts</span>
            </a>
        </div>
    </div>

    {{-- Recently Added Users --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">👥</span>
                Recently Added Users
            </h3>
            <a href="{{ route('admin.users') }}" class="text-xs text-red-700 hover:underline font-semibold">All users →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentUsers as $u)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-700 to-red-900 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr($u->full_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800 text-sm truncate">{{ $u->full_name }}</div>
                    <div class="text-xs text-slate-400">{{ $u->id_number }}</div>
                </div>
                <span class="badge-role {{ $u->role }}">{{ strtoupper($u->role) }}</span>
            </div>
            @empty
            <div class="px-4 py-6 text-center text-slate-400 text-sm">No users yet.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Announcements Panel --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    {{-- Post Announcement --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">📢</span>
                Post Announcement
            </h3>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('staff.announcements.store') }}" class="space-y-3">
                @csrf
                <div class="f-group">
                    <label class="f-label">Title</label>
                    <input type="text" name="title" class="f-input" placeholder="Announcement title…" required maxlength="255">
                </div>
                <div class="f-group">
                    <label class="f-label">Message</label>
                    <textarea name="body" class="f-textarea" rows="4" placeholder="Write the announcement body…" required></textarea>
                </div>
                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 active:scale-[.99] text-white font-semibold text-sm py-2.5 rounded-xl transition-all shadow-sm">
                    📢 Post Announcement
                </button>
            </form>
        </div>
    </div>

    {{-- Recent Announcements --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">📋</span>
                Recent Announcements
            </h3>
            <a href="{{ route('staff.announcements') }}" class="text-xs text-red-700 hover:underline font-semibold">Manage all →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentAnnouncements as $ann)
            <div class="flex items-start justify-between gap-3 px-4 py-3 hover:bg-slate-50 transition group">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800 text-sm truncate">{{ $ann->title }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        {{ $ann->poster?->full_name ?? $ann->posted_by }}
                        &mdash; {{ \Carbon\Carbon::parse($ann->created_at)->format('M d, Y g:i A') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $ann->body }}</p>
                </div>
                <form method="POST" action="{{ route('staff.announcements.destroy', $ann->id) }}" class="shrink-0"
                      onsubmit="return confirm('Delete this announcement?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition p-1 rounded-lg hover:bg-red-50"
                            title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                <span class="text-2xl mb-1">📭</span>
                <p class="text-sm">No announcements yet.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
let apptChart, inventoryChart;

function buildApptChart(apptByMonth) {
    const ctx = document.getElementById('apptChart');
    if (apptChart) apptChart.destroy();
    const labels = MONTHS;
    const data   = MONTHS.map((_, i) => apptByMonth[i + 1] || 0);
    apptChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Appointments',
                data,
                backgroundColor: 'rgba(153,27,27,0.7)',
                borderColor: '#991b1b',
                borderWidth: 1.5,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }, x: { ticks: { font: { size: 11 } } } }
        }
    });
}

function buildInventoryChart(topInventory) {
    const ctx = document.getElementById('inventoryChart');
    if (inventoryChart) inventoryChart.destroy();
    const labels = Object.keys(topInventory);
    const data   = Object.values(topInventory);
    document.getElementById('inventoryEmpty').classList.toggle('hidden', labels.length > 0);
    if (!labels.length) return;
    const colors = ['rgba(37,99,235,.75)','rgba(16,185,129,.75)','rgba(245,158,11,.75)','rgba(239,68,68,.75)','rgba(139,92,246,.75)'];
    inventoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{ data, backgroundColor: colors, borderWidth: 2, hoverOffset: 6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { font: { size: 11 }, boxWidth: 12, padding: 10 } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.raw} units` } }
            }
        }
    });
}

function loadAdminCharts() {
    const year = document.getElementById('adminChartYear').value;
    fetch(`/api/admin-dashboard-data?year=${year}`)
        .then(r => r.json())
        .then(d => {
            buildApptChart(d.apptByMonth || {});
            buildInventoryChart(d.topInventory || {});
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', loadAdminCharts);
</script>
@endsection
