@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Events')</h5>
                    <a href="{{ route('admin.events.internal.create') }}" class="btn btn-sm btn--primary">@lang('+ Create Company Event')</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Event')</th>
                                    <th>@lang('Organizer')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $event)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ strLimit($event->title, 40) }}</span><br>
                                            <small class="text-muted">{{ $event->category->name ?? '' }} · {{ $event->city->name ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $event->user->fullname ?? '-' }}</span><br>
                                            <small>{{ $event->user?->username ?? 'Admin' }}</small>
                                        </td>
                                        <td>{{ showDateTime($event->start_time, 'M d, Y g:i A') }}</td>
                                        <td>@php echo $event->statusBadge @endphp</td>
                                        <td>
                                            @if ($event->event_type == 2)
                                                <div class="d-flex gap-2 align-items-center">
                                                    <a href="{{ route('admin.events.internal.edit', $event->id) }}"
                                                        class="btn btn-outline--primary btn-sm">
                                                        <i class="las la-pen"></i> @lang('Edit')
                                                    </a>
                                                    @include('admin.partials.switch', [
                                                        'action'  => route('admin.events.internal.status', $event->id),
                                                        'checked' => $event->status == App\Constants\Status::ENABLE,
                                                        'confirm' => $event->status == App\Constants\Status::ENABLE ? __('Unpublish this company event?') : __('Publish this company event?'),
                                                        'title'   => $event->status == App\Constants\Status::ENABLE ? __('Published') : __('Unpublished'),
                                                    ])
                                                </div>
                                            @elseif ($event->status == App\Constants\Status::LISTING_PENDING)
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-outline--success btn-sm confirmationBtn"
                                                        data-question="@lang('Approve this event?')"
                                                        data-action="{{ route('admin.events.approve', $event->id) }}">
                                                        <i class="las la-check"></i> @lang('Approve')
                                                    </button>
                                                    <button class="btn btn-outline--danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $event->id }}">
                                                        <i class="las la-times"></i> @lang('Reject')
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($events->hasPages())
                    <div class="card-footer py-4">@php echo paginateLinks($events) @endphp</div>
                @endif
            </div>
        </div>
    </div>

    @foreach ($events as $event)
        @if ($event->status == App\Constants\Status::LISTING_PENDING)
        <div class="modal fade" id="rejectModal{{ $event->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('Reject Event')</h5>
                            <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">@lang('Reason (required)')</label>
                            <textarea name="reason" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--danger">@lang('Reject Event')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    <x-confirmation-modal />
@endsection
