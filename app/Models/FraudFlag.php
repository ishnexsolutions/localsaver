<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FraudFlag extends Model
{
    protected $fillable = [
        'user_id',
        'reason',
        'ip_hash',
        'device_hash',
        'metadata',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
