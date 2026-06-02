@extends('layouts.portal')
@section('title','Patients – UM Clinic')
@section('page_title','Patients')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Patient Records</h2>
        <p class="text-sm text-slate-500 mt-0.5">Search and view student patient information</p>
    </div>
</div>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or ID..."
           class="f-input max-w-xs">
    <button type="submit" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Search</button>
    @if($search)
    <a href="{{ route('staff.patients') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Email</th>
                    <th>Visits</th>
                    <th>Last Visit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $p)
                @php $lastVisit = $p->medicalRecords->first(); @endphp
                <tr>
                    <td class="font-mono text-xs text-slate-500">{{ $p->id_number }}</td>
                    <td class="font-medium">{{ $p->full_name }}</td>
                    <td>{{ $p->course ?? '—' }}</td>
                    <td class="text-sm text-slate-500">{{ $p->email ?? '—' }}</td>
                    <td class="text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                              {{ $p->medical_records_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                            {{ $p->medical_records_count }}
                        </span>
                    </td>
                    <td class="text-sm text-slate-600 whitespace-nowrap">
                        @if($lastVisit)
                            <span class="text-slate-700">{{ $lastVisit->date_consulted->format('M d, Y') }}</span>
                            <div class="text-xs text-slate-400 truncate max-w-[100px]">{{ $lastVisit->reason }}</div>
                        @else
                            <span class="text-slate-400 text-xs">No visits</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1.5 flex-wrap">
                            <a href="{{ route('staff.appointments') }}?student={{ $p->id_number }}"
                               class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold px-2.5 py-1 rounded-lg transition">Appts</a>
                            <a href="{{ route('staff.records') }}?search={{ $p->id_number }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition">Records</a>
                            <a href="{{ route('staff.records') }}?add_for={{ $p->id_number }}"
                               class="text-xs bg-green-100 hover:bg-green-200 text-green-800 font-semibold px-2.5 py-1 rounded-lg transition">
                               + Record
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-slate-400">No patients found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
