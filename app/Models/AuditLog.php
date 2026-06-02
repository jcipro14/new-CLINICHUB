<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = ['user_id', 'role', 'action', 'details', 'ip_address', 'timestamp'];

    protected $casts = ['timestamp' => 'datetime'];

    // ── Helper ───────────────────────────────────────────────
    public static function record(string $userId, string $role, string $action, string $details = ''): void
    {
        static::create([
            'user_id'    => $userId,
            'role'       => $role,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => request()->ip(),
            'timestamp'  => now(),
        ]);
    }
}
