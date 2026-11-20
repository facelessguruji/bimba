<?php

namespace App\Lib;

use App\Models\Listing;

class AiReviewSummary
{
    /**
     * Regenerate the cached AI review summary of a listing. Silent no-op
     * when AI is disabled  -  the platform never depends on it.
     */
    public static function refresh($listing)
    {
        if (!AiService::enabled('review_summary') || !AiService::underCap()) {
            return null;
        }

        $listing = $listing instanceof Listing ? $listing : Listing::findOrFail($listing);
        $reviews = $listing->reviews()->latest()->limit(30)->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        $text = $reviews->map(fn ($r) => "({$r->rating}/5) " . $r->review)->implode("\n");

        $content = AiService::chat([
            ['role' => 'system', 'content' => 'Summarize customer reviews of a business in 2-3 neutral sentences. Plain text only.'],
            ['role' => 'user', 'content' => $text],
        ], 'review_summary', $listing->user_id, 200);

        if ($content) {
            $listing->ai_review_summary = trim($content);
            $listing->save();
        }

        return $content;
    }
}
