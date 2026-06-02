<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BackupController;

// ─── PUBLIC ───────────────────────────────────────────
Route::get('/',         [AuthController::class, 'showLogin'])->name('home');
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ─── STUDENT ──────────────────────────────────────────
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard',              [StudentController::class,    'dashboard'])->name('dashboard');
    Route::get('/history',                [StudentController::class,    'history'])->name('history');
    Route::get('/health-safety',          [StudentController::class,    'healthSafety'])->name('health_safety');
    Route::get('/announcements',          [AnnouncementController::class, 'studentIndex'])->name('announcements');
    Route::get('/appointments',           [AppointmentController::class, 'studentIndex'])->name('appointments');
    Route::post('/appointments/request',  [AppointmentController::class, 'studentRequest'])->name('appointments.request');
    Route::post('/appointments/action',   [AppointmentController::class, 'studentAction'])->name('appointments.action');
    Route::get('/feedback',               [StudentController::class,    'feedbackForm'])->name('feedback');
    Route::post('/feedback',              [StudentController::class,    'saveFeedback'])->name('feedback.save');
    Route::get('/messages',               [MessageController::class,    'studentInbox'])->name('messages');
    Route::get('/messages/{id}',          [MessageController::class,    'studentShow'])->name('messages.show');
    Route::get('/profile',                [StudentController::class,    'profile'])->name('profile');
    Route::post('/profile/password',      [StudentController::class,    'updatePassword'])->name('profile.password');
});

// ─── STAFF / STA / SUPERADMIN (shared operational routes) ─
Route::middleware(['auth', 'role:staff,sta,superadmin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard',              [StaffController::class,       'dashboard'])->name('dashboard');
    Route::get('/appointments',           [AppointmentController::class, 'staffIndex'])->name('appointments');
    Route::post('/appointments/add',      [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{id}/edit',[AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('/appointments/{id}/delete',  [AppointmentController::class,'destroy'])->name('appointments.destroy');
    Route::post('/appointments/{id}/restore', [AppointmentController::class,'restore'])->name('appointments.restore');
    Route::get('/medical-records',        [MedicalRecordController::class,'index'])->name('records');
    Route::post('/medical-records',       [MedicalRecordController::class,'store'])->name('records.store');
    Route::post('/medical-records/{id}',  [MedicalRecordController::class,'update'])->name('records.update');
    Route::post('/medical-records/{id}/delete',[MedicalRecordController::class,'destroy'])->name('records.destroy');
    Route::get('/patients',               [StaffController::class,       'patients'])->name('patients');
    Route::get('/inventory',              [InventoryController::class,   'index'])->name('inventory');
    Route::post('/inventory',             [InventoryController::class,   'store'])->name('inventory.store');
    Route::post('/inventory/{id}/edit',   [InventoryController::class,   'update'])->name('inventory.update');
    Route::post('/inventory/{id}/delete', [InventoryController::class,   'destroy'])->name('inventory.destroy');
    Route::get('/logs',                   [StaffController::class,       'logs'])->name('logs');
    Route::get('/messages',               [MessageController::class,     'index'])->name('messages');
    Route::post('/messages/send',         [MessageController::class,     'send'])->name('messages.send');
    Route::get('/messages/{id}',          [MessageController::class,     'show'])->name('messages.show');
    Route::delete('/messages/{id}',       [MessageController::class,     'destroy'])->name('messages.destroy');
    Route::get('/announcements',          [AnnouncementController::class,'staffIndex'])->name('announcements');
    Route::post('/announcements',         [AnnouncementController::class,'store'])->name('announcements.store');
    Route::delete('/announcements/{id}',  [AnnouncementController::class,'destroy'])->name('announcements.destroy');
    Route::get('/feedback',               [StaffController::class,       'feedback'])->name('feedback');
    Route::get('/reports/monthly',        [ReportController::class,      'monthly'])->name('reports.monthly');
    Route::get('/inventory-report',       [ReportController::class,      'inventory'])->name('reports.inventory');
});

// ─── SUPERADMIN ───────────────────────────────────────
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',              [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',                  [UserController::class,       'index'])->name('users');
    Route::get('/users/create',           [UserController::class,       'create'])->name('users.create');
    Route::post('/users',                 [UserController::class,       'store'])->name('users.store');
    Route::get('/users/{id}/edit',        [UserController::class,       'edit'])->name('users.edit');
    Route::post('/users/{id}',            [UserController::class,       'update'])->name('users.update');
    Route::delete('/users/{id}',          [UserController::class,       'destroy'])->name('users.destroy');
    Route::get('/logs',                   [SuperAdminController::class, 'logs'])->name('logs');
    Route::get('/audit-logs',             [SuperAdminController::class, 'auditLogs'])->name('audit_logs');
    Route::get('/settings',               [SettingsController::class,   'index'])->name('settings');
    Route::post('/settings',              [SettingsController::class,   'update'])->name('settings.update');
    Route::get('/backup',                 [BackupController::class,     'index'])->name('backup');
    Route::post('/backup/download',       [BackupController::class,     'download'])->name('backup.download');
});

// ─── AJAX / JSON ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/api/dashboard-data',           [ReportController::class,      'dashboardData']);
    Route::get('/api/notifications',            [StudentController::class,     'notifications']);
    Route::get('/api/unread-messages',          [MessageController::class,     'unreadCount']);
    Route::get('/api/unread-announcements',     [AnnouncementController::class,'unreadCount']);
    Route::post('/api/mark-notification-read',  [StudentController::class,     'markNotificationRead']);
    Route::get('/api/fetch-feedback',           [StudentController::class,     'fetchFeedback']);
    Route::delete('/api/feedback/{id}',         [StudentController::class,     'deleteFeedback']);
    Route::get('/api/inventory-notifications',  [InventoryController::class,   'notifications']);
    Route::post('/api/mark-announcement-read',  [AnnouncementController::class,'markRead']);
    Route::get('/api/admin-dashboard-data',     [ReportController::class,      'adminDashboardData']);
    Route::get('/api/student-appointments',     [AppointmentController::class, 'studentAppointmentsJson']);
});
