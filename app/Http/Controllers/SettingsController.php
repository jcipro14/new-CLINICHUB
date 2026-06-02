<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::current();
        return view('superadmin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name'        => 'required|string|max:100',
            'clinic_hours'       => 'required|string|max:100',
            'auto_logout'        => 'required|integer|min:1|max:120',
            'password_policy'    => 'required|in:strong,basic',
            'student_theme_mode' => 'required|string',
            'clinic_status'      => 'required|in:open,closed',
        ]);

        $settings = SystemSetting::current();
        $settings->update($request->only([
            'system_name', 'clinic_hours', 'auto_logout',
            'password_policy', 'student_theme_mode', 'clinic_status',
        ]));

        AuditLog::record(Auth::user()->id_number, 'superadmin', 'Update Settings', 'System settings updated.');

        return back()->with('success', 'Settings saved.');
    }
}
