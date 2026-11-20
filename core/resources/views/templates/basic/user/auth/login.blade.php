@php
    $content = getContent('login.content', true);
@endphp
@extends('Template::layouts.app')
@section('panel')
    <div class="lgx-auth">
        <aside class="lgx-auth-brand">
            <a class="lgx-logo" href="{{ route('home') }}"><span class="lgx-mark">L</span> LeadxGPT</a>
            <div class="lgx-auth-pitch">
                <h2>Welcome back to your growth workspace.</h2>
                <p>Your listings, your leads, your earnings, all in one place. Sign in and pick up right where you left off.</p>
                <div class="lgx-auth-points">
                    <div><span class="lgx-tick">✓</span> Receive customer enquiries straight to your lead inbox</div>
                    <div><span class="lgx-tick">✓</span> Manage listings, events and business profile with ease</div>
                    <div><span class="lgx-tick">✓</span> Track referral earnings with a clear, auditable ledger</div>
                </div>
            </div>
            <div style="position:relative;font:500 12.5px var(--lgx-font);opacity:.7">© {{ date('Y') }} LeadxGPT. All rights reserved.</div>
        </aside>

        <main class="lgx-auth-formwrap">
            <div class="lgx-auth-card">
                <h1>{{ __(@$content?->data_values?->login_heading ?? '') }}</h1>
                <p class="lgx-sub">{{ __(@$content?->data_values?->login_subheading ?? '') }}</p>

                <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                    @csrf

                    <div class="lgx-field">
                        <label class="lgx-label" for="lgx-user">@lang('Username or Email')</label>
                        <input class="lgx-input" type="text" name="username" id="lgx-user" value="{{ is_string(old('username')) ? old('username') : '' }}" placeholder="you@example.com" required>
                    </div>

                    <div class="lgx-field">
                        <label class="lgx-label" for="lgx-pass">@lang('Password')</label>
                        <div class="lgx-pass">
                            <input class="lgx-input" type="password" name="password" id="lgx-pass" placeholder="••••••••" required>
                            <span class="lgx-toggle fas toggle-password fa-eye-slash" id="#lgx-pass"></span>
                        </div>
                    </div>

                    <x-captcha />

                    <div class="lgx-field" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                        <label class="lgx-check"><input type="checkbox" name="remember" @checked(old('remember'))> @lang('Remember me')</label>
                        <a href="{{ route('user.password.request') }}" style="color:var(--lgx-brand);font:600 13px var(--lgx-font);text-decoration:none">@lang('Forgot Password?')</a>
                    </div>

                    <button type="submit" id="recaptcha" class="btn-lgx btn-lgx-primary btn-lgx-block btn-lgx-lg">@lang('Login')</button>
                </form>

                <div class="lgx-auth-foot">@lang('New to LeadxGPT?') <a href="{{ route('user.register') }}">@lang('Create a free account')</a></div>
            </div>
        </main>
    </div>
@endsection
