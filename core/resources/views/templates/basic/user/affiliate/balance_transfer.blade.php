@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h3 class="mb-4">@lang('Transfer Balance')</h3>
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Username')</label>
                                <input type="text" name="username" class="form--control" value="{{ old('username') }}"
                                    placeholder="@lang('Receiver username')" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Amount')</label>
                                <input type="number" name="amount" class="form--control" value="{{ old('amount') }}"
                                    step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label class="form--label">@lang('2FA Code')</label>
                                    <input type="text" name="authenticator_code" class="form--control"
                                        placeholder="@lang('Google Authenticator code')" required>
                                </div>
                            @endif
                            <button type="submit" class="btn btn--base w-100">@lang('Transfer')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
