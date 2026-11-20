@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2 flex-wrap">
                @if ($listing->status == Status::LISTING_PENDING)
                    <button class="btn btn--success confirmationBtn" data-action="{{ route('admin.listing.approve', $listing->id) }}"
                        data-question="@lang('Are you sure to approve this listing')?" type="button" @disabled(@$userPlan->listings < @$approvedTotal)>
                        <i class="la la-check"></i>@lang('Approve')
                    </button>
                    <button class="btn btn--danger" data-bs-toggle="modal" data-bs-target="#rejectStatusModal" type="button">
                        <i class="la la-times"></i>@lang('Reject')
                    </button>
                @endif

                @if (!($listing->status == Status::LISTING_DISABLED || $listing->status == Status::LISTING_REJECTED))
                    @if ($listing->is_featured == Status::YES)
                        <button class="btn btn--dark btn-sm confirmationBtn" data-question="@lang('Are you sure to unfeature this listing?')"
                            data-action="{{ route('admin.listing.feature', $listing->id) }}">
                            <i class="las la-eye-slash"></i>@lang('Make unfeared')
                        </button>
                    @else
                        <button class="btn btn--primary confirmationBtn btn-sm" data-question="@lang('Are you sure to feature this listing?')"
                            data-action="{{ route('admin.listing.feature', $listing->id) }}">
                            <i class="las la-eye"></i>@lang('Make Feature')
                        </button>
                    @endif
                @endif
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Listing Details')</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="text--muted">@lang('Owner')</span>
                            <h6 class="text--primary"><a href="{{ route('admin.users.detail', $listing->user_id) }}">{{ $listing->user->username }}</a>
                            </h6>
                        </li>

                        <li class="list-group-item">
                            <span class="text--muted">@lang('Listing Id')</span>
                            <h6>#{{ $listing->listing_id }}</h6>
                        </li>

                        <li class="list-group-item">
                            <span class="text--muted">@lang('Title')</span>
                            <h6>{{ __($listing->title) }}</h6>
                        </li>
                        <li class="list-group-item">
                            <span class="text--muted">@lang('Country')</span>
                            <h6>{{ __(@$listing->country->name) }}</h6>
                        </li>
                        <li class="list-group-item">
                            <span class="text--muted">@lang('City')</span>
                            <h6>{{ __(@$listing->city->name) }}</h6>
                        </li>
                        <li class="list-group-item">
                            <span class="text--muted">@lang('Location')</span>
                            <h6>{{ __($listing->location) }}</h6>
                        </li>
                        <li class="list-group-item">
                            <span class="text--muted">@lang('Category')</span>
                            <h6>{{ __($listing->category->name) }}</h6>
                        </li>
                        @if ($listing->tag)
                            <li class="list-group-item">
                                <span>@lang('Tags')</span>

                                <div>
                                    @foreach ($listing->tags as $tags)
                                        <span class="badge badge--primary">{{ $tags }}</span>
                                    @endforeach
                                </div>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>

            @if (!blank($listing->amenities))
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center gap-2">@lang('Amenities') <span
                                class="badge bg--info">{{ count($listing->amenities) }}/{{ @$userPlan->amenities ?? 0 }}</span></h5>

                        <ul class="list-group list-group-flush">
                            @foreach ($listing->amenities as $amenity)
                                <li class="list-group-item">
                                    <span>{{ __($amenity->name) }}</span> <span> {{ __($amenity->value) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">@lang('Operating Hours')</h5>
                    <ul class="list-group list-group-flush">
                        @if ($listing->open_time)
                            <li class="list-group-item">
                                <span>@lang('Open at')</span>
                                <span>{{ showDateTime($listing->open_time, 'h:i A') }}</span>
                            </li>
                        @endif

                        @if ($listing->close_time)
                            <li class="list-group-item">
                                <span>@lang('Closed at')</span>
                                <span>{{ showDateTime($listing->close_time, 'h:i A') }}</span>
                            </li>
                        @endif
                        @if (!blank($listing->day_off))
                            <li class="list-group-item">
                                <span>@lang('Day Off')</span>
                                @php
                                    $listing->day_off = array_map(function ($day) {
                                        return ucfirst($day);
                                    }, $listing->day_off);
                                @endphp

                                <span>
                                    {{ implode(', ', $listing->day_off) }}
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">@lang('Listing Description')</h5>
                    <p> @php  echo $listing->description; @endphp</p>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text--muted">@lang('Status')</span>
                            <div>
                                @php echo $listing->statusBadge; @endphp
                            </div>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text--muted">@lang('Featured')</span>
                            <div>
                                @if ($listing->is_featured == Status::YES)
                                    <span class="badge badge--success">@lang('Yes')</span>
                                @else
                                    <span class="badge badge--danger">@lang('No')</span>
                                @endif
                            </div>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">@lang('Approved Listings')</span> <span> {{ $totalApproved }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">@lang('Allowed Listings')</span> <span>{{ @$userPlan->listings ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            @if ($listing->map)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"> @lang('Map') </h5>
                        <iframe src="{{ @$listing->map }}" style="border:0;" width="100%" height="300" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Display Image')</h5>
                    <div class="row">
                        <div class="col-sm-4">
                            <a class="gallery-thumb"
                                href="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}">
                                <img class="mw-100" src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}"
                                    alt="@lang('image')">
                            </a>
                        </div>
                    </div>
                    @if (!blank($listing->images))
                        <h5 class="card-title d-flex align-items-center mt-3 gap-2">@lang('Gallery Images') <span
                                class="badge bg--info">{{ $listing->images->count() }}/{{ @$userPlan->images ?? 0 }}</span></h5>
                        <div class="row gy-4">
                            @foreach ($listing->images as $image)
                                <div class="col-sm-4">
                                    <a class="gallery-thumb" href="{{ getImage(getFilePath('listing') . '/' . $image->name, getFileSize('listing')) }}">
                                        <img class="mw-100" src="{{ getImage(getFilePath('listing') . '/' . $image->name, getFileSize('listing')) }}"
                                            alt="@lang('image')">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @if (!blank($listing->videos))
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center gap-2">@lang('Videos') <span
                                class="badge bg--info">{{ count(@$listing->videos) }}/{{ @$userPlan->videos ?? 0 }}</span></h5>

                        @foreach ($listing->videos as $video)
                            <iframe class="video mw-100" src="{{ @$video }}" title="YouTube video player" width="100%" height="350"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="rejectStatusModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>@lang('Reject listing')</span>
                    </h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.listing.reject', $listing->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Reject reason')</label>
                            <textarea class="form-control" name="rejected_reason" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectReasonModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>@lang('Reason for Rejection')</span>
                    </h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="reson-box"></p>

                </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-outline--warning" href="{{ route('admin.review.index') }}?search={{ $listing->listing_id }}">@lang('Reviews')
        ({{ $listing->reviews_count }})</a>
    <x-back route="{{ route('admin.listing.index') }}" />
@endpush

@push('style')
    <style>
        .list-group-item {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: .8rem 0;
            border: 1px solid #f1f1f1;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.reasonBtn').on('click', function() {
                var modal = $('#rejectReasonModal');
                var asdf = $(this).data('reason');
                modal.find('.reson-box').html(($(this).data('reason')));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
