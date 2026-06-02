@extends('layouts.portal')
@section('title','System Settings – UM Clinic')
@section('page_title','Settings')

@section('content')
<div class="max-w-2xl">
    <div class="mb-5">
        <h2 class="text-xl font-bold text-slate-800">System Settings</h2>
        <p class="text-sm text-slate-500 mt-0.5">Configure the UM Clinic portal</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
            @csrf

            <div class="f-group mb-0">
                <label class="f-label">System Name <span class="text-red-500">*</span></label>
                <input type="text" name="system_name" value="{{ old('system_name', $settings->system_name) }}" class="f-input" required>
            </div>

            <div class="f-group mb-0">
                <label class="f-label">Clinic Hours</label>
                <input type="text" name="clinic_hours" value="{{ old('clinic_hours', $settings->clinic_hours) }}" class="f-input" placeholder="e.g. 8:00 AM - 5:00 PM">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="f-group mb-0">
                    <label class="f-label">Auto Logout <span class="text-xs text-slate-400">(minutes)</span></label>
                    <input type="number" name="auto_logout" value="{{ old('auto_logout', $settings->auto_logout) }}" min="1" max="120" class="f-input" required>
                </div>
                <div class="f-group mb-0">
                    <label class="f-label">Clinic Status</label>
                    <select name="clinic_status" class="f-select">
                        <option value="open"   {{ $settings->clinic_status === 'open'   ? 'selected':'' }}>🟢 Open</option>
                        <option value="closed" {{ $settings->clinic_status === 'closed' ? 'selected':'' }}>🔴 Closed</option>
                    </select>
                </div>
            </div>

            <div class="f-group mb-0">
                <label class="f-label">Password Policy</label>
                <select name="password_policy" class="f-select">
                    <option value="strong" {{ $settings->password_policy === 'strong' ? 'selected':'' }}>Strong (uppercase, number, special char)</option>
                    <option value="basic"  {{ $settings->password_policy === 'basic'  ? 'selected':'' }}>Basic (min 8 chars)</option>
                </select>
            </div>

            <div class="f-group mb-0">
                <label class="f-label">Student Theme Mode</label>
                <select name="student_theme_mode" class="f-select">
                    @foreach([
                        'default'         => 'Default',
                        'auto_holiday'    => '🤖 Auto (by current date)',
                        'christmas'       => '🎄 Christmas (Dec)',
                        'new_year'        => '🎆 New Year (Jan)',
                        'summer'          => '☀️ Summer Season (Mar–May)',
                        'rainy_season'    => '🌧️ Rainy Season (Jun–Oct)',
                        'holy_week'       => '✝️ Holy Week (Lent)',
                        'independence_day'=> '🇵🇭 Independence Day (Jun 12)',
                        'undas'           => '🕯️ Undas / All Saints Day (Nov)',
                        'halloween'       => '🎃 Halloween (Oct 31)',
                    ] as $val => $lbl)
                    <option value="{{ $val }}" {{ $settings->student_theme_mode === $val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">
                    "Auto" automatically switches themes based on the current calendar date.
                </p>
            </div>

            <button type="submit" class="w-full bg-red-700 hover:bg-red-800 active:scale-[.98] text-white font-semibold py-2.5 rounded-xl transition-all">
                Save Settings
            </button>
        </form>
    </div>
</div>
@endsection
