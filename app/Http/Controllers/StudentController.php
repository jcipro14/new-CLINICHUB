<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Models\SystemSetting;
use App\Http\Controllers\AppointmentController;

class StudentController extends Controller
{
    // ── Dashboard ────────────────────────────────────────────
    public function dashboard()
    {
        $user     = Auth::user();
        $settings = SystemSetting::current();

        // Appointment summary
        $apptSummary = Appointment::forStudent($user->id_number)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Next upcoming appointment
        $nextAppointment = Appointment::forStudent($user->id_number)
            ->whereIn('status', ['Upcoming', 'Pending'])
            ->whereNotNull('next_consultation')
            ->orderBy('next_consultation')
            ->first();

        // Recent records (last 3)
        $recentRecords = MedicalRecord::where('student_id', $user->id_number)
            ->orderByDesc('date_consulted')
            ->limit(3)
            ->get();

        // Stats
        $totalVisits    = MedicalRecord::where('student_id', $user->id_number)->count();
        $visitsThisYear = MedicalRecord::where('student_id', $user->id_number)
            ->whereYear('date_consulted', now()->year)
            ->count();

        $lastRecord = MedicalRecord::where('student_id', $user->id_number)
            ->orderByDesc('date_consulted')
            ->first();

        // Top reason
        $topReason = MedicalRecord::where('student_id', $user->id_number)
            ->selectRaw('reason, count(*) as cnt')
            ->groupBy('reason')
            ->orderByDesc('cnt')
            ->first();

        // Top medicine
        $topMedicine = MedicalRecord::where('student_id', $user->id_number)
            ->whereNotNull('medicine')
            ->where('medicine', '!=', '')
            ->selectRaw('medicine, count(*) as cnt')
            ->groupBy('medicine')
            ->orderByDesc('cnt')
            ->first();

        // Pending confirmation
        $pendingConfirmation = Appointment::forStudent($user->id_number)
            ->pendingConfirmation()
            ->first();

        $reasonOptions = AppointmentController::REASON_OPTIONS;

        return view('student.dashboard', compact(
            'user', 'settings', 'apptSummary', 'nextAppointment',
            'recentRecords', 'totalVisits', 'visitsThisYear',
            'lastRecord', 'topReason', 'topMedicine', 'pendingConfirmation',
            'reasonOptions'
        ));
    }

    // ── Medical history ───────────────────────────────────────
    public function history()
    {
        $user    = Auth::user();
        $records = MedicalRecord::where('student_id', $user->id_number)
            ->orderByDesc('date_consulted')
            ->get();

        return view('student.history', compact('user', 'records'));
    }

    // ── Health & Safety tips ──────────────────────────────────
    public function healthSafety()
    {
        $tipImages = [];
        for ($i = 1; $i <= 5; $i++) {
            $tipImages[] = asset("images/TIPS/{$i}.jpg");
        }
        return view('student.health_safety', compact('tipImages'));
    }

    // ── Feedback form ─────────────────────────────────────────
    public function feedbackForm()
    {
        $user      = Auth::user();
        $feedbacks = DB::table('feedback')
            ->where('student_id', $user->id_number)
            ->orderByDesc('created_at')
            ->get();

        return view('student.feedback', compact('user', 'feedbacks'));
    }

    // ── Save feedback ─────────────────────────────────────────
    public function saveFeedback(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'rating'  => 'nullable|integer|between:1,5',
        ]);

        $user = Auth::user();

        DB::table('feedback')->insert([
            'student_id'  => $user->id_number,
            'name'        => $user->full_name,
            'message'     => $request->message,
            'rating'      => $request->rating,
            'created_at'  => now(),
        ]);

        return back()->with('success', 'Feedback submitted. Thank you!');
    }

    // ── AJAX: notifications ───────────────────────────────────
    public function notifications()
    {
        $user = Auth::user();

        $notifications = DB::table('notifications')
            ->where('user_id', $user->id_number)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($notifications);
    }

    // ── AJAX: mark notification read ──────────────────────────
    public function markNotificationRead(Request $request)
    {
        $user = Auth::user();

        DB::table('notifications')
            ->where('user_id', $user->id_number)
            ->where('id', $request->id)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ── AJAX: fetch feedback ──────────────────────────────────
    public function fetchFeedback()
    {
        $user      = Auth::user();
        $feedbacks = DB::table('feedback')
            ->where('student_id', $user->id_number)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($feedbacks);
    }

    // ── AJAX: delete feedback ─────────────────────────────────
    public function deleteFeedback(int $id)
    {
        $user = Auth::user();
        DB::table('feedback')
            ->where('id', $id)
            ->where('student_id', $user->id_number)
            ->delete();

        return response()->json(['success' => true]);
    }

    // ── Profile ───────────────────────────────────────────────
    public function profile()
    {
        $user     = Auth::user();
        $settings = SystemSetting::current();

        $totalVisits    = MedicalRecord::where('student_id', $user->id_number)->count();
        $totalAppts     = Appointment::forStudent($user->id_number)->count();
        $lastRecord     = MedicalRecord::where('student_id', $user->id_number)
            ->orderByDesc('date_consulted')->first();

        return view('student.profile', compact('user', 'settings', 'totalVisits', 'totalAppts', 'lastRecord'));
    }

    // ── Change password ───────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => [
                'required', 'confirmed', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            ],
            'password_confirmation' => 'required',
        ], [
            'password.regex' => 'Password must have 1 uppercase, 1 lowercase, a number, and a special character.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully. Please use your new password next time you log in.');
    }
}
