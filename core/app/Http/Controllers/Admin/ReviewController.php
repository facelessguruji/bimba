<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller {
    public function index() {
        $pageTitle = 'All Reviews';
        $reviews   = Review::searchable(['review', 'rating', 'user:username', 'listing:listing_id,title,tags'])->with('user', 'listing')->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.review.index', compact('pageTitle', 'reviews'));
    }

    public function delete($id, $listingId) {
        $review  = Review::where('id', $id)->where('listing_id', $listingId)->firstOrFail();
        $review->delete();
        $notify[] = ['success', 'Review deleted successfully'];
        return back()->withNotify($notify);
    }
}
