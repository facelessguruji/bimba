@extends('Template::layouts.master')
@section('content')
    @include('Template::partials.json_ld_listing')
    <section class="listing-detials py-120">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="listing-details">
                        <div class="listing-details__header">
                            <h4 class="listing-details__header-title"> {{ __($listing->title) }}</h4>

                            <div class="d-flex align-items-center flex-wrap">

                                <div class="rating-star listing-details-item">
                                    @php echo showRatingStars($listing->avgRating()) @endphp
                                    <span class="rating__number"> {{ @$listing->reviews->count() }} @lang('Ratings') </span>
                                </div>

                                <div class="listing-details-item">
                                    <a href="{{ route('listings') }}?category={{ $listing->category->slug }}">
                                        <span class="listing-details-item__icon">
                                            <img src="{{ getImage(getFilePath('category') . '/' . $listing->category->image, getFileSize('category')) }}" alt="{{ $listing->category->name }}">
                                        </span>
                                        <span class="listing-details-item__icon-text"> {{ __($listing->category->name) }}</span>
                                    </a>
                                </div>

                                <div class="listing-details-item">
                                    <span class="listing-details-item__icon "><i class="las la-map-marker"></i></span>
                                    <span class="listing-details-item__icon-text"> {{ trim(__($listing->location) . ', ' . __(@$listing->city->name) . ( @$listing->country->name ? ', ' . __(@$listing->country->name) : ''), ', ') }} </span>
                                </div>

                                @if ($listing->open_time || $listing->close_time || count($listing->day_off ?? []))
                                    @if (isBusinessOpen($listing->open_time, $listing->close_time, $listing->day_off ?? []))
                                        <span class="badge bg--success"> @lang('Open')</span>
                                    @else
                                        <span class="badge bg--warning">@lang('Close')</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="listing-details__thumb">
                            <img class="fit-image" src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}">
                        </div>

                        <div class="listing-details__content">
                            <h5 class="listing-details__title"> @lang('Description ')</h5>
                            <div class="listing-details__desc">@php echo $listing->description @endphp</div>

                            @if ($listing->open_time || $listing->close_time || count($listing->day_off ?? []))
                            <div class="listing-details__date">
                                <h5 class="listing-details__date-title"> @lang('Working Hours')</h5>
                                <div class="listing-details__date-content flex-between gap-3">
                                    @if ($listing->open_time)
                                        <div>
                                            <h6 class="title"> @lang('Open') </h6>
                                            <div class="d-flex align-items-center">
                                                <span class="time-icon"><i class="las la-clock"></i></span>
                                                <span class="time">{{ showDateTime($listing->open_time, 'h:i A') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($listing->close_time)
                                        <div>
                                            <h6 class="title"> @lang('Close') </h6>
                                            <div class="d-flex align-items-center">
                                                <span class="time-icon"><i class="las la-clock"></i></span>
                                                <span class="time">{{ showDateTime($listing->close_time, 'h:i A') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!blank($listing->day_off))
                                        <div>
                                            <h6 class="title"> @lang('Days Off') </h6>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $listing->day_off = array_map(function ($day) {
                                                        return ucfirst($day);
                                                    }, $listing->day_off);
                                                @endphp

                                                <small>
                                                    {{ implode(', ', $listing->day_off) }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if (!blank($listing->amenities))
                                <div class="listing-details__amenities">
                                    <h5 class="listing-details__amenities-title"> @lang('Amenities') </h5>
                                    <div class="amenities-list">
                                        <div class="row gy-4">
                                            @foreach ($listing->amenities as $amenity)
                                                <div class="col-xsm-6 col-sm-6 col-md-4 ">
                                                    <h6 class="title">{{ __($amenity->name) }}</h6> <span> {{ __($amenity->value) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (count($listing->videos ?? []) || count($listing->images ?? []))
                                <div class="listing-details__gallery">
                                    <ul class="nav nav-pills custom--tab tab-three" id="pills-tab" role="tablist">
                                        @if (!blank($listing->images))
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-photos-tab" data-bs-toggle="pill" data-bs-target="#pills-photos" type="button" role="tab" aria-controls="pills-photos" aria-selected="true">@lang('Photos')</button>
                                            </li>
                                        @endif

                                        @if (!blank($listing->videos))
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link @if (blank($listing->images)) active @endif" id="pills-video-tab" data-bs-toggle="pill" data-bs-target="#pills-video" type="button" role="tab" aria-controls="pills-video" aria-selected="false"> @lang('Video')
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        @if (!blank($listing->images))
                                            <div class="tab-pane fade show active" id="pills-photos" role="tabpanel" aria-labelledby="pills-photos-tab" tabindex="0">
                                                <div class="row gy-4 justify-content-center">
                                                    @foreach ($listing->images as $image)
                                                        <div class="col-sm-4 col-xsm-6">
                                                            <a class="gallery-thumb" href="{{ getImage(getFilePath('listing') . '/' . $image->name, getFileSize('listing')) }}">
                                                                <img src="{{ getImage(getFilePath('listing') . '/' . $image->name, getFileSize('listing')) }}" alt="@lang('image')">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if (!blank($listing->videos))
                                            <div class="tab-pane fade @if (!$listing->images->count()) show active @endif" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab" tabindex="0">
                                                <div class="row g-4">
                                                    @foreach ($listing->videos as $video)
                                                        <div class="col-lg-6">
                                                            <div class="video-info">
                                                                @if (isEmbeddableVideo($video))
                                                                    <iframe src="{{ $video }}" title="Video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                                @else
                                                                    {{-- only known embed hosts get an iframe; anything else stays a plain link so a listing can never frame arbitrary sites --}}
                                                                    <a href="{{ $video }}" target="_blank" rel="noopener nofollow"><i class="las la-external-link-alt"></i> @lang('Open video')</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($listing->map && isEmbeddableMap($listing->map))
                                <div class="listing-details__map">
                                    <h5 class="title">@lang('Location') </h5>
                                    <iframe src="{{ $listing->map }}" style="border:0;" height="450" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            @elseif ($listing->location)
                                {{-- no (valid) map embed: show the address only, never a broken iframe --}}
                                <div class="listing-details__map">
                                    <h5 class="title">@lang('Location') </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="listing-details-item__icon"><i class="las la-map-marker"></i></span>
                                        <span>{{ trim(__($listing->location) . ', ' . __(@$listing->city->name) . (@$listing->country->name ? ', ' . __(@$listing->country->name) : ''), ', ') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (!auth()->check())
                            <div class="alert alert--info mb-4 text-center">
                                <a href="{{ route('user.login') }}" class="text--body font-weight-bold">@lang('Log in')</a>
                                @lang('to review this listing')
                            </div>
                        @endif

                        @if ($canReview)
                            <!-- Review Form Start -->
                            <div class="contact-form pt-3" id="comment-box">
                                <h5 class="contact__title"> @lang('Add Your Review') </h5>
                                <form action="{{ route('user.review.store', $listing->id) }}" autocomplete="off" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label class="form--label" for="text-four">@lang('Review')
                                        </label>
                                        <textarea class="form--control" id="text-four" name="review" placeholder="@lang('Type review')" required>{{ old('review') }}</textarea>
                                    </div>

                                    <div class="listing-rating">
                                        <span class="title"> @lang('Your rating for this listing') </span>
                                        <div class="rating-list d-flex gap-3">
                                            <div class="rate">
                                                <input class="giveRating" id="star5" name="rating" type="radio" value="5">
                                                <label for="star5" title="text"></label>

                                                <input class="giveRating" id="star4" name="rating" type="radio" value="4">
                                                <label for="star4" title="text"></label>

                                                <input class="giveRating" id="star3" name="rating" type="radio" value="3">
                                                <label for="star3" title="text"></label>

                                                <input class="giveRating" id="star2" name="rating" type="radio" value="2">
                                                <label for="star2" title="text"></label>

                                                <input class="giveRating" id="star1" name="rating" type="radio" value="1">
                                                <label class="giveRating" for="star1" title="text"></label>
                                            </div>
                                            <div class="rating-list__rate"><span class="ratingContainer">0</span>/5 </div>
                                        </div>
                                    </div>

                                    <button class="btn btn--base" type="submit">@lang('Submit Review')</button>

                                </form>

                            </div>
                            <!-- Review Form End -->
                        @endif

                        <!-- Display Reviews Start -->
                        <div class="col-lg-12 pt-5">
                            <h5 class="details-subtitle mb-4"> @lang('Customer Feedback') </h5>
                            <div class="review flex-between section-bg mb-5">
                                <div class="rating-list rating-star">
                                    <span class="rating-list__number">{{ getAmount($listing->avgRating()) }}</span>
                                    @php echo showRatingStars($listing->avgRating()) @endphp
                                </div>
                                <span class="rating-list__text p-2">{{ $listing->reviews->count() }} @lang('reviews')</span>
                            </div>

                            @if ($listing->ai_review_summary)
                                <div class="alert alert--info mb-4">
                                    <h6 class="mb-1"><i class="las la-magic"></i> @lang('Review Summary by AI')</h6>
                                    <p class="mb-0">{{ $listing->ai_review_summary }}</p>
                                </div>
                            @endif

                            <div class="comment-list" id="commentWrapper">
                                @include('Template::partials.reviews')
                                @if ($listing->reviews->count() == 0)
                                    <p class="text-muted">@lang('No reviews yet. Be the first to share your experience with this business.')</p>
                                @endif
                            </div>

                        </div>
                        <!-- Display Reviews End -->
                        @if ($reviews->hasPages())
                            <button type="button" class="btn btn--base loadMore">@lang('Load More')</button>
                        @endif
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="listing-details-sidebar-wrapper">
                        @include('Template::partials.lead_form', ['leadListing' => $listing])
                        <div class="listing-details-sidebar">
                            <h5 class="listing-details-sidebar__title"> @lang('Posted By') </h5>
                            <div class="listing-details-sidebar__header">
                                <div class="listing-details-sidebar__header-thumb">
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $listing->user->image, null, true) }}" alt="@lang('image')">
                                </div>
                                <div class="listing-details-sidebar__header-info">
                                    <h6 class="name">{{ $listing->user->fullname }} </h6>
                                    @if (trim((string) @$listing->user->country) !== '')
                                        <span class="title">{{ @$listing->user->country }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="listing-details-sidebar__info">
                                <ul class="info-list">
                                    @if (trim((string) @$listing->user->address) !== '' || trim((string) @$listing->user->city) !== '')
                                        <li class="info-list__item"><span class="icon"><i class="las la-map-marker"></i></span>
                                            {{ trim(__(@$listing->user->address) . ', ' . __(@$listing->user->city), ', ') }}
                                        </li>
                                    @endif
                                    @if (@$listing->user->mobile)
                                    <li class="info-list__item"><span class="icon"><i class="las la-phone"></i></span>
                                        @if (auth()->check())
                                            {{ @$listing->user->mobile }}
                                        @else
                                            {{ maskContactNumber(@$listing->user->mobile) }}
                                            <a href="{{ route('user.login') }}" class="btn btn--sm btn--base ms-2">@lang('View Contact Number')</a>
                                        @endif
                                    </li>
                                    @endif
                                    @if (@$listing->user->website)
                                        <li class="info-list__item"><span class="icon"> <i class="las la-globe"></i></span>
                                            <a class="text--body" href="{{ $listing->user->website }}">{{ $listing->user->website }}</a>
                                        </li>
                                    @endif
                                    @if (trim((string) @$listing->user->email) !== '')
                                        <li class="info-list__item"><span class="icon"><i class="las la-envelope"></i></span> {{ @$listing->user->email }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="listing-details-sidebar">
                            <h5 class="listing-details-sidebar__title"> @lang('Category') </h5>
                            <ul class="text-list style-category">
                                <li class="text-list__item">
                                    <a class="text-list__link" href="{{ route('listings') }}">
                                        <span>@lang('All')</span>
                                        <span>({{ $allListing }})</span>
                                    </a>
                                </li>

                                @foreach ($categories as $category)
                                    <li class="text-list__item">
                                        <a class="text-list__link" href="{{ route('listings') }}?category={{ $category->slug }}">
                                            <span>{{ __($category->name) }}</span>
                                            <span>({{ $category->listings_count }})</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @if (!blank($listing->tags))
                            <div class="listing-details-sidebar">
                                <h5 class="listing-details-sidebar__title"> @lang('Tags') </h5>
                                <ul class="text-list style-tag">
                                    @foreach ($listing->tags as $tag)
                                        <li class="text-list__item"><a class="text-list__link" href="{{ route('listings') }}?search={{ $tag }}"> {{ $tag }} </a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .rate {
            height: 46px;
        }

        .rate:not(:checked)>input {
            position: absolute;
            display: none !important;
        }

        .rate:not(:checked)>label {
            float: right;
            width: 1em;
            overflow: hidden;
            white-space: nowrap;
            cursor: pointer;
            font-size: 30px;
            color: #94A3B8;
        }

        .rate:not(:checked)>label:before {
            content: '\f005';
            font-family: 'Line Awesome Free';
            font-weight: 900;
        }

        .rate>input:checked~label {
            color: #F59E0B;
        }

        .rate:not(:checked)>label:hover,
        .rate:not(:checked)>label:hover~label {
            color: #F59E0B;
        }

        .rate>input:checked+label:hover,
        .rate>input:checked+label:hover~label,
        .rate>input:checked~label:hover,
        .rate>input:checked~label:hover~label,
        .rate>label:hover~input:checked~label {
            color: #F59E0B;
        }

        .language-box select {
            color: hsl(var(--black)) !important;
            border: 1px solid hsl(var(--black)/0.4) !important;
        }

        .language-box select option {
            background-color: hsl(var(--white)) !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.giveRating').on('click', function() {
                $('.ratingContainer').text($(this).val());
            });

            let url = @json($reviews->nextPageUrl());

            $('.loadMore').on('click', function() {
                const loadMoreButton = $(this);
                loadMoreButton.attr('disabled', true).html(`<i class="fa fa-spin fa-circle-notch"></i> @lang('Loading')`);

                if (url) {
                    $.get(url, (response) => {
                        const reviewsHtml = $(response.reviews).hide();
                        $('#commentWrapper').append(reviewsHtml);

                        reviewsHtml.slideDown('fast', () => {
                            url = response.next_page_url;
                            if (!url) {
                                loadMoreButton.remove();
                            } else {
                                loadMoreButton.attr('disabled', false).html(`@lang('Load More')`);
                            }
                        });

                    }, 'json');
                }
            });
        })(jQuery);
    </script>
@endpush
