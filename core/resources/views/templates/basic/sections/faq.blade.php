@php
    $faqContent = getContent('faq.content', true);
    $faqs = getContent('faq.element', orderById: true);

    $faqHeading = trim((string) @($faqContent->data_values->heading));
    if ($faqHeading === '') {
        $faqHeading = 'Frequently asked questions';
    }
    $faqSub = trim((string) @($faqContent->data_values->subheading));
    $faqSub = ($faqSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $faqSub)) ? $faqSub : null;
@endphp

@if (!blank($faqs))
    <div class="lgx-section lgx-section--tint">
        <div class="lgx-container">
            <div class="lgx-sec-head lgx-sec-head--stack">
                <h2>{{ __($faqHeading) }}</h2>
                @if ($faqSub)
                    <p>{{ __($faqSub) }}</p>
                @endif
            </div>

            <div class="lgx-faq">
                @foreach ($faqs as $faq)
                    <details @if ($loop->first) open @endif>
                        <summary>{{ __(@$faq->data_values->question) }}</summary>
                        <p class="lgx-faq__a">{{ __(@$faq->data_values->answer) }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
@endif
