@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        @php
            $earnedDirect = 0;
        @endphp

        <div class="row justify-content-center">
            <div class="col-md-12">

                {{-- Invite & Earn + Referral Link --}}
                <div class="card custom--card mb-4">
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-lg-7">
                                <h4 class="mb-2">@lang('Invite & Earn')</h4>
                                <p class="text-muted">@lang('Share your referral link. When anyone registers through it and buys a listing plan, you instantly earn your direct commission, plus level commissions as your team grows.')</p>
                                <label class="form--label mb-2">@lang('Your referral link')</label>
                                <div class="lx-reflink input-group" style="border: 1px dashed #e8edf3; border-radius: 8px; padding: .25rem;">
                                    <input type="text" class="form-control form--control" id="referralLink" readonly
                                        value="{{ $refLink }}">
                                    <button type="button" class="input-group-text btn--base text-white" id="copyReferral">
                                        <i class="las la-copy"></i> @lang('Copy')
                                    </button>
                                </div>
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <a class="btn btn--sm btn-outline--base"
                                        target="_blank"
                                        href="https://wa.me/?text={{ rawurlencode(__('Join me on ') . gs('site_name') . ': ' . $refLink) }}">
                                        <i class="lab la-whatsapp"></i> @lang('WhatsApp')</a>
                                    <a class="btn btn--sm btn-outline--base"
                                        target="_blank"
                                        href="https://twitter.com/intent/tweet?text={{ rawurlencode(__('Join me on ') . gs('site_name')) }}&url={{ rawurlencode($refLink) }}">
                                        <i class="lab la-twitter"></i> @lang('X / Twitter')</a>
                                    <a class="btn btn--sm btn-outline--base"
                                        href="mailto:?subject={{ rawurlencode(__('Join me on ') . gs('site_name')) }}&body={{ rawurlencode(__('Register with my link: ') . $refLink) }}">
                                        <i class="las la-envelope"></i> @lang('Email')</a>
                                    <a href="{{ route('user.team.levels') }}" class="btn btn--sm btn-outline--base"><i class="las la-network-wired"></i> @lang('My Team')</a>
                                    <a href="{{ route('user.held.earnings') }}" class="btn btn--sm btn-outline--base"><i class="las la-lock"></i> @lang('Held Earnings')</a>
                                    <a href="{{ route('user.ranking') }}" class="btn btn--sm btn-outline--base"><i class="las la-medal"></i> @lang('Ranks')</a>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="row gy-3">
                                    <div class="col-6">
                                        <div class="lx-stat">
    <div><h6 class="digit">{{ $extra->direct_total ?? 0 }}</h6><span class="caption">@lang('Members Referred')</span></div>
    <span class="ico lx-brand"><i class="las la-users"></i></span>
</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="lx-stat">
    <div><h6 class="digit">{{ $extra->direct_active ?? 0 }}</h6>
                                                <span class="caption">@lang('Active (Paid) Affiliates')</span>
                                            </div>
                                            <span class="ico lx-green"><i class="las la-user-check"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="lx-stat">
                                            <div><h6 class="digit">{{ max(($extra->direct_total ?? 0) - ($extra->direct_active ?? 0), 0) }}</h6><span class="caption">@lang('Free Members Referred')</span></div>
                                            <span class="ico lx-violet"><i class="las la-user-plus"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="lx-stat">
    <div><h6 class="digit">{{ showAmount($earned, currencyFormat: false) }}</h6><span class="caption">@lang('Total Affiliate Earned')</span></div>
    <span class="ico lx-brand"><i class="las la-coins"></i></span>
</div>
                                    </div>
@php
    // V2.0 rank tier wall: the earned rank sets the minimum ACTIVE plan tier that may withdraw
    $tier = (float) (optional(auth()->user()->userRanking)->min_plan_amount ?? 0);
    $tierHeld = auth()->user()->plans()
        ->where('plan_price', '>=', $tier)
        ->where('status', \App\Constants\Status::ENABLE)
        ->whereDate('expired_on', '>', now())
        ->exists();
@endphp
@if ($tier > 0)
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 mb-3 rounded border">
                                            <div>
                                                <span class="caption">@lang('Withdrawal tier for your rank') ({{ __(auth()->user()->userRanking->name) }})</span>
                                                <div><strong>{{ showAmount($tier) }}</strong>
                                                    @if ($tierHeld)
                                                        <span class="badge badge--success">@lang('Unlocked')</span>
                                                    @else
                                                        <span class="badge badge--warning">@lang('Locked - upgrade to withdraw')</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @unless ($tierHeld)
                                                <a href="{{ route('pricing') }}" class="btn btn--sm btn--base">@lang('Upgrade Plan')</a>
                                            @endunless
                                        </div>
                                    </div>
@endif
                                    <div class="col-12">
                                        <a href="{{ route('user.withdraw.money') }}" class="btn btn--base w-100 lx-wbtn">
                                            <span class="lx-wbtn__top"><i class="las la-wallet"></i> @lang('Withdraw Earnings')</span>
                                            <span class="lx-wbtn__b">@lang('Withdrawable Balance'): {{ showAmount(auth()->user()->balance) }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- My Affiliate List --}}
                <div class="card custom--card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="card-title mb-0">@lang('My Affiliate List')</h5>
                        <form class="d-flex gap-2">
                            <input type="search" name="search" class="form-control form--control form-control-sm"
                                value="{{ request('search') }}" placeholder="@lang('Search by name or email')">
                            <button class="btn btn--base btn--sm">@lang('Search')</button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table custom--table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Member')</th>
                                        <th>@lang('Joined')</th>
                                        <th>@lang('Source')</th>
                                        <th>@lang('Plan Status')</th>
                                        <th>@lang('Rank')</th>
                                        <th class="text-end">@lang('Team')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($referrals as $referral)
                                        <tr>
                                            <td>
                                                <strong>{{ $referral->fullname }}</strong><br>
                                                <small>{{ $referral->email }}</small>
                                            </td>
                                            <td>
                                                {{ showDateTime($referral->created_at) }}<br>
                                                <small>{{ diffForHumans($referral->created_at) }}</small>
                                            </td>
                                            <td>
                                                @if ($referral->ref_source == App\Constants\Status::REF_SOURCE_AUTO)
                                                    <span class="badge badge--info">@lang('AI Auto Assigned')</span>
                                                @else
                                                    <span class="badge badge--dark">@lang('Referral Link')</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($referral->activePlan)
                                                    <span class="badge badge--success">{{ $referral->activePlan->plan ? planDisplayName($referral->activePlan->plan) : __('Active') }}</span>
                                                @else
                                                    <span class="badge badge--warning">@lang('Free')</span>
                                                @endif
                                            </td>
                                            <td>{{ $referral->userRanking->name ?? '-' }}</td>
                                            <td class="text-end">
                                                @if ($referral->referrals()->exists())
                                                    <a href="{{ route('user.team.levels', $referral->username) }}" class="btn btn--base btn--sm">@lang('View Team')</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center text-muted py-4">
                                                <i class="las la-user-friends la-2x d-block mb-2"></i>
                                                @lang('You have not referred any member yet')
                                                <br><small>@lang('Share your referral link to start building your affiliate network.')</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($referrals->hasPages())
                        <div class="card-footer py-4">{{ paginateLinks($referrals) }}</div>
                    @endif
                </div>

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
