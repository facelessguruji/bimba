@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Transaction')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ showDateTime($transaction->created_at) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $transaction->user?->fullname ?? '-' }}</span><br>
                                            <span class="small">{{ $transaction->user?->username }}</span>
                                        </td>
                                        <td>{{ $transaction->trx }}<br>
                                            <small>{{ __(keyToTitle($transaction->remark)) }}</small>
                                        </td>
                                        <td>
                                            @if ($transaction->trx_type == '+')
                                                <span class="badge badge--success">@lang('Plus')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Minus')</span>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($transaction->amount) }}<br>
                                            <small>@lang('Charge'): {{ showAmount($transaction->charge) }}</small>
                                        </td>
                                        <td>{{ showAmount($transaction->post_balance) }}</td>
                                        <td>{{ __($transaction->details) }}</td>
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
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($transactions) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' placeholder='User / TRX / Details' />
@endpush
