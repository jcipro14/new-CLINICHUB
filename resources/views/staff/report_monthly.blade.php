@extends('layouts.portal')
@section('title','Monthly Report – UM Clinic')
@section('page_title','Reports')

@section('content')
<div class="flex items-center justify-between mb-5 no-print">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Monthly Consultation Report</h2>
        <p class="text-sm text-slate-500 mt-0.5">Filter and print consultation data</p>
    </div>
    <button onclick="window.print()"
            class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        🖨 Print Report
    </button>
</div>

{{-- Filter --}}
<form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-wrap gap-4 items-end mb-5 no-print">
    <div class="f-group mb-0">
        <label class="f-label">Month</label>
        <select name="month" class="f-select w-40">
            <option value="0">All Months</option>
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="f-group mb-0">
        <label class="f-label">Year</label>
        <input type="number" name="year" value="{{ $year }}" min="2020" max="{{ date('Y') }}" class="f-input w-28">
    </div>
    <button type="submit" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-semibold rounded-xl transition">Filter</button>
</form>

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    @php $summary = [['Total Consultations', $totalConsultations, 'bg-red-50', 'text-red-700'],
                     ['Distinct Reasons',    count($reasonMap),    'bg-blue-50','text-blue-700'],
                     ['Medicines Used',      count($medicineMap),  'bg-green-50','text-green-700']]; @endphp
    @foreach($summary as [$lbl, $val, $bg, $col])
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-3xl font-extrabold {{ $col }}">{{ $val }}</div>
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wide mt-1">{{ $lbl }}</div>
    </div>
    @endforeach
</div>

{{-- Breakdown grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 font-bold text-slate-800 text-sm">Reasons for Consultation</div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead><tr><th>Reason</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($reasonMap as $data)
                    <tr><td>{{ $data['label'] }}</td><td class="font-semibold">{{ $data['count'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 font-bold text-slate-800 text-sm">Medicines Dispensed</div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead><tr><th>Medicine</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($medicineMap as $data)
                    <tr><td>{{ $data['label'] }}</td><td class="font-semibold">{{ $data['count'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- All Records --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 font-bold text-slate-800 text-sm">All Consultations</div>
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead><tr><th>Date</th><th>Student</th><th>ID</th><th>Doctor</th><th>Reason</th><th>Medicine</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($records as $rec)
                <tr>
                    <td class="whitespace-nowrap text-sm">{{ $rec->date_consulted->format('M d, Y') }}</td>
                    <td class="font-medium">{{ $rec->name }}</td>
                    <td class="text-xs text-slate-400">{{ $rec->student_id }}</td>
                    <td>{{ $rec->doctor }}</td>
                    <td>{{ $rec->reason }}</td>
                    <td>{{ $rec->medicine ?: '—' }}</td>
                    <td>{{ $rec->status }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-slate-400">No records for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
