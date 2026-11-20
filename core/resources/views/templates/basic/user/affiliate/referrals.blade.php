@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <h3 class="mb-0">@lang('My Referrals')</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.my.referral') }}" class="btn btn--sm {{ !request()->filled('ref_source') ? 'btn--base' : 'btn--outline-dark' }}">@lang('All')</a>
                        <a href="{{ route('user.my.referral', ['ref_source' => 0]) }}" class="btn btn--sm {{ request()->ref_source == 0 ? 'btn--base' : 'btn--outline-dark' }}">@lang('Via Link')</a>
                        <a href="{{ route('user.my.referral', ['ref_source' => 1]) }}" class="btn btn--sm {{ request()->ref_source == 1 ? 'btn--base' : 'btn--outline-dark' }}">@lang('AI Auto Assigned')</a>
                    </div>
                </div>
                @if (!$referrals->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Member')</th>
                                            <th>@lang('Joined')</th>
                                            <th>@lang('Source')</th>
                                            <th>@lang('Plan')</th>
                                            <th>@lang('Status')</th>
                                            <th class="text-end">@lang('Team')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($referrals as $referral)
                                            <tr>
                                                <td>
                                                    <strong>{{ $referral->fullname }}</strong><br>
                                                    <small>{{ $referral->username }}</small>
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
                                                        <span class="badge badge--success">{{ $referral->activePlan->plan->title ?? __('Active') }}</span>
                                                    @else
                                                        <span class="badge badge--warning">@lang('Free')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($referral->status == App\Constants\Status::USER_ACTIVE)
                                                        <span class="badge badge--success">@lang('Active')</span>
                                                    @else
                                                        <span class="badge badge--danger">@lang('Banned')</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('user.team.levels', $referral->username) }}" class="btn btn--base btn--sm">@lang('View Team')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'No Referral Found'])
                @endif
                @if ($referrals->hasPages())
                    <div class="mt-4">{{ paginateLinks($referrals) }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
