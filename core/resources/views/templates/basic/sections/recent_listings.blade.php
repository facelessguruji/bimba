@php
    $recentListingContent = getContent('recent_listings.content', true);
    $listings             = App\Models\Listing::approved()
        ->withCount('reviews')
        ->withSum('reviews', 'rating')
        ->whereHas('user.activePlan')
        ->with('category', 'city', 'user')
        ->orderBy('id', 'DESC')
        ->limit(3)
        ->get();

    $rlHeading = trim((string) @($recentListingContent->data_values->heading));
    if ($rlHeading === '') {
        $rlHeading = 'Recently added businesses';
    }
    $rlSub = trim((string) @($recentListingContent->data_values->subheading));
    $rlSub = ($rlSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $rlSub)) ? $rlSub : null;
@endphp

@if (!blank($listings))
    <section class="lgx-section lgx-section--tint">
        <div class="lgx-container">
            <div class="lgx-sec-head">
                <div>
                    <h2>{{ __($rlHeading) }}</h2>
                    @if ($rlSub)
                        <p>{{ __($rlSub) }}</p>
                    @endif
                </div>
                <a class="lgx-sec-head__link" href="{{ route('listings') }}">@lang('Browse all')</a>
            </div>

            <div class="lgx-cards">
                @foreach ($listings as $listing)
                    @php
                        $listingRating = averageReview($listing->reviews_count, $listing->reviews_sum_rating);
                        $listingUrl    = route('listing.details', ['slug' => slug($listing->title) ?: 'listing', 'id' => $listing->id]);
                    @endphp
                    <article class="lgx-lc">
                        <a class="lgx-lc__th" href="{{ $listingUrl }}" aria-label="{{ $listing->title }}">
                            @if (trim((string) $listing->display_image) !== '')
                                <img src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}"
                                     alt="{{ $listing->title }}" loading="lazy">
                            @else
                                <span class="lgx-lc__ph"><i class="las la-store"></i></span>
                            @endif
                        </a>
                        <div class="lgx-lc__bd">
                            <h3><a href="{{ $listingUrl }}">{{ strLimit(__($listing->title), 44) }}</a></h3>
                            <p class="lgx-lc__loc">
                                <i class="las la-map-marker-alt"></i>
                                {{ __(@$listing->city->name) }}
                                @if (@$listing->category?->name)
                                    <span class="lgx-sep">·</span> {{ __($listing->category->name) }}
                                @endif
                            </p>
                            <div class="lgx-lc__rt">
                                <span class="lgx-stars">@php echo showRatingStars($listingRating) @endphp</span>
                                <span class="lgx-lc__rtn">{{ round($listingRating, 1) }} ({{ $listing->reviews_count }})</span>
                                <span class="lgx-badge lgx-badge-indigo lgx-lc__badge"><span class="lgx-dot"></span>@lang('New')</span>
                            </div>
                            <div class="lgx-lc__ft">
                                @if (@$listing->category?->name)
                                    <span class="lgx-badge lgx-badge-indigo">{{ __($listing->category->name) }}</span>
                                @else
                                    <span></span>
                                @endif
                                <a class="btn-lgx btn-lgx-ghost btn-lgx-sm" href="{{ $listingUrl }}">@lang('View details')</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
