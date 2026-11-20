@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Listings')</th>
                                    <th>@lang('Images')</th>
                                    <th>@lang('Videos')</th>
<th>@lang("Events")</th>
                                    <th>@lang('Allow Map')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>{{ strLimit(__($plan->title), 30) }}</td>
                                        <td>
                                            {{ showAmount($plan->yearly_price) }} <span class="text-muted">/@lang('Year')</span>
                                        </td>

                                        <td>{{ $plan->listings }}</td>
                                        <td>{{ $plan->images }}</td>
                                        <td>{{ $plan->videos }}</td>
                                        <td>{{ $plan->events }}</td>

                                        <td>
                                            @if ($plan->map == Status::YES)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>

                                        <td> @php echo $plan->statusBadge; @endphp </td>

                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary cuModalBtn btn-sm" data-modal_title="@lang('Update Plan')"
                                                    data-resource="{{ $plan }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>

                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.plan.status', $plan->id),
                                                    'checked' => $plan->status == Status::ENABLE,
                                                    'confirm' => $plan->status == Status::ENABLE ? __('Are you sure to disable this plan?') : __('Are you sure to enable this plan?'),
                                                    'title'   => $plan->status == Status::ENABLE ? __('Enabled') : __('Disabled'),
                                                ])
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
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!--Cu Modal -->
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.plan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input class="form-control" name="title" type="text" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Price (1 Year)')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="yearly_price" type="number" step="any" required>
                                        <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Listings') <i class="la la-info-circle" title="@lang('The number of listings can be added')"></i></label>
                                    <input class="form-control" name="listings" type="number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Amenities') <i class="la la-info-circle" title="@lang('The number of amenities can be added per listing')"></i></label>
                                    <input class="form-control" name="amenities" type="number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Images') <i class="la la-info-circle" title="@lang('The number of images can be added per listing')"></i></label>
                                    <input class="form-control" name="images" type="number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Videos') <i class="la la-info-circle" title="@lang('The number of videos can be added per listing')"></i></label>
                                    <input class="form-control" name="videos" type="number" required>
                                        <div class="form-group"><label>@lang("Events per year")</label><input class="form-control" name="events" type="number" required></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Allow Map')</label>
                                    <label class="lgx-switch" title="@lang('Allow Map')">
                                        <input type="checkbox" name="map">
                                        <span class="lgx-switch__track"></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection



@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add Plan')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.cuModalBtn').on('click', function() {
                let data = $(this).data('resource');
                let cuModal = $('#cuModal');

                // plain lgx-switch checkbox: set checked state directly
                // (the old bootstrap-toggle plugin is no longer loaded)
                if (data && data.map == 1) {
                    cuModal.find('[name=map]').prop('checked', true);
                } else {
                    cuModal.find('[name=map]').prop('checked', false);
                }
            });
        })(jQuery);
    </script>
@endpush
