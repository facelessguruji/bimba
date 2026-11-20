@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center mb-none-30">
        <div class="col-xl-7 col-lg-9">
            <div class="card">
                <form action="{{ route('admin.users.login', $user->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="mb-1">@lang('Confirm Impersonation')</h3>
                            <p class="text-muted mb-0">@lang('You are about to log in to a member account on behalf of this member.')</p>
                        </div>

                        <div class="row justify-content-center mb-none-15">
                            <div class="col-md-6 mb-15">
                                <div class="form-group">
                                    <label>@lang('Member')</label>
                                    <input type="text" class="form-control" value="{{ $user->firstname }} {{ $user->lastname }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-15">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-15">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-15">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    @if ($user->status == Status::USER_ACTIVE)
                                        <input type="text" class="form-control text--success" value="@lang('Active')" readonly>
                                    @else
                                        <input type="text" class="form-control text--danger" value="@lang('Banned')" readonly>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0">
                            <h6 class="mb-1">@lang('Please note')</h6>
                            <p class="mb-0">@lang('Every action you take will be attributed to this member. This impersonation is logged in the admin notifications and the member\'s login history.')</p>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn--secondary w-100 h-45 flex-fill">@lang('Cancel')</a>
                            <button type="submit" class="btn btn--primary w-100 h-45 flex-fill"><i class="las la-sign-in-alt"></i>@lang('Login as User')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
