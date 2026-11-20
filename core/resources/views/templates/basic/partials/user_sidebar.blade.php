@php
    $lxSideActive = function ($patterns, $class = 'active') {
        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return $class;
            }
        }
        return '';
    };
@endphp

<div class="lx-side__brand">
    <a href="{{ route('home') }}">
        <span class="lgx-mark">L</span> <b style="font:800 17px var(--lgx-font);color:var(--lgx-ink)">{{ gs('site_name') }}</b>
    </a>
    <button type="button" class="lx-side__close d-lg-none" id="lxSideClose" aria-label="@lang('Close menu')">
        <i class="las la-times"></i>
    </button>
</div>

@if (auth()->user())
<div class="lx-side__user" title="@lang('Withdrawable Balance')">
    <div class="lx-side__userinfo">
        <span class="lx-side__balancek">@lang('Wallet balance')</span>
        <span class="lx-side__balancev">{{ showAmount(auth()->user()->balance) }}</span>
        <span class="lx-side__balanceu">{{ '@' . auth()->user()->username }}</span>
    </div>
</div>
@else
<div class="lx-side__user">
    <div class="lx-side__userinfo">
        <span class="lx-side__balancek">@lang('Wallet balance')</span>
        <span class="lx-side__balancev">-</span>
        <span class="lx-side__balanceu">@lang('Guest')</span>
    </div>
</div>
@endif

<nav class="lx-side__nav">
    <span class="lx-side__label">@lang('Overview')</span>

    <a href="{{ route('user.home') }}" class="lx-side__link {{ $lxSideActive(['user.home']) }}">
        <i class="las la-tachometer-alt"></i> <span>@lang('Dashboard')</span>
    </a>

    <a href="{{ route('user.transactions') }}" class="lx-side__link {{ $lxSideActive(['user.transactions']) }}">
        <i class="las la-receipt"></i> <span>@lang('Transactions')</span>
    </a>

    <span class="lx-side__label">@lang('Affiliate')</span>

    <a href="{{ route('user.affiliate.hub') }}" class="lx-side__link {{ $lxSideActive(['user.affiliate.hub', 'user.my.referral']) }}">
        <i class="las la-handshake"></i> <span>@lang('Affiliate Hub')</span>
    </a>

    <a href="{{ route('user.team.levels') }}" class="lx-side__link {{ $lxSideActive(['user.team.levels']) }}">
        <i class="las la-network-wired"></i> <span>@lang('My Team')</span>
    </a>

    <a href="{{ route('user.held.earnings') }}" class="lx-side__link {{ $lxSideActive(['user.held.earnings']) }}">
        <i class="las la-lock"></i> <span>@lang('Held Earnings')</span>
    </a>

    <a href="{{ route('user.ranking') }}" class="lx-side__link {{ $lxSideActive(['user.ranking']) }}">
        <i class="las la-medal"></i> <span>@lang('Ranks')</span>
    </a>

    <span class="lx-side__label">@lang('Business')</span>

    <a href="{{ route('user.listing.create') }}" class="lx-side__link {{ $lxSideActive(['user.listing.create', 'user.listing.store', 'user.listing.edit']) }}">
        <i class="las la-plus-circle"></i> <span>@lang('Create Listing')</span>
    </a>

    <a href="{{ route('user.listing.all') }}" class="lx-side__link {{ $lxSideActive(['user.listing.all', 'user.listing.status']) }}">
        <i class="las la-list-ul"></i> <span>@lang('My Listings')</span>
    </a>

    <a href="{{ route('user.leads') }}" class="lx-side__link {{ $lxSideActive(['user.leads']) }}">
        <i class="las la-inbox"></i> <span>@lang('My Leads')</span>
    </a>

    <a href="{{ route('user.events.index') }}" class="lx-side__link {{ $lxSideActive(['user.events.*']) }}">
        <i class="las la-calendar-check"></i> <span>@lang('My Events')</span>
    </a>
    <a href="{{ route('user.internal.events') }}" class="lx-side__link {{ $lxSideActive(['user.internal.events*']) }}">
        <i class="las la-building"></i> <span>@lang('Company Events')</span>
    </a>

    <span class="lx-side__label">@lang('Money')</span>

    <a href="{{ route('user.deposit.index') }}" class="lx-side__link {{ $lxSideActive(['user.deposit.*']) }}">
        <i class="las la-plus-circle"></i> <span>@lang('Add Funds')</span>
    </a>

    <a href="{{ route('user.withdraw.money') }}" class="lx-side__link {{ $lxSideActive(['user.withdraw.money', 'user.withdraw.store', 'user.withdraw.preview', 'user.withdraw.submit']) }}">
        <i class="las la-money-bill-wave"></i> <span>@lang('Withdraw')</span>
    </a>

    <a href="{{ route('user.withdraw.history') }}" class="lx-side__link {{ $lxSideActive(['user.withdraw.history']) }}">
        <i class="las la-file-invoice-dollar"></i> <span>@lang('Withdraw Log')</span>
    </a>

    @if (gs('epin'))
        <a href="{{ route('user.epin.recharge') }}" class="lx-side__link {{ $lxSideActive(['user.epin.recharge', 'user.recharge.log', 'user.erecharge', 'user.pin.generate']) }}">
            <i class="las la-qrcode"></i> <span>@lang('E-Pin Center')</span>
        </a>
    @endif

    <a href="{{ route('user.deposit.history') }}" class="lx-side__link {{ $lxSideActive(['user.deposit.history', 'user.payment.history']) }}">
        <i class="las la-credit-card"></i> <span>@lang('Payment History')</span>
    </a>

    @if (gs('balance_transfer'))
        <a href="{{ route('user.balance.transfer') }}" class="lx-side__link {{ $lxSideActive(['user.balance.transfer']) }}">
            <i class="las la-exchange-alt"></i> <span>@lang('Transfer Balance')</span>
        </a>
    @endif

    <span class="lx-side__label">@lang('Account')</span>

    <a href="{{ route('user.kyc.form') }}" class="lx-side__link {{ $lxSideActive(['user.kyc.form', 'user.kyc.submit']) }}">
        <i class="las la-id-card"></i> <span>@lang('KYC Verification')</span>
    </a>

    <a href="{{ route('user.profile.setting') }}" class="lx-side__link {{ $lxSideActive(['user.profile.setting']) }}">
        <i class="las la-user-cog"></i> <span>@lang('Profile Setting')</span>
    </a>

    <a href="{{ route('user.change.password') }}" class="lx-side__link {{ $lxSideActive(['user.change.password']) }}">
        <i class="las la-key"></i> <span>@lang('Change Password')</span>
    </a>

    <a href="{{ route('user.twofactor') }}" class="lx-side__link {{ $lxSideActive(['user.twofactor']) }}">
        <i class="las la-shield-alt"></i> <span>@lang('2FA Security')</span>
    </a>

    <a href="{{ route('ticket.index') }}" class="lx-side__link {{ $lxSideActive(['ticket.*']) }}">
        <i class="las la-life-ring"></i> <span>@lang('Support Tickets')</span>
    </a>
</nav>

