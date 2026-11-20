@extends('Template::layouts.user')
@section('content')
    <div class="py-120">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="mb-1">@lang("Payment History")</h4>
                    <p class="text-muted mb-0">@lang("Track your deposit transactions")</p>
                </div>
                <form>
                    <div class="input-group">
                        <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                            placeholder="@lang('Search by transactions')">
                        <button class="input-group-text text-white btn--base">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    @if (!$deposits->isEmpty())
                        <div class="card custom--card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table custom--table table--responsive--lg">
                                        <thead>
                                            <tr>
                                                <th>@lang('Gateway | Transaction')</th>
                                                <th class="text-end text-md-center">@lang('Initiated')</th>
                                                <th class="text-end text-md-center">@lang('Amount')</th>
                                                <th class="text-end text-md-center">@lang('Conversion')</th>
                                                <th class="text-end text-md-center">@lang('Status')</th>
                                                <th>@lang('Details')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deposits as $deposit)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <span class="fw-bold">
                                                                <span class="text--base">
                                                                    {{ __($deposit->gateway?->name ?? ($deposit->method_code == 0 ? 'E-Pin' : '-')) }}
                                                                </span>
                                                            </span>
                                                            <br>
                                                            <small> {{ $deposit->trx }} </small>
                                                        </div>
                                                    </td>

                                                    <td class="text-end text-md-center">
                                                        <div>
                                                            {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                                        </div>
                                                    </td>
                                                    <td class="text-end text-md-center">
                                                        <div>
                                                            {{ showAmount($deposit->amount) }} + <span class="text--danger" data-bs-toggle="tooltip"
                                                                title="@lang('Processing Charge')">{{ showAmount($deposit->charge) }} </span>
                                                            <br>
                                                            <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                                                {{ showAmount($deposit->amount + $deposit->charge) }}
                                                            </strong>
                                                        </div>
                                                    </td>
                                                    <td class="text-end text-md-center">
                                                        <div>
                                                            {{ showAmount(1) }} = {{ showAmount($deposit->rate, currencyFormat: false) }}
                                                            {{ __($deposit->method_currency) }}
                                                            <br>
                                                            <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                                {{ __($deposit->method_currency) }}</strong>
                                                        </div>
                                                    </td>
                                                    <td class="text-end text-md-center">
                                                        @php echo $deposit->statusBadge @endphp
                                                    </td>
                                                    @php
                                                        $details = [];
                                                        if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                                            foreach (isset($deposit->detail) ? $deposit->detail : [] as $key => $info) {
                                                                $details[] = $info;
                                                                if ($info->type == 'file') {
                                                                    $details[$key]->value = route(
                                                                        'user.download.attachment',
                                                                        encrypt(getFilePath('verify') . '/' . $info->value),
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    @endphp

                                                    <td>
                                                        @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                                            <a href="javascript:void(0)" class="btn btn--base btn--sm detailBtn"
                                                                data-info="{{ json_encode($details) }}"
                                                                @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                                                <i class="fas fa-desktop"></i>
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn--success btn--sm" data-bs-toggle="tooltip"
                                                                title="@lang('Automatically processed')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
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
                        @include('Template::partials.data_not_found', ['data' => 'No Deposit Found'])
                    @endif

                    @if ($deposits->hasPages())
                        <div class="mt-4">
                            {{ paginateLinks($deposits) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
