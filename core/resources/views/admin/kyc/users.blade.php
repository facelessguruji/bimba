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
                                    <th>@lang('User')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('KYC Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $user->fullname }}</span><br>
                                            <span class="small">{{ $user->username }}</span>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->kv == App\Constants\Status::KYC_VERIFIED)
                                                <span class="badge badge--success">@lang('Verified')</span>
                                            @elseif ($user->kv == App\Constants\Status::KYC_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Unverified')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.kyc.details', $user->id) }}" class="btn btn-sm btn-outline--primary">
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
                @if ($users->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($users) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder='Username' />
@endpush
