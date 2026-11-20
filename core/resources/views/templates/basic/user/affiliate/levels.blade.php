@extends('Template::layouts.user')
@section('content')
    @php
        $teamTotal = collect($levels)->flatten()->sum(fn ($m) => 1);
    @endphp
    <div class="container py-120">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h4 class="mb-1">@lang('My Team') {{ $user->fullname }}</h4>
                        <p class="text-muted mb-0">@lang('Your affiliate network, level by level')</p>
                    </div>
                    <a href="{{ route('user.team.levels') }}" class="btn btn--base btn--sm"><i class="las la-user"></i> @lang('My Team')</a>
                </div>

                <div class="row gy-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ $user->referrals()->count() }}</h6><span class="caption">@lang('Direct Referrals')</span></div>
                            <span class="ico lx-brand"><i class="las la-users"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ $teamTotal }}</h6><span class="caption">@lang('Team Members')</span></div>
                            <span class="ico lx-violet"><i class="las la-user-check"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ $user->userExtra->direct_active ?? 0 }}</h6><span class="caption">@lang('Active Team Members')</span></div>
                            <span class="ico lx-green"><i class="las la-user-check"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ $user->userRanking->name ?? '-' }}</h6><span class="caption">@lang('Current Rank')</span></div>
                            <span class="ico lx-brand"><i class="las la-medal"></i></span>
                        </div>
                    </div>
                </div>

                @forelse ($levels as $level => $members)
                    <div class="card custom--card mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="las la-layer-group me-2"></i>{{ ladderLevelName($level) }}</h6>
                            <span class="badge badge--primary">{{ count($members) }} @lang('members')</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row gy-3">
                                @foreach ($members as $member)
                                    <div class="col-xl-3 col-md-4 col-6">
                                        <div class="d-flex align-items-center gap-2 p-2" style="border: 1px solid #f1f5f9; border-radius: 8px; background: #f8fafc;">
                                            <span class="ico {{ $member->activePlan ? 'lx-green' : 'lx-amber' }}" style="width: 36px; height: 36px; flex: 0 0 36px; font-size: .9rem;">
                                                {{ strtoupper(substr($member->firstname ?? '?', 0, 1)) }}
                                            </span>
                                            <div style="min-width: 0;">
                                                <div style="font-size: .8125rem; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $member->fullname }}</div>
                                                <div class="text-muted" style="font-size: .68rem;">
                                                    @if ($member->activePlan)
                                                        <i class="las la-check-circle text--success"></i> {{ $member->activePlan->plan->title ?? __('Active') }}
                                                    @else
                                                        <i class="las la-circle text-muted"></i> @lang('Free')
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="ms-auto">
                                                @if ($member->referrals_count > 0)
                                                    <a href="{{ route('user.team.levels', $member->username) }}" class="btn btn--sm btn-outline--base p-1" title="@lang('Expand')">
                                                        <i class="las la-angle-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="las la-network-wired la-4x text-muted d-block mb-3"></i>
                        <h5 class="text-muted">@lang('Your team is empty')</h5>
                        <p class="text-muted mb-3">@lang('Share your referral link to start building your network')</p>
                        <a href="{{ route('user.affiliate.hub') }}" class="btn btn--base"><i class="las la-share-alt"></i> @lang('Get My Link')</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
