@php
    $bannerContent = getContent('banner.content', true);
@endphp

<section class="lgx-hero">
    <div class="lgx-container lgx-hero__in">
        <h1>{{ __(@$bannerContent->data_values->heading) }}</h1>
        <p>{{ __(@$bannerContent->data_values->subheading) }}</p>

        <form class="lgx-hero__search" action="{{ route('listings') }}" method="GET" autocomplete="off">
            <input type="text" name="search" placeholder="@lang('What do you need? e.g. dentist, photographer')">
            <button type="submit" class="btn-lgx btn-lgx-primary"><i class="las la-search"></i> @lang('Search')</button>
        </form>

        {{-- service trust stats: truthful, live counts, no earnings claims (D19) --}}
        @php
            $trustStats = [
                'businesses' => App\Models\Listing::approved()->count(),
                'cities'     => App\Models\City::active()->count(),
                'categories' => App\Models\Category::active()->count(),
            ];
        @endphp
        <div class="lgx-hero__stats">
            <div><b>{{ getAmount($trustStats['businesses']) }}</b><i>@lang('Businesses listed')</i></div>
            <div><b>{{ getAmount($trustStats['cities']) }}</b><i>@lang('Cities covered')</i></div>
            <div><b>{{ getAmount($trustStats['categories']) }}</b><i>@lang('Categories to explore')</i></div>
        </div>
    </div>
</section>
