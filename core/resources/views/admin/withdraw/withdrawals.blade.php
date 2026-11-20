@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        @isset ($successful)
            <div class="col-12">
                <div class="row gy-3 mb-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">@lang('Approved'): {{ showAmount($successful) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">@lang('Pending'): {{ showAmount($pending) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0">@lang('Rejected'): {{ showAmount($rejected) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endisset

        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Method | Transaction')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdrawals as $withdrawal)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $withdrawal->method->name ?? '-' }}</span><br>
                                            <small>{{ $withdrawal->trx }}</small>
                                        </td>
                                        <td>
                                            {{ showDateTime($withdrawal->created_at) }}<br>{{ diffForHumans($withdrawal->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $withdrawal->user->fullname ?? '-' }}</span><br>
                                            <span class="small"><a href="{{ appendQuery('search',$withdrawal->user?->username) }}"><span>@</span>{{ $withdrawal->user?->username }}</a></span>
                                        </td>
                                        <td>
                                            {{ showAmount($withdrawal->amount) }} + <span class="text--danger" title="@lang('charge')">{{ showAmount($withdrawal->charge) }}</span>
                                        </td>
                                        <td>
                                            {{ showAmount(1) }} = {{ showAmount($withdrawal->rate, currencyFormat: false) }} {{ __($withdrawal->currency) }}
                                            <br><strong>{{ showAmount($withdrawal->final_amount, currencyFormat: false) }} {{ __($withdrawal->currency) }}</strong>
                                        </td>
                                        <td>@php echo $withdrawal->statusBadge @endphp</td>
                                        <td>
                                            <a href="{{ route('admin.withdraw.data.details', $withdrawal->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($withdrawals->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($withdrawals) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' placeholder='Username / TRX' />
@endpush
