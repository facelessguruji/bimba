@extends('Template::layouts.user')
@section('content')
    <div class="container py-120">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="mb-1">@lang('My Listings')</h4>
                <p class="text-muted mb-0">@lang('Manage your business listings')</p>
            </div>
            <a href="{{ route('user.listing.create') }}" class="btn btn--base btn--sm"><i class="las la-plus"></i> @lang('Add New')</a>
        </div>

        <form class="mb-4">
            <div class="input-group" style="max-width: 380px;">
                <input type="text" class="form--control" name="search" value="{{ request()->search }}" placeholder="@lang('Search listings...')">
                <button type="submit" class="input-group-text btn--base text-white"><i class="las la-search"></i></button>
            </div>
        </form>

        @if (!$listings->isEmpty())
            <div class="row gy-4">
                @foreach ($listings as $listing)
                    <div class="col-xl-4 col-md-6">
                        <div class="card custom--card h-100 overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, '372x216') }}"
                                     class="w-100" style="height: 180px; object-fit: cover;" alt="{{ $listing->title }}">
                                <div class="position-absolute top-0 end-0 p-2 d-flex gap-1">
                                    @php echo $listing->statusBadge @endphp
                                </div>
                                @if ($listing->is_featured)
                                    <span class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-warning text-dark"><i class="las la-star"></i> @lang('Featured')</span>
                                    </span>
                                @endif
                            </div>
                            <div class="card-body p-3">
                                <small class="text--base">{{ __($listing->category->name ?? '') }}</small>
                                <h6 class="mt-1 mb-2">{{ strLimit($listing->title, 45) }}</h6>
                                <div class="d-flex align-items-center gap-3 text-muted" style="font-size: .8125rem;">
                                    <span><i class="las la-map-marker-alt me-1"></i>{{ $listing->city->name ?? '' }}</span>
                                    <span><i class="las la-globe me-1"></i>{{ $listing->country->name ?? '' }}</span>
                                </div>
                            </div>
                            <div class="card-footer py-2 px-3 d-flex justify-content-between align-items-center bg-transparent">
                                <div class="d-flex gap-1">
                                    <a class="btn btn--sm btn-outline--info" href="{{ route('user.listing.edit', $listing->id) }}">
                                        <i class="la la-pen"></i> @lang('Edit')
                                    </a>
                                    @if ($listing->status == Status::LISTING_APPROVED)
                                        <button class="btn btn-outline--danger btn--sm confirmationBtn" data-question="@lang('Are you sure to disable this listing?')"
                                            data-action="{{ route('user.listing.status', ['id' => $listing->id, 'status' => Status::LISTING_DISABLED]) }}">
                                            <i class="las la-ban"></i>
                                        </button>
                                    @elseif($listing->status == Status::LISTING_DISABLED)
                                        <button class="btn btn-outline--success confirmationBtn btn--sm" data-question="@lang('Are you sure to enable this listing?')"
                                            data-action="{{ route('user.listing.status', ['id' => $listing->id, 'status' => Status::LISTING_APPROVED]) }}">
                                            <i class="las la-check"></i>
                                        </button>
                                    @endif
                                </div>
                                @if ($listing->status == Status::LISTING_APPROVED)
                                    {{-- the public detail page exists only for approved listings  --}}
                                    {{-- - linking pending/disabled ones would 404 --}}
                                    <a href="{{ route('listing.details', ['slug' => \Str::slug($listing->title), 'id' => $listing->id]) }}"
                                       class="btn btn--sm btn--base" target="_blank">
                                        <i class="las la-eye"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($listings->hasPages())
                <div class="mt-4">{{ paginateLinks($listings) }}</div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="las la-store la-4x text-muted d-block mb-3"></i>
                <h5 class="text-muted">@lang('No listings yet')</h5>
                <p class="text-muted mb-3">@lang('Create your first business listing to get discovered')</p>
                <a href="{{ route('user.listing.create') }}" class="btn btn--base"><i class="las la-plus"></i> @lang('Create Listing')</a>
            </div>
        @endif
    </div>

    <div class="modal fade" id="rejectReasonModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>@lang('Reason for Rejection')</span></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <p class="reson-box"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal :isFrontend=true />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.reasonBtn').on('click', function() {
                var modal = $('#rejectReasonModal');
                modal.find('.reson-box').html($(this).data('reason'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
