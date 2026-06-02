@extends('layouts.portal')
@section('title','My Appointments – UM Clinic')
@section('page_title','My Appointments')

@section('content')
<div x-data="{ showRequest: false }">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">My Appointments</h2>
            <p class="text-sm text-slate-500 mt-0.5">Track and manage your clinic appointments</p>
        </div>
        <button @click="showRequest = true"
                class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Request Appointment
        </button>
    </div>

    {{-- Table card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700 text-sm">All Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Requested Date</th><th>Doctor</th><th>Reason</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td class="font-medium">{{ $appt->next_consultation?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $appt->doctor ?: 'TBA' }}</td>
                        <td class="max-w-[180px] truncate">{{ $appt->reason ?: '—' }}</td>
                        <td><span class="badge-status {{ strtolower($appt->status) }}">{{ $appt->status }}</span></td>
                        <td>
                            @if($appt->needs_confirmation && $appt->status === 'Upcoming')
                            <div class="flex gap-1.5">
                                <button onclick="respond({{ $appt->appointment_id }},'accept')"
                                        class="text-xs bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-2.5 py-1 rounded-lg transition">✅ Accept</button>
                                <button onclick="respond({{ $appt->appointment_id }},'cancel')"
                                        class="text-xs bg-red-100 hover:bg-red-200 text-red-800 font-semibold px-2.5 py-1 rounded-lg transition">❌ Cancel</button>
                            </div>
                            @elseif($appt->status === 'Completed')
                            <a href="{{ route('student.history') }}"
                               class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold px-2.5 py-1 rounded-lg transition">
                                📋 View Record
                            </a>
                            @elseif($appt->status === 'Cancelled')
                            <span class="text-xs text-slate-400">—</span>
                            @else
                            <span class="text-xs text-slate-400 italic">Awaiting clinic</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-10 text-slate-400">No appointments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── REQUEST MODAL ── --}}
    <div x-show="showRequest" x-cloak class="portal-modal-overlay" @click.self="showRequest=false" style="display:none">
        <div class="portal-modal-box wide">
            <div class="portal-modal-header">
                <h3>📅 Request Appointment</h3>
                <button @click="showRequest=false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('student.appointments.request') }}">
                @csrf
                <div class="portal-modal-body grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="f-group">
                        <label class="f-label">Reason for Visit <span class="text-red-500">*</span></label>
                        <select name="reason" class="f-select" required>
                            <option value="">— Select Reason —</option>
                            @foreach($reasonOptions as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Preferred Date <span class="text-slate-400 text-xs font-normal">(optional)</span></label>
                        <input type="date" name="next_consultation" class="f-input"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <p class="text-xs text-slate-400 mt-1">Staff will confirm or adjust the date.</p>
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showRequest=false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function respond(id, action) {
    if(!confirm(`Are you sure you want to ${action} this appointment?`)) return;
    fetch('{{ route("student.appointments.action") }}',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body:JSON.stringify({appointment_id:id,action:action})
    }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message||'Error'); });
}
</script>
@endsection
