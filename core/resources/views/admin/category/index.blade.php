@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Image') | @lang('Name')</th>
                                    <th>@lang('Listings')</th>
                                    <th>@lang('Is Featured')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end justify-content-lg-start gap-2">
                                                <span class="d-block avatar avatar--xs">
                                                    <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}">
                                                </span>
                                                {{ __(strLimit($category->name, 15)) }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ getAmount($category->listings_count) }}
                                        </td>

                                        <td>
                                            @if ($category->is_featured == Status::YES)
                                                <span class="badge badge--warning"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--info"> @lang('No')</span>
                                            @endif
                                        </td>

                                        <td>
                                            @php echo $category->statusBadge; @endphp
                                        </td>
                                        @php
                                            $category->image_with_path = getImage(
                                                getFilePath('category') . '/' . $category->image,
                                                getFileSize('category'),
                                            );
                                        @endphp
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary editBtn cuModalBtn btn-sm" data-modal_title="@lang('Update Category')"
                                                    data-resource="{{ $category }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.category.status', $category->id),
                                                    'checked' => $category->status == Status::ENABLE,
                                                    'confirm' => $category->status == Status::ENABLE ? __('Are you sure to disable this category?') : __('Are you sure to enable this category?'),
                                                    'title'   => $category->status == Status::ENABLE ? __('Enabled') : __('Disabled'),
                                                ])

                                                @if ($category->is_featured == Status::YES)
                                                    <button class="btn btn-outline--info btn-sm confirmationBtn" data-question="@lang('Are you sure to unfeature this category?')"
                                                        data-action="{{ route('admin.category.feature', $category->id) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Unfeature')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--warning confirmationBtn btn-sm" data-question="@lang('Are you sure to feature this category?')"
                                                        data-action="{{ route('admin.category.feature', $category->id) }}">
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $categoryImage = getImage(getFilePath('category'), getFileSize('category'));
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
                <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" type="category" />
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
    <button class="btn btn-sm btn-outline--primary cuModalBtn" data-image_size="{{ getImage(getFilePath('category') . '/', getFileSize('category')) }}"
        data-modal_title="@lang('Add Category')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

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

            $('.cuModalBtn').on('click', function() {
                var image_size = $(this).data('image_size');
                $('#cuModal').find('.image-upload-preview').css('background-image', 'url(' + image_size + ')');
            });

        })(jQuery);
    </script>
@endpush
