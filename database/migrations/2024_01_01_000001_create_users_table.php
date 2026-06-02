<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name',  50);
            $table->string('id_number',  20)->unique();
            $table->string('email',      100)->nullable();
            $table->string('password');
            $table->enum('role', ['student','staff','sta','superadmin'])->default('student');
            $table->string('course', 10)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('users'); }
};
