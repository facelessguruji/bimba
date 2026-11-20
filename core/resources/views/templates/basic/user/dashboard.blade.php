@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">

        {{-- ============ GREETING (demo user.html topbar greeting) ============ --}}
        <div class="lx-greet mb-4">
            <div class="lx-greet__who">
                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, null, true) }}" class="lx-greet__avatar" alt="avatar">
                <div>
                    <h3 class="lx-greet__hello">@lang('Welcome back,') {{ $user->firstname }}</h3>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @if ($plan)
                            <span class="lx-chip"><i class="las la-crown"></i> {{ planDisplayName($plan->plan) ?? __('Active Plan') }}</span>
                            <span class="lx-chip"><i class="las la-calendar-check"></i> {{ __('Valid till') }} {{ showDateTime($plan->expired_on, 'M d, Y') }}</span>
                        @else
                            <span class="lx-chip"><i class="las la-info-circle"></i> @lang('No active plan')</span>
                        @endif
                        <span class="lx-chip"><i class="las la-medal"></i> {{ $user->userRanking->name ?? __('No Rank Yet') }}</span>
                        @if ($user->kv == App\Constants\Status::KYC_VERIFIED)
                            <span class="lx-chip"><i class="las la-shield-alt"></i> @lang('KYC Verified')</span>
                        @elseif ($user->kv == App\Constants\Status::KYC_PENDING)
                            <span class="lx-chip"><i class="las la-hourglass-half"></i> @lang('KYC Under Review')</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lx-greet__acts">
                <a href="{{ route('user.listing.create') }}" class="btn btn--base btn--sm"><i class="las la-plus"></i> @lang('Post New Listing')</a>
                <a href="{{ route('pricing') }}" class="btn btn--light btn--sm"><i class="las la-layer-group"></i> @lang('Manage Plan')</a>
            </div>
        </div>

        {{-- ============ SPONSOR STRIP ============ --}}
        @if ($sponsor)
            <div class="lx-sponsor p-3 mb-4 d-flex align-items-center gap-3">
                <span class="ico lx-brand"><i class="las la-user-shield"></i></span>
                <div>
                    <strong>@lang('Your Sponsor')</strong> - {{ $sponsor->fullname }} ({{ $sponsor->username }})
                    <div>
                        <small class="text-muted">
                            @if ($user->ref_source == App\Constants\Status::REF_SOURCE_AUTO)
                                @lang('Connected to you through AI Auto Assigned.')
                            @else
                                @lang('You joined through this member\'s referral link - they stay your sponsor forever.')
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ REFERRAL BOX (demo user.html gradient refbox) ============ --}}
        <div class="lx-refbox mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col-lg-5">
                    <span class="lx-refbox__t">@lang('Every purchase from your link pays you first. Share your referral link and earn on every plan your invitees buy.')</span>
                </div>
                <div class="col-lg-7">
                    <label class="lx-refbox__k" for="referralLink">@lang('Your referral link')</label>
                    <div class="lx-reflink input-group">
                        <input type="text" class="form-control" id="referralLink" readonly
                            value="{{ route('user.register') }}?ref={{ $user->dial_code . $user->mobile }}">
                        <button type="button" class="input-group-text btn--base" id="copyReferral">
                            <i class="las la-copy"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('user.affiliate.hub') }}">@lang('Open the Affiliate Hub') <i class="las la-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ STATS ROW 1 - LISTINGS ============ --}}
        <div class="row gy-3 mb-4">
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-brand"><i class="las la-list"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Total Listings')</span>
                        <h6 class="digit">{{ $data['total_listing'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-green"><i class="las la-check-circle"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Approved / Live')</span>
                        <h6 class="digit">{{ $data['approved_listing'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-amber"><i class="las la-hourglass-half"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Pending Review')</span>
                        <h6 class="digit">{{ $data['pending_listing'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-violet"><i class="las la-star"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Featured')</span>
                        <h6 class="digit">{{ $data['featured_listing'] }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ STATS ROW 2 - AFFILIATE ============ --}}
        <div class="row gy-3 mb-4">
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-brand"><i class="las la-coins"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Affiliate Earned (total)')</span>
                        <h6 class="digit">{{ showAmount($data['affiliate_earned'], currencyFormat: false) }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-green"><i class="las la-wallet"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Withdrawable Balance')</span>
                        <h6 class="digit">{{ showAmount($data['wallet_balance'], currencyFormat: false) }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-brand"><i class="las la-users"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('My Affiliates')</span>
                        <h6 class="digit">{{ $data['direct_referrals'] }} <small class="text-muted">({{ $data['active_directs'] }} @lang('active'))</small></h6>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-md-6">
                <div class="lx-stat">
                    <span class="ico lx-red"><i class="las la-lock"></i></span>
                    <div class="lx-stat__text">
                        <span class="caption">@lang('Held (Upgrade to Unlock)')</span>
                        <h6 class="digit">{{ showAmount($data['held_earnings'], currencyFormat: false) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ QUICK ACTIONS ============ --}}
        <div class="row gy-3 mb-4">
            <div class="col-12"><h5 class="mb-0">@lang('Quick Actions')</h5></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.listing.create') }}" class="lx-action"><i class="las la-plus-circle"></i>@lang('Add Listing')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.listing.all') }}" class="lx-action"><i class="las la-list"></i>@lang('My Listings')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.affiliate.hub') }}" class="lx-action"><i class="las la-network-wired"></i>@lang('My Affiliates')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.leads') }}" class="lx-action"><i class="las la-inbox"></i>@lang('My Leads')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.withdraw.money') }}" class="lx-action"><i class="las la-money-bill-wave"></i>@lang('Withdraw')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.transactions') }}" class="lx-action"><i class="las la-receipt"></i>@lang('Transactions')</a></div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.held.earnings') }}" class="lx-action"><i class="las la-lock"></i>@lang('Held Earnings')</a></div>
            <div class="col-lg-2 col-md-3 col-6">
                <a href="{{ gs('epin') ? route('user.epin.recharge') : route('user.deposit.history') }}" class="lx-action">
                    <i class="las la-{{ gs('epin') ? 'map-pin' : 'credit-card' }}"></i>@lang(gs('epin') ? 'E-Pin Center' : 'Payment History')
                </a>
            </div>
            <div class="col-lg-2 col-md-3 col-6"><a href="{{ route('user.ranking') }}" class="lx-action"><i class="las la-medal"></i>@lang('Ranks')</a></div>
        </div>

        {{-- ============ PLAN USAGE + RECENTS ============ --}}
        <div class="row gy-4">
            <div class="col-lg-5">
                <div class="card custom--card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">@lang('My Plan Usage')</h5></div>
                    <div class="card-body">
                        @if ($plan)
                            @php
                                $limit = (int) $data['listings_limit'];
                                $used  = (int) $data['listings_used'];
                                $pct   = $limit > 0 ? min(100, round($used / $limit * 100)) : ($used > 0 ? 100 : 0);
                            @endphp
                            <div class="d-flex justify-content-between mb-1">
                                <span>@lang('Listings used')</span>
                                <span class="fw-bold">{{ $used }} / {{ $limit > 0 ? $limit : __('Unlimited') }}</span>
                            </div>
                            <div class="lx-progress mb-4">
                                <div class="progress-bar" style="width: {{ $pct }}%;"></div>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><i class="las la-images me-2"></i>@lang('Images per listing')</span><strong>{{ $plan->images }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><i class="las la-video me-2"></i>@lang('Videos per listing')</span><strong>{{ $plan->videos }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><i class="las la-list-ul me-2"></i>@lang('Amenities per listing')</span><strong>{{ $plan->amenities }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><i class="las la-map-marked-alt me-2"></i>@lang('Google Map')</span>
                                    <strong>{!! $plan->allow_map ? '<i class="las la-check text-success"></i>' : '<i class="las la-times text-danger"></i>' !!}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><i class="las la-calendar-times me-2"></i>@lang('Expires on')</span><strong>{{ showDateTime($plan->expired_on, 'M d, Y') }}</strong>
                                </li>
                            </ul>
                            <div class="row g-2 mt-3">
                                <div class="col-6">
                                    <a href="{{ route('user.plan.choose', ['id' => $plan->plan_id]) }}" class="btn btn--base w-100">@lang('Renew Plan')</a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('pricing') }}" class="btn btn-outline--base w-100">@lang('Upgrade Plan')</a>
                                </div>
                            </div>
                            <p class="text-muted mb-0 mt-2"><small>@lang('Renewal extends the same plan for one more year. Pay by payment gateway or E-Pin.')</small></p>
                        @else
                            <div class="text-center py-4">
                                <i class="las la-layer-group la-3x text-muted d-block mb-2"></i>
                                <p class="text-muted">@lang('Buy a plan to publish your listings and start earning affiliate payouts.')</p>
                                <a href="{{ route('pricing') }}" class="btn btn--base">@lang('Buy a Plan')</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="card custom--card h-100">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">@lang('Recent Listings')</h6>
                                <a href="{{ route('user.listing.all') }}" class="small">@lang('View all')</a>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @forelse ($listings as $listing)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ strLimit($listing->title, 26) }}</strong><br>
                                                <small class="text-muted">{{ $listing->city->name ?? '' }}</small>
                                            </div>
                                            @if ($listing->status == App\Constants\Status::LISTING_APPROVED)
                                                <span class="badge badge--success">@lang('Live')</span>
                                            @elseif ($listing->status == App\Constants\Status::LISTING_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif ($listing->status == App\Constants\Status::LISTING_REJECTED)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Disabled')</span>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center py-4"><i class="las la-store la-2x text-muted d-block mb-2"></i><span class="text-muted">@lang("No listings yet. Add your first listing.")</span></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card custom--card h-100">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">@lang('Recent Payouts')</h6>
                                <a href="{{ route('user.transactions') }}" class="small">@lang('View all')</a>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @forelse ($payouts as $payout)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="text--success">+{{ showAmount($payout->amount, currencyFormat: false) }}</strong><br>
                                                <small class="text-muted">{{ __(keyToTitle($payout->remark)) }}</small>
                                            </div>
                                            <small class="text-muted">{{ diffForHumans($payout->created_at) }}</small>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-muted py-4">@lang('No affiliate payouts yet. Share your referral link to get started.')</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <div class="card custom--card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('My Events')</h5>
                    </div>
                    <div class="card-body pt-0">
                        @php
                            $eventGroups = [
                                'Followed Events'   => $followedEvents,
                                'Interested Events' => $interestedEvents,
                                'Joined Events'     => $joinedEvents,
                            ];
                        @endphp
                        <div class="row g-3">
                            @foreach ($eventGroups as $groupLabel => $groupEvents)
                                <div class="col-md-4">
                                    <div class="lx-subcard">
                                        <h6 class="mb-2">
                                            {{ __($groupLabel) }}
                                            <span class="badge badge--info ms-1">{{ $groupEvents->count() }}</span>
                                        </h6>
                                        @forelse ($groupEvents->take(4) as $event)
                                            <div class="py-1">
                                                <a href="{{ route($event->event_type == 1 ? 'event.details' : 'user.internal.event.details', [slug($event->title), $event->id]) }}" class="d-block text-decoration-none">
                                                    <strong>{{ strLimit($event->title, 40) }}</strong>
                                                </a>
                                                <small class="text-muted">{{ showDateTime($event->start_time) }}</small>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0"><small>@lang('Nothing here yet.')</small></p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <h6 class="mt-4 mb-2" style="font-weight:700">
                            @lang('Organised Events')
                            <span class="badge badge--base ms-1">{{ $organisedEvents->count() }}</span>
                        </h6>
                        @forelse ($organisedEvents->take(4) as $event)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <a href="{{ route('user.events.edit', $event->id) }}" class="d-block text-decoration-none">
                                        <strong>{{ strLimit($event->title, 50) }}</strong>
                                    </a>
                                    <small class="d-block text-muted">{{ showDateTime($event->start_time) }}</small>
                                </div>
                                <span class="badge badge--base">@lang('Organiser')</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">@lang('You have not organised any event yet. Create one from My Events.')</p>
                        @endforelse
                        @if ($organisedEvents->count() > 4)
                            <a href="{{ route('user.events.index') }}" class="d-inline-block mt-2">@lang('View all organised events')</a>
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
            function copyToClipboard(text, btn) {
                var showDone = function () {
                    var $btn = $(btn);
                    $btn.html('<i class="las la-check"></i>');
                    if (window.iziToast) iziToast.success({ title: 'Copied', message: 'Referral link copied to clipboard.', position: 'topRight', timeout: 2500 });
                    setTimeout(function () { $btn.html('<i class="las la-copy"></i>'); }, 1500);
                };
                var manualSelect = function () {
                    var input = document.getElementById('referralLink');
                    if (input) { input.focus(); input.select(); input.setSelectionRange(0, input.value.length); }
                    if (window.iziToast) iziToast.warning({ title: 'Copy manually', message: 'Link selected - press Ctrl+C to copy it.', position: 'topRight', timeout: 4000 });
                };
                // 1. temp editable textarea + execCommand - the most compatible
                //    path (works in embedded browsers where the async clipboard
                //    API is permission-blocked, and readonly inputs are skipped)
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                ta.setSelectionRange(0, ta.value.length);
                var ok = false;
                try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
                document.body.removeChild(ta);
                if (ok) { showDone(); return; }
                // 2. async clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(showDone, manualSelect);
                    return;
                }
                // 3. last resort: select the visible field for manual Ctrl+C
                manualSelect();
            }
            $(document).on('click', '#copyReferral', function (e) {
                e.preventDefault();
                copyToClipboard(document.getElementById('referralLink').value, this);
            });
        })(jQuery);
    </script>
@endpush
