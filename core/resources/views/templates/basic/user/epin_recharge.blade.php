@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h3 class="mb-4">@lang('Recharge Your Wallet')</h3>

                <div class="card custom--card mb-4">
                    <div class="card-body">
                        <form action="{{ route('user.erecharge') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('E-Pin Code')</label>
                                <input type="text" name="pin" class="form--control" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                            </div>
                            <button type="submit" class="btn btn--base w-100">@lang('Recharge Now')</button>
                        </form>
                    </div>
                </div>

                <div class="card custom--card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Generate E-Pin from Balance')</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $isAgent = (bool) auth()->user()->is_epin_agent;
                            $agentTiers = $isAgent ? App\Http\Controllers\Admin\AgentController::tiers() : [];
                        @endphp
                        <form action="{{ route('user.pin.generate') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Amount Per Pin')</label>
                                <input type="number" name="amount" class="form--control" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Quantity')</label>
                                <input type="number" name="number" class="form--control" value="1" min="1" max="1000" required>
                                @if ($isAgent)
                                    <small class="text-muted d-block mt-1">@lang('You are an e-pin agent: volume pricing applies and no generation charge is added.')</small>
                                    @if (count($agentTiers))
                                        <small class="text-muted d-block">
                                            @lang('Volume discounts'):
                                            @foreach ($agentTiers as $tier)
                                                {{ $tier['qty'] }}+ @lang('pins'): {{ rtrim(rtrim(number_format($tier['percent'], 2), '0'), '.') }}%@if (!$loop->last), @endif
                                            @endforeach
                                        </small>
                                    @endif
                                    @if (gs('epin_seller_bonus_percent') > 0)
                                        <small class="text-muted d-block mt-1">@lang('Seller bonus'): @lang('when a member activates a plan with one of your pins, you earn') {{ showAmount(gs('epin_seller_bonus_percent'), currencyFormat: false) }}% @lang('of the purchase amount.')</small>
                                    @endif
                                @elseif (gs('epin_charge') > 0)
                                    <small class="text-muted">@lang('Generation charge'): {{ showAmount(gs('epin_charge'), currencyFormat: false) }}%</small>
                                @endif
                            </div>
                            <button type="submit" class="btn btn--base w-100">@lang('Generate E-Pin')</button>
                        </form>
                    </div>
                </div>

                <h5 class="mb-3">@lang('Your Generated E-Pins')</h5>
                @if (!$pins->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('E-Pin')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Created')</th>
                                            <th>@lang('Used By')</th>
                                            <th>@lang('Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pins as $pin)
                                            <tr>
                                                <td>
                                                    <small class="font-monospace">{{ $pin->pin }}</small>
                                                    <button type="button" class="btn btn-outline--base btn--xs ms-2 copyPin" data-pin="{{ $pin->pin }}" title="@lang('Copy')">
                                                        <i class="las la-copy"></i>
                                                    </button>
                                                </td>
                                                <td>{{ showAmount($pin->amount) }}</td>
                                                <td>{{ showDateTime($pin->created_at) }}</td>
                                                <td>
                                                    @if ($pin->status == App\Constants\Status::PIN_USED && $pin->user)
                                                        {{ '@' . $pin->user->username }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($pin->status == App\Constants\Status::PIN_USED)
                                                        <span class="badge badge--success">@lang('Used')</span>
                                                    @else
                                                        <span class="badge badge--warning">@lang('Unused')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'No E-Pin generated yet'])
                @endif
                @if ($pins->hasPages())
                    <div class="mt-4">{{ paginateLinks($pins) }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $(document).on('click', '.copyPin', function() {
                var pin = $(this).data('pin');
                window.lgxCopyText(String(pin), this, 'Pin code copied.');
            });
        })(jQuery);
    </script>
@endpush
