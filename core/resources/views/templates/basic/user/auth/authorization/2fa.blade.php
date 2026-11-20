@extends('Template::layouts.user')
@section('content')
    <section class="py-120">
        <div class="container">
            <div class="d-flex justify-content-center">
                <div class="verification-code-wrapper">
                    <div class="verification-area">
                        <h4 class="mb-4">@lang('Two Factor Authentication')</h4>
                        <p class="mb-4">@lang('Enter the 6-digit code from your authenticator app to continue.')</p>
                        <form action="{{ route('user.2fa.verify') }}" method="POST" class="submit-form">
                            @csrf

                            @include('Template::partials.verification_code')

                            <div class="form-group">
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
