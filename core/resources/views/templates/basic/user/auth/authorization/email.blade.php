@extends('Template::layouts.user')
@section('content')
    <section class="py-120">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="verification-code-wrapper">
                    <div class="verification-area">
                        <form action="{{ route('user.verify.email') }}" method="POST" class="submit-form">
                            @csrf
                            <p class="verification-text">@lang('A 6 digit verification code sent to your email address'): {{ showEmailAddress(auth()->user()->email) }}</p>

                            @include('Template::partials.verification_code')

                            <div class="mb-3">
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>

                            <div class="mb-3">
                                <p>
                                    @lang('If you don\'t get any code'), <span class="countdown-wrapper">@lang('try again after') <span id="countdown" class="fw-bold">--</span>
                                        @lang('seconds')</span> <a href="{{ route('user.send.verify.code', 'email') }}" class="try-again-link d-none">
                                        @lang('Try again')</a>
                                </p>
                                <form action="{{ route('user.logout') }}" method="POST" class="d-inline">@csrf<button type="submit" class="text-danger" style="all:unset;cursor:pointer;">@lang('Logout')</button></form>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        "use strict";
        var distance =Number("{{ isset($user->ver_code_send_at) ? $user->ver_code_send_at->addMinutes(2)->timestamp - time() : '' }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush
