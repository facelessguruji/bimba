@extends('Template::layouts.user')
@section('content')
    <div class="container">
        <div class="lx-page-head">
        <div>
            <h4>@lang('My Events')</h4>
            <p class="lx-sec-sub">@lang('Manage your business events')</p>
        </div>
        <div class="lx-page-actions">
            <a href="{{ route('user.events.create') }}" class="btn btn--base btn--sm"><i class="las la-plus"></i> @lang('Create Event')</a>
        </div>
    </div>
    @if (!$events->isEmpty())
        <div class="row gy-4">
            @foreach ($events as $event)
                <div class="col-xl-4 col-md-6">
                    <div class="card h-100 overflow-hidden" style="border-radius: 12px;">
                        <div class="position-relative">
                            <img src="{{ getImage(getFilePath('event') . '/' . $event->cover_image, '372x216') }}"
                                 class="w-100" style="height: 160px; object-fit: cover;" alt="{{ $event->title }}">
                            <div class="position-absolute top-0 end-0 p-2">
                                @php echo $event->statusBadge @endphp
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <small class="text--base">{{ $event->category->name ?? '' }}</small>
                            <h6 class="mt-1 mb-2">{{ strLimit($event->title, 45) }}</h6>
                            <div class="d-flex flex-wrap gap-3 text-muted" style="font-size: .8125rem;">
                                <span><i class="las la-calendar me-1"></i>{{ showDateTime($event->start_time, 'M d, Y') }}</span>
                                <span><i class="las la-map-marker-alt me-1"></i>{{ $event->city->name ?? '' }}</span>
                            </div>
                            @if ($event->status == \App\Constants\Status::LISTING_REJECTED && $event->rejected_reason)
                                <div class="alert alert-danger py-2 px-3 mt-2 mb-0" style="font-size: .8125rem;">
                                    <i class="las la-info-circle"></i> @lang('Rejected'): {{ $event->rejected_reason }}
                                </div>
                            @endif
                        </div>
                        <div class="card-footer py-2 px-3 d-flex justify-content-between align-items-center bg-transparent">
                            <a class="btn btn--sm btn-outline--info" href="{{ route('user.events.edit', $event->id) }}">
                                <i class="la la-pen"></i> @lang('Edit')
                            </a>
                            <span class="text-muted" style="font-size: .75rem;">
                                <i class="las la-clock me-1"></i>{{ showDateTime($event->start_time, 'g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($events->hasPages())
            <div class="mt-4">{{ paginateLinks($events) }}</div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="las la-calendar-plus la-4x text-muted d-block mb-3"></i>
            <h5 class="text-muted">@lang('No events yet')</h5>
            <p class="text-muted mb-3">@lang('Create your first event to connect with your audience')</p>
            <a href="{{ route('user.events.create') }}" class="btn btn--base"><i class="las la-plus"></i> @lang('Create Event')</a>
        </div>
    @endif
    </div>
@endsection
