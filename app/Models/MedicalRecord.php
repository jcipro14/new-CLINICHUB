<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table = 'medicalrecords';
    protected $primaryKey = 'med_id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'name', 'staff', 'date_consulted',
        'doctor', 'reason', 'status', 'medicine', 'quantity',
    ];

    protected $casts = [
        'date_consulted' => 'date',
    ];

    // ── Relationship ────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id', 'id_number');
    }
}
