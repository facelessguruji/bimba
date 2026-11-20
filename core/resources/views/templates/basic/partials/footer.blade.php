@php
    $footerContent = getContent('footer.content', true);
    $iconElement = getContent('social_icon.element', false, null, true);
    $policyPages = getContent('policy_pages.element', false, null, true);
    $contactElement = getContent('contact_us.element', false, 3, true);
@endphp
<footer class="lgx-footer">
    <div class="lgx-container">
        <div class="lgx-footer__grid">
            <div class="lgx-footer__about">
                <a class="lgx-logo" href="{{ route('home') }}"><span class="lgx-mark">L</span> <b>{{ gs('site_name') }}</b></a>
                <p>{{ __(@$footerContent->data_values->content) }}</p>
                <ul class="lgx-social">
                    @foreach ($iconElement as $icon)
                        <li><a href="{{ @$icon->data_values->url }}" target="_blank" rel="noopener">@php echo @$icon->data_values->social_icon; @endphp</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4>@lang('Explore')</h4>
                <a href="{{ route('listings') }}">@lang('Listings')</a>
                <a href="{{ route('cities') }}">@lang('Cities')</a>
                <a href="{{ route('events') }}">@lang('Events')</a>
                <a href="{{ route('pricing') }}">@lang('Pricing')</a>
                <a href="{{ route('blog') }}">@lang('Blogs')</a>
            </div>

            <div>
                <h4>@lang('Members')</h4>
                <a href="{{ route('user.login') }}">@lang('Member Login')</a>
                <a href="{{ route('user.register') }}">@lang('Referral Program')</a>
                <a href="{{ route('faq') }}">@lang('FAQ')</a>
                <a href="{{ route('contact') }}">@lang('Support')</a>
            </div>

            <div>
                <h4>@lang('Legal')</h4>
                @foreach ($policyPages as $link)
                    <a href="{{ route('policy.pages', @$link->slug) }}">{{ __($link->data_values->title) }}</a>
                @endforeach
            </div>
        </div>

        <div class="lgx-footer__contact">
            @foreach ($contactElement as $contact)
                <span>{!! @$contact->data_values->icon !!} {{ @$contact->data_values->content }}</span>
            @endforeach
        </div>

        <div class="lgx-footer__bottom">
            <span>&copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ __(gs()->site_name) }}</a>. @lang('All rights reserved.')</span>
            <span>@lang('Get found. Get leads. Grow.')</span>
        </div>
    </div>
</footer>
