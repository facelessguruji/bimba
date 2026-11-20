<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class UserRanking extends Model
{
    use GlobalStatus;
    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeOrdered($query)
    {
        return $query->active()->orderBy('level', 'asc');
    }
}
