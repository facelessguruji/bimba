@extends('Template::layouts.master')
@section('content')
<section class="py-120 lx-page">
    <div class="container">
        <div class="lx-pagehead">
            <div>
                <h1>@lang('Events')</h1>
                <p class="lx-sec-sub">@lang('Workshops, seminars and networking sessions hosted by businesses near you.')</p>
            </div>
            @if ($events->total())
                <span class="lx-count-chip"><i class="las la-calendar-day"></i> {{ $events->total() }} {{ $events->total() == 1 ? __('event') : __('events') }} @lang('listed')</span>
            @endif
        </div>
        <form action="{{ route('events') }}" method="get" class="lx-toolbar">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5 col-md-6">
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control form--control" placeholder="@lang('Search events by title or venue')">
                </div>
                <div class="col-lg-4 col-md-6">
                    <select name="category" class="form-select form--control">
                        <option value="">@lang('All categories')</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" {{ request()->category == $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn--base w-100"><i class="las la-search"></i> @lang('Search')</button>
                </div>
            </div>
        </form>
        <div class="row g-4">
            @forelse ($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="lx-ev">
                        <div class="lx-ev__cover">
                            @if ($event->cover_image)
                                <img loading="lazy" src="{{ getImage(getFilePath('event') . '/' . $event->cover_image) }}" alt="{{ $event->title }}">
                            @endif
                            <div class="lx-ev__date">
                                <b>{{ optional($event->start_time)->format('d') }}</b>
                                <span>{{ optional($event->start_time)->format('M') }}</span>
                            </div>
                        </div>
                        <div class="lx-ev__body">
                            @if ($event->category?->name)
                                <span class="lx-ev__cat">{{ $event->category->name }}</span>
                            @endif
                            <h5 class="lx-ev__title">
                                <a href="{{ route('event.details', [\Str::slug($event->title) ?: 'event', $event->id]) }}">{{ $event->title }}</a>
                            </h5>
                            <div class="lx-ev__meta">
                                <span><i class="las la-map-marker-alt"></i> {{ $event->venue }}</span>
                                <span><i class="las la-clock"></i> {{ showDateTime($event->start_time, 'g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="lx-empty">
                        <div class="lx-empty__ico"><i class="las la-calendar-plus"></i></div>
                        <h5>@lang('No events found')</h5>
                        <p>@lang('Check back soon for upcoming events.')</p>
                    </div>
                </div>
            @endforelse
        </div>
        @if ($events->hasPages())
            <div class="mt-4">{{ paginateLinks($events) }}</div>
        @endif
    </div>
</section>
@endsection
