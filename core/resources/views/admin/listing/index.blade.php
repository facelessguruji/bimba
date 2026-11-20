@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Listing')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Is Featured')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($listings as $listing)
                                    <tr>
                                        <td class="text-start">
                                            <div class="d-flex justify-content-end justify-content-lg-start flex-wrap">
                                                <span class="avatar avatar--xs me-2">
                                                    <img src="{{ getImage(getFilePath('listing') . '/' . $listing->display_image, getFileSize('listing')) }}">
                                                </span>

                                                <span>
                                                    {{ strLimit(__($listing->title), 40) }}
                                                    <br>
                                                    <em class="text-muted">
                                                        <i class="la la-map-marker-alt"></i>
                                                        @if (@$listing->country->name)
                                                            {{ strLimit(__(@$listing->country->name), 20) }},
                                                        @endif{{ strLimit(__($listing->city->name), 20) }}
                                                    </em>
                                                </span>
                                            </div>
                                        </td>

                                        <td>

                                            {{ __($listing->category->name) }}
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.users.detail', $listing->user->id) }}">{{ $listing->user->username }}</a>
                                        </td>

                                        <td>
                                            @php  echo  $listing->featuredBadge;  @endphp
                                        </td>

                                        <td>
                                            @php  echo  $listing->statusBadge;  @endphp
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-outline--primary btn-sm" href="{{ route('admin.listing.details', $listing->id) }}">
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($listings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($listings) }}
                    </div>
                @endif
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
    <x-search-form />
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('select').on('change', function() {
                let form = $('.listing-filter');
                form.submit();
            });

            $('.reasonBtn').on('click', function() {
                var modal = $('#rejectReasonModal');
                var asdf = $(this).data('reason');
                modal.find('.reson-box').html(($(this).data('reason')));
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
