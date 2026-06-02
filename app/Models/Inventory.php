<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'medicine_id';
    public $timestamps = false;

    protected $fillable = [
        'medicine_name', 'receive_date', 'expiry_date',
        'quantity', 'remaining_quantity', 'dispensed_quantity',
    ];

    protected $casts = [
        'receive_date' => 'date',
        'expiry_date'  => 'date',
    ];

    // ── Scopes ──────────────────────────────────────────────
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereDate('expiry_date', '<=', now()->addDays($days))
                     ->whereDate('expiry_date', '>=', now());
    }

    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('remaining_quantity', '<=', $threshold)
                     ->where('remaining_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('remaining_quantity', '<=', 0);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->expiry_date
            && !$this->expiry_date->isPast()
            && $this->expiry_date->lte(now()->addDays($days));
    }
}
