@php
    $navPages = isset($pages) ? $pages->where('is_default', 0) : collect();
@endphp
<header class="lgx-header" id="header">
    <div class="lgx-container lgx-header__in">
        <a class="lgx-logo" href="{{ route('home') }}">
            <span class="lgx-mark">L</span>
            <b>{{ gs('site_name') }}</b>
        </a>

        <button class="lgx-burger" type="button" data-bs-toggle="collapse" data-bs-target="#lgxNav"
            aria-controls="lgxNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="las la-bars"></i>
        </button>

        <nav class="collapse lgx-nav" id="lgxNav">
            <a class="{{ menuActive('home') }}" href="{{ route('home') }}">@lang('Home')</a>
            <a class="{{ menuActive('listings') }}" href="{{ route('listings') }}">@lang('Listings')</a>
            <a class="{{ menuActive('events') }}" href="{{ route('events') }}">@lang('Events')</a>
            <a class="{{ menuActive('pricing') }}" href="{{ route('pricing') }}">@lang('Pricing')</a>
            @foreach ($navPages as $data)
                <a class="{{ menuActive('pages', null, $data->slug) }}" href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
            @endforeach
            <a class="{{ menuActive('blog') }}" href="{{ route('blog') }}">@lang('Blog')</a>
            <a class="{{ menuActive('contact') }}" href="{{ route('contact') }}">@lang('Contact')</a>

            <span class="lgx-nav__cta">
            @auth
                <a class="btn-lgx btn-lgx-ghost btn-lgx-sm {{ menuActive('user.home') }}" href="{{ route('user.home') }}"><i class="las la-user-circle"></i> @lang('Account')</a>
            @else
                <a href="{{ route('user.login') }}" class="btn-lgx btn-lgx-ghost btn-lgx-sm">@lang('Login')</a>
                <a href="{{ route('user.register') }}" class="btn-lgx btn-lgx-primary btn-lgx-sm">@lang('List Your Business')</a>
            @endauth
            </span>
        </nav>
    </div>
</header>
