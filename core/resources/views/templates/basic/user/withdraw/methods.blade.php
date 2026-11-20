@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div>
                    <h4 class="mb-1">@lang("Withdraw Money")</h4>
                    <p class="text-muted mb-0">@lang("Choose a method and enter the amount")</p>
                </div>
                @if (!$withdrawMethod->isEmpty())
        @php $gateMsg = $gateMessage; @endphp
        @if (isset($gateMessage) && $gateMessage)
            <div class="alert alert--warning mb-3">
                @foreach (isset($gateNotify) ? $gateNotify : [] as $gateItem)
                    <p class="mb-0">{{ $gateItem[1] ?? '' }}</p>
                @endforeach
            </div>
        @endif
                    <div class="row gy-4">
                        @foreach ($withdrawMethod as $method)
                            <div class="col-lg-4 col-md-6">
                                <div class="card custom--card h-100">
                                    <div class="card-body">
<h5 class="card-title">{{ $method->name }}</h5>
                                        <ul class="caption-list">
                                            <li>
                                                <span class="caption">@lang('Currency')</span>
                                                <span class="value">{{ $method->currency }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Rate')</span>
                                                <span class="value">1 {{ __(gs('cur_text')) }} = {{ showAmount($method->rate, currencyFormat: false) }} {{ $method->currency }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Limit')</span>
                                                <span class="value">{{ showAmount($method->min_limit) }} - {{ showAmount($method->max_limit) }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Charge')</span>
                                                <span class="value">{{ showAmount($method->fixed_charge) }} + {{ showAmount($method->percent_charge, currencyFormat: false) }}%</span>
                                            </li>
                                        </ul>
                                        <form action="{{ route('user.withdraw.store') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="method_code" value="{{ $method->id }}">
                                            <div class="form-group">
                                                <label class="form--label">@lang('Amount')</label>
                                                <input type="number" name="amount" class="form--control" step="0.01"
                                                    placeholder="{{ showAmount($method->min_limit) }} - {{ showAmount($method->max_limit) }}" required>
                                            </div>
                                            <button type="submit" class="btn btn--base w-100">@lang('Withdraw Now')</button>
                                        </form>
                                        <small class="text-muted d-block mt-2">@lang('KYC must be approved and an active plan is required to withdraw.')</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'No Withdraw Method Found'])
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
