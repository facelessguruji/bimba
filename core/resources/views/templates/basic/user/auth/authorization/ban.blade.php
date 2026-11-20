@extends('Template::layouts.user')
@section('content')
    <section class="py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom--card">
                        <div class="card-body text-center">
                            <h3 class="text-center text--danger">@lang('You are banned')</h3>
                            <p class="fw-bold mb-1">@lang('Reason'):</p>
                            <p>{{ $user->ban_reason }}</p>
                            <a class="btn btn--base mt-3" href="{{ route('home') }}">@lang('Browse') {{ gs('site_name') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

