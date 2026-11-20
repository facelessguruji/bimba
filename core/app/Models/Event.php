<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use GlobalStatus;

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::LISTING_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::LISTING_REJECTED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            if ($this->status == Status::ENABLE) {
                return '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } elseif ($this->status == Status::LISTING_PENDING) {
                return '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->status == Status::LISTING_REJECTED) {
                return '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
            }
            return '';
        });
    }
}
