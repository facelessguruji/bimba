@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Global Auto-Referral & Economy Settings')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.setting.auto-refer') }}" method="post">
                        @csrf
                        <h5 class="mb-3 mt-2">@lang('Global Auto-Referral')</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Enable Auto-Referral')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Enable Auto-Referral')">
                                        <input type="checkbox" name="auto_refer_enable" @checked(gs('auto_refer_enable'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Eligibility Requires an Active Plan')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Eligibility Requires an Active Plan')">
                                        <input type="checkbox" name="auto_refer_require_active_plan" @checked(gs('auto_refer_require_active_plan'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                    <small class="text-muted">@lang('Currently eligible members'): {{ $eligiblePool }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Eligibility Requires a Verified Email')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Eligibility Requires a Verified Email')">
                                        <input type="checkbox" name="auto_refer_require_email" @checked(gs('auto_refer_require_email'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Assignment Method')</label>
                                    <select name="auto_refer_method" class="form-control">
                                        <option value="round_robin" @selected(gs('auto_refer_method') == 'round_robin')>@lang('Round Robin (eligible members)')</option>
                                        <option value="random" @selected(gs('auto_refer_method') == 'random')>@lang('Random (eligible members)')</option>
                                        <option value="weakest_team" @selected(gs('auto_refer_method') == 'weakest_team')>@lang('Weakest Team (smallest network)')</option>
                                        <option value="top_recruiter" @selected(gs('auto_refer_method') == 'top_recruiter')>@lang('Top Recruiter (most directs)')</option>
                                        <option value="fixed" @selected(gs('auto_refer_method') == 'fixed')>@lang('Fixed Account')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Fixed Fallback User ID') <i class="la la-info-circle" title="@lang('Used by the fixed method and as the fallback when no eligible member exists')"></i></label>
                                    <input type="number" name="auto_refer_fixed_user_id" class="form-control" min="0"
                                        value="{{ gs('auto_refer_fixed_user_id') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Rotation Pointer (last user id)')</label>
                                    <input type="number" name="auto_refer_last_user_id_display" class="form-control"
                                        value="{{ gs('auto_refer_last_user_id') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">@lang('Economy Rules')</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="d-block">@lang('E-Pin Module')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('E-Pin Module')">
                                        <input type="checkbox" name="epin" @checked(gs('epin'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('E-Pin Generation Charge (%)')</label>
                                    <input type="number" name="epin_charge" class="form-control" min="0" max="100" step="any"
                                        value="{{ gs('epin_charge') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="d-block">@lang('Ranking Module')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Ranking Module')">
                                        <input type="checkbox" name="user_ranking" @checked(gs('user_ranking'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Member-to-Member Balance Transfer')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Member-to-Member Balance Transfer')">
                                        <input type="checkbox" name="balance_transfer" @checked(gs('balance_transfer'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                        <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
