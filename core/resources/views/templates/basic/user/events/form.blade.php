@extends('Template::layouts.user')
@section('content')
    <div class="lx-page-head">
        <div>
            <h4>{{ isset($event) ? __('Edit Event') : __('Create Event') }}</h4>
        </div>
        <div class="lx-page-actions">
            <a href="{{ route('user.events.index') }}" class="btn btn-outline--base btn--sm"><i class="las la-arrow-left"></i> @lang('Back')</a>
        </div>
    </div>
    <div class="card" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ isset($event) ? route('user.events.update', $event->id) : route('user.events.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label">@lang('Event Title') <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $event->title ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Description') <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required>{{ old('description', $event->description ?? '') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Start Time') <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control"
                                       value="{{ old('start_time', isset($event) ? $event->start_time?->format('Y-m-d\\TH:i') : '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('End Time') <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control"
                                       value="{{ old('end_time', isset($event) ? $event->end_time?->format('Y-m-d\\TH:i') : '') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">@lang('Cover Image') @if(!isset($event)) <span class="text-danger">*</span> @endif</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*" @if (!isset($event)) required @endif>
                            @if (isset($event) && $event->cover_image)
                                <img src="{{ getImage(getFilePath('event') . '/' . $event->cover_image, '372x216') }}"
                                     class="mt-2 rounded" style="max-height: 120px;" alt="cover">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Category') <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <option value="">@lang('Select category')</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $event->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Country') <span class="text-danger">*</span></label>
                            <select name="country_id" class="form-control" required id="countrySelect">
                                <option value="">@lang('Select country')</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $event->country_id ?? '') == $country->id)>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('City') <span class="text-danger">*</span></label>
                            <select name="city_id" class="form-control" required id="citySelect">
                                <option value="">@lang('Select city')</option>
                                @foreach ($countries as $country)
                                    @foreach ($country->cities as $city)
                                        <option value="{{ $city->id }}" data-country="{{ $country->id }}" @selected(old('city_id', $event->city_id ?? '') == $city->id)>{{ $city->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Venue') <span class="text-danger">*</span></label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue', $event->venue ?? '') }}" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--base w-100">@lang('Submit for Approval')</button>
            </form>
        </div>
    </div>

    @push('script')
        <script>
            "use strict";
            // only offer the cities of the selected country
            const countrySelect = document.getElementById('countrySelect');
            const citySelect    = document.getElementById('citySelect');
            function filterCities() {
                const countryId = countrySelect.value;
                let first = true;
                citySelect.querySelectorAll('option').forEach(function (option) {
                    if (!option.value) return;
                    const match = option.dataset.country === countryId;
                    option.hidden = !match;
                    if (match && first) { option.selected = citySelect.dataset.touched ? option.selected : true; first = false; }
                });
                if (!citySelect.selectedOptions.length || citySelect.selectedOptions[0].hidden) {
                    citySelect.value = '';
                }
            }
            citySelect.addEventListener('change', function () { citySelect.dataset.touched = '1'; });
            countrySelect.addEventListener('change', filterCities);
            filterCities();
        </script>
    @endpush
@endsection
