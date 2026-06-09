<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Log;
use App\Mail\AppointmentConfirmedMail;
use App\Mail\AppointmentReminderMail;

class AppointmentController extends Controller
{
    public const REASON_OPTIONS = [
        'Fever',
        'Headache / Migraine',
        'Cough / Colds',
        'Body pain / Muscle pain / Back pain',
        'Sore throat / Tonsil-related',
        'Stomach ache / Abdominal pain',
        'Dizziness / Vertigo',
        'Vomiting',
        'Diarrhea / LBM',
        'Allergic reaction / Rashes',
        'Wound / Injury / Sprain',
        'BP check / Vital signs',
        'General consultation / Check-up',
        'Dental (Toothache / Oral concern)',
        'Rest only',
        'Other medical complaint',
    ];

    // ── Student: list own appointments ─────────────────────
    public function studentIndex()
    {
        $user          = Auth::user();
        $appointments  = Appointment::forStudent($user->id_number)
            ->orderByDesc('appointment_id')
            ->get();
        $reasonOptions = self::REASON_OPTIONS;

        return view('student.appointments', compact('appointments', 'reasonOptions'));
    }

    // ── Student: request appointment ───────────────────────
    public function studentRequest(Request $request)
    {
        $request->validate([
            'reason'            => 'required|string|max:255',
            'next_consultation' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();

        // Block duplicate Pending
        $existing = Appointment::forStudent($user->id_number)
            ->where('status', 'Pending')
            ->first();

        if ($existing) {
            $msg = 'You already have a pending appointment.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['appointment' => $msg]);
        }

        Appointment::create([
            'student_user_id'   => $user->id,
            'student_id'        => $user->id_number,
            'name'              => $user->full_name,
            'reason'            => $request->reason,
            'next_consultation' => $request->next_consultation,
            'status'            => 'Pending',
        ]);

        Log::record($user->full_name, "Student {$user->full_name} requested an appointment: {$request->reason}");

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Appointment request submitted.'])
            : back()->with('success', 'Appointment request submitted.');
    }

    // ── Student: accept or cancel their own appointment ────
    public function studentAction(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|integer',
            'action'         => 'required|in:accept,cancel',
        ]);

        $user        = Auth::user();
        $appointment = Appointment::where('appointment_id', $request->appointment_id)
            ->where('student_id', $user->id_number)
            ->firstOrFail();

        if ($request->action === 'accept') {
            $appointment->update(['status' => 'Upcoming', 'needs_confirmation' => false]);
            return response()->json(['success' => true, 'message' => 'Appointment accepted.']);
        }

        if ($request->action === 'cancel') {
            $appointment->update(['status' => 'Cancelled', 'needs_confirmation' => false]);
            return response()->json(['success' => true, 'message' => 'Appointment cancelled.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    // ── AJAX: active appointments for a student ────────────
    public function studentAppointmentsJson(Request $request)
    {
        $idNumber = $request->query('student_id');
        if (!$idNumber) {
            return response()->json([]);
        }

        $appts = Appointment::where('student_id', $idNumber)
            ->whereIn('status', ['Pending', 'Upcoming'])
            ->orderByDesc('appointment_id')
            ->get(['appointment_id', 'reason', 'next_consultation', 'status', 'doctor']);

        return response()->json($appts->map(fn($a) => [
            'id'               => $a->appointment_id,
            'reason'           => $a->reason,
            'status'           => $a->status,
            'doctor'           => $a->doctor,
            'next_consultation'=> $a->next_consultation?->format('Y-m-d'),
            'label'            => ($a->next_consultation ? $a->next_consultation->format('M d, Y') . ' – ' : '') . $a->reason . ' (' . $a->status . ')',
        ]));
    }

    // ── Staff: list all appointments ───────────────────────
    public function staffIndex(Request $request)
    {
        $selectedStudent = $request->query('student');
        $scheduleView    = $request->query('view') === 'schedule';
        $deletedView     = $request->query('view') === 'deleted';

        if ($deletedView) {
            $query = Appointment::onlyTrashed()->with('student');
            if ($selectedStudent) {
                $query->where('student_id', $selectedStudent);
            }
            $appointments = $query->orderByDesc('deleted_at')->get();
        } else {
            $query = Appointment::with('student');

            if ($scheduleView) {
                $query->whereIn('status', ['Upcoming', 'Pending'])
                      ->whereNotNull('next_consultation')
                      ->orderBy('next_consultation');
            } else {
                $query->orderByDesc('appointment_id');
            }

            if ($selectedStudent) {
                $query->where('student_id', $selectedStudent);
            }

            $appointments = $query->get();
        }

        // Staff & STA dropdown for assignment
        $staffOptions = User::whereIn('role', ['staff', 'sta'])
            ->orderBy('first_name')
            ->get()
            ->map(fn($u) => $u->full_name . ' (' . strtoupper($u->role) . ')');

        $students  = User::where('role', 'student')->orderBy('first_name')->get();
        $medicines = Inventory::select('medicine_name')
            ->where('remaining_quantity', '>', 0)
            ->distinct()->orderBy('medicine_name')
            ->pluck('medicine_name');

        return view('staff.appointments', compact(
            'appointments', 'staffOptions', 'students',
            'selectedStudent', 'scheduleView', 'deletedView', 'medicines'
        ))->with('reasonOptions', self::REASON_OPTIONS);
    }

    // ── Staff: restore soft-deleted appointment ─────────
    public function restore(int $id)
    {
        $appointment = Appointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();

        $staffUser = Auth::user();
        Log::record($staffUser->full_name, "Restored deleted appointment ID {$id}");

        return redirect()->route('staff.appointments', ['view' => 'deleted'])
            ->with('success', 'Appointment restored successfully.');
    }

    // ── Staff: add appointment ──────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'target_student'    => 'required|string',
            'reason'            => 'required|string',
            'next_consultation' => 'nullable|date',
            'doctor'            => 'nullable|string|max:255',
            'staff'             => 'nullable|string|max:255',
            'status'            => 'required|in:Pending,Upcoming,Completed,Cancelled',
            'rec_date'          => 'nullable|date',
            'rec_status'        => 'nullable|string|max:100',
            'rec_medicine'      => 'nullable|string|max:100',
            'rec_quantity'      => 'nullable|integer|min:0',
        ]);

        $student = User::where('id_number', $request->target_student)->first();
        if (!$student) {
            return back()->withErrors(['appointment' => 'Student not found.']);
        }

        // Prevent duplicate Pending
        $dup = Appointment::where('student_id', $student->id_number)
            ->where('status', 'Pending')
            ->whereNull('next_consultation')
            ->first();

        if ($dup && $request->status === 'Pending') {
            return back()->withErrors(['appointment' => 'Student already has a pending appointment.']);
        }

        $appointment = Appointment::create([
            'student_user_id'  => $student->id,
            'student_id'       => $student->id_number,
            'name'             => $student->full_name,
            'staff'            => $request->staff ?? '',
            'doctor'           => $request->doctor ?? '',
            'next_consultation'=> $request->next_consultation,
            'reason'           => $request->reason,
            'status'           => $request->status,
            'needs_confirmation'=> $request->status === 'Upcoming' ? true : false,
        ]);

        // Send confirmation email if status is Upcoming
        if ($appointment->status === 'Upcoming' && $student->email) {
            try {
                Mail::to($student->email)->send(new AppointmentConfirmedMail($appointment, $student));
            } catch (\Exception $e) {
                logger()->warning('Appointment email failed: ' . $e->getMessage());
            }
        }

        $staffUser = Auth::user();
        Log::record($staffUser->full_name, "Added appointment for student {$student->id_number}");

        // Auto-create medical record if added directly as Completed
        if ($request->status === 'Completed') {
            $recDate = $request->rec_date ?? today()->toDateString();

            MedicalRecord::create([
                'student_id'     => $student->id_number,
                'name'           => $student->full_name,
                'staff'          => $staffUser->full_name,
                'date_consulted' => $recDate,
                'doctor'         => $request->doctor ?? '',
                'reason'         => $request->reason,
                'status'         => $request->rec_status ?: 'Completed',
                'medicine'       => $request->rec_medicine ?? '',
                'quantity'       => (int) ($request->rec_quantity ?? 0),
            ]);

            if ($request->rec_medicine && (int) $request->rec_quantity > 0) {
                $this->deductInventory(
                    $request->rec_medicine,
                    (int) $request->rec_quantity,
                    $staffUser->full_name
                );
            }

            Log::record($staffUser->full_name,
                "Medical record auto-created for {$student->full_name} via new completed appointment");

            return back()->with('success', 'Appointment added and medical record saved.');
        }

        return back()->with('success', 'Appointment added successfully.');
    }

    // ── Staff: update appointment ───────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'reason'            => 'required|string',
            'next_consultation' => 'nullable|date',
            'doctor'            => 'nullable|string|max:255',
            'staff'             => 'nullable|string|max:255',
            'status'            => 'required|in:Pending,Upcoming,Completed,Cancelled',
            'rec_date'          => 'nullable|date',
            'rec_status'        => 'nullable|string|max:100',
            'rec_medicine'      => 'nullable|string|max:100',
            'rec_quantity'      => 'nullable|integer|min:0',
        ]);

        $appointment = Appointment::findOrFail($id);
        $oldStatus   = $appointment->status;

        // Capture reason BEFORE update so we use the correct value in the record
        $appointmentReason = $request->reason;
        $appointmentDoctor = $request->doctor ?? '';

        $appointment->update([
            'staff'              => $request->staff ?? '',
            'doctor'             => $appointmentDoctor,
            'next_consultation'  => $request->next_consultation,
            'reason'             => $appointmentReason,
            'status'             => $request->status,
            'needs_confirmation' => ($request->status === 'Upcoming' && $oldStatus !== 'Upcoming'),
        ]);

        $staffUser = Auth::user();
        Log::record($staffUser->full_name, "Updated appointment ID {$id} to status {$request->status}");

        // ── Auto-create medical record when marked Completed ──────────
        // Triggers when: status is set to Completed AND was NOT already Completed
        if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            $student = User::where('id_number', $appointment->student_id)->first();

            if ($student) {
                $recDate = $request->rec_date ?? today()->toDateString();

                MedicalRecord::create([
                    'student_id'     => $student->id_number,
                    'name'           => $student->full_name,
                    'staff'          => $staffUser->full_name,
                    'date_consulted' => $recDate,
                    'doctor'         => $appointmentDoctor ?: '',
                    'reason'         => $appointmentReason,
                    'status'         => $request->rec_status ?: 'Completed',
                    'medicine'       => $request->rec_medicine ?? '',
                    'quantity'       => (int) ($request->rec_quantity ?? 0),
                ]);

                if ($request->rec_medicine && (int) $request->rec_quantity > 0) {
                    $this->deductInventory(
                        $request->rec_medicine,
                        (int) $request->rec_quantity,
                        $staffUser->full_name
                    );
                }

                Log::record($staffUser->full_name,
                    "Medical record auto-created for {$student->full_name} from appointment ID {$id}");
            }
        }

        $msg = $request->status === 'Completed'
            ? 'Appointment completed and medical record saved successfully.'
            : 'Appointment updated.';

        return back()->with('success', $msg);
    }

    // ── FIFO inventory deduction ────────────────────────────
    private function deductInventory(string $medicineName, int $qtyNeeded, string $staffName): void
    {
        $batches   = Inventory::where('medicine_name', $medicineName)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')->get();
        $remaining = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $batchRemaining = (int) $batch->remaining_quantity;
            $batchDispensed = (int) ($batch->dispensed_quantity ?? 0);
            $deductNow      = min($batchRemaining, $remaining);

            $batch->update([
                'remaining_quantity' => $batchRemaining - $deductNow,
                'dispensed_quantity' => $batchDispensed + $deductNow,
            ]);

            Log::record($staffName,
                "Deducted {$deductNow} pc(s) of {$medicineName} (batch {$batch->medicine_id}). Remaining: " . ($batchRemaining - $deductNow));

            $remaining -= $deductNow;
        }
    }

    // ── Staff: delete appointment ───────────────────────────
    public function destroy(Request $request, int $id)
    {
        $appointment = Appointment::findOrFail($id);
        $staffUser   = Auth::user();
        $studentId   = $appointment->student_id;
        $reason      = $appointment->reason;
        $date        = $appointment->next_consultation
                        ? $appointment->next_consultation->toDateString()
                        : today()->toDateString();
        $wasCompleted = $appointment->status === 'Completed';

        $appointment->delete();
        Log::record($staffUser->full_name, "Deleted appointment ID {$id}");

        // If staff chose to also delete the linked medical record
        if ($request->input('with_record') === '1' && $wasCompleted) {
            $deleted = MedicalRecord::where('student_id', $studentId)
                ->where('reason', $reason)
                ->whereDate('date_consulted', $date)
                ->delete();

            if ($deleted) {
                Log::record($staffUser->full_name,
                    "Also deleted linked medical record for student {$studentId} — reason: {$reason}");
            }
        }

        $msg = ($request->input('with_record') === '1' && $wasCompleted)
            ? 'Appointment and linked medical record deleted.'
            : 'Appointment deleted.';

        return redirect()->route('staff.appointments')->with('success', $msg);
    }
}
