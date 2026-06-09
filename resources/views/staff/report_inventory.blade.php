@extends('layouts.portal')
@section('title','Inventory Report – UM Clinic')
@section('page_title','Inventory Report')

@section('content')

<div class="flex items-center justify-between mb-5 no-print">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Inventory Report</h2>
        <p class="text-sm text-slate-500 mt-0.5">Current stock levels, expiry status, and usage summary</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('staff.reports.monthly') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
            📊 Consultation Report
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
            🖨 Print Report
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-3xl font-extrabold text-red-700">{{ $inventory->count() }}</div>
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wide mt-1">Total Batches</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-3xl font-extrabold text-blue-700">{{ number_format($totalValue) }}</div>
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wide mt-1">Total Units Remaining</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-3xl font-extrabold text-amber-600">{{ $expiring->count() }}</div>
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wide mt-1">Expiring (30 days)</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-3xl font-extrabold text-rose-700">{{ $lowStock->count() }}</div>
        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wide mt-1">Low Stock Items</div>
    </div>
</div>

{{-- Alerts Grid --}}
@if($expiring->isNotEmpty() || $lowStock->isNotEmpty())
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

    @if($expiring->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center text-sm">⚠️</span>
            <h3 class="font-bold text-slate-800 text-sm">Expiring Within 30 Days</h3>
            <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $expiring->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead><tr><th>Medicine</th><th>Expiry Date</th><th>Remaining</th></tr></thead>
                <tbody>
                    @foreach($expiring as $item)
                    <tr class="row-expiring">
                        <td class="font-medium">{{ $item->medicine_name }}</td>
                        <td class="whitespace-nowrap text-amber-700 font-semibold">{{ $item->expiry_date->format('M d, Y') }}</td>
                        <td>{{ $item->remaining_quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($lowStock->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center text-sm">📉</span>
            <h3 class="font-bold text-slate-800 text-sm">Low Stock (≤ 10 units)</h3>
            <span class="ml-auto bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $lowStock->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead><tr><th>Medicine</th><th>Remaining</th><th>Dispensed</th></tr></thead>
                <tbody>
                    @foreach($lowStock as $item)
                    <tr class="row-expiring">
                        <td class="font-medium">{{ $item->medicine_name }}</td>
                        <td class="text-red-700 font-bold">{{ $item->remaining_quantity }}</td>
                        <td class="text-slate-400">{{ $item->dispensed_quantity ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endif

{{-- Full Inventory Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 font-bold text-slate-800 text-sm flex items-center gap-2">
        <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">📦</span>
        Complete Inventory
        <span class="ml-auto text-xs text-slate-400 font-normal">{{ $inventory->count() }} batch(es)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Received</th>
                    <th>Expiry</th>
                    <th>Original Qty</th>
                    <th>Dispensed</th>
                    <th>Remaining</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $item)
                @php
                    $isExpired  = $item->expiry_date ? $item->expiry_date->isPast() : false;
                    $isExpiring = $item->expiry_date && !$isExpired && $item->expiry_date->diffInDays(now()) <= 30;
                    $isLow      = $item->remaining_quantity <= 10 && $item->remaining_quantity > 0;
                    $isOut      = $item->remaining_quantity <= 0;
                    $rowClass   = $isExpired ? 'row-expired' : ($isExpiring ? 'row-expiring' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-xs text-slate-400">{{ $item->medicine_id }}</td>
                    <td class="font-medium">{{ $item->medicine_name }}</td>
                    <td class="text-xs text-slate-500 whitespace-nowrap">{{ $item->receive_date ? \Carbon\Carbon::parse($item->receive_date)->format('M d, Y') : '—' }}</td>
                    <td class="whitespace-nowrap">
                        {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                        @if($isExpired)
                            <span class="tag-expired">EXPIRED</span>
                        @elseif($isExpiring)
                            <span class="tag-expiring">Soon</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center text-slate-500">{{ $item->dispensed_quantity ?? 0 }}</td>
                    <td class="text-center font-bold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-green-700') }}">
                        {{ $item->remaining_quantity }}
                    </td>
                    <td>
                        @if($isOut)
                            <span class="badge-status cancelled">Out of Stock</span>
                        @elseif($isExpired)
                            <span class="badge-status cancelled">Expired</span>
                        @elseif($isExpiring || $isLow)
                            <span class="badge-status pending">Attention</span>
                        @else
                            <span class="badge-status upcoming">Good</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-10 text-slate-400">No inventory records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
