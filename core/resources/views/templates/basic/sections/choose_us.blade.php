@php
    $chooseContent = getContent('choose_us.content', true);
    $chooseElement = getContent('choose_us.element', false, null, true);

    $cuHeading = trim((string) @($chooseContent->data_values->heading));
    if ($cuHeading === '') {
        $cuHeading = 'Why businesses choose LeadxGPT';
    }
    $cuSub = trim((string) @($chooseContent->data_values->subheading));
    $cuSub = ($cuSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $cuSub)) ? $cuSub : null;
@endphp

@if (!blank($chooseElement))
    <section class="lgx-section lgx-section--tint">
        <div class="lgx-container">
            <div class="lgx-sec-head">
                <div>
                    <h2>{{ __($cuHeading) }}</h2>
                    @if ($cuSub)
                        <p>{{ __($cuSub) }}</p>
                    @endif
                </div>
                <a class="lgx-sec-head__link" href="{{ route('pricing') }}">@lang('See listing packages')</a>
            </div>

            <div class="lgx-serv">
                @foreach ($chooseElement as $choice)
                    <div class="lgx-sv">
                        <span class="lgx-tile lgx-tile--sm lgx-tile--{{ ['g', 'b', 'a', 'v', 'i'][$loop->iteration % 5] }}">
                            @php echo @$choice->data_values->icon; @endphp
                        </span>
                        <div>
                            <b>{{ __(@$choice->data_values->title) }}</b>
                            <span>{{ __(@$choice->data_values->description) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
