@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Method')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Limit')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($methods as $method)
                                    <tr>
                                        <td>{{ $method->name }}</td>
                                        <td>{{ $method->currency }}</td>
                                        <td>1 {{ __(gs()->cur_text) }} = {{ showAmount($method->rate, currencyFormat: false) }} {{ $method->currency }}</td>
                                        <td>{{ showAmount($method->min_limit) }} - {{ showAmount($method->max_limit) }}</td>
                                        <td>{{ showAmount($method->fixed_charge) }} + {{ showAmount($method->percent_charge, currencyFormat: false) }}%</td>
                                        <td> @php echo $method->statusBadge ?? ($method->status ? '<span class="badge badge--success">'.__('Enabled').'</span>' : '<span class="badge badge--danger">'.__('Disabled').'</span>') @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary ms-1" href="{{ route('admin.withdraw.method.edit', $method->id) }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </a>
                                                @include('admin.partials.switch', [
                                                    'action'  => route('admin.withdraw.method.status', $method->id),
                                                    'checked' => $method->status,
                                                    'confirm' => $method->status ? __('Are you sure to disable this method?') : __('Are you sure to enable this method?'),
                                                    'title'   => $method->status ? __('Enabled') : __('Disabled'),
                                                ])
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
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.withdraw.method.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush
