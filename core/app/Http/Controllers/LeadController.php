<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\AiService;
use App\Models\Lead;
use App\Models\Listing;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Public lead capture  -  site-wide "tell us what you need".
     */
    public function submit(Request $request)
    {
        $validated = $this->validateLead($request);
        return $this->storeLead($validated, null);
    }

    /**
     * Public lead capture from a specific listing page.
     */
    public function submitForListing(Request $request, $id)
    {
        $listing = Listing::approved()->findOrFail($id);
        $validated = $this->validateLead($request);
        return $this->storeLead($validated, $listing);
    }

    protected function validateLead(Request $request)
    {
        return $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:40',
            'message' => 'required|string|max:3000',
        ]);
    }

    protected function storeLead($data, $listing = null)
    {
        $lead             = new Lead();
        $lead->listing_id = $listing?->id;
        $lead->user_id    = $listing?->user_id;
        $lead->name       = $data['name'];
        $lead->email      = $data['email'];
        $lead->phone      = $data['phone'] ?? null;
        $lead->message    = $data['message'];
        $lead->status     = 'new';

        // AI matching: rank approved listings against the request when AI
        // is on; graceful fallback = simple keyword scoring.
        $matches = $this->matchListings($lead);
        $lead->match_result = $matches ? json_encode($matches) : null;

        // route the lead to the best owner (listing owner when known,
        // otherwise the owner of the best match)
        if (!$lead->user_id && $matches) {
            $best = Listing::approved()->find($matches[0]['id'] ?? null);
            $lead->user_id = $best?->user_id;
            if ($best && !$lead->listing_id) {
                $lead->listing_id = $best->id;
            }
        }

        $lead->save();

        $notify[] = ['success', 'Thank you! Your request has been submitted. The right businesses will contact you.'];
        return back()->withNotify($notify);
    }

    protected function matchListings($lead)
    {
        $listings = Listing::approved()->with('category', 'city')->latest()->limit(40)->get();
        if ($listings->isEmpty()) {
            return null;
        }

        if (AiService::enabled('lead_match') && AiService::underCap()) {
            $catalog = $listings->map(fn ($l) => [
                'id'       => $l->id,
                'title'    => $l->title,
                'category' => $l->category?->name,
                'city'     => $l->city?->name,
            ])->values()->all();

            $content = AiService::chat([
                ['role' => 'system', 'content' => 'You match customer requests to business listings. Reply with ONLY a JSON array of listing ids, best match first, e.g. [12, 4, 7]. Reply [] if nothing fits.'],
                ['role' => 'user', 'content' => 'Customer request: ' . $lead->message . "\n\nListings: " . json_encode($catalog)],
            ], 'lead_match', null, 200);

            $ids = null;
            if ($content && preg_match('/\[[\s\S]*\]/', $content, $m)) {
                $ids = json_decode($m[0], true);
            }
            if (is_array($ids) && $ids) {
                $scores = [];
                $rank = count($ids);
                foreach ($ids as $id) {
                    $scores[(int) $id] = $rank--;
                }
                $matched = $listings->filter(fn ($l) => isset($scores[$l->id]))
                    ->sortByDesc(fn ($l) => $scores[$l->id])
                    ->map(fn ($l) => ['id' => $l->id, 'title' => $l->title, 'score' => $scores[$l->id]])
                    ->values()->all();
                if ($matched) {
                    return $matched;
                }
            }
        }

        // graceful fallback: keyword scoring on title/category/city
        $words = preg_split('/\W+/', strtolower($lead->message . ' ' . $lead->name), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_filter($words, fn ($w) => strlen($w) > 3);
        $scored = $listings->map(function ($l) use ($words) {
            $haystack = strtolower(($l->title ?? '') . ' ' . ($l->category?->name ?? '') . ' ' . ($l->city?->name ?? ''));
            $score = 0;
            foreach ($words as $word) {
                if (str_contains($haystack, $word)) {
                    $score++;
                }
            }
            return ['id' => $l->id, 'title' => $l->title, 'score' => $score];
        })->filter(fn ($m) => $m['score'] > 0)
            ->sortByDesc('score')
            ->take(5)
            ->values()->all();

        return $scored ?: null;
    }
}
