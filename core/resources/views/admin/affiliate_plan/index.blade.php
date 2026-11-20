@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1">@lang('Affiliate Plans: Commission Configuration')</h5>
                    <p class="text-muted mb-0">@lang('Manages the ten-level referral commission structure. This is separate from Manage Plans, which controls the membership/product pricing tiers.')</p>
                </div>
            </div>
        </div>
    </div>

            <form action="{{ route('admin.affiliate.plan.update') }}" method="POST">
                @csrf

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">@lang('Commission Settings')</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Company Allocation %')</label>
                                    <input type="number" name="affiliate_company_percent" class="form-control" min="0" max="100" step="0.01" value="{{ gs('affiliate_company_percent') }}" required>
                                    <small class="text-muted">@lang('Revenue kept by the platform (default 60%)')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Affiliate Commission Pool %')</label>
                                    <input type="number" name="affiliate_pool_percent" class="form-control" min="0" max="100" step="0.01" value="{{ gs('affiliate_pool_percent') }}" required>
                                    <small class="text-muted">@lang('Distributed across the ten levels (default 40%)')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Extended-Team Royalty %')</label>
                                    <input type="number" name="affiliate_royalty_percent" class="form-control" min="0" max="100" step="0.01" value="{{ gs('affiliate_royalty_percent') }}" required>
                                    <small class="text-muted">@lang('Reserved for future royalty layers (default 0%)')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-1">@lang('Level Settings: Ten-Level Weights')</h5>
                        <p class="text-muted">@lang('Level Commission = Pool × (Weight ÷ Total Weights). Level 1 is the buyer’s direct sponsor.')</p>
                        <div class="row">
                            @php $weights = explode(',', gs('affiliate_level_weights')); @endphp
                            @foreach (range(1, 10) as $level)
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('Level {{ $level }} Weight')</label>
                                        <input type="number" name="affiliate_level_weights[]" class="form-control" min="0" step="1"
                                            value="{{ $weights[$level - 1] ?? 0 }}" data-weight-input>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">@lang('Total weight: ') <span data-weight-total>{{ array_sum($weights) }}</span></small>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">@lang('Distribution & Referral Settings')</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Missing Referrer Handling')</label>
                                    <p class="text-muted mb-0">@lang('If a referrer does not exist at a level, that level’s allocation remains unallocated (kept by the company). No redistribution.')</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Duplicate Commission Prevention')</label>
                                    <p class="text-muted mb-0">@lang('Automatic: one commission record per payment transaction per level. Renewals generate zero commission.')</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Referral Method (automatic placement)')</label>
                                    <select name="auto_refer_method" class="form-control">
                                        <option value="round_robin" @selected(gs('auto_refer_method') == 'round_robin')>@lang('Round Robin')</option>
                                        <option value="random" @selected(gs('auto_refer_method') == 'random')>@lang('Random')</option>
                                        <option value="weakest_team" @selected(gs('auto_refer_method') == 'weakest_team')>@lang('Weakest Team (smallest network)')</option>
                                        <option value="top_recruiter" @selected(gs('auto_refer_method') == 'top_recruiter')>@lang('Top Recruiter (most directs)')</option>
                                        <option value="fixed" @selected(gs('auto_refer_method') == 'fixed')>@lang('Fixed Account')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Fixed Fallback User ID')</label>
                                    <input type="number" name="auto_refer_fixed_user_id" class="form-control" min="0" value="{{ gs('auto_refer_fixed_user_id') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Activation Rule: receiver must hold an active plan for referral commissions')</label>
                                    <label class="lgx-switch" title="@lang('Activation Rule')">
                                        <input type="checkbox" name="activation_rule" value="1" @checked(gs('activation_rule'))>
                                        <span class="lgx-switch__track"></span>
                                    </label>
                                    <small class="text-muted d-block mt-1">@lang('Monoline earnings are unaffected by this rule.')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Save Affiliate Plan Configuration')</button>
            </form>
        </div>
    </div>

@push('script')
    <script>
        "use strict";
        // live total-weight indicator
        document.addEventListener('input', function (e) {
            if (!e.target.matches('[data-weight-input]')) return;
            let total = 0;
            document.querySelectorAll('[data-weight-input]').forEach(i => total += Number(i.value) || 0);
            const t = document.querySelector('[data-weight-total]');
            if (t) t.textContent = total;
        });
    </script>
@endpush
@endsection
