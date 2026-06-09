@extends('layouts.portal')
@section('title','My Medical History – UM Clinic')
@section('page_title','My History')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Consultation History</h2>
        <p class="text-sm text-slate-500 mt-0.5">A complete record of your clinic visits</p>
    </div>
    <a href="{{ route('student.appointments') }}"
       class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Appointment
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-700 text-sm">All Records</h3>
        <span class="text-xs text-slate-400 font-medium">{{ $records->count() }} record(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Date</th><th>Doctor</th><th>Reason</th><th>Medicine</th><th>Qty</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                <tr>
                    <td class="font-medium whitespace-nowrap">{{ $rec->date_consulted?->format('M d, Y') ?? '—' }}</td>
                    <td>{{ $rec->doctor }}</td>
                    <td>{{ $rec->reason }}</td>
                    <td>{{ $rec->medicine ?: '—' }}</td>
                    <td>{{ $rec->quantity ?: '—' }}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $rec->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-2xl">📋</div>
                        <p class="text-slate-400 text-sm font-medium">No consultation records yet</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
