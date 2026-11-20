@extends('Template::layouts.user')
@section('content')
    <div class="container py-120">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <h4 class="mb-0">@lang('Transactions')</h4>
                    <form class="d-flex gap-2">
                        <input type="search" name="search" class="form-control form--control form-control-sm" value="{{ request()->search }}" placeholder="@lang('Search transaction...')">
                        <button class="btn btn--base btn--sm"><i class="las la-search"></i></button>
                    </form>
                </div>

                <div class="show-filter mb-3">
                    <button type="button" class="btn btn--base showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
                </div>
                <div class="card responsive-filter-card mb-4 @if (!request()->search && !request()->trx_type && !request()->remark) d-none @endif">
                    <div class="card-body">
                        <form>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="flex-grow-1">
                                    <label class="form--label">@lang('Type')</label>
                                    <select name="trx_type" class="form--control">
                                        <option value="">@lang('All')</option>
                                        <option value="+" @selected(request()->trx_type == '+')>@lang('Income')</option>
                                        <option value="-" @selected(request()->trx_type == '-')>@lang('Expense')</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form--label">@lang('Remark')</label>
                                    <select name="remark" class="form--control">
                                        <option value="">@lang('All')</option>
                                        @foreach ($remarks as $remark)
                                            <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="align-self-end">
                                    <button class="btn btn--base w-100">@lang('Filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if (!$transactions->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg mb-0">
                                    <thead>
                                        <tr>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Transaction')</th>
                                            <th>@lang('Type')</th>
                                            <th class="text-end">@lang('Amount')</th>
                                            <th class="text-end">@lang('Post Balance')</th>
                                            <th>@lang('Details')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td>{{ showDateTime($transaction->created_at) }}</td>
                                                <td>
                                                    <span class="font-monospace" style="font-size: .8125rem;">{{ $transaction->trx }}</span><br>
                                                    <small class="text-muted">{{ __(keyToTitle($transaction->remark)) }}</small>
                                                </td>
                                                <td>
                                                    @if ($transaction->trx_type == '+')
                                                        <span class="badge badge--success"><i class="las la-arrow-up me-1"></i>@lang('Income')</span>
                                                    @elseif($transaction->trx_type == '-')
                                                        <span class="badge badge--danger"><i class="las la-arrow-down me-1"></i>@lang('Expense')</span>
                                                    @else
                                                        <span class="badge badge--dark">@lang('Info')</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($transaction->trx_type == '+')
                                                        <strong class="text--success">+{{ showAmount($transaction->amount, currencyFormat: false) }}</strong>
                                                    @elseif($transaction->trx_type == '-')
                                                        <strong class="text--danger">-{{ showAmount($transaction->amount, currencyFormat: false) }}</strong>
                                                    @else
                                                        <strong>{{ showAmount($transaction->amount, currencyFormat: false) }}</strong>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ showAmount($transaction->post_balance, currencyFormat: false) }}</td>
                                                <td>{{ strLimit(__($transaction->details), 50) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="las la-receipt la-4x text-muted d-block mb-3"></i>
                        <h5 class="text-muted">@lang('No transactions yet')</h5>
                        <p class="text-muted">@lang('Your transaction history will appear here')</p>
                    </div>
                @endif

                @if ($transactions->hasPages())
                    <div class="mt-4">{{ paginateLinks($transactions) }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
