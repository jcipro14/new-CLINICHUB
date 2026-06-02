<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UM Clinic')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/um_logo_no_bg.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @yield('styles')
    <style>
        #portalSidebar  { transition: transform .28s cubic-bezier(.4,0,.2,1); }
        #portalOverlay  { transition: opacity .25s ease; }
        [x-cloak]       { display:none !important; }
        /* Nav active state */
        .nav-active { background:rgba(255,255,255,.15); border-left:3px solid #fbbf24; padding-left:13px !important; color:#fff !important; }
        /* Pulse */
        @keyframes pulse-dot{0%,100%{opacity:1}50%{opacity:.4}}
        .pulse-dot { animation:pulse-dot 1.6s ease-in-out infinite; }
        /* Table */
        .portal-table { width:100%; border-collapse:collapse; font-size:.875rem; }
        .portal-table thead th { background:#991b1b; color:#fff; padding:.6rem .9rem; text-align:left; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
        .portal-table thead th:first-child { border-radius:.5rem 0 0 0; }
        .portal-table thead th:last-child  { border-radius:0 .5rem 0 0; }
        .portal-table tbody tr { border-bottom:1px solid #f1f5f9; }
        .portal-table tbody tr:hover { background:#f8fafc; }
        .portal-table tbody td { padding:.6rem .9rem; color:#334155; }
        /* Badge status */
        .badge-status { display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:capitalize; }
        .badge-status.pending   { background:#fef3c7; color:#92400e; }
        .badge-status.upcoming  { background:#d1fae5; color:#065f46; }
        .badge-status.completed { background:#dbeafe; color:#1e40af; }
        .badge-status.cancelled { background:#fee2e2; color:#991b1b; }
        /* Badge role */
        .badge-role { display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
        .badge-role.student    { background:#dbeafe; color:#1e40af; }
        .badge-role.staff      { background:#d1fae5; color:#065f46; }
        .badge-role.sta        { background:#fef3c7; color:#92400e; }
        .badge-role.superadmin { background:#fee2e2; color:#991b1b; }
        /* Modal */
        .portal-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9998; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .portal-modal-box { background:#fff; border-radius:1rem; box-shadow:0 25px 60px rgba(0,0,0,.2); width:100%; max-width:480px; max-height:90vh; overflow-y:auto; }
        .portal-modal-box.wide { max-width:640px; }
        .portal-modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid #f1f5f9; }
        .portal-modal-header h3 { font-size:1rem; font-weight:700; color:#1e293b; margin:0; }
        .portal-modal-body { padding:1.4rem; }
        .portal-modal-footer { padding:.9rem 1.4rem; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:.5rem; }
        /* Form */
        .f-label { display:block; font-size:.8rem; font-weight:600; color:#475569; margin-bottom:.25rem; }
        .f-input, .f-select, .f-textarea {
            width:100%; border:1.5px solid #e2e8f0; border-radius:.6rem;
            padding:.55rem .75rem; font-size:.875rem; color:#1e293b;
            transition:border-color .15s; outline:none; box-sizing:border-box;
        }
        .f-input:focus, .f-select:focus, .f-textarea:focus { border-color:#991b1b; }
        .f-textarea { resize:vertical; min-height:90px; }
        .f-group { margin-bottom:.85rem; }
        /* Alert banners */
        .alert-danger  { background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:.75rem 1rem; border-radius:.75rem; font-size:.875rem; margin-bottom:.75rem; }
        .alert-warning { background:#fef3c7; border:1px solid #fde68a; color:#92400e; padding:.75rem 1rem; border-radius:.75rem; font-size:.875rem; margin-bottom:.75rem; }
        .alert-info    { background:#dbeafe; border:1px solid #bfdbfe; color:#1e40af; padding:.75rem 1rem; border-radius:.75rem; font-size:.875rem; margin-bottom:.75rem; }
        /* Row highlights */
        .row-expired  { background:#fff1f2 !important; }
        .row-expiring { background:#fffbeb !important; }
        .tag-expired  { background:#fecaca; color:#991b1b; font-size:.65rem; font-weight:700; padding:.15rem .45rem; border-radius:999px; margin-left:.3rem; }
        .tag-expiring { background:#fde68a; color:#92400e; font-size:.65rem; font-weight:700; padding:.15rem .45rem; border-radius:999px; margin-left:.3rem; }
        /* Print */
        @media print { #portalSidebar,#portalOverlay,#portalTopbar,.no-print{display:none!important} }
    </style>
</head>

<body class="bg-slate-100 min-h-screen font-sans antialiased">
<div id="portalApp" x-data="portalApp()" x-init="init()">

{{-- ── OVERLAY ── --}}
<div id="portalOverlay"
     x-show="sidebarOpen" x-cloak
     @click="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-40"
     style="display:none"></div>

{{-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ --}}
@php
    $role = Auth::user()->role;
    $cur  = request()->route()?->getName() ?? '';

    // Theme system — only students see seasonal themes
    $portalTheme = 'default';
    if ($role === 'student') {
        $portalTheme = \App\Models\SystemSetting::current()->active_theme;
    }
    $portalThemeCfg = [
        'default'          => ['bg' => '',                                                                       'nav' => 'text-red-200',    'hover' => 'hover:text-white'],
        'christmas'        => ['bg' => 'background:linear-gradient(to bottom,#14532d,#166534,#14532d)',          'nav' => 'text-green-200',  'hover' => 'hover:text-white'],
        'summer'           => ['bg' => 'background:linear-gradient(to bottom,#78350f,#b45309,#78350f)',          'nav' => 'text-amber-200',  'hover' => 'hover:text-white'],
        'rainy_season'     => ['bg' => 'background:linear-gradient(to bottom,#1e3a5f,#1e40af,#1e3a5f)',         'nav' => 'text-blue-200',   'hover' => 'hover:text-white'],
        'holy_week'        => ['bg' => 'background:linear-gradient(to bottom,#3b0764,#6d28d9,#3b0764)',         'nav' => 'text-purple-200', 'hover' => 'hover:text-white'],
        'undas'            => ['bg' => 'background:linear-gradient(to bottom,#1c1917,#44403c,#1c1917)',         'nav' => 'text-stone-300',  'hover' => 'hover:text-white'],
        'new_year'         => ['bg' => 'background:linear-gradient(to bottom,#1e1b4b,#3730a3,#1e1b4b)',         'nav' => 'text-indigo-200', 'hover' => 'hover:text-white'],
        'independence_day' => ['bg' => 'background:linear-gradient(to bottom,#1e3a8a,#1d4ed8,#1e3a8a)',         'nav' => 'text-blue-200',   'hover' => 'hover:text-white'],
        'halloween'        => ['bg' => 'background:linear-gradient(to bottom,#1c1917,#7c2d12,#1c1917)',         'nav' => 'text-orange-300', 'hover' => 'hover:text-white'],
    ];
    $portalSidebarStyle = $portalThemeCfg[$portalTheme]['bg']  ?? '';
    $portalNavText      = $portalThemeCfg[$portalTheme]['nav'] ?? 'text-red-200';
@endphp

<aside id="portalSidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-[#7f1d1d] via-[#991b1b] to-[#7f1d1d]
              z-50 flex flex-col shadow-2xl -translate-x-full overflow-hidden"
       @if($portalSidebarStyle) style="{{ $portalSidebarStyle }}" @endif>

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 shrink-0">
        <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="UM" class="w-10 h-10 object-contain drop-shadow">
        <div class="flex-1 min-w-0">
            <div class="text-white font-bold text-base leading-tight">UM CLINIC</div>
            <div class="text-red-300 text-xs mt-0.5">Tagum City</div>
        </div>
        <button @click="closeSidebar()" class="p-1.5 rounded-lg text-red-300 hover:text-white hover:bg-white/10 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- NAV --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

    {{-- ─── STUDENT ─────────────────────── --}}
    @if($role === 'student')
        @php
            $snav = [
                ['student.dashboard',     'Dashboard',      'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['student.appointments',  'Appointments',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['student.history',       'My History',     'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['student.announcements', 'Announcements',  'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                ['student.health_safety', 'Health & Safety','M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['student.messages',      'Clinic Inbox',   'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['student.feedback',      'Feedback',       'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                ['student.profile',       'My Profile',     'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ];
        @endphp
        @foreach($snav as [$route, $label, $path])
        <a href="{{ route($route) }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ str_starts_with($cur, $route) ? 'nav-active' : $portalNavText . ' hover:bg-white/10 hover:text-white' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
            {{ $label }}
        </a>
        @endforeach
    @endif

    {{-- ─── STAFF ───────────────────────── --}}
    @if($role === 'staff')
        @php
            $snav = [
                ['staff.dashboard',      'Dashboard',      'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['staff.records',        'Medical Records','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['staff.inventory',      'Inventory',      'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['staff.patients',       'Patients',       'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['staff.appointments',   'Appointments',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['staff.messages',       'Messages',       'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['staff.announcements',  'Announcements',  'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                ['staff.feedback',       'Feedback',       'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                ['staff.reports.monthly',  'Reports',          'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['staff.reports.inventory','Inventory Report','M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['staff.logs',             'Logs',             'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ];
        @endphp
        @foreach($snav as [$route, $label, $path])
        <a href="{{ route($route) }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ str_starts_with($cur, str_replace('.monthly','',$route)) ? 'nav-active' : 'text-red-200 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
            {{ $label }}
        </a>
        @endforeach
    @endif

    {{-- ─── STA ─────────────────────────── --}}
    @if($role === 'sta')
        @php
            $snav = [
                ['staff.dashboard',    'Dashboard',      'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['staff.records',      'Medical Records','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['staff.inventory',    'Inventory',      'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['staff.patients',     'Patients',       'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['staff.appointments', 'Appointments',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['staff.messages',     'Messages',       'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ];
        @endphp
        @foreach($snav as [$route, $label, $path])
        <a href="{{ route($route) }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ str_starts_with($cur, $route) ? 'nav-active' : 'text-red-200 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
            {{ $label }}
        </a>
        @endforeach
    @endif

    {{-- ─── SUPERADMIN ──────────────────── --}}
    @if($role === 'superadmin')
        @php
            $snav = [
                ['admin.dashboard',    'Dashboard',      'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['admin.users',        'Manage Users',   'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['staff.records',      'Medical Records','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['staff.inventory',    'Inventory',      'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['staff.appointments', 'Appointments',   'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['staff.announcements','Announcements',  'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                ['staff.messages',     'Messages',       'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['admin.logs',         'Logs',           'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['admin.audit_logs',   'Audit Logs',     'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['admin.settings',     'Settings',       'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ['admin.backup',       'Backup',         'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
            ];
        @endphp
        @foreach($snav as [$route, $label, $path])
        <a href="{{ route($route) }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ str_starts_with($cur, $route) ? 'nav-active' : 'text-red-200 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
            {{ $label }}
        </a>
        @endforeach
    @endif

    </nav>

    {{-- Logout --}}
    <div class="px-4 pb-5 pt-3 border-t border-white/10 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-800/60 hover:bg-red-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-colors border border-red-700/50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════════════════════════════════
     MAIN WRAPPER
══════════════════════════════════════════ --}}
<div id="portalMain" class="min-h-screen flex flex-col transition-all duration-300">

    {{-- ── TOP BAR ── --}}
    <header id="portalTopbar" class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button @click="toggleSidebar()" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition" aria-label="Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="" class="w-6 h-6 object-contain hidden sm:block">
                <span class="font-semibold text-slate-700 text-sm sm:text-base">@yield('page_title', 'UM Clinic')</span>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Bell --}}
                <a href="{{ $role === 'student' ? route('student.announcements') : ($role === 'superadmin' ? route('admin.dashboard') : route('staff.announcements')) }}"
                   @click.prevent="clearBadge($event)"
                   class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span x-show="announcementCount > 0" x-cloak x-text="announcementCount"
                          class="absolute -top-0.5 -right-0.5 bg-red-600 text-white text-xs font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1 leading-none"></span>
                </a>
                {{-- Messages icon for all roles --}}
                @if(in_array($role, ['staff','sta','superadmin']))
                <a href="{{ route('staff.messages') }}" @click="clearMessages()" class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="messageCount > 0" x-cloak x-text="messageCount"
                          class="absolute -top-0.5 -right-0.5 bg-blue-600 text-white text-xs font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1 leading-none"></span>
                </a>
                @elseif($role === 'student')
                <a href="{{ route('student.messages') }}" @click="clearMessages()" class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="messageCount > 0" x-cloak x-text="messageCount"
                          class="absolute -top-0.5 -right-0.5 bg-blue-600 text-white text-xs font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1 leading-none"></span>
                </a>
                @endif
                {{-- User chip (clickable for students) --}}
                @if($role === 'student')
                <a href="{{ route('student.profile') }}"
                   class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 active:scale-95 rounded-xl px-3 py-1.5 transition-all group"
                   title="My Profile">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-700 to-red-900 flex items-center justify-center text-white text-xs font-bold shrink-0 group-hover:ring-2 group-hover:ring-red-400 transition-all">
                        {{ strtoupper(substr(Auth::user()->first_name,0,1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-slate-800 leading-none">{{ Auth::user()->full_name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5 leading-none">{{ strtoupper($role) }}</div>
                    </div>
                </a>
                @else
                <div class="flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-1.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-700 to-red-900 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->first_name,0,1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-slate-800 leading-none">{{ Auth::user()->full_name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5 leading-none">{{ strtoupper($role) }}</div>
                    </div>
                </div>
                @endif

                {{-- ── QUICK LOGOUT ── --}}
                <form method="POST" action="{{ route('logout') }}" style="display:contents">
                    @csrf
                    <button type="submit"
                            title="Logout"
                            class="relative p-2 rounded-xl text-slate-500 hover:bg-red-50 hover:text-red-600 active:scale-95 transition-all group"
                            style="line-height:0">
                        {{-- logout / door-arrow icon --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{-- Tooltip --}}
                        <span class="pointer-events-none absolute -bottom-8 left-1/2 -translate-x-1/2
                                     bg-slate-800 text-white text-[.65rem] font-semibold px-2 py-1 rounded-lg
                                     opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                            Logout
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ── CONTENT ── --}}
    <main class="flex-1 p-4 sm:p-6 max-w-screen-xl mx-auto w-full space-y-5">

        @if(session('success'))
        <div id="flashSuccess" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button onclick="this.closest('#flashSuccess').remove()" class="text-green-600 hover:text-green-800 font-bold">✕</button>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-sm space-y-1">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
        @endif

        @yield('content')

    </main>

    @include('partials.um_footer')
</div>

</div>{{-- /#portalApp --}}

@yield('scripts')

<script>
function portalApp() {
    return {
        sidebarOpen: false,
        announcementCount: 0,
        messageCount: 0,

        init() {
            document.addEventListener('keydown', e => { if (e.key === 'Escape') this.closeSidebar(); });
            this.pollBadges();
            setInterval(() => this.pollBadges(), 30000);
        },

        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        closeSidebar()  { this.sidebarOpen = false; },

        /* Bell click: stamp last-seen in DB then navigate */
        clearBadge(event) {
            var href = event.currentTarget.getAttribute('href');
            this.announcementCount = 0;
            fetch('/api/mark-announcement-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).finally(() => {
                if (href) window.location.href = href;
            });
        },

        clearMessages() {
            this.messageCount = 0;
        },

        pollBadges() {
            fetch('/api/unread-announcements', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null)
                .then(d => { if (d) this.announcementCount = d.count || 0; })
                .catch(() => {});

            fetch('/api/unread-messages', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null)
                .then(d => { if (d) this.messageCount = d.count || 0; })
                .catch(() => {});
        }
    };
}
document.addEventListener('DOMContentLoaded',()=>{
    const f=document.getElementById('flashSuccess');
    if(f) setTimeout(()=>{f.style.transition='opacity .4s';f.style.opacity='0';setTimeout(()=>f.remove(),400);},5000);
});
</script>
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
