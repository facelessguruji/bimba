@extends('Template::layouts.user')
@section('content')
    <div class="container py-120">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card custom--card mb-4" style="background: linear-gradient(135deg, hsl(var(--base-h), var(--base-s), var(--base-l)) 0%, hsl(var(--base-h), var(--base-s), calc(var(--base-l) + 12%)) 100%); color: #fff;">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center gy-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="ico" style="background: rgba(255,255,255,.12); color: #fff; width: 56px; height: 56px; font-size: 1.6rem;">
                                        <i class="las la-lock"></i>
                                    </span>
                                    <div>
                                        <h3 class="mb-0">{{ showAmount($heldTotal) }}</h3>
                                        <small class="text-white-50">@lang('Held Earnings - Unlock by Upgrading')</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="text-white-50 mb-2" style="font-size: .8125rem;">
                                    @lang('Your plan tier determines how many commission levels you can receive. Held earnings never expire - upgrade to unlock them.')
                                </p>
                                <a href="{{ route('pricing') }}" class="btn btn--base btn--sm">
                                    <i class="las la-rocket"></i> @lang('Upgrade Plan')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row gy-3 mb-4">
                    <div class="col-md-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ showAmount($heldTotal, currencyFormat: false) }}</h6><span class="caption">@lang('Currently Locked')</span></div>
                            <span class="ico lx-amber"><i class="las la-lock"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="lx-stat">
                            <div><h6 class="digit">{{ showAmount($released, currencyFormat: false) }}</h6><span class="caption">@lang('Released to Balance (lifetime)')</span></div>
                            <span class="ico lx-green"><i class="las la-unlock"></i></span>
                        </div>
                    </div>
                </div>

                @if ($heldTotal > 0)
                    <div class="alert alert--warning d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <span><i class="las la-info-circle me-1"></i>@lang('Held earnings unlock automatically when your plan tier covers the commission level. They never expire.')</span>
                        <a href="{{ route('pricing') }}" class="btn btn--base btn--sm">@lang('Upgrade Now')</a>
                    </div>
                @endif

                <div class="card custom--card">
                    <div class="card-header py-3"><h6 class="mb-0">@lang('Commission History')</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table custom--table table--responsive--lg mb-0">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('From Member')</th>
                                        <th>@lang('Position')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Reason')</th>
                                        <th>@lang('Status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($heldLogs as $log)
                                        <tr>
                                            <td>{{ showDateTime($log->created_at) }}</td>
                                            <td>{{ $log->fromUser->username ?? '-' }}</td>
                                            <td><span class="badge badge--dark">{{ ladderLevelName($log->level) }}</span></td>
                                            <td class="fw-bold">{{ showAmount($log->amount) }}</td>
                                            <td>
                                                @if ($log->reason == 'no_plan')
                                                    <span class="badge badge--warning">@lang('No active plan')</span>
                                                @else
                                                    <span class="badge badge--info">@lang('Beyond plan depth')</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->status == App\Constants\Status::HELD)
                                                    <span class="badge badge--warning"><i class="las la-lock me-1"></i>@lang('Held')</span>
                                                @else
                                                    <span class="badge badge--success"><i class="las la-check me-1"></i>@lang('Released')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center text-muted py-4">
                                                <i class="las la-lock-open la-2x d-block mb-2"></i>
                                                @lang('No held earnings - you\'re earning directly to your wallet!')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($heldLogs->hasPages())
                        <div class="card-footer py-3">{{ paginateLinks($heldLogs) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
