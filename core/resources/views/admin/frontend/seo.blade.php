@extends('admin.layouts.app')

@push('breadcrumb-plugins')
    @if (\App\Lib\AiService::enabled('seo'))
        <button type="button" class="btn btn-sm btn-outline--primary" id="aiSeoBtn">
            <i class="las la-magic"></i> @lang('Generate with AI')
        </button>
    @endif
@endpush

@push('script')
    @if (\App\Lib\AiService::enabled('seo'))
    <script>
        "use strict";
        (function($) {
            $(document).on('click', '#aiSeoBtn', function() {
                var btn = $(this).prop('disabled', true);
                $.get('{{ route("admin.ai.seo") }}', { subject: 'Business directory homepage of {{ gs("site_name") }}' }, function(res) {
                    btn.prop('disabled', false);
                    if (res.error) { alert(res.message); return; }
                    if (res.data) {
                        var d = $('textarea[name="description"]');
                        if (d.length && !d.val()) d.val(res.data.description || '');
                        alert('AI generated SEO content - review and save.');
                    }
                }).fail(function() { btn.prop('disabled', false); });
            });
        })(jQuery);
    </script>
    @endif
@endpush


@section('panel')
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                     <form action="{{ route('admin.frontend.seo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>@lang('SEO Image')</label>
                                    <x-image-uploader class="w-100" :imagePath="getImage(getFilePath('seo') . '/' . (isset($seo->data_values->image) ? $seo->data_values->image : ''), getFileSize('seo'))" :size="getFileSize('seo')" :required="false" />
                                </div>
                            </div>

                            <div class="col-xl-8 mt-xl-0 mt-4">
                                <div class="form-group">
                                    <label>@lang('Meta Keywords')</label>
                                    <small class="ms-2 mt-2  ">@lang('Separate multiple keywords by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                    <select name="keywords[]" class="form-control select2-auto-tokenize" multiple="multiple" required>
                                        @if (isset($seo->data_values->keywords) && $seo->data_values->keywords)
                                            @foreach ($seo->data_values->keywords as $option)
                                                <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Meta Robots') <small>(@lang('optional'))</small></label>
                                    <input type="text" class="form-control" name="meta_robots" value="{{ isset($seo->data_values->meta_robots) ? $seo->data_values->meta_robots : '' }}" placeholder="e.g. noindex, follow">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Meta Description')</label>
                                    <textarea name="description" rows="3" class="form-control" required>{{ isset($seo->data_values->description) ? $seo->data_values->description : '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Title')</label>
                                    <input type="text" class="form-control" name="social_title" value="{{ isset($seo->data_values->social_title) ? $seo->data_values->social_title : '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Social Description')</label>
                                    <textarea name="social_description" rows="3" class="form-control" required>{{ isset($seo->data_values->social_description) ? $seo->data_values->social_description : '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

