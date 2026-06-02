@extends('layouts.portal')
@section('title','STA Dashboard – UM Clinic')
@section('page_title','STA Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div class="relative bg-gradient-to-br from-amber-800 via-amber-700 to-yellow-600 rounded-3xl overflow-hidden shadow-xl">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="relative z-10 flex items-center justify-between px-6 py-6 sm:px-8 sm:py-7 gap-4">
        <div class="flex-1 min-w-0">
            <p class="text-yellow-200 text-sm font-medium mb-1">Welcome,</p>
            <h2 class="text-white text-2xl sm:text-3xl font-extrabold leading-tight truncate">
                {{ $user->full_name }}
            </h2>
            <p class="text-yellow-200 text-xs mt-1 font-semibold uppercase tracking-wider">Student Teaching Assistant</p>
            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
                        {{ $settings->clinic_status === 'open'
                           ? 'bg-green-500/20 text-green-200 border border-green-400/30'
                           : 'bg-red-400/20 text-red-100 border border-red-300/30' }}">
                <span class="w-2 h-2 rounded-full pulse-dot {{ $settings->clinic_status === 'open' ? 'bg-green-400' : 'bg-red-300' }}"></span>
                Clinic {{ strtoupper($settings->clinic_status) }}
                @if($settings->clinic_hours) &mdash; {{ $settings->clinic_hours }} @endif
            </div>
        </div>
        <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="UM Logo"
             class="w-20 h-20 sm:w-24 sm:h-24 object-contain opacity-80 drop-shadow-2xl shrink-0 select-none">
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php $stats = [
        ['Pending',         $pendingCount,  'bg-amber-50',  'text-amber-600',  '🕐'],
        ['Upcoming',        $upcomingCount, 'bg-green-50',  'text-green-600',  '📅'],
        ['Done (Month)',    $completedCount,'bg-blue-50',   'text-blue-600',   '✅'],
        ['Total Students',  $totalStudents, 'bg-purple-50', 'text-purple-600', '👥'],
    ]; @endphp
    @foreach($stats as [$label, $val, $bg, $color, $icon])
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:-translate-y-0.5 transition-transform">
        <div class="flex items-start justify-between mb-3">
            <span class="text-slate-500 text-xs font-semibold uppercase tracking-wide leading-tight">{{ $label }}</span>
            <div class="w-9 h-9 {{ $bg }} rounded-xl flex items-center justify-center text-base shrink-0">{{ $icon }}</div>
        </div>
        <div class="text-3xl font-extrabold {{ $color }}">{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Today's Appointments --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">📅</span>
                Today's Appointments
                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $todayAppts->count() }}</span>
            </h3>
            <a href="{{ route('staff.appointments') }}" class="text-xs text-amber-700 hover:underline font-semibold">Manage →</a>
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

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-7 h-7 bg-yellow-100 rounded-lg flex items-center justify-center">⚡</span>
                    Quick Actions
                </h3>
            </div>
            <div class="p-4 grid grid-cols-2 gap-2">
                <a href="{{ route('staff.records') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 border border-transparent rounded-xl transition text-center">
                    <span class="text-xl">📋</span>
                    <span class="text-xs font-semibold text-slate-700">Med. Records</span>
                </a>
                <a href="{{ route('staff.appointments') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-green-50 hover:border-green-200 border border-transparent rounded-xl transition text-center">
                    <span class="text-xl">📅</span>
                    <span class="text-xs font-semibold text-slate-700">Appointments</span>
                </a>
                <a href="{{ route('staff.patients') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-blue-50 hover:border-blue-200 border border-transparent rounded-xl transition text-center">
                    <span class="text-xl">👥</span>
                    <span class="text-xs font-semibold text-slate-700">Patients</span>
                </a>
                <a href="{{ route('staff.inventory') }}" class="flex flex-col items-center justify-center gap-1.5 p-3 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 border border-transparent rounded-xl transition text-center">
                    <span class="text-xl">📦</span>
                    <span class="text-xs font-semibold text-slate-700">Inventory</span>
                </a>
                <a href="{{ route('staff.messages') }}" class="col-span-2 flex items-center justify-center gap-2 p-3 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 border border-transparent rounded-xl transition text-xs font-semibold text-slate-700">
                    <span>✉️</span> Messages
                </a>
            </div>
        </div>

        {{-- Inventory Alerts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex-1">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">⚠️</span>
                    Inventory Alerts
                </h3>
                <a href="{{ route('staff.inventory') }}" class="text-xs text-amber-700 hover:underline font-semibold">Manage →</a>
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

    </div>
</div>

@endsection
