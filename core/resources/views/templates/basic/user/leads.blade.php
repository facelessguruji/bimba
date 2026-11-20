@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
                    <h3 class="mb-0">@lang('Lead Inbox')</h3>
                    @if (!$leads->isEmpty())
                        <a href="{{ route('user.leads.export') }}" class="btn btn--sm btn--base">
                            <i class="las la-file-export"></i> @lang('Export CSV')
                        </a>
                    @endif
                </div>
                @if (!$leads->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Contact')</th>
                                            <th>@lang('Message')</th>
                                            <th>@lang('Listing')</th>
                                            <th>@lang('Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leads as $lead)
                                            <tr>
                                                <td>{{ showDateTime($lead->created_at) }}</td>
                                                <td>
                                                    <strong>{{ $lead->name }}</strong><br>
                                                    <small>{{ $lead->email }}@if ($lead->phone) · {{ $lead->phone }} @endif</small>
                                                </td>
                                                <td>{{ strLimit($lead->message, 90) }}</td>
                                                <td>{{ $lead->listing?->title ?? '-' }}</td>
                                                <td>
                                                    <form action="{{ route('user.leads.status', $lead->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                                        @csrf
                                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width:110px">
                                                            <option value="new" @selected($lead->status == 'new')>@lang('New')</option>
                                                            <option value="contacted" @selected($lead->status == 'contacted')>@lang('Contacted')</option>
                                                            <option value="closed" @selected($lead->status == 'closed')>@lang('Closed')</option>
                                                        </select>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'No leads yet. Publish a listing and customer enquiries will land here.'])
                @endif
                @if ($leads->hasPages())
                    <div class="mt-4">{{ paginateLinks($leads) }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
