<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class City extends Model {
    use GlobalStatus;

    public function listings() {
        return $this->hasMany(Listing::class);
    }
    
    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function scopeActive($query) {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', Status::YES);
    }

    public function scopeCountApprovedListing($query) {
        return $query->withCount(['listings' => function ($query) {
            $query->approved();
        }]);
    }

    public function scopeHasListings($query) {
        return $query->whereHas('listings', function ($q) {
            $q->approved();
        });
    }
}
