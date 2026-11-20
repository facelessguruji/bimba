<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\UserPlan;

class PlanSubscription
{
    public $plan;
    public $user;

    public function __construct($plan, $user)
    {
        $this->plan = $plan;
        $this->user = $user;
    }

    public function subscribe()
    {

        if (@$this->user->plans->count()) {
            $lastPlan = @$this->user->plans()->where('status', Status::ENABLE)->first();
            if ($lastPlan) {
                $lastPlan->status = Status::DISABLE;
                $lastPlan->save();
            }
        }

        $userPlan = new UserPlan();
        $userPlan->user_id         = $this->user->id;
        $userPlan->plan_id         = $this->plan->id;
        $userPlan->plan_price      = $this->plan->price;
        $userPlan->listings        = $this->plan->listings;
        $userPlan->amenities       = $this->plan->amenities;
        $userPlan->images          = $this->plan->images;
        $userPlan->videos          = $this->plan->videos;
        $userPlan->events          = $this->plan->events ?? 0;
        $userPlan->allow_map       = $this->plan->map;
        $userPlan->expired_on      = $this->plan->expired_on;
        $userPlan->save();
    }

    public function disabledExtraListings()
    {

        $approvedListings = Listing::approved()->where('user_id', $this->user->id)->orderBy('id', 'asc')->get();

        $oldApprovedListingCount = $approvedListings->count();

        $newListingCount = $this->plan->listings;

        // a listings value of 0 means unlimited  -  nothing to disable
        if ($newListingCount > 0 && $oldApprovedListingCount > $newListingCount) {
            $inactiveListingCount = $oldApprovedListingCount - $newListingCount;
            // Get IDs of those listings to be inactivated
            $inactiveListingIds = $approvedListings
                ->take($inactiveListingCount)
                ->pluck('id');


            Listing::whereIn('id', $inactiveListingIds)->update(['status' => Status::LISTING_DISABLED]);

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $this->user->id;
            $adminNotification->title = 'Listing plan downgrade by ' . $this->user->username;
            $adminNotification->click_url = urlPath('admin.listing.pending');
            $adminNotification->save();
        }
    }

    public function removeExtraData()
    {
        $listings = Listing::where('user_id', $this->user->id)->get();

        foreach ($listings as $listing) {

            if ($this->plan->map == Status::NO) {
                $listing->map = null;
            }

            $oldAmenities = $listing->amenities ?? [];
            $newAmenitiesCount = $this->plan->amenities;

            if ($newAmenitiesCount === 0) {
                $listing->amenities = [];
            } elseif (count($oldAmenities) > $newAmenitiesCount) {
                $listing->amenities = array_slice($oldAmenities, -$newAmenitiesCount);
            }

            $oldVideos = $listing->videos ?? [];
            $newVideosCount = $this->plan->videos;

            if ($newVideosCount === 0) {
                $listing->videos = [];
            } elseif (count($oldVideos) > $newVideosCount) {
                $listing->videos = array_slice($oldVideos, -$newVideosCount);
            }


            $listing->save();

            $oldImagesCount = ListingImage::where('listing_id', $listing->id)->count();
            $newImagesCount = $this->plan->images;
            $popImages = $oldImagesCount - $newImagesCount;

            if ($popImages > 0) {
                $imagesToDelete = ListingImage::where('listing_id', $listing->id)
                    ->orderBy('id', 'asc')
                    ->limit($popImages)
                    ->get();

                $path = getFilePath('listing');
                foreach ($imagesToDelete as $image) {
                    @unlink($path . '/' . $image->name);
                    $image->delete();
                }
            }
        }
    }
}
