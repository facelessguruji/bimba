@php
    $testimonialElement = getContent('testimonial.element', orderById: true);

    // drop seed-junk quotes (lorem filler) so only real member quotes render
    $testimonialElement = collect($testimonialElement)
        ->filter(function ($testimonial) {
            $quote = trim((string) @($testimonial->data_values->quote));

            return $quote !== '' && !preg_match('/lorem[\s,]*ipsum/i', $quote);
        })
        ->values();
@endphp

@if (!blank($testimonialElement))
    <section class="lgx-section">
        <div class="lgx-container">
            <div class="lgx-sec-head lgx-sec-head--stack">
                <h2>@lang('What members say')</h2>
                <p>@lang('Business owners using LeadxGPT to get found and stay booked.')</p>
            </div>

            <div class="lgx-quotes">
                @foreach ($testimonialElement as $testimonial)
                    @php
                        $quoteImage = trim((string) @($testimonial->data_values->image));
                        $quoteAuthor = trim((string) __(@$testimonial->data_values->author));
                    @endphp
                    <figure class="lgx-quote">
                        <blockquote>@lang(@$testimonial->data_values->quote)</blockquote>
                        <figcaption class="lgx-quote__who">
                            <span class="lgx-tile lgx-tile--round lgx-tile--{{ ['b', 'g', 'v'][$loop->iteration % 3] }}">
                                @if ($quoteImage !== '')
                                    <img src="{{ frontendImage('testimonial', $testimonial->data_values->image, '200x200') }}"
                                         alt="{{ $quoteAuthor }}" loading="lazy">
                                @else
                                    <b>{{ $quoteAuthor !== '' ? mb_strtoupper(mb_substr($quoteAuthor, 0, 1)) : '?' }}</b>
                                @endif
                            </span>
                            <span>
                                <b>{{ $quoteAuthor }}</b>
                                <i>{{ __(@$testimonial->data_values->designation) }}</i>
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif
