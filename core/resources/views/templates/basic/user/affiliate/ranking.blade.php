@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h3 class="mb-4">@lang('Affiliate Ranks')</h3>

                <div class="card custom--card mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center gap-3">
                        <span class="ico lx-brand"><i class="las {{ $user->userRanking->icon ?? 'la-medal' }}"></i></span>
                        <div>
                            <h5 class="mb-1">{{ $user->userRanking->name ?? __('No Rank Yet') }}</h5>
                            <span class="caption">@lang('Your current rank') · @lang('Active team members'): {{ $user->userExtra->direct_active ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                @if (!$userRankings->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Rank')</th>
                                            <th>@lang('Active Team Members to Build')</th>
                                            <th>@lang('Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userRankings as $ranking)
                                            <tr>
                                                <td>
                                                    <i class="la {{ $ranking->icon }} me-2"></i>
                                                    <strong>{{ $ranking->name }}</strong>
                                                </td>
                                                <td>{{ $ranking->direct_active }}</td>
                                                <td>
                                                    @if ($user->user_ranking_id >= $ranking->id && $user->userRanking && $user->userRanking->level >= $ranking->level)
                                                        <span class="badge badge--success">@lang('Achieved')</span>
                                                    @else
                                                        <span class="badge badge--dark">@lang('Locked')</span>
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
                    @include('Template::partials.data_not_found', ['data' => 'No Rank Defined'])
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
