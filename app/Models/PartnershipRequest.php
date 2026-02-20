<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnershipRequest extends Model
{
    protected $fillable = [
        'requester_business_id',
        'target_business_id',
        'message',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function requester()
    {
        return $this->belongsTo(Business::class, 'requester_business_id');
    }

    public function target()
    {
        return $this->belongsTo(Business::class, 'target_business_id');
    }
}
