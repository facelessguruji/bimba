@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">

            <div class="row gy-4">
                <div class="col-12">
                    <div class="emptyArea"></div>
                </div>
                @foreach ($settings as $key => $setting)
                    <div class="col-xxl-4 col-md-6 {{ $key }} searchItems">
                        @php
                            $params = null;
                            if (isset($setting->params) && $setting->params) {
                                foreach ($setting->params as $paramVal) {
                                    $params[] = array_values((array)$paramVal)[0];
                                }
                            }
                        @endphp
                        <x-widget style="2" link="{{ route($setting->route_name,$params) }}" icon="{{ $setting->icon }}" heading="{{ $setting->title }}" subheading="{{ $setting->subtitle }}" cover_cursor=1 icon_style="fill" color="primary" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/highlighter22.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var settingsData = @php echo json_encode($settings) @endphp;

            function filterSettings(query) {
                let filteredSettings = [];
                for (var key in settingsData) {
                    if (settingsData.hasOwnProperty(key)) {
                        var setting = settingsData[key];

                        var keywordMatch = setting.keyword.some(function(keyword) {
                            return keyword.toLowerCase().includes(query.toLowerCase());
                        });
                        var titleMatch = setting.title.toLowerCase().includes(query.toLowerCase());
                        var subtitleMatch = setting.subtitle.toLowerCase().includes(query.toLowerCase());

                        if (keywordMatch || titleMatch || subtitleMatch) {
                            filteredSettings[key] = setting;
                        }
                    }
                }
                return filteredSettings;
            }

            function isEmpty(obj) {
                return Object.keys(obj).length === 0;
            }


            function renderSettings(filteredSettings, query) {
                $('.searchItems').addClass('d-none');
                $('.emptyArea').html('');
                if (isEmpty(filteredSettings)) {
                    $('.emptyArea').html(`<div class="col-12 searchItems text-center mt-4"><div class="card">
                                <div class="card-body">
                                    <div class="empty-search text-center">
                                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                        <h5 class="text-muted">@lang('No search result found.')</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                } else {
                    for (const key in filteredSettings) {
                        if (Object.hasOwnProperty.call(filteredSettings, key)) {
                            const element = filteredSettings[key];
                            var setting = element;
                            $(`.searchItems.${key}`).removeClass('d-none');
                        }
                    }
                }
            }




        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        
        .widget-seven .widget-seven__content-amount {
            font-size: 22px;
        }

        .widget-seven .widget-seven__content-subheading {
            font-weight: normal;
        }
        .empty-search img{
            width: 120px;
            margin-bottom: 15px;
        }

        a.item-link:focus,a.item-link:hover {
            background: #4F46E538;
        }
    </style>
@endpush
