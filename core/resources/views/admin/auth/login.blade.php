@extends('admin.layouts.master')
@section('content')
<div class="lgx-auth" style="min-height:100vh">
    <aside class="lgx-auth-brand">
        <a class="lgx-logo" href="{{ route('home') }}"><span class="lgx-mark">L</span> {{ gs('site_name') }}</a>
        <div class="lgx-auth-pitch">
            <h2>@lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong></h2>
            <p>@lang('Admin Dashboard')</p>
        </div>
        <div style="position:relative;font:500 12.5px var(--lgx-font);opacity:.7">© {{ date('Y') }} {{ gs('site_name') }}. @lang('All rights reserved.')</div>
    </aside>

    <main class="lgx-auth-formwrap">
        <div class="lgx-auth-card">
            <h1>@lang('Admin Login')</h1>
            <p class="lgx-sub">@lang('Sign in to the control center of your platform.')</p>

            <form action="{{ route('admin.login') }}" method="POST" class="verify-gcaptcha login-form">
                @csrf
                <div class="lgx-field">
                    <label class="lgx-label" for="lgx-admin-user">@lang('Username')</label>
                    <input class="lgx-input" type="text" name="username" id="lgx-admin-user" value="{{ old('username') }}" placeholder="@lang('Your admin username')" required>
                </div>
                <div class="lgx-field">
                    <div style="display:flex;justify-content:space-between;align-items:baseline">
                        <label class="lgx-label" for="lgx-admin-pass">@lang('Password')</label>
                        <a href="{{ route('admin.password.reset') }}" style="color:var(--lgx-brand);font:600 13px var(--lgx-font);text-decoration:none">@lang('Forgot Password?')</a>
                    </div>
                    <div class="lgx-pass">
                        <input class="lgx-input" type="password" name="password" id="lgx-admin-pass" placeholder="••••••••" required>
                        <span class="lgx-toggle fas toggle-password fa-eye-slash" id="#lgx-admin-pass"></span>
                    </div>
                </div>

                <x-captcha />

                <button type="submit" class="btn-lgx btn-lgx-primary btn-lgx-block btn-lgx-lg" style="margin-top:6px">@lang('Login')</button>
            </form>

            <div class="lgx-auth-foot"><a href="{{ route('home') }}">@lang('Back to site')</a></div>
        </div>
    </main>
</div>
@endsection

@push('script')
    <script>
        // the admin layout does not load the front template's main.js, so the
        // password eye needs its own handler (sibling input, icon swap)
        (function($) {
            'use strict';
            $(document).on('click', '.toggle-password', function () {
                var input = $(this).siblings('input');
                $(this).toggleClass('fa-eye fa-eye-slash');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });
        })(jQuery);
    </script>
@endpush
