@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $country)
                                    <tr>
                                        <td>
                                            {{ __($country->name) }}
                                        </td>
                                        <td>
                                            {{ __($country->slug) }}
                                        </td>
                                        <td>
                                            @php echo $country->statusBadge; @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary editBtn cuModalBtn btn-sm" data-country="{{ $country->name }}"
                                                    data-modal_title="@lang('Update Country')" data-resource="{{ $country }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.country.status', $country->id),
                                                    'checked' => $country->status == Status::ENABLE,
                                                    'confirm' => $country->status == Status::ENABLE ? __('Are you sure to disable this country?') : __('Are you sure to enable this country?'),
                                                    'title'   => $country->status == Status::ENABLE ? __('Enabled') : __('Disabled'),
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
                @if ($countries->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($countries) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

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
                <form action="{{ route('admin.country.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="country-name">@lang('Country')</label>
                            <select class="select2-basic" name="name" required>
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach ($countryList as $key => $country)
                                    <option value="{{ $country->country }}">
                                        {{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Slug')</label>
                            <input class="form-control" name="slug" type="text" required>
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
    <button class="btn btn-sm btn-outline--primary addBtn cuModalBtn" data-modal_title="@lang('Add Country')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.select2-basic').select2({
                dropdownParent: $('#cuModal')
            })


            $('[name=name]').on('change', function() {
                let inputString = $(this).val();
                var slug = createSlug(inputString);
                $('[name=slug]').val(slug);
            })

function createSlug(inputString) {
    if (!inputString) return ''; // prevent null/undefined errors

    return inputString.toLowerCase()
        .replace(/[^\w\-]+/g, '-')
        .replace(/\-+/g, '-')
        .replace(/^-+|-+$/g, '');
}

            $('.editBtn').on('click', function(e) {
                var country = $(this).data('country');
                $('.select2-basic').val(country).trigger('change');
            });
            $('.addBtn').on('click', function(e) {
                $('.select2-basic').val('').trigger('change');
            });


        })(jQuery);
    </script>
@endpush
