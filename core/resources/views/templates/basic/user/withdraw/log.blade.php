@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h4 class="mb-1">@lang("Withdraw Log")</h4>
                        <p class="text-muted mb-0">@lang("Track your payout requests")</p>
                    </div>
                    <form>
                        <div class="input-group">
                            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                                placeholder="@lang('Search by transaction')">
                            <button class="input-group-text text-white btn--base">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                @if (!$withdraws->isEmpty())
                    <div class="card custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table custom--table table--responsive--lg">
                                    <thead>
                                        <tr>
                                            <th>@lang('Method | Transaction')</th>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Conversion')</th>
                                            <th>@lang('Status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($withdraws as $withdraw)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text--base">{{ $withdraw->method->name ?? '-' }}</span><br>
                                                    <small>{{ $withdraw->trx }}</small>
                                                </td>
                                                <td>
                                                    {{ showDateTime($withdraw->created_at) }}<br>
                                                    <small>{{ diffForHumans($withdraw->created_at) }}</small>
                                                </td>
                                                <td>
                                                    {{ showAmount($withdraw->amount) }} + <span class="text--danger">{{ showAmount($withdraw->charge) }}</span>
                                                </td>
                                                <td>
                                                    {{ showAmount(1) }} = {{ showAmount($withdraw->rate, currencyFormat: false) }} {{ __($withdraw->currency) }}
                                                    <br><strong>{{ showAmount($withdraw->final_amount, currencyFormat: false) }} {{ __($withdraw->currency) }}</strong>
                                                </td>
                                                <td>@php echo $withdraw->statusBadge @endphp
                                                    @if ($withdraw->status == App\Constants\Status::WITHDRAW_CANCEL && $withdraw->admin_feedback)
                                                        <br><small>@lang('Feedback'): {{ $withdraw->admin_feedback }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($withdraw->status == App\Constants\Status::WITHDRAW_PENDING)
                                                        <form action="{{ route('user.withdraw.cancel', $withdraw->trx) }}" method="POST" class="d-inline"
                                                              onclick="return confirm('@lang('Cancel this withdrawal and return the amount to your balance?')')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Cancel')</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">&mdash;</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'No Withdrawal Found'])
                @endif
                @if ($withdraws->hasPages())
                    <div class="mt-4">{{ paginateLinks($withdraws) }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
