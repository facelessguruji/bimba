@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">

                @if ($user->activePlan)
                    <div class="col-xxl-3 col-sm-6 col-xl-4">
                        <x-widget style="7" link="" icon="la la-project-diagram"
                            title="Expired on: {{ showDateTime($user->activePlan->expired_on, 'd, M Y') }}"
                            value="Current Plan: {{ $user->activePlan->plan->title }}" bg="success" type="2" />
                    </div>
                @else
                    <div class="col-xxl-3 col-sm-6 col-xl-4">
                        <x-widget style="7" link="" icon="la la-project-diagram" title="Current Plan" value="N/A" bg="danger"
                            type="2" />
                    </div>
                @endif


                <div class="col-xxl-3 col-sm-6 col-xl-4">
                    <x-widget style="7" link="{{ route('admin.deposit.list', $user->id) }}" title="Payments" icon="las la-wallet"
                        value="{{ showAmount($totalDeposit) }}" bg="18" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6 col-xl-4">
                    <x-widget style="7" link="{{ route('admin.listing.index') }}?user_id={{ $user->id }}" title="Listings" icon="las la-building"
                        value="{{ $totalListings }}" bg="11" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6 col-xl-4">
                    <x-widget style="7" link="{{ route('admin.listing.approved') }}?user_id={{ $user->id }}" title="Approved Listings"
                        icon="la la-check" value="{{ $listing['total_approved_listings'] }}" bg="7" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6 col-xl-4">
                    <x-widget style="7" link="{{ route('admin.listing.pending') }}?user_id={{ $user->id }}" title="Pending Listings"
                        icon="fas fa-sync" value="{{ $listing['total_pending_listings'] }}" bg="6" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6 col-xl-4">
                    <x-widget style="7" link="{{ route('admin.listing.disabled') }}?user_id={{ $user->id }}" title="Disabled Listings" icon="las la-ban"
                        value="{{ $listing['total_disabled_listings'] }}" bg="9" type="2" />
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">

                <div class="flex-fill">
                    <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}" class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.users.notification.log', $user->id) }}" class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>


                <div class="flex-fill">
                    @if ($user->status == Status::USER_ACTIVE)
                        <div class="d-flex align-items-center justify-content-center gap-2 py-3">
                            <label class="lgx-switch userBanSwitch lgx-switch-labeled" title="@lang('Banning blocks dashboard access')">
                                <input type="checkbox" aria-label="@lang('Ban or unban this user')" checked>
                                        <span class="lgx-switch__track"></span>
                                <span class="lgx-switch-state"><span class="st-on">@lang('Active')</span><span class="st-off">@lang('Banned')</span></span>
                            </label>
                            <span>@lang('Ban User')</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center gap-2 py-3">
                            <label class="lgx-switch userBanSwitch lgx-switch-labeled" title="@lang('Unbanning restores dashboard access')">
                                <input type="checkbox" aria-label="@lang('Ban or unban this user')">
                                        <span class="lgx-switch__track"></span>
                                <span class="lgx-switch-state"><span class="st-on">@lang('Active')</span><span class="st-off">@lang('Banned')</span></span>
                            </label>
                            <span>@lang('Unban User')</span>
                        </div>
                    @endif
                </div>
            </div>


            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{ $user->fullname }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required value="{{ $user->firstname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required value="{{ $user->lastname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    <input class="form-control" type="email" name="email" value="{{ $user->email }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile"
                                            class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{ $user->address }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{ $user->city }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{ $user->state }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" value="{{ $user->zip }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}"
                                                @selected($user->country_code == $key)>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Email verification status')">
                                        <input type="checkbox" name="ev" @if ($user->ev) checked @endif>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Verified')</span><span class="st-off">@lang('Unverified')</span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Mobile verification status')">
                                        <input type="checkbox" name="sv" @if ($user->sv) checked @endif>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Verified')</span><span class="st-off">@lang('Unverified')</span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($user->status == Status::USER_ACTIVE)
                            @lang('Ban User')
                        @else
                            @lang('Unban User')
                        @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($user->status == Status::USER_ACTIVE)
                            <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $user->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($user->status == Status::USER_ACTIVE)
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Balance Adjust --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Balance Adjust') - @lang('Current'): {{ showAmount($user->balance) }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.balance', $user->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Operation')</label>
                                    <select name="act" class="form-control">
                                        <option value="add">@lang('Add')</option>
                                        <option value="sub">@lang('Subtract')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Amount')</label>
                                    <input class="form-control" type="number" name="amount" step="any" min="0.00000001" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Remark')</label>
                                    <input class="form-control" type="text" name="remark" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.users.team', $user->username) }}" class="btn btn-sm btn-outline--primary"><i class="las la-network-wired"></i>@lang('Team')</a>
    <a href="{{ route('admin.users.login.confirm', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--primary"><i
        class="las la-sign-in-alt"></i>@lang('Login as User')</a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            // Ban/unban switch opens the reason modal; the POST still goes
            // through the modal form (route admin.users.status). If the
            // modal is dismissed without submitting, revert the switch.
            let userStatusSubmitted = false;
            $('#userStatusModal form').on('submit', function() {
                userStatusSubmitted = true;
            });
            $('.userBanSwitch input').on('change', function() {
                userStatusSubmitted = false;
                $('#userStatusModal').modal('show');
            });
            $('#userStatusModal').on('hidden.bs.modal', function() {
                if (!userStatusSubmitted) {
                    $('.userBanSwitch input').prop('checked', @if($user->status == Status::USER_ACTIVE) true @else false @endif);
                }
            });

        })(jQuery);
    </script>
@endpush
