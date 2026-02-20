<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDaily extends Model
{
    protected $table = 'analytics_daily';

    protected $fillable = [
        'date',
        'dau',
        'wau',
        'total_users',
        'total_businesses',
        'total_coupons',
        'redemptions_count',
        'revenue',
        'conversion_rate',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'revenue' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
            'metadata' => 'array',
        ];
    }
}
