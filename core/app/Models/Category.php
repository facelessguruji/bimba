<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GlobalStatus;

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', Status::YES);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeHasListings($query)
    {
        return $query->whereHas('listings', function ($q) {
            $q->approved();
        });
    }

    public function scopeAllListing($query)
    {
        return $query->whereHas('listings', function ($q) {
            $q->approved()->count();
        });
    }

    public function scopeCountApprovedListing($query)
    {
        return $query->withCount(['listings' => function ($query) {
            $query->approved();
        }]);
    }
}
