<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    private $pageTitle;

    public function index()
    {
        $this->pageTitle = 'All Listings';
        return $this->listingData();
    }

    public function pendingListings()
    {
        $this->pageTitle = 'Pending Listings';
        return $this->listingData('pending');
    }
    public function approvedListings()
    {
        $this->pageTitle = 'Approved Listings';
        return $this->listingData('approved');
    }
    public function rejectedListings()
    {
        $this->pageTitle = 'Rejected Listings';
        return $this->listingData('rejected');
    }
    public function disabledListings()
    {
        $this->pageTitle = 'Disabled Listings';
        return $this->listingData('disabled');
    }

    protected function listingData($scope = null)
    {
        if ($scope) {
            $listings = Listing::$scope();
        } else {
            $listings = Listing::query();
        }

        $pageTitle = $this->pageTitle;
        $listings  = $listings
            ->searchable(['title', 'city:name', 'country:name', 'category:name', 'user:username'])
            ->with('category', 'country', 'city', 'images', 'user')
            ->filter(['user_id'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.listing.index', compact('pageTitle', 'listings'));
    }

    public function details($id)
    {
        $pageTitle     = 'Listing Details';
        $listing       = Listing::with('category', 'city', 'images', 'user')->withCount('reviews')->findOrFail($id);
        $totalApproved = Listing::approved()->where('user_id', $listing->user->id)->count();
        $userPlan      = $listing->user->activePlan ?? $listing->user->currentPlan;
        return view('admin.listing.details', compact('pageTitle', 'listing', 'userPlan', 'totalApproved'));
    }

    public function feature($id)
    {
        return Listing::changeStatus($id, 'is_featured');
    }

    public function status($id)
    {
        return Listing::changeStatus($id);
    }

    public function approve($id)
    {
        $listing = Listing::pending()->find($id);

        // already processed (double click / stale pending tab): fail soft
        if (!$listing) {
            $notify[] = ['error', 'Listing not found among pending listings. It may already have been processed.'];
            return back()->withNotify($notify);
        }

        $user       = $listing->user;
        $activePlan = $user->activePlan;

        if (!$activePlan) {
            $notify[] = ['error', 'This member has no active plan'];
            return back()->withNotify($notify);
        }

        $totalApproved = Listing::approved()->where('user_id', $listing->user_id)->count();

        // a listings value of 0 means unlimited (Business plan)
        if ($activePlan->listings > 0 && $totalApproved >= $activePlan->listings) {
            $notify[] = ['error', 'The member has reached their plan’s approved-listings limit'];
            $notify[] = ['info', 'Upgrade the member’s plan to approve more listings.'];
            return back()->withNotify($notify);
        }

        $listing->status = Status::LISTING_APPROVED;
        $listing->save();

        notify($user, 'LISTING_APPROVED', [
            'title' => $listing->title,
            'city'  => $listing->city->name,
        ]);

        $notify[] = ['success',  "Listing approved successfully"];
        return back()->withNotify($notify);
    }

    public function reject(Request $request, $id)
    {
        $listing = Listing::pending()->find($id);

        // already processed (double click / stale pending tab): fail soft
        if (!$listing) {
            $notify[] = ['error', 'Listing not found among pending listings. It may already have been processed.'];
            return back()->withNotify($notify);
        }

        $user = User::findOrFail($listing->user_id);

        $listing->status          = Status::LISTING_REJECTED;
        $listing->rejected_reason = $request->rejected_reason;
        $listing->save();

        notify($user, 'LISTING_REJECTED', [
            'title' => $listing->title,
            'city'  => $listing->city->name,
        ]);

        $notify[] = ['success',  "Listing rejected successfully"];
        return back()->withNotify($notify);
    }
}
