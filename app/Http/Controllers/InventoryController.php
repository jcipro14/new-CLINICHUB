<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Log;

class InventoryController extends Controller
{
    // ── List inventory ──────────────────────────────────────
    public function index()
    {
        $inventory = Inventory::orderBy('medicine_name')->orderBy('expiry_date')->get();

        $expiringSoon = Inventory::expiringSoon(7)->get();
        $lowStock     = Inventory::lowStock(10)->get();
        $outOfStock   = Inventory::outOfStock()->get();

        return view('staff.inventory', compact('inventory', 'expiringSoon', 'lowStock', 'outOfStock'));
    }

    // ── Add medicine batch ──────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:100',
            'receive_date'  => 'required|date',
            'expiry_date'   => 'required|date|after:today',
            'quantity'      => 'required|integer|min:1',
        ]);

        $item = Inventory::create([
            'medicine_name'      => trim($request->medicine_name),
            'receive_date'       => $request->receive_date,
            'expiry_date'        => $request->expiry_date,
            'quantity'           => $request->quantity,
            'remaining_quantity' => $request->quantity,
            'dispensed_quantity' => 0,
        ]);

        Log::record(Auth::user()->full_name,
            "Added new inventory item: {$item->medicine_name} (Qty: {$item->quantity})");

        return back()->with('success', 'Inventory item added.');
    }

    // ── Edit medicine batch ─────────────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:100',
            'receive_date'  => 'required|date',
            'expiry_date'   => 'required|date',
            'quantity'      => 'required|integer|min:0',
        ]);

        $item = Inventory::findOrFail($id);

        // Recalculate remaining_quantity so it stays consistent with the corrected quantity
        $dispensed            = (int) ($item->dispensed_quantity ?? 0);
        $newRemaining         = max(0, (int) $request->quantity - $dispensed);

        $item->update([
            'medicine_name'      => $request->medicine_name,
            'receive_date'       => $request->receive_date,
            'expiry_date'        => $request->expiry_date,
            'quantity'           => $request->quantity,
            'remaining_quantity' => $newRemaining,
        ]);

        Log::record(Auth::user()->full_name,
            "Edited inventory item ID {$id}: {$item->medicine_name}");

        return back()->with('success', 'Inventory item updated.');
    }

    // ── Delete medicine batch ───────────────────────────────
    public function destroy(int $id)
    {
        $item = Inventory::findOrFail($id);
        $name = $item->medicine_name;
        $item->delete();

        Log::record(Auth::user()->full_name, "Deleted inventory item: {$name} (ID {$id})");

        return back()->with('success', 'Inventory item deleted.');
    }

    // ── AJAX: inventory notifications ──────────────────────
    public function notifications()
    {
        $expiring = Inventory::expiringSoon(7)->get(['medicine_name', 'expiry_date', 'remaining_quantity']);
        $low      = Inventory::lowStock(10)->get(['medicine_name', 'remaining_quantity']);
        $out      = Inventory::outOfStock()->get(['medicine_name']);

        return response()->json(compact('expiring', 'low', 'out'));
    }
}
