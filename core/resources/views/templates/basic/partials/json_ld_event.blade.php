@php
    $ld = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Event',
        'name'        => $event->title,
        'description' => \Illuminate\Support\Str::limit(strip_tags($event->description), 300),
        'url'         => route('event.details', [slug($event->title) ?: 'event', $event->id]),
        'startDate'   => optional($event->start_time)->toIso8601String(),
        'endDate'     => optional($event->end_time)->toIso8601String(),
        'eventStatus' => 'https://schema.org/EventScheduled',
        'location'    => ['@type' => 'Place', 'name' => $event->venue, 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $event->city?->name, 'addressCountry' => $event->country?->name]],
        'organizer'   => ['@type' => 'Person', 'name' => $event->user?->fullname],
    ];
    if ($event->cover_image) {
        $ld['image'] = asset(getFilePath('event') . '/' . $event->cover_image);
    }
@endphp
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
