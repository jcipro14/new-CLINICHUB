<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    // ── Staff/STA/Admin: inbox + sent combined view ─────────
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->query('tab', 'inbox');

        $inbox = Message::with(['sender', 'receiver'])
            ->where('receiver_id', $user->id_number)
            ->orderByDesc('sent_at')
            ->get();

        $sent = Message::with(['sender', 'receiver'])
            ->where('sender_id', $user->id_number)
            ->orderByDesc('sent_at')
            ->get();

        // Staff/STA/Admin can message each other AND students
        $contacts = User::whereIn('role', ['staff', 'sta', 'superadmin', 'student'])
            ->where('id_number', '!=', $user->id_number)
            ->orderByRaw("FIELD(role,'staff','sta','superadmin','student')")
            ->orderBy('first_name')
            ->get();

        return view('staff.messages', compact('inbox', 'sent', 'contacts', 'tab'));
    }

    // ── Student: inbox only (read-only, cannot send) ────────
    public function studentInbox(Request $request)
    {
        $user = Auth::user();

        $inbox = Message::with('sender')
            ->where('receiver_id', $user->id_number)
            ->orderByDesc('sent_at')
            ->get();

        return view('student.messages', compact('inbox'));
    }

    // ── Student: view single received message ───────────────
    public function studentShow(int $id)
    {
        $user    = Auth::user();
        $message = Message::with(['sender', 'receiver'])->findOrFail($id);

        if ($message->receiver_id !== $user->id_number) {
            abort(403);
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('student.message_view', compact('message'));
    }

    // ── Send message ────────────────────────────────────────
    public function send(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string',
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
        ]);

        $sender   = Auth::user();
        $receiver = User::where('id_number', $request->receiver)->first();

        if (!$receiver) {
            return back()->withErrors(['message' => 'Recipient not found.']);
        }

        Message::create([
            'sender_id'   => $sender->id_number,
            'receiver_id' => $receiver->id_number,
            'subject'     => $request->subject,
            'body'        => $request->body,
            'is_read'     => false,
            'sent_at'     => now(),
        ]);

        return back()->with('success', 'Message sent.');
    }

    // ── Staff/STA/Admin: view single message ───────────────
    public function show(int $id)
    {
        $user    = Auth::user();
        $message = Message::with(['sender', 'receiver'])->findOrFail($id);

        // Mark as read if this user is the receiver
        if ($message->receiver_id === $user->id_number && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('staff.message_view', compact('message'));
    }

    // ── Delete message ──────────────────────────────────────
    public function destroy(int $id)
    {
        $user    = Auth::user();
        $message = Message::findOrFail($id);

        // Only sender or receiver can delete
        if ($message->sender_id !== $user->id_number && $message->receiver_id !== $user->id_number) {
            abort(403);
        }

        $message->delete();
        return back()->with('success', 'Message deleted.');
    }

    // ── AJAX: unread count ──────────────────────────────────
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::user()->id_number)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
