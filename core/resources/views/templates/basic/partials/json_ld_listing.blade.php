@php
    $ldAddress = trim(implode(', ', array_filter([
        $listing->location,
        $listing->city?->name,
        $listing->country?->name,
    ])));
    $ld = [
        '@context'    => 'https://schema.org',
        '@type'       => 'LocalBusiness',
        'name'        => $listing->title,
        'description' => \Illuminate\Support\Str::limit(strip_tags($listing->description), 300),
        'url'         => route('listing.details', [slug($listing->title) ?: 'listing', $listing->id]),
        'address'     => ['@type' => 'PostalAddress', 'streetAddress' => $listing->location, 'addressLocality' => $listing->city?->name, 'addressCountry' => $listing->country?->name],
        'telephone'   => auth()->check() ? $listing->user?->mobile : null,
    ];
    if ($listing->display_image) {
        $ld['image'] = asset(getFilePath('listing') . '/' . $listing->display_image);
    }
    if ($listing->reviews_count) {
        $ld['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => round($listing->reviews_sum_rating / max($listing->reviews_count, 1), 1),
            'reviewCount' => $listing->reviews_count,
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
