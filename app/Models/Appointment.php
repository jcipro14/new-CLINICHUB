<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';
    public $timestamps = false;

    protected $fillable = [
        'student_user_id', 'student_id', 'name', 'staff', 'doctor',
        'next_consultation', 'reason', 'status', 'needs_confirmation',
        'reminder_1day_sent', 'reminder_3day_sent',
    ];

    protected $casts = [
        'next_consultation'  => 'date',
        'needs_confirmation' => 'boolean',
        'reminder_1day_sent' => 'boolean',
        'reminder_3day_sent' => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────────
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeForStudent($query, string $idNumber)
    {
        return $query->where('student_id', $idNumber);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['Upcoming', 'Pending']);
    }

    public function scopePendingConfirmation($query)
    {
        return $query->where('needs_confirmation', true)->where('status', 'Upcoming');
    }
}
