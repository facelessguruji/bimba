@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.lead.index') }}" class="btn btn-sm @if (!request()->status) btn--primary @else btn-outline--primary @endif">
                                @lang('All') <span class="badge badge--dark ms-1">{{ $totals['all'] }}</span>
                            </a>
                            <a href="{{ route('admin.lead.index', ['status' => 'new']) }}" class="btn btn-sm @if (request()->status == 'new') btn--warning @else btn-outline--warning @endif">
                                @lang('New') <span class="badge badge--dark ms-1">{{ $totals['new'] }}</span>
                            </a>
                            <a href="{{ route('admin.lead.index', ['status' => 'contacted']) }}" class="btn btn-sm @if (request()->status == 'contacted') btn--info @else btn-outline--info @endif">
                                @lang('Contacted') <span class="badge badge--dark ms-1">{{ $totals['contacted'] }}</span>
                            </a>
                            <a href="{{ route('admin.lead.index', ['status' => 'closed']) }}" class="btn btn-sm @if (request()->status == 'closed') btn--success @else btn-outline--success @endif">
                                @lang('Closed') <span class="badge badge--dark ms-1">{{ $totals['closed'] }}</span>
                            </a>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.lead.export', request()->only(['status'])) }}" class="btn btn-sm btn-outline--success">
                                <i class="las la-file-export"></i> @lang('Export CSV')
                            </a>
                            <button type="button" class="btn btn-sm btn--primary" data-bs-toggle="modal" data-bs-target="#importLeadsModal">
                                <i class="las la-file-import"></i> @lang('Import CSV')
                            </button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.lead.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">@lang('All statuses')</option>
                                @foreach (['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $value => $label)
                                    <option value="{{ $value }}" @selected(request()->status == $value)>@lang($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn--secondary w-100"><i class="las la-filter"></i> @lang('Apply Filters')</button>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Contact')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Routed To')</th>
                                    <th>@lang('Listing')</th>
                                    <th>@lang('Source')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leads as $lead)
                                    <tr>
                                        <td>{{ showDateTime($lead->created_at) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $lead->name }}</span><br>
                                            <small>{{ $lead->email }}@if ($lead->phone) · {{ $lead->phone }} @endif</small>
                                        </td>
                                        <td>{{ strLimit($lead->message, 60) }}</td>
                                        <td>{{ $lead->owner?->username ?? '-' }}</td>
                                        <td>{{ strLimit($lead->listing?->title ?? '-', 30) }}</td>
                                        <td>
                                            @if ($lead->source == 'import')
                                                <span class="badge badge--info">@lang('Imported')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Public Form')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->status == 'new')
                                                <span class="badge badge--warning">@lang('New')</span>
                                            @elseif ($lead->status == 'contacted')
                                                <span class="badge badge--info">@lang('Contacted')</span>
                                            @else
                                                <span class="badge badge--success">@lang('Closed')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.lead.details', $lead->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i>
                                                </a>
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
                @if ($leads->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($leads) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="importLeadsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Import Leads from CSV')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.lead.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">@lang('CSV file') <span class="text--danger">*</span></label>
                            <input type="file" name="import_file" accept=".csv,text/csv" class="form-control" required>
                        </div>
                        <div class="alert alert-primary" role="alert">
                            <p class="mb-1"><i class="las la-info-circle"></i> @lang('Required columns'): <b>listing_id</b>, <b>name</b>, <b>email</b></p>
                            <p class="mb-0"><i class="las la-info-circle"></i> @lang('Optional columns'): <b>phone</b>, <b>message</b>. @lang('Each lead is routed to the listing owner automatically.')</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Import')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder='Name / Email / Phone / Listing' />
@endpush
