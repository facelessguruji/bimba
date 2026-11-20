@php
    $pricingContent = getContent('pricing.content', true);
@endphp
@extends('Template::layouts.master')
@section('content')
    <section class="pricing-section py-120 section-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-heading">
                        <h2 class="section-heading__title"> {{ __(@$pricingContent?->data_values?->heading ?? '') }}</h2>
                        <p class="section-heading__desc">{{ __(@$pricingContent?->data_values?->subheading ?? '') }}</p>
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                @foreach ($plans as $plan)
                    <div class="col-lg-4 col-sm-6">
                        <div class="pricing-card ">
                            @if (auth()->user() && auth()->user()->activePlan && auth()->user()->activePlan->plan_id == $plan->id)
                                <div class="text-end"><span class="current-plan-badge">@lang('Current Plan')</span></div>
                            @endif

                            @if (trim($plan->title))
                            <h3 class="pricing-card__title">{{ __($plan->title) }}</h3>
                            @endif

                            <h2 class="pricing-card__number">
                                {{ showAmount($plan->yearly_price) }}<span class="time">/@lang('Year')</span>
                            </h2>

                            <ul class="pricing-card__list">
                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            @if ($plan->listings)
                                                <span class="pricing-card__item-icon text--success"><i class="la la-check"></i></span>
                                            @else
                                                <span class="pricing-card__item-icon text--success"><i class="la la-infinity"></i></span>
                                            @endif
                                            @lang('Listings can be added')
                                        </span>

                                        <span>{{ $plan->listings ? $plan->listings : __('Unlimited') }}</span>
                                    </div>
                                </li>

                                @php
                                    // events you can host per year: plan entitlement column
                                    $planEventsQuota = (int) ($plan->events ?? 0);
                                @endphp
                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <span class="pricing-card__item-icon @if ($planEventsQuota) text--success @else text--danger @endif">
                                                <i class="la @if ($planEventsQuota) la-check @else la-times @endif"></i></span>
                                            @lang('Events you can host per year')
                                        </span>
                                        <span>{{ $planEventsQuota }}</span>
                                    </div>
                                </li>

                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <span class="pricing-card__item-icon @if ($plan->amenities) text--success @else text--danger @endif">
                                                <i class="la @if ($plan->amenities) la-check @else la-times @endif"></i></span>
                                            @lang('Amenities per listing')
                                        </span>
                                        <span>{{ $plan->amenities }} </span>
                                    </div>
                                </li>

                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <span class="pricing-card__item-icon">
                                                <i class="la @if ($plan->images) text--success la-check @else text--danger la-times @endif"></i>
                                            </span>
                                            @lang('Images per listing')
                                        </span>

                                        <span>{{ $plan->images }} </span>
                                    </div>
                                </li>

                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <span class="pricing-card__item-icon">
                                                <i class="la @if ($plan->videos) text--success la-check @else text--danger la-times @endif"></i>
                                            </span>

                                            @lang('Videos per listing')
                                        </span>

                                        <span>{{ $plan->videos }} </span>
                                    </div>
                                </li>

                                <li class="pricing-card__item">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <span class="pricing-card__item-icon">
                                                <i class="la @if ($plan->map) text--success la-check @else text--danger la-times @endif"></i>
                                            </span>
                                            @lang('Allow google map')
                                        </span>

                                        <span>
                                            @if ($plan->map)
                                                @lang('Yes')
                                            @else
                                                @lang('No')
                                            @endif
                                        </span>
                                    </div>
                                </li>
                            </ul>

                            @guest
                                <button type="button" class="btn pricing-card__button w-100 loginRequiredBtn mt-3">@lang('Buy Now')
                                </button>
                            @else
                                <form action="{{ route('user.plan.choose', $plan->id) }}" class="mt-3" method="get">
                                    <button type="submit" class="btn pricing-card__button w-100">
                                        {{ auth()->user() && auth()->user()->activePlan && auth()->user()->activePlan->plan_id == $plan->id ? __('Renew now for one more year') : __('Buy Now') }}
                                    </button>
                                </form>
                            @endguest
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="plans-referral-note">@lang('Members can also join our referral program.') <a
                    href="{{ route('user.register') }}">@lang('Join free')</a></p>
        </div>
    </section>
@endsection

@push('modal')
    <div class="modal fade" id="loginRequired" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="text-center mt-3 text--danger">@lang('This action requires login')</h4>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn--sm btn--dark" data-bs-dismiss="modal" aria-label="Close">@lang('Cancel')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--sm btn--base">@lang('Login Now')</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var modal = $('#loginRequired');
            $(document).on('click', '.loginRequiredBtn', (e) => {
                let data = e.currentTarget.dataset;
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
