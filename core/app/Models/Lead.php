<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}
