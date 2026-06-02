@extends('layouts.portal')
@section('title','Appointments – UM Clinic')
@section('page_title','Appointments')

@section('content')

<div x-data="{ showAdd: false, showEdit: false, editData: {}, addStatus: 'Pending' }"
     @portal-edit.window="editData = $event.detail; showEdit = true">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Appointments</h2>
            @if($selectedStudent)
            <p class="text-sm mt-0.5 flex items-center gap-2">
                <span class="text-slate-500">Filtered by student:</span>
                <span class="bg-blue-100 text-blue-700 font-semibold text-xs px-2 py-0.5 rounded-full">{{ $selectedStudent }}</span>
                <a href="{{ route('staff.appointments') }}" class="text-xs text-red-600 hover:underline">Clear filter</a>
            </p>
            @else
            <p class="text-sm text-slate-500 mt-0.5">Manage all clinic appointments</p>
            @endif
        </div>
        <button @click="showAdd = true"
                class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Appointment
        </button>
    </div>

    {{-- View toggle: All / Schedule / Deleted --}}
    <div class="flex gap-2 mb-4">
        <a href="{{ route('staff.appointments') }}{{ $selectedStudent ? '?student='.$selectedStudent : '' }}"
           class="text-sm font-semibold px-4 py-2 rounded-xl transition {{ (!$scheduleView && !$deletedView) ? 'bg-red-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            All Appointments
        </a>
        <a href="{{ route('staff.appointments') }}?view=schedule{{ $selectedStudent ? '&student='.$selectedStudent : '' }}"
           class="text-sm font-semibold px-4 py-2 rounded-xl transition {{ $scheduleView ? 'bg-red-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            🗓 Upcoming Schedule
        </a>
        <a href="{{ route('staff.appointments') }}?view=deleted{{ $selectedStudent ? '&student='.$selectedStudent : '' }}"
           class="text-sm font-semibold px-4 py-2 rounded-xl transition {{ $deletedView ? 'bg-slate-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            🗑 Deleted
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @if($deletedView)
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
            <span class="text-sm text-slate-500">Showing soft-deleted appointments — restore any record below to bring it back.</span>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead>
                    @if($deletedView)
                    <tr><th>#</th><th>Student</th><th>ID</th><th>Reason</th><th>Status</th><th>Deleted At</th><th>Action</th></tr>
                    @elseif($scheduleView)
                    <tr><th>Date</th><th>Student</th><th>ID</th><th>Doctor</th><th>Reason</th><th>Status</th><th>Action</th></tr>
                    @else
                    <tr><th>#</th><th>Student</th><th>ID</th><th>Staff</th><th>Date</th><th>Doctor</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    @if(!$deletedView)
                    <script type="application/json" id="appt-{{ $appt->appointment_id }}">{!! json_encode($appt, JSON_HEX_TAG) !!}</script>
                    @endif
                    @if($deletedView)
                    <tr class="opacity-75">
                        <td class="text-xs text-slate-400">{{ $appt->appointment_id }}</td>
                        <td class="font-medium">{{ $appt->name }}</td>
                        <td class="text-xs text-slate-400">{{ $appt->student_id }}</td>
                        <td class="max-w-[140px] truncate">{{ $appt->reason ?: '—' }}</td>
                        <td><span class="badge-status {{ strtolower($appt->status) }}">{{ $appt->status }}</span></td>
                        <td class="text-xs text-slate-400 whitespace-nowrap">{{ $appt->deleted_at?->format('M d, Y g:i A') }}</td>
                        <td>
                            <form method="POST" action="{{ route('staff.appointments.restore', $appt->appointment_id) }}"
                                  onsubmit="return confirm('Restore this appointment?')">
                                @csrf
                                <button type="submit"
                                        class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold px-2.5 py-1 rounded-lg transition whitespace-nowrap">
                                    ↩ Restore
                                </button>
                            </form>
                        </td>
                    </tr>
                    @elseif($scheduleView)
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
                    @else
                    <tr>
                        <td class="text-xs text-slate-400">{{ $appt->appointment_id }}</td>
                        <td class="font-medium">{{ $appt->name }}</td>
                        <td class="text-xs text-slate-400">{{ $appt->student_id }}</td>
                        <td>{{ $appt->staff ?: '—' }}</td>
                        <td class="whitespace-nowrap">{{ $appt->next_consultation?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $appt->doctor ?: '—' }}</td>
                        <td class="max-w-[120px] truncate">{{ $appt->reason ?: '—' }}</td>
                        <td><span class="badge-status {{ strtolower($appt->status) }}">{{ $appt->status }}</span></td>
                        <td>
                            <div class="flex gap-1.5 flex-wrap">
                                @if(in_array($appt->status, ['Pending','Upcoming']))
                                <a href="{{ route('staff.records') }}?appt_id={{ $appt->appointment_id }}"
                                   class="text-xs bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-2.5 py-1 rounded-lg transition whitespace-nowrap">📋 Record</a>
                                @endif
                                <button type="button"
                                        onclick="portalEditAppt({{ $appt->appointment_id }})"
                                        class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition">Edit</button>
                                <button type="button"
                                        onclick="deleteAppt({{ $appt->appointment_id }})"
                                        class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="{{ $deletedView ? 7 : ($scheduleView ? 7 : 9) }}" class="text-center py-10 text-slate-400">
                        {{ $deletedView ? 'No deleted appointments.' : 'No appointments found.' }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── ADD MODAL ── --}}
    <div x-show="showAdd" x-cloak class="portal-modal-overlay" @click.self="showAdd = false" style="display:none">
        <div class="portal-modal-box wide">
            <div class="portal-modal-header">
                <h3>📅 Add Appointment</h3>
                <button @click="showAdd = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('staff.appointments.store') }}">
                @csrf
                <div class="portal-modal-body grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="f-group sm:col-span-2">
                        <label class="f-label">Student <span class="text-red-500">*</span></label>
                        <select name="target_student" class="f-select" required>
                            <option value="">— Select Student —</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id_number }}">{{ $s->full_name }} ({{ $s->id_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Assigned Staff</label>
                        <select name="staff" class="f-select">
                            <option value="">— Optional —</option>
                            @foreach($staffOptions as $opt)<option>{{ $opt }}</option>@endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Doctor</label>
                        <input type="text" name="doctor" class="f-input" placeholder="Doctor name">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Consultation Date</label>
                        <input type="date" name="next_consultation" class="f-input">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Reason <span class="text-red-500">*</span></label>
                        <select name="reason" class="f-select" required>
                            <option value="">— Select Reason —</option>
                            @foreach($reasonOptions as $r)<option>{{ $r }}</option>@endforeach
                        </select>
                    </div>
                    <div class="f-group sm:col-span-2">
                        <label class="f-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="f-select" required x-model="addStatus">
                            <option value="Pending">Pending</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    {{-- Medical Record section: appears when Completed is selected --}}
                    <div x-show="addStatus === 'Completed'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="sm:col-span-2">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-xs">✓</div>
                                <span class="font-black text-emerald-800 text-sm">Medical Record — Auto Created</span>
                                <span class="text-[.65rem] text-emerald-600 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">Saved to Medical Records instantly</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                                <div class="f-group">
                                    <label class="f-label">Date Consulted</label>
                                    <input type="date" name="rec_date" class="f-input" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Record Status</label>
                                    <select name="rec_status" class="f-select">
                                        <option value="Completed">Completed</option>
                                        <option value="Treated">Treated</option>
                                        <option value="For follow-up">For follow-up</option>
                                        <option value="Referred">Referred</option>
                                        <option value="Rest only">Rest only</option>
                                    </select>
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Medicine Dispensed</label>
                                    <select name="rec_medicine" class="f-select">
                                        <option value="">— None —</option>
                                        @foreach($medicines as $med)
                                        <option value="{{ $med }}">{{ $med }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Quantity</label>
                                    <input type="number" name="rec_quantity" class="f-input" value="0" min="0">
                                </div>
                            </div>
                            <p class="text-[.68rem] text-emerald-600 mt-1">💡 A medical record is created automatically using the reason and doctor above.</p>
                        </div>
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showAdd = false; addStatus = 'Pending'"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-white text-sm font-semibold transition shadow-sm"
                            :class="addStatus === 'Completed' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-700 hover:bg-red-800'">
                        <span x-text="addStatus === 'Completed' ? '✓ Save & Create Record' : 'Save Appointment'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div x-show="showEdit" x-cloak class="portal-modal-overlay" @click.self="showEdit = false" style="display:none">
        <div class="portal-modal-box wide" style="max-width:700px">
            <div class="portal-modal-header">
                <h3>✏️ Edit Appointment</h3>
                <button @click="showEdit = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" :action="'/staff/appointments/' + editData.appointment_id + '/edit'">
                @csrf
                <div class="portal-modal-body grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="f-group">
                        <label class="f-label">Assigned Staff</label>
                        <select name="staff" class="f-select" x-model="editData.staff">
                            <option value="">— Optional —</option>
                            @foreach($staffOptions as $opt)<option value="{{ $opt }}">{{ $opt }}</option>@endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Doctor</label>
                        <input type="text" name="doctor" class="f-input" x-model="editData.doctor">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Consultation Date</label>
                        <input type="date" name="next_consultation" class="f-input"
                               x-effect="$el.value = (editData.next_consultation||'').toString().substring(0,10)">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Reason <span class="text-red-500">*</span></label>
                        <select name="reason" class="f-select" x-model="editData.reason" required>
                            <option value="">— Select Reason —</option>
                            @foreach($reasonOptions as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach
                        </select>
                    </div>
                    <div class="f-group sm:col-span-2">
                        <label class="f-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="f-select" x-model="editData.status" required>
                            <option value="Pending">Pending</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    {{-- ── MEDICAL RECORD SECTION (shows when Completed) ── --}}
                    <div x-show="editData.status === 'Completed'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="sm:col-span-2 mt-1">

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-xs">✓</div>
                                <span class="font-black text-emerald-800 text-sm">Medical Record — Auto Created</span>
                                <span class="text-[.65rem] text-emerald-600 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">
                                    Saved instantly to Medical Records
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                                <div class="f-group">
                                    <label class="f-label">Date Consulted <span class="text-red-500">*</span></label>
                                    <input type="date" name="rec_date" class="f-input"
                                           value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Record Status <span class="text-red-500">*</span></label>
                                    <select name="rec_status" class="f-select">
                                        <option value="Completed">Completed</option>
                                        <option value="Treated">Treated</option>
                                        <option value="For follow-up">For follow-up</option>
                                        <option value="Referred">Referred</option>
                                        <option value="Rest only">Rest only</option>
                                    </select>
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Medicine Dispensed</label>
                                    <select name="rec_medicine" class="f-select">
                                        <option value="">— None —</option>
                                        @foreach($medicines as $med)
                                        <option value="{{ $med }}">{{ $med }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="f-group">
                                    <label class="f-label">Quantity</label>
                                    <input type="number" name="rec_quantity" class="f-input"
                                           value="0" min="0" placeholder="0">
                                </div>
                            </div>
                            <p class="text-[.68rem] text-emerald-600 mt-1">
                                💡 A medical record will be created automatically using the appointment's reason and doctor. Medicine and quantity are optional.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="portal-modal-footer">
                    <button type="button" @click="showEdit = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-white text-sm font-semibold transition shadow-sm"
                            :class="editData.status === 'Completed'
                                ? 'bg-emerald-600 hover:bg-emerald-700'
                                : 'bg-red-700 hover:bg-red-800'">
                        <span x-text="editData.status === 'Completed' ? '✓ Complete & Save Record' : 'Update Appointment'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- /x-data --}}
@endsection

@section('scripts')
<script>
function deleteAppt(id) {
    // Read appointment data from the embedded JSON
    const el     = document.getElementById('appt-' + id);
    const appt   = el ? JSON.parse(el.textContent) : {};
    const status = appt.status || '';

    if (status === 'Completed') {
        // Show two-option dialog for Completed appointments
        const choice = confirm(
            'This appointment is Completed and may have a linked medical record.\n\n' +
            'Click OK  →  Delete appointment AND its medical record.\n' +
            'Click Cancel  →  Keep the medical record (appointment only deleted).'
        );

        // choice = true → delete both | false → appointment only | null = no action
        // We use a second confirm for "appointment only" vs "do nothing"
        let withRecord = '0';
        if (choice) {
            withRecord = '1';
        } else {
            if (!confirm('Delete the appointment only (medical record will be kept)?')) return;
        }

        submitDeleteForm(id, withRecord);
    } else {
        if (!confirm('Delete this appointment?')) return;
        submitDeleteForm(id, '0');
    }
}

function submitDeleteForm(id, withRecord) {
    const form    = document.createElement('form');
    form.method   = 'POST';
    form.action   = '/staff/appointments/' + id + '/delete';

    const csrf    = document.createElement('input');
    csrf.type     = 'hidden'; csrf.name = '_token';
    csrf.value    = document.querySelector('meta[name="csrf-token"]').content;

    const recFlag = document.createElement('input');
    recFlag.type  = 'hidden'; recFlag.name = 'with_record';
    recFlag.value = withRecord;

    form.appendChild(csrf);
    form.appendChild(recFlag);
    document.body.appendChild(form);
    form.submit();
}

function portalEditAppt(id) {
    var el = document.getElementById('appt-' + id);
    if (!el) { console.error('Appointment row data not found:', id); return; }
    try {
        window.dispatchEvent(new CustomEvent('portal-edit', { detail: JSON.parse(el.textContent) }));
    } catch (e) {
        console.error('Failed to parse appointment row data:', e);
    }
}
</script>
@endsection
