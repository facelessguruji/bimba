@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('AI (GPT) Settings')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ai.settings') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-block">@lang('Enable AI Features')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Enable AI Features')">
                                        <input type="checkbox" name="ai_enable" @checked(gs('ai_enable'))>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                    <small class="text-muted">@lang('Without a key, every AI UI stays hidden and the platform is fully functional.')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('API Key')</label>
                                    <input type="text" name="ai_api_key" class="form-control"
                                        value="{{ gs('ai_api_key') ? '••••••••' . substr(gs('ai_api_key'), -4) : '' }}"
                                        placeholder="@lang('Leave empty to keep the current key')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('API Base URL (OpenAI-compatible)')</label>
                                    <input type="url" name="ai_base_url" class="form-control" value="{{ gs('ai_base_url') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Model')</label>
                                    <input type="text" name="ai_model" class="form-control" value="{{ gs('ai_model') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Monthly Token Cap') <i class="la la-info-circle" title="@lang('0 = unlimited')"></i></label>
                                    <input type="number" name="ai_monthly_cap" class="form-control" min="0" value="{{ gs('ai_monthly_cap') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>@lang('Feature Toggles')</label>
                            </div>
                            @php
                                $aiFeatures = App\Lib\AiService::features();
                            @endphp
                            @foreach (['listing_writer' => 'AI Listing Writer', 'seo' => 'AI SEO Helper', 'lead_match' => 'AI Lead Matching', 'review_summary' => 'AI Review Summaries'] as $key => $label)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="d-block">{{ __($label) }}</label>
                                        <label class="lgx-switch lgx-switch-labeled" title="{{ __($label) }}">
                                            <input type="checkbox" name="ai_features[{{ $key }}]" value="1" @checked($aiFeatures[$key])>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
