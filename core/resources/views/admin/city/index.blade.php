@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Listings')</th>
                                    <th>@lang('Is Featured')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cities as $city)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end justify-content-lg-start gap-2">
                                                <span class="d-block avatar avatar--xs">
                                                    <img src="{{ getImage(getFilePath('city') . '/' . $city->image, getFileSize('city')) }}">
                                                </span>
                                                {{ __(strLimit($city->name, 10)) }}
                                            </div>
                                        </td>
                                        <td>

                                            @if (@$city->country->name)
                                                {{ __(@$city->country->name) }}
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>
                                            {{ getAmount($city->listings_count) }}
                                        </td>
                                        <td>
                                            @if ($city->is_featured == Status::YES)
                                                <span class="badge badge--warning"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--info"> @lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php echo $city->statusBadge; @endphp
                                        </td>
                                        @php
                                            $city->image_with_path = getImage(getFilePath('city') . '/' . $city->image, getFileSize('city'));
                                        @endphp
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn cuModalBtn" data-resource="{{ $city }}"
                                                    data-country="{{ $city->country_id }}" data-modal_title="@lang('Edit City')"
                                                    type="button">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.city.status', $city->id),
                                                    'checked' => $city->status == Status::ENABLE,
                                                    'confirm' => $city->status == Status::ENABLE ? __('Are you sure to disable this city?') : __('Are you sure to enable this city?'),
                                                    'title'   => $city->status == Status::ENABLE ? __('Enabled') : __('Disabled'),
                                                ])
                                                @if ($city->is_featured == Status::YES)
                                                    <button class="btn btn-outline--info btn-sm confirmationBtn" data-question="@lang('Are you sure to unfeature this city?')"
                                                        data-action="{{ route('admin.city.feature', $city->id) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Unfeature')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--warning confirmationBtn btn-sm" data-question="@lang('Are you sure to feature this city?')"
                                                        data-action="{{ route('admin.city.feature', $city->id) }}">
                                                        <i class="las la-eye"></i>@lang('Feature')
                                                    </button>
                                                @endif
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

                @if ($cities->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($cities) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    @php
        $cityImage = getImage(getFilePath('city'), getFileSize('city'));
    @endphp
    <!--Cu Modal -->
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.city.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" type="city" :required=false />
                        </div>

                        <div class="form-group">
                            <label>@lang('Country')</label>
                            <select class="form-control select2-basic" name="country_id" required>
                                <option value="">@lang('Select one')</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"> {{ __($country->name) }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Slug')</label>
                            <input class="form-control" name="slug" type="text" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary h-45 cuModalBtn addBtn"
        data-image_size="{{ getImage(getFilePath('city') . '/', getFileSize('city')) }}" data-modal_title="@lang('Add New City')" type="button">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {

                $('.select2-basic').select2({
                    dropdownParent: $('#cuModal')
                })

                $('[name=name]').on('keyup', function() {
                    let inputString = $(this).val();
                    var slug = createSlug(inputString);
                    $('[name=slug]').val(slug);
                })

                function createSlug(inputString) {
                    var slug = inputString.toLowerCase()
                        .replace(/[^\w\-]+/g, '-')
                        .replace(/\-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    return slug;
                }

                $('.editBtn').on('click', function(e) {
                    var country = $(this).data('country');
                    $('.select2-basic').val(country).trigger('change');
                });
                $('.addBtn').on('click', function(e) {
                    $('.select2-basic').val('').trigger('change');
                });

                $('.cuModalBtn').on('click', function() {
                    var image_size = $(this).data('image_size');
                    $('#cuModal').find('.image-upload-preview').css('background-image', 'url(' + image_size + ')');
                });

            });
        })(jQuery);
    </script>
@endpush
