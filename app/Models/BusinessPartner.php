<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    protected $table = 'business_partners';

    protected $fillable = ['business_id', 'partner_business_id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function partner()
    {
        return $this->belongsTo(Business::class, 'partner_business_id');
    }
}
