@extends('Template::layouts.user')
@section('content')
<section class="py-120 section-bg">
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                @php
                    $planPrice = $plan->yearly_price; // plans are yearly-only
                @endphp

                <div class="card custom--card mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <h5 class="card-title mb-0"><i class="las la-layer-group me-2 text--base"></i>@lang('Plan'): {{ planDisplayName($plan) }}</h5>
                        <span class="text--base fw-bold">{{ showAmount($planPrice) }}@lang('/Year')</span>
                    </div>
                </div>

                @if (!$isRenewal && $planPrice <= 0)
                    {{-- FREE plan: no payment involved, one activation button --}}
                    <div class="card custom--card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Free Plan')</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.plan.purchase') }}" method="post">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="price_type" value="yearly">
                                <input type="hidden" name="method" value="wallet">
                                <p class="text-muted">@lang('This plan costs nothing. Activate it to start publishing listings.')</p>
                                <button type="submit" class="btn btn--base w-100"><i class="las la-check-circle"></i> @lang('Activate Free Plan')</button>
                            </form>
                        </div>
                    </div>
                @elseif ((!$isRenewal && auth()->user()->balance >= $planPrice) || gs('epin'))
                    <div class="card custom--card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Instant Purchase')</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.plan.purchase') }}" method="post">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="price_type" value="yearly">
                                @if (!$isRenewal && auth()->user()->balance >= $planPrice)
                                    <input type="hidden" name="method" value="wallet" id="purchaseMethod">
                                @else
                                    <input type="hidden" name="method" value="epin" id="purchaseMethod">
                                @endif
                                <div class="row g-3">
                                    @if (!$isRenewal && auth()->user()->balance >= $planPrice)
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn--base w-100"
                                                onclick="var m = document.getElementById('purchaseMethod'); if (m) m.value = 'wallet';">
                                                <i class="las la-wallet"></i> @lang('Pay with Wallet Balance')
                                                ({{ showAmount(auth()->user()->balance) }})
                                            </button>
                                        </div>
                                    @endif
                                    @if (gs('epin'))
                                        <div class="col-md-{{ (!$isRenewal && auth()->user()->balance >= $planPrice) ? '6' : '12' }}">
                                            <div class="input-group">
                                                <input type="text" name="pin" class="form-control form--control"
                                                    placeholder="@lang('E-Pin Code')" @unless (gs('epin')) disabled @endunless>
                                                <button type="submit" class="input-group-text btn--base text-white"
                                                    onclick="var m = document.getElementById('purchaseMethod'); if (m) m.value = 'epin';">
                                                    @lang('Pay with E-Pin')
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
                    @csrf
                    <input type="hidden" name="currency">
                    <div class="gateway-card">
                        <div class="row justify-content-center gy-sm-4 gy-3">
                            <div class="col-12">
                                <h5 class="text-center">@lang('Pay via Payment Gateway')</h5><p class="text-center text-muted mb-0">@lang('You need to pay') {{ showAmount($planPrice) }}</p>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-system-list is-scrollable gateway-option-list">
                                    @foreach ($gatewayCurrency as $data)
                                        <label for="{{ titleToKey($data->name) }}"
                                            class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                <span class="payment-item__name">{{ __($data->name) }}</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                                <img class="payment-item__thumb-img"
                                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                    alt="@lang('payment-thumb')">
                                            </div>
                                            <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                                data-gateway='@json($data)' type="radio" name="gateway" value="{{ $data->method_code }}"
                                                @if (old('gateway')) @checked(old('gateway') == $data->method_code) @else @checked($loop->first) @endif
                                                data-min-amount="{{ showAmount($data->min_amount) }}"
                                                data-max-amount="{{ showAmount($data->max_amount) }}">
                                        </label>
                                    @endforeach
                                    @if ($gatewayCurrency->count() > 4)
                                        <button type="button" class="payment-item__btn more-gateway-option">
                                            <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                            <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-system-list p-3">
                                    <input type="hidden" name="amount" value="{{ $planPrice }}">
                                    <input type="hidden" name="currency">
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="type" value="yearly">

                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon"> @lang('Limit')
                                                <span></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="gateway-limit">@lang('0.00')</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon">@lang('Processing Charge')
                                                <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')" class="proccessing-fee-info"><i
                                                        class="las la-info-circle"></i> </span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Total')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}</p>
                                        </div>
                                    </div>

                                    <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Conversion')
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"></p>
                                        </div>
                                    </div>
                                    <div class="deposit-info conversion-currency d-none total-amount pt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">
                                                @lang('In') <span class="gateway-currency"></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text">
                                                <span class="in-currency"></span>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="d-none crypto-message mb-3">
                                        @lang('Conversion with') <span class="gateway-currency"></span>. @lang('The final amount will be shown on the next step.')
                                    </div>
                                    <button type="submit" class="btn btn--base w-100" disabled>
                                        @lang('Submit')
                                    </button>
                                    <div class="info-text pt-3">
                                        <p class="text">@lang('Complete your plan purchase securely with your preferred payment option. Your plan activates the moment payment is confirmed.')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    "use strict";
    (function($) {


        var amount = parseFloat($('input[name=amount]').val() || 0);
        var gateway, minAmount, maxAmount;

        $('.gateway-input').on('change', function(e) {
            gatewayChange();
        });

        function gatewayChange() {
            let gatewayElement = $('.gateway-input:checked');
            let methodCode = gatewayElement.val();
            gateway = gatewayElement.data('gateway');
            minAmount = gatewayElement.data('min-amount');
            maxAmount = gatewayElement.data('max-amount');

            let processingFeeInfo =
                `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
            $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
            calculation();
        }

        gatewayChange();

        $(".more-gateway-option").on("click", function(e) {
            let paymentList = $(".gateway-option-list");
            paymentList.find(".gateway-option").removeClass("d-none");
            $(this).addClass('d-none');
            paymentList.animate({
                scrollTop: (paymentList.height() - 60)
            }, 'slow');
        });

        function calculation() {
            if (!gateway) return;
            $(".gateway-limit").text(minAmount + " - " + maxAmount);

            let percentCharge = 0;
            let fixedCharge = 0;
            let totalPercentCharge = 0;

            if (amount) {
                percentCharge = parseFloat(gateway.percent_charge);
                fixedCharge = parseFloat(gateway.fixed_charge);
                totalPercentCharge = parseFloat(amount / 100 * percentCharge);
            }

            let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
            let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

            $(".final-amount").text(totalAmount.toFixed(2));
            $(".processing-fee").text(totalCharge.toFixed(2));
            $("input[name=currency]").val(gateway.currency);
            $(".gateway-currency").text(gateway.currency);

            if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                $(".deposit-form button[type=submit]").attr('disabled', true);
            } else {
                $(".deposit-form button[type=submit]").removeAttr('disabled');
            }

            if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {

                $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                $(".gateway-conversion").find('.deposit-info__input .text').html(
                    `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                );
                $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ? 8 : 2));
            } else {
                $(".gateway-conversion, .conversion-currency").addClass('d-none');

            }

            if (gateway.method.crypto == 1) {
                $('.crypto-message').removeClass('d-none');
            } else {
                $('.crypto-message').addClass('d-none');
            }
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        $('.gateway-input').change();

    })(jQuery);
</script>
@endpush

