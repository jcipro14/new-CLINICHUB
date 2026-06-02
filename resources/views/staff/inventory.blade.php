@extends('layouts.portal')
@section('title','Inventory – UM Clinic')
@section('page_title','Inventory')

@section('content')

{{-- Alpine component: listens for the portal-edit window event --}}
<div x-data="{ showAdd: false, showEdit: false, editData: {} }"
     @portal-edit.window="editData = $event.detail; showEdit = true">

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Medicine Inventory</h2>
            <p class="text-sm text-slate-500 mt-0.5">Track medicine stock and expiry dates</p>
        </div>
        <button @click="showAdd = true"
                class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Medicine
        </button>
    </div>

    {{-- Alert Banners --}}
    @if($outOfStock->isNotEmpty())
    <div class="alert-danger"><strong>🚫 Out of Stock:</strong> {{ $outOfStock->pluck('medicine_name')->implode(', ') }}</div>
    @endif
    @if($lowStock->isNotEmpty())
    <div class="alert-warning"><strong>⚠️ Low Stock (≤10):</strong> {{ $lowStock->map(fn($i)=>$i->medicine_name.' ('.$i->remaining_quantity.')')->implode(', ') }}</div>
    @endif
    @if($expiringSoon->isNotEmpty())
    <div class="alert-info"><strong>⏰ Expiring within 7 days:</strong> {{ $expiringSoon->map(fn($i)=>$i->medicine_name.' ('.$i->expiry_date->format('M d, Y').')')->implode(', ') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="portal-table">
                <thead>
                    <tr><th>#</th><th>Medicine</th><th>Received</th><th>Expiry</th><th>Initial Qty</th><th>Remaining</th><th>Dispensed</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    {{-- Hidden JSON data for this row --}}
                    <script type="application/json" id="inv-{{ $item->medicine_id }}">{!! json_encode($item, JSON_HEX_TAG) !!}</script>
                    <tr class="{{ $item->isExpired() ? 'row-expired' : ($item->isExpiringSoon() ? 'row-expiring' : '') }}">
                        <td class="text-xs text-slate-400">{{ $item->medicine_id }}</td>
                        <td class="font-medium">{{ $item->medicine_name }}</td>
                        <td class="whitespace-nowrap text-sm">{{ $item->receive_date?->format('M d, Y') }}</td>
                        <td class="whitespace-nowrap text-sm">
                            {{ $item->expiry_date?->format('M d, Y') }}
                            @if($item->isExpired()) <span class="tag-expired">EXPIRED</span>
                            @elseif($item->isExpiringSoon()) <span class="tag-expiring">SOON</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center font-semibold {{ $item->remaining_quantity <= 10 ? 'text-red-600' : 'text-slate-800' }}">{{ $item->remaining_quantity }}</td>
                        <td class="text-center text-slate-500">{{ $item->dispensed_quantity ?? 0 }}</td>
                        <td>
                            <div class="flex gap-1.5">
                                <button type="button"
                                        onclick="portalEditInv({{ $item->medicine_id }})"
                                        class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition">Edit</button>
                                <form method="POST" action="{{ route('staff.inventory.destroy', $item->medicine_id) }}" onsubmit="return confirm('Delete this item?')">
                                    @csrf
                                    <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-10 text-slate-400">No inventory items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── ADD MODAL ── --}}
    <div x-show="showAdd" x-cloak
         class="portal-modal-overlay"
         @click.self="showAdd = false"
         style="display:none">
        <div class="portal-modal-box">
            <div class="portal-modal-header">
                <h3>📦 Add Medicine Batch</h3>
                <button @click="showAdd = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('staff.inventory.store') }}">
                @csrf
                <div class="portal-modal-body">
                    <div class="f-group">
                        <label class="f-label">Medicine Name <span class="text-red-500">*</span></label>
                        <input type="text" name="medicine_name" class="f-input" placeholder="e.g. Paracetamol 500mg" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date Received <span class="text-red-500">*</span></label>
                        <input type="date" name="receive_date" value="{{ date('Y-m-d') }}" class="f-input" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Expiry Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expiry_date" class="f-input" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" class="f-input" required>
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showAdd = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div x-show="showEdit" x-cloak
         class="portal-modal-overlay"
         @click.self="showEdit = false"
         style="display:none">
        <div class="portal-modal-box">
            <div class="portal-modal-header">
                <h3>✏️ Edit Medicine Batch</h3>
                <button @click="showEdit = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" :action="'/staff/inventory/' + editData.medicine_id + '/edit'">
                @csrf
                <div class="portal-modal-body">
                    <div class="f-group">
                        <label class="f-label">Medicine Name <span class="text-red-500">*</span></label>
                        <input type="text" name="medicine_name" class="f-input"
                               x-model="editData.medicine_name" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Date Received <span class="text-red-500">*</span></label>
                        <input type="date" name="receive_date" class="f-input" required
                               x-effect="$el.value = (editData.receive_date||'').toString().substring(0,10)">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Expiry Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expiry_date" class="f-input" required
                               x-effect="$el.value = (editData.expiry_date||'').toString().substring(0,10)">
                    </div>
                    <div class="f-group">
                        <label class="f-label">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" class="f-input"
                               x-model="editData.quantity" min="0" required>
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
/* Reads the hidden JSON script tag for this row and fires a window event
   that the Alpine component listens to via @portal-edit.window */
function portalEditInv(id) {
    var el = document.getElementById('inv-' + id);
    if (!el) { console.error('Inventory row data not found for id:', id); return; }
    try {
        var data = JSON.parse(el.textContent);
        window.dispatchEvent(new CustomEvent('portal-edit', { detail: data }));
    } catch (e) {
        console.error('Failed to parse inventory row data:', e);
    }
}
</script>
@endsection
