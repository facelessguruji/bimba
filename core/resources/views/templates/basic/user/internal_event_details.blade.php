@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <a href="{{ route('user.internal.events') }}" class="btn btn--sm btn-outline--base mb-3">
                    <i class="las la-arrow-left"></i> @lang('Back to Company Events')
                </a>
                <div class="row gy-4">
                    <div class="col-lg-8">
                        <div class="card custom--card p-4">
                            <span class="badge badge--info mb-3" style="width:fit-content">@lang('Company Event')</span>
                            <h3>{{ $event->title }}</h3>
                            {{-- escaped like the public event page: descriptions must never render raw --}}
                            <div class="mt-3">{!! nl2br(e($event->description)) !!}</div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card custom--card p-4">
                            <h6 class="mb-3">@lang('Event Details')</h6>
                            <ul class="list-unstyled">
                                <li class="d-flex justify-content-between py-2 border-bottom"><span>@lang('Venue')</span><strong>{{ $event->venue }}</strong></li>
                                <li class="d-flex justify-content-between py-2 border-bottom"><span>@lang('City')</span><strong>{{ $event->city?->name }}</strong></li>
                                <li class="d-flex justify-content-between py-2 border-bottom"><span>@lang('Start')</span><strong>{{ showDateTime($event->start_time) }}</strong></li>
                                <li class="d-flex justify-content-between py-2 border-bottom"><span>@lang('Price')</span><strong>{{ $event->price > 0 ? showAmount($event->price) : __('Free') }}</strong></li>
                            </ul>

                            @if ($participation && $participation->status == 'joined')
                                <div class="alert alert--success mt-3 mb-0">@lang('You are registered for this event.')</div>
                            @else
                                <form action="{{ route('user.internal.event.register', $event->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="status" value="joined">
                                    @if ($event->price > 0)
                                        <div class="form-group">
                                            <label class="form--label">@lang('Payment Method')</label>
                                            <select name="pay_method" class="form--control" required>
                                                <option value="wallet">@lang('Wallet Balance') ({{ showAmount(auth()->user()->balance) }})</option>
                                                <option value="epin">@lang('E-Pin')</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form--label">@lang('E-Pin (if selected)')</label>
                                            <input type="text" name="epin" class="form--control" placeholder="@lang('XXXX-XXXX-XXXX-XXXX')">
                                        </div>
                                    @endif
                                    <button type="submit" class="btn btn--base w-100">@lang('Register Now')</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
