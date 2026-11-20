@php
    $content = getContent('registration.content', true);
    $policyPages = getContent('policy_pages.element', false, null, true);
@endphp
@extends('Template::layouts.app')
@section('panel')
    @if (gs('registration'))
        <div class="lgx-auth">
            <aside class="lgx-auth-brand">
                <a class="lgx-logo" href="{{ route('home') }}"><span class="lgx-mark">L</span> LeadxGPT</a>
                <div class="lgx-auth-pitch">
                    <h2>List your business. Get found. Receive real leads.</h2>
                    <p>Join free, publish your business page in minutes, and let customers find you on LeadxGPT and on search engines.</p>
                    <div class="lgx-auth-points">
                        <div><span class="lgx-tick">✓</span> Free account, full business page with photos, map and hours</div>
                        <div><span class="lgx-tick">✓</span> Customer enquiries delivered straight to your lead inbox</div>
                        <div><span class="lgx-tick">✓</span> Verified badge and reviews that build customer trust</div>
                    </div>
                </div>
                <div style="position:relative;font:500 12.5px var(--lgx-font);opacity:.7">© {{ date('Y') }} LeadxGPT. All rights reserved.</div>
            </aside>

            <main class="lgx-auth-formwrap">
                <div class="lgx-auth-card">
                    <h1>{{ __(@$content?->data_values?->register_heading ?? '') }}</h1>
                    <p class="lgx-sub">{{ __(@$content?->data_values?->register_subheading ?? '') }}</p>

                    @if (isset($referrer) && $referrer)
                        <div class="lgx-sponsor">
                            <span class="lgx-sp-av">{{ strtoupper(substr($referrer->fullname, 0, 1)) }}</span>
                            <span><b>{{ $referrer->fullname }}</b><span>@lang('invited you to join LeadxGPT')</span></span>
                            <span class="lgx-sp-tag">@lang('Your referrer')</span>
                        </div>
                    @elseif (isset($autoSponsor) && $autoSponsor)
                        <div class="lgx-sponsor">
                            <span class="lgx-sp-av">{{ strtoupper(substr($autoSponsor->fullname, 0, 1)) }}</span>
                            <span><b>{{ $autoSponsor->fullname }} ({{ $autoSponsor->username }})</b><span>@lang('An active member is assigned to welcome you on LeadxGPT.')</span></span>
                            <span class="lgx-sp-tag">@lang('AI Auto Assigned')</span>
                        </div>
                    @endif

                    <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha disableSubmission">
                        @csrf
                        <input type="hidden" name="ref" value="{{ request()->query('ref', is_string(old('ref')) ? old('ref') : '') }}">

                        <div class="lgx-field" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label class="lgx-label" for="lgx-fn">@lang('First Name')</label>
                                <input class="lgx-input checkUser" type="text" name="firstname" id="lgx-fn" value="{{ is_string(old('firstname')) ? old('firstname') : '' }}" required>
                            </div>
                            <div>
                                <label class="lgx-label" for="lgx-ln">@lang('Last Name')</label>
                                <input class="lgx-input checkUser" type="text" name="lastname" id="lgx-ln" value="{{ is_string(old('lastname')) ? old('lastname') : '' }}" required>
                            </div>
                        </div>

                        <div class="lgx-field">
                            <label class="lgx-label" for="lgx-email">@lang('Email Address')</label>
                            <input class="lgx-input checkUser" type="text" name="email" id="lgx-email" value="{{ is_string(old('email')) ? old('email') : '' }}" placeholder="you@example.com" required>
                        </div>

                        <div class="lgx-field" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label class="lgx-label" for="lgx-pass">@lang('Password')</label>
                                <div class="lgx-pass">
                                    <input class="lgx-input @if (gs('secure_password')) secure-password @endif" type="password" name="password" id="lgx-pass" placeholder="••••••••">
                                    <span class="lgx-toggle fas toggle-password fa-eye-slash" id="#lgx-pass"></span>
                                </div>
                            </div>
                            <div>
                                <label class="lgx-label" for="lgx-pass2">@lang('Confirm Password')</label>
                                <div class="lgx-pass">
                                    <input class="lgx-input" type="password" name="password_confirmation" id="lgx-pass2" placeholder="••••••••">
                                    <span class="lgx-toggle fas toggle-password fa-eye-slash" id="#lgx-pass2"></span>
                                </div>
                            </div>
                        </div>

                        <x-captcha />

                        @if (gs('agree'))
                            <div class="lgx-field lgx-check" style="align-items:flex-start">
                                <input type="checkbox" id="lgx-agree" name="agree" @checked(old('agree')) required style="margin-top:3px">
                                <span>@lang('I agree with')
                                    @foreach ($policyPages as $policy)
                                        <a target="_blank" href="{{ route('policy.pages', @$policy->slug) }}">{{ __(@$policy->data_values->title ?? '') }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        <button type="submit" id="recaptcha" class="btn-lgx btn-lgx-primary btn-lgx-block btn-lgx-lg">@lang('Create Free Account')</button>
                    </form>

                    <div class="lgx-auth-foot">@lang('Already have an account?') <a href="{{ route('user.login') }}">@lang('Login')</a></div>
                    <div class="lgx-auth-legal">@lang('By continuing you accept our platform terms and the referral program rules shown on the policy pages.')</div>
                </div>
            </main>
        </div>
    @else
        <div class="register-disable">
            <div>
                <img class="register-disable-image" src="{{ getImage('assets/images/logo_icon/logo.png') }}" alt="logo">
                <h2 class="register-disable-title">@lang('Registration Closed')</h2>
                <p class="register-disable-desc">@lang('New registrations are temporarily paused. Please check back soon or contact support if you already have an account.')</p>
                <div style="text-align:center"><a href="{{ route('home') }}" class="btn-lgx btn-lgx-primary">@lang('Go to Home')</a></div>
            </div>
        </div>
    @endif
@endsection

@push('modal')
    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endpush

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
