@php
    $aboutContent = getContent('about.content', true);
    $aboutElement = getContent('about.element', false, null, true);

    $abHeading = trim((string) @($aboutContent->data_values->heading));
    $abBody    = trim((string) @($aboutContent->data_values->content));
    $abImage   = trim((string) @($aboutContent->data_values->image));
@endphp

@if ($abHeading !== '' || $abBody !== '')
    <section class="lgx-section">
        <div class="lgx-container">
            <div class="lgx-about">
                <div class="lgx-about__media">
                    @if ($abImage !== '')
                        <img class="lgx-about__th"
                             src="{{ frontendImage('about', $aboutContent->data_values->image, '540x515') }}"
                             alt="{{ $abHeading }}" loading="lazy">
                    @else
                        <div class="lgx-about__tile">
                            <i class="las la-briefcase"></i>
                        </div>
                    @endif
                </div>
                <div class="lgx-about__body">
                    <h2>{{ __($abHeading) }}</h2>
                    @if ($abBody !== '')
                        <p>{{ __($abBody) }}</p>
                    @endif
                    @foreach ($aboutElement as $about)
                        <div class="lgx-about__item">
                            <span class="lgx-tile lgx-tile--sm lgx-tile--{{ ['b', 'g', 'v'][$loop->iteration % 3] }}">
                                @php echo @$about->data_values->icon; @endphp
                            </span>
                            <div>
                                <b>{{ __(@$about->data_values->title) }}</b>
                                <span>{{ __(@$about->data_values->description) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
