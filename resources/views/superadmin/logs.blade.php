@extends('layouts.portal')
@section('title','System Logs – UM Clinic')
@section('page_title','System Logs')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">System Logs</h2>
        <p class="text-sm text-slate-500 mt-0.5">Monitor all system-level activity</p>
    </div>
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $search }}" placeholder="Search logs..."
           class="f-input max-w-xs">
    <button type="submit" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Search</button>
    @if($search)
    <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead><tr><th>Timestamp</th><th>User</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-xs text-slate-400 whitespace-nowrap">{{ $log->timestamp->format('M d, Y H:i:s') }}</td>
                    <td class="font-medium text-sm">{{ $log->user_name }}</td>
                    <td class="text-sm">{{ $log->action }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-10 text-slate-400">No logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
