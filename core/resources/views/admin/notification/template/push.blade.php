@extends('admin.layouts.app')
@push('topBar')
  @include('admin.notification.top_bar')
@endpush
@section('panel')
    @include('admin.notification.template.nav')
    @include('admin.notification.template.shortcodes')


    <form action="{{ route('admin.setting.notification.template.update',['push',$template->id]) }}" method="post">
        @csrf
        <div class="row">

            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header bg--primary">
                        <h5 class="card-title text-white">@lang('Push Notification Template')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Notification Title')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Notification Title')" name="push_title" value="{{ $template->push_title }}">
                                    <small class="text-primary"><i><i class="las la-info-circle"></i> @lang('Make the field empty if you want to use global template\'s title as notification title.')</i></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Send Push Notify')">
                                        <input type="checkbox" name="push_status" @if($template->push_status) checked @endif>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message')</label>
                                    <textarea name="push_body" rows="10" class="form-control" placeholder="@lang('Your message using short-codes')" required>{{ $template->push_body }}</textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </div>
            </div>

        </div>

    </form>
@endsection


@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.setting.notification.templates') }}" />
@endpush
