@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Affiliate Ranks')</h5>
                    <p class="text-muted mb-0 mt-2"><small>
                        @lang('Ranks are earned on active team members alone (single-line referral model). The doubling ladder 0 / 3 / 6 / 12 / 24 / 48 / 96 / 192 / 384 / 768 reflects long-term team building: every active team member heads a network of its own, so the top ranks demand a real organization.')
                    </small></p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Rank')</th>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Active Team Members to Build')</th>
                                    <th>@lang('Withdrawal Tier Plan')</th>
                                    <th>@lang('Bonus')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userRankings as $ranking)
                                    <tr>
                                        <td>
                                            @if ($ranking->icon && str_contains($ranking->icon, 'la'))
                                                <i class="{{ $ranking->icon }} me-1"></i>
                                            @elseif ($ranking->icon)
                                                <img src="{{ getImage(getFilePath('userRanking') . '/' . $ranking->icon) }}" alt="" width="24">
                                            @endif
                                            {{ $ranking->name }}
                                        </td>
                                        <td>{{ $ranking->level }}</td>

                                        <td>{{ $ranking->direct_active }}</td>
                                        <td>@if($ranking->min_plan_amount > 0){{ showAmount($ranking->min_plan_amount) }}@else @lang('Active plan only') @endif</td>
                                        <td>{{ showAmount($ranking->bonus) }}</td>
                                        <td> @php echo $ranking->statusBadge ?? ($ranking->status ? '<span class="badge badge--success">'.__('Enabled').'</span>' : '<span class="badge badge--danger">'.__('Disabled').'</span>') @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary cuModalBtn btn-sm"
                                                    data-modal_title="@lang('Update Rank')"
                                                    data-resource="{{ $ranking }}">
                                                    <i class="las la-pen"></i>
                                                </button>
                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.ranking.status', $ranking->id),
                                                    'checked' => $ranking->status,
                                                    'confirm' => $ranking->status ? __('Are you sure to disable this rank?') : __('Are you sure to enable this rank?'),
                                                    'title'   => $ranking->status ? __('Enabled') : __('Disabled'),
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($userRankings->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($userRankings) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.ranking.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Level')</label>
                            <input class="form-control" name="level" type="number" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Active Team Members to Build')</label>
                            <input class="form-control" name="direct_active" type="number" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Withdrawal Tier Plan (price)')</label>
                            <div class="input-group">
                                <input class="form-control" name="min_plan_amount" type="number" step="any" min="0" value="0" required>
                                <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                            </div>
                            <small class="text-muted">@lang('Members at this rank must hold an ACTIVE plan of at least this price to withdraw. 0 = any active plan.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Bonus (always 0)')</label>
                            <div class="input-group">
                                <input class="form-control" name="bonus" type="number" step="any" min="0" max="0" value="0" readonly required>
                                <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                            </div>
                            <small class="text-muted">@lang('Ranks are status only and pay no bonuses.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Icon (image or line-awesome class)')</label>
                            <input class="form-control" name="icon" type="text" placeholder="las la-medal">
                            <input class="form-control mt-2" name="icon_file" type="file" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add Rank')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush


