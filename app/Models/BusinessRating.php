<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessRating extends Model
{
    protected $fillable = ['business_id', 'user_id', 'rating', 'comment'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
