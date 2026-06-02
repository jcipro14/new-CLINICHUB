@extends('layouts.portal')
@section('title','Audit Logs – UM Clinic')
@section('page_title','Audit Logs')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Audit Logs</h2>
        <p class="text-sm text-slate-500 mt-0.5">Full audit trail of system activity</p>
    </div>
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $search }}" placeholder="Search by user or action..."
           class="f-input max-w-xs">
    <button type="submit" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Search</button>
    @if($search)
    <a href="{{ route('admin.audit_logs') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead><tr><th>Timestamp</th><th>User ID</th><th>Role</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-xs text-slate-400 whitespace-nowrap">{{ $log->timestamp->format('M d, Y H:i:s') }}</td>
                    <td class="font-mono text-xs text-slate-600">{{ $log->user_id }}</td>
                    <td><span class="badge-role {{ strtolower($log->role) }}">{{ strtoupper($log->role) }}</span></td>
                    <td class="font-medium text-sm">{{ $log->action }}</td>
                    <td class="text-sm text-slate-500 max-w-[200px] truncate">{{ $log->details }}</td>
                    <td class="font-mono text-xs text-slate-400">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-slate-400">No audit logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
