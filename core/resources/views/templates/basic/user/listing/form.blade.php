@extends('Template::layouts.user')
@section('content')
    <div class="feature-grid py-120">
        <div class="container">
            @include('Template::partials.ai_writer')
            @if ($activePlan)
                <form action="{{ route('user.listing.store', @$listing->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="create-listing-form">

                        <div class="create-listing-form-body">
                            <h5>@lang('Listing Details')</h5>

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Title')</label>
                                        <input type="text" class="form--control " name="title" value="{{ old('title', @$listing->title) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Description')</label>
                                        <textarea class="form-control nicEdit" name="description" rows="5">{{ old('description', @$listing->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Category')</label>
                                        <select name="category_id" class="form--control select2" required>
                                            <option disabled selected value="">@lang('Select one')</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected($category->id == @$listing->category_id)>
                                                    {{ __($category->name) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Country')</label>
                                        <select name="country_id" class="form--control select2" required>
                                            <option disabled selected value="">@lang('Select one')</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @selected($country->id == @$listing->country_id)
                                                    data-cities='@json($country->cities)'>
                                                    {{ __($country->name) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('City')</label>
                                        <select name="city_id" class="form--control select2" required>
                                            <option disabled selected value="">@lang('Select one')</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @selected($city->id == @$listing->city_id)>
                                                    {{ __($city->name) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Location')</label>
                                        <input type="text" name="location" class="form--control" value="{{ old('location', @$listing->location) }}"
                                            required>
                                    </div>
                                </div>

                                @if ($activePlan->allow_map)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Google Map') <i class="las la-info-circle"
                                                    title="@lang('Embed link')"></i></label>

                                            <input type="text" class="form--control" name="map" value="{{ old('map', @$listing->map) }}" />
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Tags') </label>
                                        <small class="ms-2">@lang('Separate multiple keywords with commas')</small>
                                        <select class="form--control select2-auto-tokenize" name="tags[]" multiple>
                                            @if (@$listing->tags)
                                                @foreach ($listing->tags as $option)
                                                    <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($activePlan->amenities)
                            <div class="create-listing-form-body border-top">
                                <h5 class="d-inline me-2">@lang('Amenities')</h5>
                                <small class="text--danger"> (@lang('Up to') {{ $activePlan->amenities }} @lang('amenities on your plan'))</small>

                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    <button type="button" class="btn btn--sm btn-outline--secondary amenityAddBtn">
                                        <i class="las la-plus"></i> @lang('Add Amenity')
                                    </button>
                                </div>

                                <div class="amenities-wrapper d-flex gap-3 flex-wrap">
                                    @isset($listing->amenities)
                                        @foreach ($listing->amenities as $key => $amenity)
                                            <div class="input-group singleAmenity">
                                                <input type="text" name="amenities[{{ $key }}][name]" value="{{ $amenity->name }}"
                                                    placeholder="@lang('Name')" class="form-control form--control">
                                                <input type="text" name="amenities[{{ $key }}][value]" value="{{ $amenity->value }}"
                                                    placeholder="@lang('Value')" class="form-control form--control">
                                                <button type="button" class="btn btn--sm btn--danger h-auto py-0 px-3 amenityRemoveBtn"><i
                                                        class="la la-times"></i></button>
                                            </div>
                                        @endforeach
                                    @endisset

                                </div>
                            </div>
                        @endif

                        <div class="create-listing-form-body border-top">
                            <h5>@lang('Operating Hours')</h5>

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Day Off')</label>
                                        <select class="form--control select2 dayOff" name="day_off[]" multiple>
                                            <option value="saturday">@lang('Saturday')</option>
                                            <option value="sunday">@lang('Sunday')</option>
                                            <option value="monday">@lang('Monday')</option>
                                            <option value="tuesday">@lang('Tuesday')</option>
                                            <option value="wednesday">@lang('Wednesday')</option>
                                            <option value="thursday">@lang('Thursday')</option>
                                            <option value="friday">@lang('Friday')</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        @php $openTime = @$listing->open_time ? showDateTime(@$listing->open_time, 'H:i') : '' @endphp
                                        <label class="form--label">@lang('Office Open')</label>
                                        <input type="time" class="form-control form--control" name="open_time"
                                            value="{{ old('open_time', $openTime) }}" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        @php $closeTime = @$listing->close_time ? showDateTime(@$listing->close_time, 'H:i') : '' @endphp
                                        <label class="form--label">@lang('Office Close')</label>
                                        <input type="time" class="form-control form--control" name="close_time"
                                            value="{{ old('close_time', $closeTime) }}" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="create-listing-form-body border-top">
                            <h5>@lang('Media Content')</h5>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Display Image') </label>
                                        <input type='file' class="form-control form--control" name="display_image" id="listingCoverUpload"
                                            accept=".png, .jpg, .jpeg" @if (!@$listing->id) required @endif />
                                        <small>
                                            <i class="la la-info-circle"></i> @lang('The image will be resized into') {{ getFileSize('listing') }}
                                        </small>

                                        <div class="image-preview">
                                            <img src="@if (@$listing->display_image) {{ getImage(getFilePath('listing') . '/' . @$listing->display_image) }} @else {{ asset('assets/images/placeholder-image.png') }} @endif"
                                                alt="@lang('image')">
                                        </div>

                                    </div>
                                </div>

                                @if ($activePlan->images)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Gallery Images') <small class="text--danger"> (@lang('Up to')
                                                    {{ $activePlan->images }} @lang('images on your plan'))</small></label>
                                            <div class="row">
                                                <div class="input-images pb-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($activePlan->videos)
                                    <div class="col-md-12">
                                        <div class="form-group">

                                            <div class="file-upload">

                                                <div class="d-flex justify-content-between">
                                                    <label class="form--label">
                                                        @lang('Videos') <i class="las la-info-circle" title="@lang('You need to put the youtube embed link only.')"></i>
                                                        <small class="text--danger"> @lang('Up to') {{ $activePlan->videos }}
                                                            @lang('videos on your plan')</small>
                                                    </label>
                                                    <button type="button" class="btn btn--sm btn-outline--secondary addVideoBtn">
                                                        <i class="las la-plus"></i> @lang('Add Video')
                                                    </button>
                                                </div>

                                                @if (@$listing->videos)
                                                    @foreach ($listing->videos as $key => $video)
                                                        <div class="input-group my-3">
                                                            <input type="text" name="videos[{{ $key }}]"
                                                                class="form-control form--control control-two" value="{{ $video }}" required />
                                                            @if (!$loop->first)
                                                                <button type="button" class="input-group-text btn--danger   remove-btn text-white"><i
                                                                        class="las la-times"></i></button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif

                                                <div id="listingVideoContainer"></div>

                                            </div>

                                        </div>
                                    </div>
                                @endif

                            </div>
                            <button type="submit" @disabled(!$activePlan) class="btn btn--base w-100">@lang('Submit')</button>
                        </div>

                    </div>
                </form>
            @else
                <div class="card custom--card">
                    <div class="card-body text-center text--warning">
                        <h5 class="text-center">@lang('No Active Plan')</h5>
                        <p class="mb-4">@lang('Currently, you have no subscribed plan. Please buy a plan to publish your listings or add new listings.')</p>

                        <div class="text-center">
                            <a href="{{ route('pricing') }}" class="btn btn--sm btn--base">@lang('Buy a Plan')</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('style')
    <style>
        .form--control,
        .image-uploader {
            border-radius: 0.5rem;
        }

        .image-uploader {
            border: 1px solid hsl(var(--black)/0.1);
        }

        .image-preview {
            margin-top: .8rem;
            width: 217px;
            height: 126px;
            padding: 2px;
            border: 1px solid hsl(var(--black)/0.1);
            border-radius: 3px;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .input-group-text {
            background-color: #fbfbfb;
            border: 1px solid #e9e9e9;
            border-radius: .5rem;
        }

        :focus-visible {
            outline: none !important;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/nicEdit.js') }}"></script>
    <script src="{{ asset('assets/global/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/global/css/image-uploader.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";


            @if (old() && old('category_id'))
                $('[name=category_id]').val(@json(old('category_id')));
            @endif

            @if (old() && old('city_id'))
                $('[name=city_id]').val(@json(old('city_id')));
            @endif

            @if (old() && old('day_off'))
                $('.dayOff').val(@json(old('day_off')));
            @elseif (isset($listing->day_off))
                $('.dayOff').val(@json($listing->day_off));
            @endif

            bkLib.onDomLoaded(function() {
                $(".nicEdit").each(function(index) {
                    $(this).attr("id", "nicEditor" + index);
                    new nicEditor({
                        fullPanel: true
                    }).panelInstance('nicEditor' + index, {
                        hasPanel: true
                    });
                });
            });

            $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
                $('.nicEdit-main').focus();
            });



            // image uploder
            @if (isset($images))
                let preloaded = @json($images);
            @else
                let preloaded = [];
            @endif

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'images',
                preloadedInputName: 'old',
                maxSize: 3 * 1024 * 1024,
                maxFiles: {{ $activePlan?->images ?? 0 }},
            });

            $('.addVideoBtn').on('click', function() {
                let count = $(document).find('.singleVideo').length;
                let max = @json(@$activePlan->videos) * 1;

                if (count < max) {
                    $("#listingVideoContainer").append(`
                        <div class="input-group my-3 singleVideo">
                            <input type="text" name="videos[]" placeholder="i.e. https://www.youtube.com/embed/WOb4cj7izpE" class="form-control form--control control-two" required />
                            <button type="button" class="input-group-text btn--danger remove-btn text-white"><i class="las la-times"></i></button>
                        </div>
                    `)
                }
            });

            $(document).on('click', '.remove-btn', function() {
                $(this).parent().remove();
            });

            $('.amenityAddBtn').on('click', function() {
                let count = $(document).find('.singleAmenity').length;
                let max = @json(@$activePlan->amenities) * 1;
                if (count < max) {
                    $('.amenities-wrapper').append(`
                        <div class="input-group singleAmenity">
                            <input type="text" name="amenities[${count}][name]" placeholder="@lang('Name')" class="form-control form--control">
                            <input type="text" name="amenities[${count}][value]" placeholder="@lang('Value')" class="form-control form--control">
                            <button type="button" class="btn btn--sm btn--danger h-auto py-0 px-3 amenityRemoveBtn"><i class="la la-times"></i></button>
                        </div>
                    `);
                }
            });

            $(document).on('click', '.amenityRemoveBtn', function() {
                $(this).parent().remove();
            });


            //image
            function displayImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = $(input).parent().find('.image-preview img');
                        $(preview).attr('src', e.target.result);
                        $(preview).addClass('has-image');
                        $(preview).hide();
                        $(preview).fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#listingCoverUpload").on('change', function() {
                displayImage(this);
            });

            $('[name=country_id]').on('change', function() {
                var cities = $(this).find('option:selected').data('cities');
                var option = [`<option value="">@lang('Select one')</option>`];
                $.each(cities, function(index, value) {
                    option += "<option value='" + value.id + "' " + (value.id == "{{ @$listing->city_id }}" ? "selected" : "") +
                        ">" + value.name + "</option>";
                });
                $('select[name=city_id]').html(option);
            }).change();


        })(jQuery);
    </script>
@endpush
