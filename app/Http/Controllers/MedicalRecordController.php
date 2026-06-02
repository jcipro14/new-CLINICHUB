<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Log;

class MedicalRecordController extends Controller
{
    // ── List records ────────────────────────────────────────
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $query   = MedicalRecord::orderByDesc('date_consulted');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $records  = $query->get();
        $students = User::where('role', 'student')->orderBy('first_name')->get();
        $medicines = Inventory::select('medicine_name')
            ->where('remaining_quantity', '>', 0)
            ->distinct()
            ->orderBy('medicine_name')
            ->pluck('medicine_name');

        // Pre-fill add modal from appointment link (?appt_id=X)
        $autoFillAppt    = null;
        $addForStudentId = null;
        if ($request->query('appt_id')) {
            $autoFillAppt = Appointment::find($request->query('appt_id'));
        } elseif ($request->query('add_for')) {
            $addForStudentId = $request->query('add_for');
        }

        return view('staff.medical_records', compact(
            'records', 'students', 'medicines', 'autoFillAppt', 'addForStudentId'
        ));
    }

    // ── Create record ────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'student_id'     => 'required|string',
            'date_consulted' => 'required|date',
            'doctor'         => 'required|string|max:100',
            'reason'         => 'required|string|max:255',
            'status'         => 'required|string|max:100',
            'medicine'       => 'nullable|string|max:100',
            'quantity'       => 'nullable|integer|min:1',
            'appointment_id' => 'nullable|integer',
        ]);

        $student = User::where('id_number', $request->student_id)->first();
        if (!$student) {
            return back()->withErrors(['record' => 'Student not found.']);
        }

        $staffUser = Auth::user();

        MedicalRecord::create([
            'student_id'     => $student->id_number,
            'name'           => $student->full_name,
            'staff'          => $staffUser->full_name,
            'date_consulted' => $request->date_consulted,
            'doctor'         => $request->doctor,
            'reason'         => $request->reason,
            'status'         => $request->status,
            'medicine'       => $request->medicine ?? '',
            'quantity'       => $request->quantity ?? 0,
        ]);

        // FIFO inventory deduction
        if ($request->medicine && $request->quantity > 0) {
            $this->deductInventory($request->medicine, (int)$request->quantity, $staffUser->full_name);
        }

        // Mark only the linked appointment as Completed
        if ($request->status === 'Completed') {
            if ($request->appointment_id) {
                Appointment::where('appointment_id', $request->appointment_id)
                    ->where('student_id', $student->id_number)
                    ->update(['status' => 'Completed']);
            } else {
                // Fall back: only the most recent active appointment
                Appointment::where('student_id', $student->id_number)
                    ->whereIn('status', ['Upcoming', 'Pending'])
                    ->orderByDesc('appointment_id')
                    ->limit(1)
                    ->update(['status' => 'Completed']);
            }
        }

        Log::record($staffUser->full_name, "Added medical record for student {$student->id_number} ({$student->full_name})");

        return back()->with('success', 'Medical record added.');
    }

    // ── Update record ────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'date_consulted' => 'required|date',
            'doctor'         => 'required|string|max:100',
            'reason'         => 'required|string|max:100',
            'status'         => 'required|string|max:100',
            'medicine'       => 'nullable|string|max:100',
            'quantity'       => 'nullable|integer|min:0',
        ]);

        $record = MedicalRecord::findOrFail($id);
        $staffUser = Auth::user();

        // Build change log for audit
        $changes = [];
        foreach (['date_consulted', 'medicine', 'quantity', 'reason', 'doctor', 'status'] as $field) {
            $old = $record->$field;
            $new = $request->$field;
            if ((string)$old !== (string)$new) {
                $changes[] = "{$field}: '{$old}' → '{$new}'";
            }
        }

        $record->update([
            'date_consulted' => $request->date_consulted,
            'doctor'         => $request->doctor,
            'reason'         => $request->reason,
            'status'         => $request->status,
            'medicine'       => $request->medicine ?? '',
            'quantity'       => $request->quantity ?? 0,
        ]);

        if ($changes) {
            Log::record($staffUser->full_name,
                "Edited medical record ID {$id} for student {$record->student_id}: " . implode(', ', $changes));
        }

        return back()->with('success', 'Record updated.');
    }

    // ── Delete record (superadmin only) ──────────────────────
    public function destroy(int $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only administrators can delete medical records.');
        }

        $record = MedicalRecord::findOrFail($id);
        $record->delete();
        Log::record(Auth::user()->full_name, "Deleted medical record ID {$id}");
        return back()->with('success', 'Record deleted.');
    }

    // ── FIFO inventory deduction (ported from medicalrecord.php) ──
    private function deductInventory(string $medicineName, int $qtyNeeded, string $staffName): void
    {
        $batches = Inventory::where('medicine_name', $medicineName)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        $remaining = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $batchRemaining = (int)$batch->remaining_quantity;
            $batchDispensed = (int)($batch->dispensed_quantity ?? 0);

            if ($batchRemaining <= 0) continue;

            $deductNow = min($batchRemaining, $remaining);

            $batch->update([
                'remaining_quantity' => $batchRemaining - $deductNow,
                'dispensed_quantity' => $batchDispensed + $deductNow,
            ]);

            Log::record($staffName,
                "Deducted {$deductNow} pc(s) of {$medicineName} from batch {$batch->medicine_id}. " .
                "Remaining: " . ($batchRemaining - $deductNow));

            $remaining -= $deductNow;
        }
    }
}
