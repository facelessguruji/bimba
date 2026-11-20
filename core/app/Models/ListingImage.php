<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ListingImage extends Model {
    use GlobalStatus;
    public function listings() {
        return $this->hasMany(Listing::class);
    }

    public function scopeHasListings($query) {
        return $query->whereHas('listings', function ($q) {
            $q->approved();
        });
    }
}
