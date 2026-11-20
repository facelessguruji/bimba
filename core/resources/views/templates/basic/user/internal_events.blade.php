@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-1">@lang("Company Events")</h4>
                        <p class="text-muted mb-0">@lang("Training programmes, workshops and conferences for registered affiliates.")</p>
                    </div>
                </div>
                <div class="row gy-4">
                    @forelse ($events as $event)
                        <div class="col-lg-4 col-md-6">
                            <div class="card custom--card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge badge--info">@lang('Company Event')</span>
                                        @if ($event->price > 0)
                                            <span class="badge badge--base">{{ showAmount($event->price) }}</span>
                                        @else
                                            <span class="badge badge--success">@lang('Free')</span>
                                        @endif
                                    </div>
                                    <h5>{{ $event->title }}</h5>
                                    <p class="text-muted">{{ strLimit(strip_tags($event->description), 100) }}</p>
                                    <a href="{{ route('user.internal.event.details', [slug($event->title), $event->id]) }}" class="btn btn--sm btn--base">@lang('View Details')</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">@lang('No company events available right now.')</div>
                    @endforelse
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
