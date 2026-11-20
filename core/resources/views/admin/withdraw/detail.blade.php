@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-lg-4 col-md-4">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Withdraw Via') {{ __(@$withdrawal->method->name) }}</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime($withdrawal->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Trx Number')
                            <span class="fw-bold">{{ $withdrawal->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}"><span>@</span>{{ $withdrawal->user->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">{{ __($withdrawal->method->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($withdrawal->amount) }} {{ __(gs()->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="fw-bold">{{ showAmount($withdrawal->charge) }} {{ __(gs()->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($withdrawal->after_charge) }} {{ __(gs()->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Rate')
                            <span class="fw-bold">1 {{ __(gs()->cur_text) }} = {{ showAmount($withdrawal->rate, currencyFormat: false) }} {{ $withdrawal->currency }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payable')
                            <span class="fw-bold">{{ showAmount($withdrawal->final_amount, currencyFormat: false) }} {{ $withdrawal->currency }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $withdrawal->statusBadge @endphp
                        </li>
                        @if ($withdrawal->admin_feedback)
                            <li class="list-group-item">
                                <span class="text-black">@lang('Admin Response')</span>
                                <p class="mt-1">{{ $withdrawal->admin_feedback }}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-8">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('User Withdraw Information')</h5>

                    @if ($withdrawal->withdraw_information)
                        @foreach ($withdrawal->withdraw_information as $information)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6>{{ __(keyToTitle($information->name ?? '')) }}</h6>
                                    @if ($information->type == 'file')
                                        @if ($information->value)
                                            <a href="{{ route('admin.download.attachment', encrypt(getFilePath('withdrawVerify') . '/' . $information->value)) }}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a>
                                        @else
                                            @lang('No File')
                                        @endif
                                    @else
                                        <p>{{ $information->value }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($withdrawal->status == App\Constants\Status::WITHDRAW_PENDING)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn-outline--success btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="las la-check"></i> @lang('Approve')
                                </button>

                                <button class="btn btn-outline--danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="las la-ban"></i> @lang('Reject')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve Withdrawal Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.withdraw.approve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $withdrawal->id }}">
                    <div class="modal-body">
                        <p>@lang('Have you sent') <span class="fw-bold text--success">{{ showAmount($withdrawal->final_amount, currencyFormat: false) }} {{ $withdrawal->currency }}</span>?</p>
                        <textarea name="details" class="form-control" rows="3" placeholder="@lang('Provide the details. eg: transaction number')"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Withdrawal Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.withdraw.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $withdrawal->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Reason of Rejection')</label>
                            <textarea name="details" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
