<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\FavoriteListing;
use App\Models\GatewayCurrency;
use App\Models\Lead;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle                = 'Dashboard';
        $user                     = auth()->user();
        $plan                     = $user->activePlan;
        $data['total_listing']    = Listing::whereUserId($user->id)->count();
        $data['approved_listing'] = Listing::whereUserId($user->id)->approved()->count();
        $data['pending_listing']  = Listing::whereUserId($user->id)->pending()->count();
        $data['disabled_listing'] = Listing::whereUserId($user->id)->disabled()->count();
        $data['rejected_listing'] = Listing::whereUserId($user->id)->rejected()->count();
        $data['featured_listing'] = Listing::whereUserId($user->id)->approved()->featured()->count();

        // affiliate + wallet figures for the dashboard tiles
        $extra                    = $user->userExtra;
        $data['wallet_balance']   = $user->balance;
        $data['held_earnings']    = $extra->held_commission ?? 0;
        $data['affiliate_earned'] = Transaction::where('user_id', $user->id)
            ->whereIn('remark', ['referral_commission', 'level_commission', 'monoline_commission', 'matching_bonus', 'commission_released', 'rank_bonus'])
            ->sum('amount');
        $data['direct_referrals'] = $user->referrals()->count();
        $data['active_directs']   = $user->referrals()->whereHas('activePlan')->count();

        // plan usage (approved listings vs the plan's slot limit, 0 = unlimited)
        $data['listings_used'] = $data['approved_listing'];
        $data['listings_limit'] = $plan->listings ?? 0;

        $listings  = Listing::with('category', 'city')->where('user_id', $user->id)->latest()->take(5)->get();
        $payouts   = Transaction::where('user_id', $user->id)
            ->whereIn('remark', ['referral_commission', 'level_commission', 'monoline_commission', 'matching_bonus', 'commission_released', 'rank_bonus'])
            ->latest()->take(5)->get();
        $sponsor   = $user->ref_by ? User::find($user->ref_by) : null;

        // my event participations, grouped by the three separate statuses
        $myParticipations = \App\Models\EventParticipation::where('user_id', $user->id)
            ->whereIn('status', ['follow', 'interested', 'joined'])
            ->pluck('status', 'event_id');
        $participatedEvents = \App\Models\Event::whereIn('id', $myParticipations->keys())->get()
            ->map(function ($event) use ($myParticipations) {
                $event->my_status = $myParticipations[$event->id] ?? null;
                return $event;
            });
        $followedEvents    = $participatedEvents->where('my_status', 'follow')->values();
        $interestedEvents  = $participatedEvents->where('my_status', 'interested')->values();
        $joinedEvents      = $participatedEvents->where('my_status', 'joined')->values();
        $organisedEvents   = \App\Models\Event::where('user_id', $user->id)->latest()->get();

        return view('Template::user.dashboard', compact(
            'pageTitle', 'user', 'data', 'listings', 'plan', 'payouts', 'sponsor', 'followedEvents', 'interestedEvents', 'joinedEvents', 'organisedEvents'
        ));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Payment History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'Complete Profile';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => ['required', Rule::unique('users')->ignore($user->id), 'min:4', 'max:40'],
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['error', 'Username may contain only lowercase letters, numbers, and underscores.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;


        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = isset($request->country) ? $request->country : '';
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    function choosePlan(Request $request, $id)
    {
        $type      = 'yearly'; // plans are yearly-only

        $plan      = Plan::active()->findOrFail($id);
        $pageTitle = "Buy Plan";
        // re-buying the level you currently hold active is a RENEWAL:
        // gateway-only payment, extends the same plan for another year
        $isRenewal = auth()->user()->plans()
            ->where('plan_id', $plan->id)
            ->where('plan_price', '>', 0)
            ->where('status', Status::ENABLE)
            ->where('expired_on', '>', now())
            ->exists();

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        return view('Template::user.buy_plan', compact('pageTitle', 'type', 'gatewayCurrency', 'plan', 'isRenewal'));
    }

    public function storeRating(Request $request, $id)
    {
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|in:1,2,3,4,5',
        ]);

        // only published listings can be rated
        $listing = Listing::approved()->findOrFail($id);

        if ($listing->user_id == auth()->user()->id) {
            $notify[] = ['error', 'Sorry, You can\'t give review on your own listing!'];
            return back()->withNotify($notify);
        }

        $existingReview = Review::where('user_id', auth()->id())->where('listing_id', $listing->id)->first();

        if ($existingReview) {
            $notify[] = ['error', 'You\'ve already reviewed this listing'];
            return back()->withNotify($notify);
        }

        $review             = new Review();
        $review->user_id    = auth()->id();
        $review->listing_id = $listing->id;
        $review->review     = $request->review;
        $review->rating     = $request->rating;
        $review->save();

        \App\Lib\AiReviewSummary::refresh($listing);

        $notify[] = ['success', 'Review submitted successfully'];
        return back()->withNotify($notify);
    }

    public function leads()
    {
        $pageTitle = 'Lead Inbox';
        $leads     = Lead::where('user_id', auth()->id())->with('listing')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.leads', compact('pageTitle', 'leads'));
    }

    public function leadStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:new,contacted,closed']);

        $lead = Lead::where('user_id', auth()->id())->findOrFail($id);
        $lead->status = $request->status;
        $lead->save();

        $notify[] = ['success', 'Lead status updated'];
        return back()->withNotify($notify);
    }

    /**
     * Export the member's own leads to CSV (opens directly in Excel).
     */
    public function exportLeads()
    {
        $fileName = 'leadxgpt-my-leads-' . now()->format('Y-m-d-His') . '.csv';

        $leads = Lead::where('user_id', auth()->id())->with('listing')->orderBy('id', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($leads) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens accented names correctly
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Listing', 'Enquirer Name', 'Email', 'Phone', 'Message', 'Status', 'Source', 'Date']);
            foreach ($leads as $lead) {
                fputcsv($out, [
                    $lead->id,
                    csvSafe($lead->listing?->title),
                    csvSafe($lead->name),
                    csvSafe($lead->email),
                    csvSafe($lead->phone),
                    csvSafe($lead->message),
                    $lead->status,
                    $lead->source,
                    optional($lead->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }


    public function addToFavorite($id)
    {

        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json(['error' => 'Something is wrong']);
        }

        $favoriteExcite = FavoriteListing::where('user_id', auth()->user()->id)->where('listing_id', $id)->first();

        if ($favoriteExcite) {
            $favoriteExcite->delete();
            return response()->json(['success' => 'Removed from favorite list']);
        }

        $favorite             = new FavoriteListing();
        $favorite->listing_id = $id;
        $favorite->user_id    = auth()->id();
        $favorite->save();
        return response()->json(['success' => 'Added to favorite list']);
    }

    public function favoriteListings(Request $request)
    {
        $pageTitle = "Favorite listings";

        $listings = Listing::approved()->whereHas('favoriteListing', function ($favoriteListing) {
            $favoriteListing->where('user_id', auth()->id());
        })->WhereFavoriteList()
            ->withCount('reviews')
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

        $listings = $listings->orderBy('id', 'DESC')->paginate(getPaginate());


        $categories = Category::active()->get();
        $cities     = City::active()->get();
        $countries  = Country::with('cities')->active()->get();
        return view('Template::listings', compact('pageTitle', 'listings', 'categories', 'cities', 'countries'));
    }


    public function downloadAttachment($fileHash)
    {
        try {
            $filePath = decrypt($fileHash);
        } catch (\Exception $e) {
            abort(404);
        }
        // the file must belong to the member - it is either a manual-deposit
        // attachment or a KYC verification file owned by this user
        $fileName = basename($filePath);
        $owned = Deposit::where('user_id', auth()->id())->where('detail', 'like', '%' . $fileName . '%')->exists()
            || User::where('id', auth()->id())->where('kyc_data', 'like', '%' . $fileName . '%')->exists();
        if (!$owned) {
            abort(404);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exist'];
            return back()->withNotify($notify);
        }
        // closing quote completes the filename token; CR/LF stripped so a
        // crafted extension can never split the header
        $title = str_replace(["\r", "\n"], '', $title);
        header('Content-Disposition: attachment; filename="' . $title . '"');
        header('Content-Type: ' . $mimetype);
        return readfile($filePath);
    }

}
