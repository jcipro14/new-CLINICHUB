<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_name', 'last_name', 'id_number', 'email',
        'password', 'role', 'course', 'announcements_last_seen',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'announcements_last_seen' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'student_user_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'student_id', 'id_number');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'patient_id', 'id_number');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isStudent(): bool    { return $this->role === 'student'; }
    public function isStaff(): bool      { return $this->role === 'staff'; }
    public function isSta(): bool        { return $this->role === 'sta'; }
    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }

    // Course label map
    public function getCourseLabelAttribute(): string
    {
        return match ($this->course) {
            'BSIT'  => 'Bachelor of Science in Information Technology (BSIT)',
            'BSCS'  => 'Bachelor of Science in Computer Science (BSCS)',
            'BSCpE' => 'Bachelor of Science in Computer Engineering (BSCpE)',
            'BSEE'  => 'Bachelor of Science in Electrical Engineering (BSEE)',
            'BSECE' => 'Bachelor of Science in Electronics Engineering (BSEcE)',
            'DEE'   => 'Department of Engineering Education (DEE)',
            default => $this->course ?? 'N/A',
        };
    }
}
