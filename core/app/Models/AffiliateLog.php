<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLog extends Model
{
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_id');
    }
}
