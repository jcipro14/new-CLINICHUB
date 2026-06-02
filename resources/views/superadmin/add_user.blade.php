@extends('layouts.portal')
@section('title','Add User – UM Clinic')
@section('page_title','Add User')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Add New User</h2>
            <p class="text-sm text-slate-500 mt-0.5">Create a new system account</p>
        </div>
        <a href="{{ route('admin.users') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
            ← Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div class="f-group">
                <label class="f-label">ID Number <span class="text-red-500">*</span></label>
                <input type="text" name="id_number" value="{{ old('id_number') }}" class="f-input" required>
                @error('id_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="f-group mb-0">
                    <label class="f-label">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="f-input" required>
                </div>
                <div class="f-group mb-0">
                    <label class="f-label">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="f-input" required>
                </div>
            </div>
            <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="f-input">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="f-group mb-0">
                    <label class="f-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="f-select" required>
                        <option value="student"    {{ old('role')==='student'    ? 'selected':'' }}>Student</option>
                        <option value="staff"      {{ old('role')==='staff'      ? 'selected':'' }}>Staff</option>
                        <option value="sta"        {{ old('role')==='sta'        ? 'selected':'' }}>STA</option>
                        <option value="superadmin" {{ old('role')==='superadmin' ? 'selected':'' }}>Superadmin</option>
                    </select>
                </div>
                <div class="f-group mb-0">
                    <label class="f-label">Course <span class="text-xs text-slate-400">(Students only)</span></label>
                    <select name="course" class="f-select">
                        <option value="">— None —</option>
                        <option value="BSIT"  {{ old('course')==='BSIT'  ? 'selected':'' }}>BSIT – BS Information Technology</option>
                        <option value="BSCS"  {{ old('course')==='BSCS'  ? 'selected':'' }}>BSCS – BS Computer Science</option>
                        <option value="BSCpE" {{ old('course')==='BSCpE' ? 'selected':'' }}>BSCpE – BS Computer Engineering</option>
                        <option value="BSEE"  {{ old('course')==='BSEE'  ? 'selected':'' }}>BSEE – BS Electrical Engineering</option>
                        <option value="BSECE" {{ old('course')==='BSECE' ? 'selected':'' }}>BSEcE – BS Electronics Engineering</option>
                        <option value="DEE"   {{ old('course')==='DEE'   ? 'selected':'' }}>DEE – Dept. of Engineering Education</option>
                    </select>
                </div>
            </div>
            <div class="f-group">
                <label class="f-label">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="f-input" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-red-700 hover:bg-red-800 active:scale-[.98] text-white font-semibold py-2.5 rounded-xl transition-all">
                    Create User
                </button>
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
