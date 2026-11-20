@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Lead Details')</h5>
                    <span>
                        @if ($lead->status == 'new')
                            <span class="badge badge--warning">@lang('New')</span>
                        @elseif ($lead->status == 'contacted')
                            <span class="badge badge--info">@lang('Contacted')</span>
                        @else
                            <span class="badge badge--success">@lang('Closed')</span>
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between"><span>@lang('Name')</span><span>{{ $lead->name }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>@lang('Email')</span><span>{{ $lead->email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>@lang('Phone')</span><span>{{ $lead->phone ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>@lang('Routed To')</span><span>{{ $lead->owner?->fullname ?? '-' }} {{ $lead->owner?->username }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>@lang('Listing')</span><span>{{ $lead->listing?->title ?? '-' }}</span></li>
                    </ul>

                    <h6 class="mt-4 mb-2">@lang('Message')</h6>
                    <p>{{ $lead->message }}</p>

                    @if ($matches)
                        <h6 class="mt-4 mb-2">@lang('AI Matches')</h6>
                        <ul class="list-group">
                            @foreach ($matches as $match)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $match['title'] ?? 'Listing #' . ($match['id'] ?? '?') }}</span>
                                    <span class="badge badge--primary">@lang('Score'): {{ $match['score'] ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <form action="{{ route('admin.lead.status', $lead->id) }}" method="post" class="mt-4 d-flex gap-2">
                        @csrf
                        <select name="status" class="form-control">
                            <option value="new" @selected($lead->status == 'new')>@lang('New')</option>
                            <option value="contacted" @selected($lead->status == 'contacted')>@lang('Contacted')</option>
                            <option value="closed" @selected($lead->status == 'closed')>@lang('Closed')</option>
                        </select>
                        <button type="submit" class="btn btn--primary px-4">@lang('Update')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.lead.index') }}" />
@endpush
