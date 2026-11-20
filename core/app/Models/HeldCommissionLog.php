<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class HeldCommissionLog extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function scopeHeld($query)
    {
        return $query->where('status', Status::HELD);
    }

    public function scopeReleased($query)
    {
        return $query->where('status', Status::RELEASED);
    }
}
