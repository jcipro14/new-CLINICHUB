@extends('layouts.portal')
@section('title','Schedule – UM Clinic')
@section('page_title','Schedule')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Appointment Schedule</h2>
        <p class="text-sm text-slate-500 mt-0.5">Upcoming and scheduled appointments</p>
    </div>
    <a href="{{ route('staff.appointments') }}"
       class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        Manage All →
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr><th>Date</th><th>Student</th><th>ID</th><th>Doctor</th><th>Reason</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($appointments as $appt)
                <tr>
                    <td class="font-medium whitespace-nowrap">{{ $appt->next_consultation?->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('staff.records') }}?search={{ $appt->student_id }}"
                           class="hover:text-red-700 hover:underline transition">{{ $appt->name }}</a>
                    </td>
                    <td class="text-xs text-slate-400">{{ $appt->student_id }}</td>
                    <td>{{ $appt->doctor ?: 'TBA' }}</td>
                    <td class="max-w-[140px] truncate">{{ $appt->reason ?: '—' }}</td>
                    <td><span class="badge-status {{ strtolower($appt->status) }}">{{ $appt->status }}</span></td>
                    <td>
                        <a href="{{ route('staff.records') }}?appt_id={{ $appt->appointment_id }}"
                           class="inline-flex items-center gap-1 text-xs bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-2.5 py-1 rounded-lg transition">
                            📋 Create Record
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-slate-400">No upcoming appointments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
