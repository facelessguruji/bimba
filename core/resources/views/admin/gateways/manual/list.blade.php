@extends('admin.layouts.app')
@section('panel')
@push('topBar')
@include('admin.gateways.top_bar')
@endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                            <tr>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($gateways as $gateway)
                                <tr>
                                    <td>
                                        {{__($gateway->name)}}
                                    </td>

                                    <td>
                                        @php
                                            echo $gateway->statusBadge
                                        @endphp
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.gateway.manual.edit', $gateway->alias) }}" class="btn btn-sm btn-outline--primary editGatewayBtn">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </a>

                                            @include('admin.partials.switch', [
                                                'action'  => route('admin.gateway.manual.status', $gateway->id),
                                                'checked' => $gateway->status == Status::ENABLE,
                                                'confirm' => $gateway->status == Status::ENABLE ? __('Are you sure to disable this gateway?') : __('Are you sure to enable this gateway?'),
                                                'title'   => $gateway->status == Status::ENABLE ? __('Enabled') : __('Disabled'),
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
                        </table><!-- table end -->
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="input-group w-auto search-form">
        <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
        <button class="btn btn--primary input-group-text"><i class="fas fa-search"></i></button>
    </div>
    <a class="btn btn-outline--primary" href="{{ route('admin.gateway.manual.create') }}"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush
