@extends('Template::layouts.master')
@section('content')
    @include('Template::partials.json_ld_event')

<section class="pb-120 pt-4 lx-page">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-8">
                @if ($event->cover_image)
                <div class="lx-ev__cover" style="border-radius: 12px; overflow: hidden;">
                    <img src="{{ getImage(getFilePath('event') . '/' . $event->cover_image) }}" class="w-100" style="max-height: 420px; object-fit: cover;" alt="{{ $event->title }}">
                </div>
                @endif
                <div class="mt-4">
                    <span class="lx-ev__cat">{{ $event->category?->name }}</span>
                    <h2 class="mt-2" style="font-weight: 700; color: var(--lx-ink);">{{ $event->title }}</h2>
                    <div class="d-flex flex-wrap gap-4 mt-3 text-muted">
                        <span><i class="las la-calendar me-1"></i> {{ showDateTime($event->start_time, 'l, F d, Y') }}</span>
                        <span><i class="las la-clock me-1"></i> {{ showDateTime($event->start_time, 'g:i A') }} - {{ showDateTime($event->end_time, 'g:i A') }}</span>
                    </div>
                    <div class="mt-4">
                        <h5>@lang('About this event')</h5>
                        <p class="text-muted mt-2">{!! nl2br(e($event->description)) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="lx-panel">
                    <div class="p-4">
                        <h6 class="lx-sec-title mb-4"><i class="las la-info-circle"></i> @lang('Event Details')</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span><i class="las la-map-marker-alt me-2"></i>@lang('Venue')</span>
                                <strong>{{ $event->venue }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span><i class="las la-city me-2"></i>@lang('City')</span>
                                <strong>{{ $event->city?->name }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span><i class="las la-globe me-2"></i>@lang('Country')</span>
                                <strong>{{ $event->country?->name }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span><i class="las la-tag me-2"></i>@lang('Category')</span>
                                <strong>{{ $event->category?->name }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span><i class="las la-user me-2"></i>@lang('Organizer')</span>
                                <strong>{{ $event->user?->fullname }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span><i class="las la-users me-2"></i>@lang('Registered Attendees')</span>
                                <strong>{{ \App\Models\EventAttendee::where('event_id', $event->id)->count() }}</strong>
                            </li>
                        </ul>

                        @if (auth()->check() && auth()->id() === $event->user_id && \App\Models\EventAttendee::where('event_id', $event->id)->count())
                            <a href="{{ route('event.attendees.export', $event->id) }}" class="btn btn--base w-100 mt-3">
                                <i class="las la-file-export"></i> @lang('Export Attendees (CSV)')
                            </a>
                        @endif

                        @auth
                        <div class="mt-3">
                            @php $participation = \App\Models\EventParticipation::where('event_id', $event->id)->where('user_id', auth()->id())->first(); @endphp
                            @foreach (['follow' => 'Follow', 'interested' => 'Interested', 'joined' => 'Join / RSVP'] as $st => $label)
                                <form action="{{ route('event.participate', $event->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $st }}">
                                    <button type="submit" class="btn w-100 {{ $participation && $participation->status == $st ? 'btn--base' : 'btn-outline--base' }}">{{ __($label) }}</button>
                                </form>
                            @endforeach
                        </div>
                        @endauth
                    </div>
                </div>

                <div class="card custom--card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="las la-calendar-plus me-2"></i>@lang('Register for This Event')</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('event.register', $event->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Your Name') <span class="text--danger">*</span></label>
                                <input type="text" name="name" class="form--control" value="{{ is_string(old('name', auth()->user()->fullname ?? '')) ? old('name', auth()->user()->fullname ?? '') : '' }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Your Email') <span class="text--danger">*</span></label>
                                <input type="email" name="email" class="form--control" value="{{ is_string(old('email', auth()->user()->email ?? '')) ? old('email', auth()->user()->email ?? '') : '' }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Phone (optional)')</label>
                                <input type="text" name="phone" class="form--control" value="{{ is_string(old('phone')) ? old('phone') : '' }}">
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('What are you looking for? (optional)')</label>
                                <input type="text" name="interest" class="form--control" value="{{ is_string(old('interest')) ? old('interest') : '' }}" placeholder="@lang('Networking, services, partners...')">
                            </div>
                            <button type="submit" class="btn btn--base w-100">@lang('Register Now')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($event->event_type == 1)
    <div class="container mt-4">
        <div class="alert alert--info mb-0">
            <strong>@lang('Event Registration & Payment Information')</strong><br>
            @lang('This platform helps people discover events, share their interests, connect with others and participate in activities worldwide. Event organisers are responsible for managing their own event registrations, fees and payment arrangements. Any registration instructions, payment details or external payment links mentioned in an event description are provided directly by the event organiser. All payments are made directly to the respective organiser through their own payment methods. This platform does not collect, process or manage event registration fees or payments and is not responsible for transactions between participants and event organisers.')
        </div>
    </div>
    @endif
</section>
@endsection
