<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'daily_deals',
        'flash_deals',
        'milestones',
        'comeback',
        'silent_start',
        'silent_end',
    ];

    protected function casts(): array
    {
        return [
            'daily_deals' => 'boolean',
            'flash_deals' => 'boolean',
            'milestones' => 'boolean',
            'comeback' => 'boolean',
            'silent_start' => 'datetime:H:i',
            'silent_end' => 'datetime:H:i',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isInSilentHours(): bool
    {
        if (!$this->silent_start || !$this->silent_end) {
            return false;
        }
        $now = now()->format('H:i');
        $start = $this->silent_start->format('H:i');
        $end = $this->silent_end->format('H:i');
        if ($start < $end) {
            return $now >= $start && $now <= $end;
        }
        return $now >= $start || $now <= $end;
    }
}
