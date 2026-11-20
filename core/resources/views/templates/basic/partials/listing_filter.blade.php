<form action="" method="GET" autocomplete="off">
    <div class="feature-sidebar">
        <button class="feature-sidebar__close" type="button"><i class="las la-times"></i></button>
        <div class="feature-sidebar__wrapper">
            <div class="d-lg-flex justify-content-between gap-2">

                <div class="position-relative w-100 pb-lg-0 pb-2">
                    <select class="filter-select form-select form--control select2" name="category">
                        <option value=""> @lang('All Categories') </option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected($category->slug == request()->category)>{{ __($category->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="position-relative w-100 pb-lg-0 pb-2">
                    <select class="filter-select form-select form--control select2" name="country">
                        <option value="">@lang('All Countries')</option>
                        @foreach ($countries as $country)
                            <option data-cities='@json($country->cities)' value="{{ $country->id }}" @selected($country->id == request()->country)>{{ __($country->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="position-relative w-100 pb-lg-0 pb-2">
                    <select class="filter-select form-select form--control select2" name="city">
                        <option value="">@lang('Select City')</option>
                    </select>
                </div>

                <div class="w-100 pb-lg-0 pb-2">
                    <input class="form--control form-control" name="search" type="text" value="{{ request()->search }}" placeholder="@lang('Search')">
                </div>

                <div class="listing-filter-button">
                    <button class="filter-btn btn btn--base w-100" type="submit">@lang('Filter') </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script')
    <script>
        (function($) {
            "use strict";

            $('[name=country]').on('change', function() {
                var cities = $(this).find('option:selected').data('cities');
                var option = [`<option value="">@lang('Select City')</option>`];
                $.each(cities, function(index, value) {
                    option += "<option value='" + value.slug + "' " + (value.slug == "{{ request()->city }}" ? "selected" : "") + ">" + value.name + "</option>";
                });
                $('select[name=city]').html(option);
            }).change();

            @if(request()->city && !request()->country)
                const cityName= "{{request()->city}}";
                const counties=@json($countries->pluck('cities')->toArray());
                let city="";

                $.each(counties, function (i, elements) {
                    // filter param is a city slug, so match on slug (not name)
                    city=elements.find(element=> (element.slug || '').toLowerCase() == cityName.toLowerCase());
                    if(city) return false;
                });
                if(city){
                    $('[name=country]').val(city.country_id).select2({
                        dropdownParent: $('[name=country]').parent()
                    }).change();
                }
            @endif

        })(jQuery);
    </script>
@endpush

