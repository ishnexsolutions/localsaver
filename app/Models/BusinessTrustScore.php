<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTrustScore extends Model
{
    protected $fillable = ['business_id', 'score', 'factors'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'factors' => 'array',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
