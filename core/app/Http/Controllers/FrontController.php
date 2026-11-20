<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Event;
use App\Models\Plan;
use App\Models\Listing;
use App\Models\Category;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Frontend;

class FrontController extends Controller
{

    public function categories()
    {
        $pageTitle  = 'All Categories';
        $categories = Category::active()->countApprovedListing()->orderBy('name', 'asc')->get();
        return view('Template::categories', compact('pageTitle', 'categories'));
    }

    public function cities()
    {
        $pageTitle = 'All Cities';
        $cities    = City::active()->countApprovedListing()->orderBy('name', 'asc')->paginate(getPaginate());
        return view('Template::cities', compact('pageTitle', 'cities'));
    }

    public function listings(Request $request)
    {
        $pageTitle = "Listings";
        $listings  = Listing::approved()
            ->withCount('reviews')
            ->withSum('reviews', 'rating')
            ->whereHas('user.activePlan')
            ->with('category', 'city', 'user.activePlan')
            ->searchable(['title', 'tags', 'location', 'description']);

        if ($request->category) {
            $listings->whereHas('category', function ($category) use ($request) {
                $category->active()->where('slug', $request->category);
            });
        }

        $countryId = $request->country ?? 0;
        if ($countryId) {
            $listings->whereHas('city', function ($city) use ($countryId) {
                $city->active()->where('country_id', $countryId);
            });
        }

        if ($request->city) {
            $listings->whereHas('city', function ($city) use ($request) {
                $city->active()->where('slug', $request->city);
            });
        }

        $listings = $listings->orderBy('id', 'DESC')->paginate(getPaginate(12));

        $categories = Category::active()->get();
        $cities     = City::active()->get();
        $countries  = Country::with('cities')->active()->get();
        return view('Template::listings', compact('pageTitle', 'listings', 'categories', 'cities', 'countries'));
    }

    public function listingDetail($slug, $id)
    {
        $pageTitle = 'Listing Details';
        $listing   = Listing::approved()->with('category', 'city', 'images', 'user')->withCount('reviews')->findOrFail($id);
        $plan      = $listing->user->activePlan;

        if (!$plan) {
            abort(404);
        }

        $canReview = false;
        $userId    = auth()->id() ?? 0;

        if ($userId && $listing->user_id != $userId) {
            $isExist   = $listing->reviews()->where('user_id', $userId)->exists();
            $canReview = !$isExist;
        }
        $allListing = Listing::approved()->count();
        $categories = Category::active()->hasListings()->withCount(['listings' => fn ($q) => $q->approved()])->orderBy('listings_count', 'desc')->limit(10)->get();
        $reviews    = $listing->reviews()->with('user:id,username,image')->orderBy('id', 'desc')->paginate(getPaginate(5));

        if (request()->ajax()) {
            $reviewData = view('Template::partials.reviews', compact('reviews'))->render();
            return response()->json([
                'reviews'       => $reviewData,
                'next_page_url' => $reviews->nextPageUrl(),
            ]);
        }

        $seoContents['keywords']           = $listing->tags ?? Frontend::where('data_keys', 'seo.data')->first('data_values')?->data_values->keywords;
        $seoContents['social_title']       = $listing->title;
        $seoContents['description']        = strLimit(strip_tags($listing->description), 200);
        $seoContents['social_description'] = strLimit(strip_tags($listing->description), 150);
        $seoContents['image']              = getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing'));
        $seoContents['image_size']         = getFileSize('listing');

        return view('Template::listing_details',  compact('pageTitle', 'listing', 'categories', 'reviews', 'seoContents', 'canReview', 'allListing'));
    }

    public function events()
    {
        $pageTitle  = 'Events';
        $categories = \App\Models\EventCategory::active()->orderBy('name')->get();
        $events     = Event::approved()->where('event_type', 1)->where('end_time', '>=', now())->orderBy('start_time')->with('category', 'city', 'country')
            ->when(request()->search, function ($q) {
                $s = request()->search;
                $q->where(function ($qq) use ($s) {
                    $qq->where('title', 'like', "%$s%")->orWhere('venue', 'like', "%$s%");
                });
            })
            ->when(request()->category, function ($q) {
                $q->whereHas('category', function ($qq) {
                    $qq->where('slug', request()->category);
                });
            })
            ->paginate(9);
        return view('Template::events', compact('pageTitle', 'events', 'categories'));
    }

    public function eventDetails($slug, $id)
    {
        $event     = Event::approved()->where('event_type', 1)->with('category', 'city', 'country', 'user')->findOrFail($id);
        $participation = \App\Models\EventParticipation::where('event_id', $event->id)
            ->where('user_id', auth()->id())->first();
        $pageTitle = $event->title;
        return view('Template::event_details', compact('pageTitle', 'event', 'participation'));
    }

    public function pricingPlan()
    {
        $pageTitle = "Pricing Plan";
        $plans     = Plan::active()->orderBy('yearly_price', 'asc')->get();
        return view('Template::plans', compact('pageTitle', 'plans'));
    }

    /**
     * Event attendee registration: guests and members can register interest
     * in an approved event. The organizer sees the list and can export it.
     */
    public function eventRegister(Request $request, $id)
    {
        $event = \App\Models\Event::approved()->where('event_type', 1)->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:40',
            'interest' => 'nullable|string|max:255',
        ]);

        \App\Models\EventAttendee::updateOrCreate(
            ['event_id' => $event->id, 'email' => strtolower($request->email)],
            [
                'user_id'   => auth()->id(),
                'name'      => $request->name,
                'phone'     => $request->phone,
                'interest'  => $request->interest,
                'updated_at' => now(),
            ]
        );

        // keep participation and registration in sync: a logged-in member who
        // registers has confirmed attendance (Join/RSVP)
        if (auth()->check()) {
            \App\Models\EventParticipation::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => auth()->id()],
                ['status' => 'joined', 'updated_at' => now()]
            );
        }

        $notify[] = ['success', 'You are registered for this event. See you there!'];
        return back()->withNotify($notify);
    }

    /**
     * Participation actions: follow / interested / joined — separate statuses.
     */
    public function eventParticipate(Request $request, $id)
    {
        // participation statuses belong to public events; company events
        // have their own members-only registration flow
        $event = \App\Models\Event::approved()->where('event_type', 1)->findOrFail($id);
        $request->validate(['status' => 'required|in:follow,interested,joined']);

        if (!auth()->check()) {
            return to_route('user.login');
        }

        \App\Models\EventParticipation::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => auth()->id()],
            ['status' => $request->status, 'updated_at' => now()]
        );

        // Join/RSVP is a registration: the member lands in the organizer's
        // attendee list and export like a form registration would
        if ($request->status == 'joined') {
            $member = auth()->user();
            \App\Models\EventAttendee::updateOrCreate(
                ['event_id' => $event->id, 'email' => strtolower($member->email)],
                [
                    'user_id'   => $member->id,
                    'name'      => trim($member->firstname . ' ' . $member->lastname),
                    'phone'     => $member->mobile,
                    'updated_at' => now(),
                ]
            );
        }

        $notify[] = ['success', 'Your event status has been updated'];
        return back()->withNotify($notify);
    }


    /**
     * Organizer CSV export of their event attendees.
     */
    public function eventAttendeesExport($id)
    {
        $event = \App\Models\Event::approved()->findOrFail($id);
        abort_unless($event->user_id === auth()->id(), 403);

        // filename hardening: slug() output still gets a strict whitelist pass
        // before it reaches the Content-Disposition header
        $fileName = 'attendees-' . preg_replace('/[^A-Za-z0-9_-]/', '', slug($event->title)) . '-' . now()->format('Y-m-d-His') . '.csv';
        $attendees = \App\Models\EventAttendee::where('event_id', $event->id)->orderBy('id')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($attendees) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Interest', 'Checked In', 'Registered At']);
            foreach ($attendees as $attendee) {
                fputcsv($out, [
                    $attendee->id,
                    $attendee->name,
                    $attendee->email,
                    $attendee->phone,
                    $attendee->interest,
                    $attendee->checked_in ? 'Yes' : 'No',
                    optional($attendee->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}