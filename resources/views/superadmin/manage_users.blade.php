@extends('layouts.portal')
@section('title','Manage Users – UM Clinic')
@section('page_title','Manage Users')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Manage Users</h2>
        <p class="text-sm text-slate-500 mt-0.5">Add, edit, and delete system users</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New User
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead><tr><th>ID Number</th><th>Name</th><th>Role</th><th>Course</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="font-mono text-xs text-slate-500">{{ $u->id_number }}</td>
                    <td class="font-medium">{{ $u->full_name }}</td>
                    <td><span class="badge-role {{ $u->role }}">{{ strtoupper($u->role) }}</span></td>
                    <td class="text-sm text-slate-500">{{ $u->course ?? '—' }}</td>
                    <td class="text-sm text-slate-500">{{ $u->email ?? '—' }}</td>
                    <td>
                        <div class="flex gap-1.5">
                            <a href="{{ route('admin.users.edit', $u->id_number) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-2.5 py-1 rounded-lg transition">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id_number) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-slate-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
