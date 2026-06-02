<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('student_user_id')->nullable();
            $table->string('student_id',  20)->nullable()->index();
            $table->string('name',        100)->nullable();
            $table->string('staff',       100)->nullable();
            $table->string('doctor',      100)->nullable();
            $table->date('next_consultation')->nullable();
            $table->string('reason',      255)->nullable();
            $table->enum('status', ['Pending','Upcoming','Completed','Cancelled'])->default('Pending');
            $table->boolean('needs_confirmation')->default(false);
            $table->boolean('reminder_1day_sent')->default(false);
            $table->boolean('reminder_3day_sent')->default(false);

            $table->foreign('student_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void { Schema::dropIfExists('appointments'); }
};
