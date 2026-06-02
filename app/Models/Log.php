<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    public $timestamps = false;

    protected $fillable = ['user_name', 'action', 'timestamp'];

    protected $casts = ['timestamp' => 'datetime'];

    // ── Helper ───────────────────────────────────────────────
    public static function record(string $userName, string $action): void
    {
        static::create([
            'user_name' => $userName,
            'action'    => $action,
            'timestamp' => now(),
        ]);
    }
}
