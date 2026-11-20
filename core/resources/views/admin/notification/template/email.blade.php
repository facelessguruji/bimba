@extends('admin.layouts.app')
@push('topBar')
  @include('admin.notification.top_bar')
@endpush
@section('panel')
    @include('admin.notification.template.nav')
    @include('admin.notification.template.shortcodes')


    <form action="{{ route('admin.setting.notification.template.update',['email',$template->id]) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header bg--primary">
                        <h5 class="card-title text-white">@lang('Email Template')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Subject')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Email subject')" name="subject" value="{{ $template->subject }}" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Status') <span class="text--danger">*</span></label>
                                    <label class="lgx-switch lgx-switch-labeled" title="@lang('Send Email')">
                                        <input type="checkbox" name="email_status" @if($template->email_status) checked @endif>
                                        <span class="lgx-switch__track"></span>
                                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email Sent From - Name')</label>
                                    <input type="text" class="form-control" name="email_sent_from_name" value="{{ $template->email_sent_from_name }}">
                                    <small class="text-primary"><i><i class="las la-info-circle"></i> @lang('Make the field empty if you want to use global template\'s name as email sent from name.')</i></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email Sent From - Email')</label>
                                    <input type="text" class="form-control" name="email_sent_from_address" value="{{ $template->email_sent_from_address }}">
                                    <small class="text-primary"><i><i class="las la-info-circle"></i> @lang('Make the field empty if you want to use global template\'s email as email sent from.')</i></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message') <span class="text--danger">*</span></label>
                                    <textarea name="email_body" rows="10" class="form-control nicEdit" placeholder="@lang('Your message using short-codes')">{{ $template->email_body }}</textarea>
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
