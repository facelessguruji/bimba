@php
    $cityContent = getContent('popular_city.content', true);
    $cities = App\Models\City::active()->countApprovedListing()->featured()->orderBy('listings_count', 'DESC')->get();

    $cityHeading = trim((string) @($cityContent->data_values->heading));
    if ($cityHeading === '') {
        $cityHeading = 'Popular cities';
    }
    $citySub = trim((string) @($cityContent->data_values->subheading));
    $citySub = ($citySub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $citySub)) ? $citySub : null;
@endphp

@if (!blank($cities))
    <section class="lgx-section">
        <div class="lgx-container">
            <div class="lgx-sec-head">
                <div>
                    <h2>{{ __($cityHeading) }}</h2>
                    @if ($citySub)
                        <p>{{ __($citySub) }}</p>
                    @endif
                </div>
                <a class="lgx-sec-head__link" href="{{ route('cities') }}">@lang('All cities')</a>
            </div>

            <div class="lgx-cities">
                @foreach ($cities as $city)
                    @php
                        $cityImage = trim((string) $city->image);
                        $cityTone  = ['v', 'a', 'g', 'b', 'i'][$loop->iteration % 5];
                    @endphp
                    <a class="lgx-city" href="{{ route('listings') }}?city={{ $city->slug }}">
                        <span class="lgx-tile lgx-tile--{{ $cityTone }}">
                            @if ($cityImage !== '')
                                <img src="{{ getImage(getFilePath('city') . '/' . $city->image, getFileSize('city')) }}"
                                     alt="{{ __($city->name) }}" loading="lazy">
                            @else
                                <i class="las la-city"></i>
                            @endif
                        </span>
                        <span class="lgx-city__meta">
                            <b class="lgx-city__name">{{ __($city->name) }}</b>
                            <i class="lgx-city__count">{{ getAmount($city->listings_count) }} {{ $city->listings_count == 1 ? __('listing') : __('listings') }}</i>
                        </span>
                        <i class="las la-arrow-right lgx-city__go"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
