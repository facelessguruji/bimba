@extends('Template::layouts.master')
@section('content')
    <div class="py-120 ">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-12">
                    @include('Template::partials.listing_filter')
                </div>
                <div class="col-lg-12">
                    <div class="listing-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <button class="filter-button btn btn--base" type="submit"><i class="las la-filter"></i></button>
                            <h6 class="listing-header__title">
                                @lang('Showing') {{ $listings->total() }} {{ $listings->total() == 1 ? __('listing') : __('listings') }}
                            </h6>
                        </div>
                        <div class="d-flex align-items-center view-buttons gap-2">
                            <button class="btn text--body active gridView" type="button">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="btn text--body listView" type="button">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="listing-wrapper">
                        <div class="row g-3 g-md-4">
                            @include('Template::partials.listing', ['class' => 'col-lg-4 col-sm-6 listing-item'])

                            @if (blank($listings))
                                @include('Template::partials.data_not_found', ['data' => 'No Listing Found'])
                            @endif
                        </div>
                    </div>
                    @if ($listings->hasPages())
                        <div class="mt-4">
                            {{ paginateLinks($listings) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.listView').on('click', function() {
                sessionStorage.setItem("setView", "listView");
                $(this).addClass('active');
                $('.gridView').removeClass('active');
                $('.listing-wrapper').addClass('list-view');
            });

            $('.gridView').on('click', function() {
                sessionStorage.setItem("setView", "");
                $(this).addClass('active');
                $('.listView').removeClass('active');
                $('.listing-wrapper').removeClass('list-view');
            });

            if (sessionStorage.getItem("setView")) {
                $('.listView').addClass('active');
                $('.gridView').removeClass('active');
                $('.listing-wrapper').addClass('list-view');
            } else {
                $('.gridView').addClass('active');
                $('.listView').removeClass('active');
                $('.listing-wrapper').removeClass('list-view');
            }

        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .filter-btn {
            word-break: normal;
        }
    </style>
@endpush
