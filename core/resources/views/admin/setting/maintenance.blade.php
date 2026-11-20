@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>@lang('Status')</label>
                        <label class="lgx-switch lgx-switch-labeled" title="@lang('Maintenance Mode')">
                          <input type="checkbox" name="status" @if(gs('maintenance_mode')) checked @endif>
                                        <span class="lgx-switch__track"></span>
                        <span class="lgx-switch-state"><span class="st-on">@lang('Enabled')</span><span class="st-off">@lang('Disabled')</span></span>
</label>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" :imagePath="getImage(getFilePath('maintenance') . '/' . (isset($maintenance->data_values->image) ? $maintenance->data_values->image : ''), getFileSize('maintenance'))" :size="getFileSize('maintenance')" :required="false" name="image" />

                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="form-group">
                          <label>@lang('Description')</label>
                            <textarea class="form-control nicEdit" rows="10" name="description">
                                @php echo isset($maintenance->data_values->description) ? $maintenance->data_values->description : '' @endphp
                            </textarea>
                        </div>
                    </div>
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
