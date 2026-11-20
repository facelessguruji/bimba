@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            @if (isset($heldTotal))
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-0">@lang('Total currently held across all members'): <strong>{{ showAmount($heldTotal) }}</strong></h6>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Receiver')</th>
                                    <th>@lang('From Member')</th>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Reason')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($heldLogs as $log)
                                    <tr>
                                        <td>{{ showDateTime($log->created_at) }}</td>
                                        <td><span class="fw-bold">{{ $log->user?->username }}</span></td>
                                        <td>{{ $log->fromUser?->username }}</td>
                                        <td>{{ $log->level }}</td>
                                        <td>{{ showAmount($log->amount) }}</td>
                                        <td>
                                            @if ($log->reason == 'no_plan')
                                                <span class="badge badge--dark">@lang('No plan')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Depth')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->status == App\Constants\Status::HELD)
                                                <span class="badge badge--warning">@lang('Held')</span>
                                            @else
                                                <span class="badge badge--success">@lang('Released')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->status == App\Constants\Status::HELD)
                                                <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-question="@lang('Release this held commission to the member balance?')"
                                                    data-action="{{ route('admin.held.release', $log->id) }}">
                                                    @lang('Release')
                                                </button>
                                            @endif
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
                @if ($heldLogs->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($heldLogs) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder='Username' />
@endpush
