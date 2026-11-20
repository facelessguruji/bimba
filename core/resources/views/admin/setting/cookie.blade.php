@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card">
            <form method="post">
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>@lang('Status')</label>
                        <label class="lgx-switch lgx-switch-labeled" title="@lang('Cookie Consent')">
                          <input type="checkbox" name="status" @if(isset($cookie->data_values->status) && $cookie->data_values->status) checked @endif>
                          <span class="lgx-switch__track"></span>
                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
</label>
                      </div>
                    </div>
                  </div>
                    <div class="form-group">
                      <label>@lang('Short Description')</label>
                        <textarea class="form-control" rows="5" required name="short_desc">{{ isset($cookie->data_values->short_desc) ? $cookie->data_values->short_desc : '' }}</textarea>
                    </div>
                    <div class="form-group">
                      <label>@lang('Description')</label>
                        <textarea class="form-control nicEdit" rows="10" name="description">@php echo isset($cookie->data_values->description) ? $cookie->data_values->description : '' @endphp</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

