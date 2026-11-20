@php
    $categoryContent = getContent('category.content', true);
    $categories      = App\Models\Category::active()->featured()->countApprovedListing()->get();

    // admin copy wins, but seed junk (empty or lorem) renders nothing
    $catHeading = trim((string) @($categoryContent->data_values->heading));
    if ($catHeading === '') {
        $catHeading = 'Browse by category';
    }
    $catSub = trim((string) @($categoryContent->data_values->subheading));
    $catSub = ($catSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $catSub)) ? $catSub : null;
@endphp

@if (!blank($categories))
    <section class="lgx-section">
        <div class="lgx-container">
            <div class="lgx-sec-head">
                <div>
                    <h2>{{ __($catHeading) }}</h2>
                    @if ($catSub)
                        <p>{{ __($catSub) }}</p>
                    @endif
                </div>
                <a class="lgx-sec-head__link" href="{{ route('listings') }}">@lang('All listings')</a>
            </div>

            <div class="lgx-cats">
                @foreach ($categories as $category)
                    @php
                        $catImage = trim((string) $category->image);
                        $catTone  = ['b', 'g', 'a', 'v', 'i'][$loop->iteration % 5];
                    @endphp
                    <a class="lgx-cat" href="{{ route('listings') }}?category={{ $category->slug }}">
                        <span class="lgx-tile lgx-tile--{{ $catTone }}">
                            @if ($catImage !== '')
                                <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                     alt="{{ __($category->name) }}" loading="lazy">
                            @else
                                <b>{{ mb_strtoupper(mb_substr(__($category->name), 0, 1)) }}</b>
                            @endif
                        </span>
                        <b class="lgx-cat__name">{{ __($category->name) }}</b>
                        <i class="lgx-cat__count">{{ getAmount($category->listings_count) }} {{ $category->listings_count == 1 ? __('listing') : __('listings') }}</i>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
