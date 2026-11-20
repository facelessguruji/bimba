@php
    $favoriteList = [];

    if (auth()->check()) {
        $favoriteList = auth()
            ->user()
            ->favoriteListings->pluck('listing_id')
            ->toArray();
    }
@endphp

@foreach ($listings as $listing)
    <div class="{{ $class }}">
        <div class="feature-item">
            <div class="feature-item__thumb">
                <div class="feature-item__thumb-link">
                    <img src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}">
                    @auth
                        <button type="button" class="listing-item__thumb-icon @if (in_array($listing->id, $favoriteList)) wishlisted @endif clickWishList" data-listing_id="{{ $listing->id }}">
                            <i class="las la-heart"></i>
                        </button>
                    @endauth
                </div>
            </div>

            <div class="feature-item__content-wrapper">
                <div class="feature-item__content">
                    <h5 class="feature-item__title ">
                        <a href="{{ route('listing.details', ['slug' => slug($listing->title) ?: 'listing', 'id' => $listing->id]) }}" class="feature-item__link">
                            {{ __(strLimit($listing->title, 30)) }}
                        </a>
                    </h5>

                    <div class="d-flex justify-content-between flex-wrap gap-1 align-items-center">
                        <div class="rating-star">
                            @php echo showRatingStars(averageReview($listing->reviews_count, $listing->reviews_sum_rating)) @endphp
                            <span> ({{ @$listing->reviews_count }}) </span>
                        </div>

                        <div>
                            <span class="listing-item__icon"> <img src="{{ getImage(getFilePath('category') . '/' . $listing->category->image, getFileSize('category')) }}" alt="{{ $listing->category->name }}"> </span>
                            <span class="listing-item__icon-text"> {{ @$listing->category->name }} </span>
                        </div>
                    </div>
                </div>

                <div class="feature-item__contact flex-between">
                    <div class="feature-contact-wrapper">
                        <span class="text--body position-absolute h-100 w-100"></span>
                        <span class="feature-item__contact-icon">
                            <i class="las la-map-marker"></i>
                        </span>
                        <span class="feature-item__contact-area">
                            {{ __($listing->city->name) }}
                        </span>
                    </div>

                    <div class="feature-contact-wrapper">
                        @php
                            // guests see a masked number - full contact requires registration
                            $visibleMobile = auth()->check()
                                ? $listing->user?->mobile
                                : ($listing->user?->mobile ? maskContactNumber($listing->user->mobile) : null);
                        @endphp
                        @if (auth()->check() && $listing->user?->mobile)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $listing->user->mobile) }}" class="text--body position-absolute h-100 w-100"></a>
                        @endif
                        <span class="feature-item__contact-icon">
                            <i class="las la-phone"></i>
                        </span>
                        <span class="feature-item__contact-number">
                            {{ $visibleMobile ?? __('Log in for contact') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if (!app()->offsetExists('wishlist_script'))
    @push('script')
        <script>
            (function($) {
                'use strict';

                $(document).on('click', '.clickWishList', function(e) {
                    e.preventDefault();

                    let button = $(this);
                    let listingId = button.data('listing_id');

                    let routeTemplate = `{{ route('user.listing.add.to.favorite', ['id' => ':id']) }}`;
                    let url = routeTemplate.replace(':id', listingId);

                    $.post(url, {
                        _token: "{{ csrf_token() }}",
                    }, function(response) {
                        button.toggleClass('wishlisted');
                    });
                });


            })(jQuery);
        </script>
    @endpush

    @php app()->offsetSet('wishlist_script',true) @endphp
@endif
