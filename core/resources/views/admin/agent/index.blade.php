@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Agent Settings')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.agent.settings') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Seller Bonus Percent')</label>
                            <div class="input-group">
                                <input class="form-control" name="epin_seller_bonus_percent" type="number" step="0.01" min="0" max="10" value="{{ gs('epin_seller_bonus_percent') }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">@lang('Paid to the pin issuer (when they are an agent) out of a plan purchase paid with their e-pin. Range 0 to 10 percent. A bonus of zero disables the payout.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Volume Discount Tiers')</label>
                            <input class="form-control" name="epin_agent_discounts" type="text" value="{{ gs('epin_agent_discounts') }}" placeholder="50:3,100:5,500:8">
                            <small class="text-muted">@lang('Format: qty:percent, comma separated, ascending quantities, percent 0 to 50. Agents buying at least qty pins in one order pay that percent less instead of the generation charge. Leave empty for no discounts.')</small>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Save Settings')</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Grant Agent Status')</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" id="grantAgentForm">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Member ID')</label>
                            <input class="form-control" name="user_id" type="number" min="1" placeholder="123" required>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Grant Agent Status')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('E-Pin Agents')</h5>
                    <p class="text-muted mb-0 mt-2"><small>@lang('Agents buy e-pins at volume prices and earn the seller bonus when a member activates a plan with one of their pins.')</small></p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Member')</th>
                                    <th>@lang('Pins Generated')</th>
                                    <th>@lang('Used')</th>
                                    <th>@lang('Unused')</th>
                                    <th>@lang('Generated Volume')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($agents as $agent)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $agent->id) }}">{{ $agent->username ?: $agent->fullname }}</a>
                                            <span class="d-block text-muted"><small>#{{ $agent->id }}</small></span>
                                        </td>
                                        <td>{{ $agent->total_pins }}</td>
                                        <td>{{ $agent->used_pins }}</td>
                                        <td>{{ $agent->unused_pins }}</td>
                                        <td>{{ showAmount($agent->pins_volume ?? 0) }}</td>
                                        <td>
                                            @include('admin.partials.switch', [
                                                'action'  => route('admin.agent.toggle', $agent->id),
                                                'checked' => true,
                                                'confirm' => __('Are you sure to revoke agent status?'),
                                                'title'   => __('Agent status enabled'),
                                            ])
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
                @if ($agents->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($agents) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            // submit the grant form to the toggle route for the entered id
            $(document).on('submit', '#grantAgentForm', function(e) {
                e.preventDefault();
                var form = this;
                var url = '{{ route('admin.agent.toggle', ':id') }}'.replace(':id', form.elements.user_id.value);
                var f = document.createElement('form');
                f.action = url;
                f.method = 'post';
                var token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                f.appendChild(token);
                document.body.appendChild(f);
                f.submit();
            });
        })(jQuery);
    </script>
@endpush
