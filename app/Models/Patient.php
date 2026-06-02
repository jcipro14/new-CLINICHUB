<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ── Patient ──────────────────────────────────────────────────────
class Patient extends Model
{
    protected $table = 'patients';
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'name', 'age', 'address', 'contact_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'patient_id', 'id_number');
    }
}
