<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generate_user_id');
    }

    public function scopeUnused($query)
    {
        return $query->where('status', Status::PIN_UNUSED);
    }

    public function scopeUsed($query)
    {
        return $query->where('status', Status::PIN_USED);
    }
}
