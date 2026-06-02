@extends('layouts.portal')
@section('title','Edit User – UM Clinic')
@section('page_title','Edit User')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit User</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ $user->full_name }}</p>
        </div>
        <a href="{{ route('admin.users') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
            ← Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id_number) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="f-group mb-0">
                    <label class="f-label">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="f-input" required>
                </div>
                <div class="f-group mb-0">
                    <label class="f-label">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="f-input" required>
                </div>
            </div>
            <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="f-input">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="f-group mb-0">
                    <label class="f-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="f-select" required>
                        @foreach(['student','staff','sta','superadmin'] as $r)
                        <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="f-group mb-0">
                    <label class="f-label">Course</label>
                    <select name="course" class="f-select">
                        <option value="">— None —</option>
                        @foreach([
                            'BSIT'  => 'BSIT – BS Information Technology',
                            'BSCS'  => 'BSCS – BS Computer Science',
                            'BSCpE' => 'BSCpE – BS Computer Engineering',
                            'BSEE'  => 'BSEE – BS Electrical Engineering',
                            'BSECE' => 'BSEcE – BS Electronics Engineering',
                            'DEE'   => 'DEE – Dept. of Engineering Education',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ old('course', $user->course) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="f-group">
                <label class="f-label">New Password <span class="text-xs text-slate-400">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="f-input">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-red-700 hover:bg-red-800 active:scale-[.98] text-white font-semibold py-2.5 rounded-xl transition-all">
                    Save Changes
                </button>
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
