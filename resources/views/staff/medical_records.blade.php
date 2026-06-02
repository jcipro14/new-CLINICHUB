@extends('layouts.portal')
@section('title','Medical Records – UM Clinic')
@section('page_title','Medical Records')

@section('content')

<div x-data="{ showAdd: {{ ($autoFillAppt || $addForStudentId) ? 'true' : 'false' }}, showEdit: false, editData: {} }"
     @portal-edit.window="editData = $event.detail; showEdit = true">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Medical Records</h2>
            <p class="text-sm text-slate-500 mt-0.5">All student consultation records</p>
        </div>
        <button @click="showAdd = true"
                class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Record
        </button>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or ID..." class="f-input max-w-xs">
        <button type="submit" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Search</button>
        @if(request('search'))
        <a href="{{ route('staff.records') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead>
                    <tr><th>#</th><th>Student</th><th>ID</th><th>Staff</th><th>Date</th><th>Doctor</th><th>Reason</th><th>Medicine</th><th>Qty</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <script type="application/json" id="rec-{{ $rec->med_id }}">{!! json_encode($rec, JSON_HEX_TAG) !!}</script>
                    <tr>
                        <td class="text-xs text-slate-400">{{ $rec->med_id }}</td>
                        <td class="font-medium">{{ $rec->name }}</td>
                        <td class="text-xs text-slate-400">{{ $rec->student_id }}</td>
                        <td>{{ $rec->staff }}</td>
                        <td class="whitespace-nowrap">{{ $rec->date_consulted->format('M d, Y') }}</td>
                        <td>{{ $rec->doctor }}</td>
                        <td class="max-w-[120px] truncate">{{ $rec->reason }}</td>
                        <td>{{ $rec->medicine ?: '—' }}</td>
                        <td>{{ $rec->quantity ?: '—' }}</td>
                        <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ $rec->status }}</span></td>
                        <td>
                            <div class="flex gap-1.5">
                                <button type="button"
                                        onclick="portalEditRec({{ $rec->med_id }})"
                                        class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition">Edit</button>
                                @if(Auth::user()->role === 'superadmin')
                                <button type="button"
                                        onclick="deleteRecord({{ $rec->med_id }})"
                                        class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Delete</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center py-10 text-slate-400">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── ADD MODAL ── --}}
    <div x-show="showAdd" x-cloak class="portal-modal-overlay" @click.self="showAdd = false" style="display:none">
        <div class="portal-modal-box wide">
            <div class="portal-modal-header">
                <h3>➕ Add Medical Record</h3>
                <button @click="showAdd = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('staff.records.store') }}" id="addRecordForm">
                @csrf
                <input type="hidden" name="appointment_id" id="addApptIdHidden"
                       value="{{ $autoFillAppt?->appointment_id ?? '' }}">
                <div class="portal-modal-body grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="f-group sm:col-span-2">
                        <label class="f-label">Student <span class="text-red-500">*</span></label>
                        <select name="student_id" id="addStudentSelect" class="f-select" required
                                onchange="onStudentChange(this.value)">
                            <option value="">— Select Student —</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id_number }}"
                                {{ (($autoFillAppt && $autoFillAppt->student_id === $s->id_number) || $addForStudentId === $s->id_number) ? 'selected' : '' }}>
                                {{ $s->full_name }} ({{ $s->id_number }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Appointment selector (auto-populated via AJAX) --}}
                    <div class="f-group sm:col-span-2" id="apptSelectorWrap" style="{{ $autoFillAppt ? '' : 'display:none' }}">
                        <label class="f-label">Linked Appointment <span class="text-xs text-green-600 font-normal">(auto-fills reason & doctor)</span></label>
                        <select id="addApptSelect" class="f-select" onchange="onApptChange(this.value)">
                            <option value="">— None —</option>
                            @if($autoFillAppt)
                            <option value="{{ $autoFillAppt->appointment_id }}" selected>
                                {{ $autoFillAppt->next_consultation?->format('M d, Y') ? $autoFillAppt->next_consultation->format('M d, Y').' – ' : '' }}{{ $autoFillAppt->reason }} ({{ $autoFillAppt->status }})
                            </option>
                            @endif
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date Consulted <span class="text-red-500">*</span></label>
                        <input type="date" name="date_consulted" id="addDateConsulted"
                               value="{{ date('Y-m-d') }}" class="f-input" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Doctor <span class="text-red-500">*</span></label>
                        <input type="text" name="doctor" id="addDoctor" class="f-input" placeholder="Doctor name"
                               value="{{ $autoFillAppt?->doctor ?? '' }}" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Reason <span class="text-red-500">*</span></label>
                        <select name="reason" id="addReason" class="f-select" required>
                            <option value="">— Select Reason —</option>
                            @foreach(\App\Http\Controllers\AppointmentController::REASON_OPTIONS as $r)
                            <option value="{{ $r }}" {{ ($autoFillAppt && $autoFillAppt->reason === $r) ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="f-select" required>
                            <option value="Treated" selected>Treated</option>
                            <option value="Completed">Completed</option>
                            <option value="For follow-up">For follow-up</option>
                            <option value="Referred">Referred</option>
                            <option value="Rest only">Rest only</option>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Medicine Dispensed</label>
                        <select name="medicine" class="f-select">
                            <option value="">— None —</option>
                            @foreach($medicines as $med)<option value="{{ $med }}">{{ $med }}</option>@endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" class="f-input">
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showAdd = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div x-show="showEdit" x-cloak class="portal-modal-overlay" @click.self="showEdit = false" style="display:none">
        <div class="portal-modal-box wide">
            <div class="portal-modal-header">
                <h3>✏️ Edit Medical Record</h3>
                <button @click="showEdit = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" :action="'/staff/medical-records/' + editData.med_id">
                @csrf
                <div class="portal-modal-body grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="f-group">
                        <label class="f-label">Date Consulted <span class="text-red-500">*</span></label>
                        <input type="date" name="date_consulted" class="f-input" required
                               x-effect="$el.value = (editData.date_consulted||'').toString().substring(0,10)">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Doctor <span class="text-red-500">*</span></label>
                        <input type="text" name="doctor" class="f-input" x-model="editData.doctor" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Reason <span class="text-red-500">*</span></label>
                        <select name="reason" class="f-select" x-model="editData.reason" required>
                            <option value="">— Select —</option>
                            @foreach(['Fever','Headache / Migraine','Cough / Colds','Body pain / Muscle pain / Back pain',
                                      'Sore throat / Tonsil-related','Stomach ache / Abdominal pain','Dizziness / Vertigo',
                                      'Vomiting','Diarrhea / LBM','Allergic reaction / Rashes','Wound / Injury / Sprain',
                                      'BP check / Vital signs','General consultation / Check-up',
                                      'Dental (Toothache / Oral concern)','Rest only','Other medical complaint'] as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="f-select" x-model="editData.status" required>
                            <option value="Treated">Treated</option>
                            <option value="Completed">Completed</option>
                            <option value="For follow-up">For follow-up</option>
                            <option value="Referred">Referred</option>
                            <option value="Rest only">Rest only</option>
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Medicine</label>
                        <input type="text" name="medicine" class="f-input" x-model="editData.medicine">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Quantity</label>
                        <input type="number" name="quantity" class="f-input" x-model="editData.quantity" min="0">
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showEdit = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- /x-data --}}
@endsection

@section('scripts')
<script>
/* Delete record — JS approach avoids form-in-table-cell browser quirks */
function deleteRecord(id) {
    if (!confirm('Are you sure you want to delete this medical record? This cannot be undone.')) return;
    const form   = document.createElement('form');
    form.method  = 'POST';
    form.action  = '/staff/medical-records/' + id + '/delete';
    const csrf   = document.createElement('input');
    csrf.type    = 'hidden';
    csrf.name    = '_token';
    csrf.value   = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}

function portalEditRec(id) {
    var el = document.getElementById('rec-' + id);
    if (!el) return;
    try {
        window.dispatchEvent(new CustomEvent('portal-edit', { detail: JSON.parse(el.textContent) }));
    } catch (e) {}
}

function onStudentChange(studentId) {
    const wrap   = document.getElementById('apptSelectorWrap');
    const select = document.getElementById('addApptSelect');
    const hidden = document.getElementById('addApptIdHidden');
    hidden.value = '';
    select.innerHTML = '<option value="">— None —</option>';

    if (!studentId) { wrap.style.display = 'none'; return; }

    fetch('/api/student-appointments?student_id=' + encodeURIComponent(studentId))
        .then(r => r.json())
        .then(appts => {
            if (!appts.length) { wrap.style.display = 'none'; return; }
            appts.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.label;
                select.appendChild(opt);
            });
            wrap.style.display = '';
        })
        .catch(() => { wrap.style.display = 'none'; });
}

function onApptChange(apptId) {
    const hidden = document.getElementById('addApptIdHidden');
    hidden.value = apptId || '';
    if (!apptId) return;

    // Pre-fill reason and doctor from selected appointment
    const select = document.getElementById('addApptSelect');
    const opt    = select.options[select.selectedIndex];
    // We stored full data via AJAX — re-fetch to get individual fields
    fetch('/api/student-appointments?student_id=' + document.getElementById('addStudentSelect').value)
        .then(r => r.json())
        .then(appts => {
            const appt = appts.find(a => String(a.id) === String(apptId));
            if (!appt) return;
            if (appt.reason) {
                const reasonSel = document.getElementById('addReason');
                for (let o of reasonSel.options) {
                    if (o.value === appt.reason) { reasonSel.value = appt.reason; break; }
                }
            }
            if (appt.doctor) {
                document.getElementById('addDoctor').value = appt.doctor;
            }
        })
        .catch(() => {});
}

// Auto-load appointments if a student is pre-filled (from ?appt_id)
document.addEventListener('DOMContentLoaded', function () {
    const studentSel = document.getElementById('addStudentSelect');
    if (studentSel && studentSel.value) {
        onStudentChange(studentSel.value);
    }
});
</script>
@endsection
