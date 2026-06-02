<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Log;

class UserController extends Controller
{
    // ── List users ──────────────────────────────────────────
    public function index()
    {
        $users = User::orderBy('role')->orderBy('first_name')->get();
        return view('superadmin.manage_users', compact('users'));
    }

    // ── Show create form ────────────────────────────────────
    public function create()
    {
        return view('superadmin.add_user');
    }

    // ── Store new user ──────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_number'  => 'required|string|max:20|unique:users,id_number',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'password'   => [
                'required', 'string', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            ],
            'role'       => 'required|in:student,staff,sta,superadmin',
            'email'      => 'nullable|email|max:100',
            'course'     => 'nullable|string|in:BSIT,BSCS,BSCpE,BSEE,BSECE,DEE',
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.',
        ]);

        $admin = Auth::user();

        $user = User::create([
            'id_number'  => trim($request->id_number),
            'first_name' => trim($request->first_name),
            'last_name'  => trim($request->last_name),
            'email'      => trim($request->email ?? ''),
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'course'     => $request->course,
        ]);

        AuditLog::record(
            $admin->id_number,
            $admin->role,
            'Add User',
            "Created user ID: {$user->id_number} ({$user->full_name}) Role: {$user->role}"
        );

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    // ── Show edit form ──────────────────────────────────────
    public function edit(string $id)
    {
        $user = User::where('id_number', $id)->firstOrFail();
        return view('superadmin.edit_user', compact('user'));
    }

    // ── Update user ─────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $user = User::where('id_number', $id)->firstOrFail();

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'role'       => 'required|in:student,staff,sta,superadmin',
            'email'      => 'nullable|email|max:100',
            'course'     => 'nullable|string|in:BSIT,BSCS,BSCpE,BSEE,BSECE,DEE',
            'password'   => [
                'nullable', 'string', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            ],
        ]);

        $data = [
            'first_name' => trim($request->first_name),
            'last_name'  => trim($request->last_name),
            'role'       => $request->role,
            'email'      => trim($request->email ?? ''),
            'course'     => $request->course,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $admin = Auth::user();
        AuditLog::record(
            $admin->id_number,
            $admin->role,
            'Edit User',
            "Updated user ID: {$user->id_number} ({$user->full_name})"
        );

        return redirect()->route('admin.users')->with('success', 'User updated.');
    }

    // ── Delete user ─────────────────────────────────────────
    public function destroy(string $id)
    {
        $user = User::where('id_number', $id)->firstOrFail();
        $name = $user->full_name;
        $user->delete();

        $admin = Auth::user();
        AuditLog::record(
            $admin->id_number,
            $admin->role,
            'Delete User',
            "Deleted user ID: {$id} ({$name})"
        );

        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }
}
