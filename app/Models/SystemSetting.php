<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    public $timestamps = false;

    protected $fillable = [
        'system_name', 'clinic_hours', 'auto_logout',
        'password_policy', 'student_theme_mode', 'clinic_status',
    ];

    // ── Singleton helper ─────────────────────────────────────
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'system_name'        => 'UM Visayan Clinic',
            'clinic_hours'       => '8:00 AM - 5:00 PM',
            'auto_logout'        => 5,
            'password_policy'    => 'strong',
            'student_theme_mode' => 'default',
            'clinic_status'      => 'open',
        ]);
    }

    // ── Active theme resolver ─────────────────────────────────
    public function getActiveThemeAttribute(): string
    {
        $mode = $this->student_theme_mode ?? 'default';

        if ($mode !== 'auto_holiday') {
            return $mode;
        }

        $now = now()->setTimezone('Asia/Manila');
        $md  = $now->format('m-d');
        $m   = (int) $now->format('n');

        // Specific dates take priority
        if ($md === '01-01')                        return 'new_year';
        if ($md === '06-12')                        return 'independence_day';
        if ($md === '10-31')                        return 'halloween';
        if ($md === '11-01' || $md === '11-02')     return 'undas';
        if ($md >= '12-01' && $md <= '12-31')       return 'christmas';

        // Holy Week approximation (covers Palm Sunday through Easter, typically late March – early April)
        if ($md >= '03-24' && $md <= '04-07')       return 'holy_week';

        // Broader seasonal ranges
        if (in_array($m, [3, 4, 5]))                return 'summer';
        if (in_array($m, [6, 7, 8, 9, 10]))         return 'rainy_season';

        return 'default';
    }
}
