<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Medical Records ──────────────────────────────────
        Schema::create('medicalrecords', function (Blueprint $table) {
            $table->id('med_id');
            $table->string('student_id',  20)->nullable()->index();
            $table->string('name',        100)->nullable();
            $table->string('staff',       100)->nullable();
            $table->date('date_consulted')->nullable();
            $table->string('doctor',      100)->nullable();
            $table->string('reason',      255)->nullable();
            $table->string('status',      100)->nullable();
            $table->string('medicine',    100)->nullable();
            $table->integer('quantity')->default(0);
        });

        // ── Inventory ────────────────────────────────────────
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('medicine_id');
            $table->string('medicine_name',    100);
            $table->date('receive_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->integer('dispensed_quantity')->default(0);
        });

        // ── Patients ─────────────────────────────────────────
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 20)->unique();
            $table->string('name',       100)->nullable();
            $table->integer('age')->nullable();
            $table->string('address',    255)->nullable();
            $table->string('contact_number', 20)->nullable();
        });

        // ── Activity Logs ─────────────────────────────────────
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 100)->nullable();
            $table->text('action')->nullable();
            $table->timestamp('timestamp')->nullable();
        });

        // ── Audit Logs ────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',    20)->nullable();
            $table->string('role',       20)->nullable();
            $table->string('action',    100)->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('timestamp')->nullable();
        });

        // ── Messages ─────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id',   20)->nullable();
            $table->string('receiver_id', 20)->nullable();
            $table->string('subject',    255)->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
        });

        // ── Announcements ─────────────────────────────────────
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title',     255);
            $table->text('body');
            $table->string('posted_by', 100)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // ── System Settings ───────────────────────────────────
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name',        100)->default('UM Visayan Clinic');
            $table->string('clinic_hours',       100)->default('8:00 AM - 5:00 PM');
            $table->integer('auto_logout')->default(5);
            $table->string('password_policy',     20)->default('strong');
            $table->string('student_theme_mode',  20)->default('default');
            $table->string('clinic_status',       10)->default('open');
        });

        // ── Feedback ──────────────────────────────────────────
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 20)->nullable();
            $table->string('name',      100)->nullable();
            $table->text('message');
            $table->tinyInteger('rating')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // ── Notifications ─────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20)->nullable();
            $table->string('type',    50)->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('logs');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('medicalrecords');
    }
};
