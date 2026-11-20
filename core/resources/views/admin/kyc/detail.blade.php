@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('KYC Details') - {{ $user->fullname }}</h5>
                    <span>
                        @if ($user->kv == App\Constants\Status::KYC_VERIFIED)
                            <span class="badge badge--success">@lang('Verified')</span>
                        @elseif ($user->kv == App\Constants\Status::KYC_PENDING)
                            <span class="badge badge--warning">@lang('Pending')</span>
                        @else
                            <span class="badge badge--danger">@lang('Unverified')</span>
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @if ($user->kyc_data)
                            @foreach ($user->kyc_data as $kycData)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ __(keyToTitle($kycData->name ?? '')) }}</span>
                                    <span>
                                        @if ($kycData->type == 'file')
                                            <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $kycData->value)) }}"><i class="la la-file"></i> @lang('Attachment')</a>
                                        @elseif (is_array($kycData->value))
                                            {{-- checkbox/radio multi-value answers arrive as arrays - join them safely --}}
                                            {{ implode(', ', $kycData->value) }}
                                        @else
                                            {{ $kycData->value }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item text-muted">@lang('No KYC data submitted')</li>
                        @endif
                    </ul>

                    @if ($user->kyc_rejection_reason)
                        <div class="mt-3 alert alert-danger">
                            <strong>@lang('Last rejection reason')</strong>: {{ $user->kyc_rejection_reason }}
                        </div>
                    @endif

                    @if ($user->kv == App\Constants\Status::KYC_PENDING)
                        <form action="{{ route('admin.kyc.approve', $user->id) }}" method="post" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn--success w-100 h-45">@lang('Approve KYC')</button>
                        </form>
                        <form action="{{ route('admin.kyc.reject', $user->id) }}" method="post" class="mt-3">
                            @csrf
                            <label class="fw-bold">@lang('Rejection Reason (required)')</label>
                            <textarea name="reason" rows="2" class="form-control mb-3" required></textarea>
                            <button type="submit" class="btn btn--danger w-100 h-45">@lang('Reject KYC')</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.kyc.all') }}" />
@endpush
