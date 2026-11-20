@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">{{ $user->fullname }} ({{ $user->username }})</h5>
                        <span class="small">@lang('Active directs'): {{ $user->userExtra->direct_active ?? 0 }} ·
                            @lang('Active directs'): {{ $user->userExtra->direct_active ?? 0 }} ·
                            @lang('Rank'): {{ $user->userRanking->name ?? '-' }}</span>
                    </div>
                    <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-outline--primary">@lang('User Detail')</a>
                </div>
            </div>

            @forelse ($levels as $level => $members)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('Level') {{ $level }}</h5>
                        <span class="badge badge--primary">{{ count($members) }} @lang('members')</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Member')</th>
                                        <th>@lang('Plan')</th>
                                        <th>@lang('Directs')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($members as $member)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $member->fullname }}</span><br>
                                                <span class="small">{{ $member->username }}</span>
                                            </td>
                                            <td>
                                                @if ($member->activePlan)
                                                    <span class="badge badge--success">{{ $member->activePlan->plan->title ?? __('Active') }}</span>
                                                @else
                                                    <span class="badge badge--warning">@lang('Free')</span>
                                                @endif
                                            </td>
                                            <td>{{ $member->userExtra->direct_total ?? 0 }}</td>
                                            <td>
                                                @if ($member->referrals()->exists())
                                                    <a href="{{ route('admin.users.team', $member->username) }}" class="btn btn-sm btn-outline--primary">@lang('Expand')</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center text-muted">@lang('This member has no team yet')</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.users.all') }}" />
@endpush
