<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCommission($query)
    {
        return $query->whereIn('remark', ['referral_commission', 'level_commission', 'commission_released', 'rank_bonus']);
    }
}
