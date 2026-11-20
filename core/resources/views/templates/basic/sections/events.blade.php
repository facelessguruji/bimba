@php
    // the section promises upcoming events, so never list ones that already ended
    $homeEvents = \App\Models\Event::approved()
        ->with('category', 'city')
        ->where('event_type', 1)
        ->where('end_time', '>=', now())
        ->orderBy('start_time')
        ->take(3)
        ->get();
@endphp

<section class="lgx-section">
    <div class="lgx-container">
        <div class="lgx-sec-head">
            <div>
                <h2>@lang('Upcoming events')</h2>
                <p>@lang('Workshops, meetups and networking sessions hosted by the community.')</p>
            </div>
            @if ($homeEvents->count() > 0)
                <a class="lgx-sec-head__link" href="{{ route('events') }}">@lang('All events')</a>
            @endif
        </div>

        <div class="lgx-evs">
            @forelse ($homeEvents as $event)
                @php
                    $eventUrl  = route('event.details', [\Str::slug($event->title) ?: 'event', $event->id]);
                    $eventDesc = strLimit(strip_tags($event->description), 90);
                @endphp
                <article class="lgx-ev">
                    <a class="lgx-ev__th" href="{{ $eventUrl }}" aria-label="{{ $event->title }}">
                        @if (trim((string) $event->cover_image) !== '')
                            <img src="{{ getImage(getFilePath('event') . '/' . $event->cover_image, '372x216') }}"
                                 alt="{{ $event->title }}" loading="lazy">
                        @else
                            <span class="lgx-ev__ph"><i class="las la-calendar"></i></span>
                        @endif
                        @if ($event->category)
                            <span class="lgx-badge lgx-badge-indigo lgx-ev__cat">{{ __($event->category->name) }}</span>
                        @endif
                    </a>
                    <div class="lgx-ev__bd">
                        <p class="lgx-ev__meta">
                            <span><i class="las la-calendar"></i> {{ showDateTime($event->start_time, 'M d, Y') }}</span>
                            @if ($event->city)
                                <span><i class="las la-map-marker-alt"></i> {{ __($event->city->name) }}</span>
                            @endif
                        </p>
                        <h3><a href="{{ $eventUrl }}">{{ strLimit(__($event->title), 52) }}</a></h3>
                        @if ($eventDesc)
                            <p class="lgx-ev__desc">{{ $eventDesc }}</p>
                        @endif
                        <a class="lgx-ev__more" href="{{ $eventUrl }}">@lang('Event details') <i class="las la-arrow-right"></i></a>
                    </div>
                </article>
            @empty
                <div class="lgx-empty">
                    <i class="las la-calendar-plus"></i>
                    <p>@lang('No upcoming events right now. Check back soon.')</p>
                </div>
            @endforelse
        </div>

        @if ($homeEvents->count() >= 3)
            <div class="lgx-more">
                <a href="{{ route('events') }}" class="btn-lgx btn-lgx-ghost">@lang('Browse all events') <i class="las la-arrow-right"></i></a>
            </div>
        @endif
    </div>
</section>
