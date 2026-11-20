@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h3 class="mb-4">@lang('2FA Security')</h3>
                <div class="card custom--card mb-4">
                    <div class="card-body">
                        <p>@lang('Protect your account with Google Authenticator. Scan the QR code, then confirm with the 6-digit code.')</p>
                        <div class="text-center mb-3">
                            <img src="{{ $qrCodeUrl }}" alt="@lang('QR Code')" class="img-fluid" style="max-width: 220px;">
                        </div>
                        <form action="{{ route('user.twofactor.enable') }}" method="post">
                            @csrf
                            <input type="hidden" name="key" value="{{ $secret }}">
                            <div class="form-group">
                                <label class="form--label">@lang('Authenticator Code')</label>
                                <input type="text" name="code" class="form--control" placeholder="@lang('6-digit code')" required>
                            </div>
                            <button type="submit" class="btn btn--base w-100"
                                @if (auth()->user()->ts) disabled @endif>@lang('Enable 2FA')</button>
                        </form>
                    </div>
                </div>

                @if (auth()->user()->ts)
                    <div class="card custom--card">
                        <div class="card-body">
                            <form action="{{ route('user.twofactor.disable') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="form--label">@lang('Authenticator Code')</label>
                                    <input type="text" name="code" class="form--control" placeholder="@lang('6-digit code')" required>
                                </div>
                                <button type="submit" class="btn btn--danger w-100">@lang('Disable 2FA')</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
