@extends('layouts.portal')
@section('title','Database Backup – UM Clinic')
@section('page_title','Backup')

@section('content')
<div class="max-w-2xl space-y-5">

    <div class="mb-1">
        <h2 class="text-xl font-bold text-slate-800">Database Backup</h2>
        <p class="text-sm text-slate-500 mt-0.5">Download and track full SQL backups of the ClinicHub database</p>
    </div>

    {{-- Download Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
        <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">💾</div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Download SQL Backup</h3>
        <p class="text-slate-500 text-sm mb-1">Click below to download a full SQL backup of the ClinicHub database.</p>
        <p class="text-slate-400 text-xs mb-4">The file will include all tables and data at the time of download.</p>

        @if($backupHistory->isNotEmpty())
        @php $last = $backupHistory->first(); @endphp
        <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-500 text-xs px-3 py-1.5 rounded-full mb-5">
            <span>🕐</span>
            Last backup: {{ \Carbon\Carbon::parse($last->timestamp)->format('M d, Y g:i A') }}
            <span class="text-slate-400">({{ \Carbon\Carbon::parse($last->timestamp)->diffForHumans() }})</span>
        </div>
        @endif

        <div>
            <form method="POST" action="{{ route('admin.backup.download') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white font-semibold px-6 py-3 rounded-xl transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download SQL Backup
                </button>
            </form>
        </div>
    </div>

    {{-- Backup History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center">🕐</span>
                Backup History
                <span class="text-xs text-slate-400 font-normal ml-1">(last 10 downloads)</span>
            </h3>
            <span class="text-xs text-slate-400">{{ $backupHistory->count() }} record(s)</span>
        </div>

        @if($backupHistory->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-slate-400">
            <span class="text-3xl mb-2">📭</span>
            <p class="text-sm font-medium">No backup history yet.</p>
            <p class="text-xs mt-1">Each download is automatically recorded here.</p>
        </div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($backupHistory as $i => $log)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition">
                <div class="w-8 h-8 {{ $i === 0 ? 'bg-purple-100' : 'bg-slate-100' }} rounded-xl flex items-center justify-center text-sm shrink-0">
                    {{ $i === 0 ? '💾' : '📄' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 flex items-center gap-1.5">
                        Backup Downloaded
                        @if($i === 0)<span class="text-xs bg-green-100 text-green-700 font-semibold px-1.5 py-0.5 rounded-full">Latest</span>@endif
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        By: <span class="font-medium">{{ $log->user_id }}</span>
                        &bull;
                        {{ \Carbon\Carbon::parse($log->timestamp)->format('M d, Y g:i A') }}
                    </div>
                </div>
                <span class="text-xs text-slate-400 shrink-0 hidden sm:block">{{ \Carbon\Carbon::parse($log->timestamp)->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Backup Tips --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <h4 class="text-sm font-bold text-amber-800 flex items-center gap-2 mb-2">
            <span>💡</span> Backup Best Practices
        </h4>
        <ul class="text-xs text-amber-700 space-y-1 list-disc list-inside">
            <li>Download a backup before any major system changes or updates</li>
            <li>Store backup files in a secure, off-system location (external drive or cloud)</li>
            <li>Perform regular backups — at least once a week during active use</li>
            <li>Keep at least 3 recent backup copies for redundancy</li>
        </ul>
    </div>

</div>
@endsection
