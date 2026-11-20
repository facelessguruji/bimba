<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model {
    use GlobalStatus;

    protected $guarded;
    protected $casts = [
        'working_schedule' => 'object',
        'amenities'        => 'object',
        'tags'             => 'object',
        'videos'           => 'object',
        'day_off'          => 'object',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function favoriteListing() {
        return $this->hasMany(FavoriteListing::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function images() {
        return $this->hasMany(ListingImage::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function avgRating() {
        return $this->reviews->avg('rating');
    }

    public function statusBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::LISTING_APPROVED) {
                $html = '<span class="badge badge--success">' . trans("Approved") . '</span>';
            } elseif ($this->status == Status::LISTING_DISABLED) {
                $html = '<span class="badge badge--dark">' . trans("Disabled") . '</span>';
            } elseif ($this->status == Status::LISTING_PENDING) {
                $html = '<span class="badge badge--warning">' . trans("Pending") . '</span>';
            } elseif ($this->status == Status::LISTING_REJECTED) {
                $html = '<span class="badge badge--danger">' . trans("Rejected") . '</span>
                <button data-bs-toggle="modal" data-bs-target="#rejectReasonModal" data-reason="'.$this->rejected_reason.'" class="badge badge--danger reasonBtn"> <i class="las la-info-circle"></i> </button>';
            }
            return $html;
        });
    }

    public function featuredBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->is_featured == Status::YES) {
                $html = '<span class="badge badge--primary">' . trans("Yes") . '</span>';
            } else {
                $html = '<span class="badge badge--warning">' . trans("No") . '</span>';
            }
            return $html;
        });
    }

    // SCOPES
    public function scopePending($query) {
        return $query->where('status', Status::LISTING_PENDING);
    }

    public function scopeApproved($query) {
        return $query->where('status', Status::LISTING_APPROVED);
    }
    public function scopeRejected($query) {
        return $query->where('status', Status::LISTING_REJECTED);
    }
    public function scopeDisabled($query) {
        return $query->where('status', Status::LISTING_DISABLED);
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', Status::YES);
    }

    public function scopePublishable($query) {
        return $query->approved()->whereHas('user.activePlan', Status::LISTING_DISABLED);
    }

    public function scopeWhereFavoriteList($query) {
        return $query->whereHas('favoriteListing', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }
}
