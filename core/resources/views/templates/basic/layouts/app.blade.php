<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/magnific-popup.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/main.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-ui.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-polish.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-mobile.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/custom.css') }}">

    @stack('style-lib')

    @stack('style')

    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/color.php') }}?color={{ gs()->base_color }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-contrast.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-uiux.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-front.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxos.css') }}?v=34">
</head>
@php echo loadExtension('google-analytics') @endphp

<body>

    <!--==================== Preloader Start ====================-->
    <div class="preloader-wrapper">
        <div class="preloader">
            <div class="wrapper">
                <div class="loader">
                    <div class="dot"></div>
                </div>
                <div class="loader">
                    <div class="dot"></div>
                </div>
                <div class="loader">
                    <div class="dot"></div>
                </div>
                <div class="loader">
                    <div class="dot"></div>
                </div>
                <div class="loader">
                    <div class="dot"></div>
                </div>
                <div class="loader">
                    <div class="dot"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="body-overlay"></div>

    <div class="sidebar-overlay"></div>

    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>

    @stack('fbComment')

    @yield('panel')



    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie?->data_values?->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <!-- cookies dark version start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}"
                    target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base btn--xl w-100 policy">@lang('Allow')</a>
            </div>
        </div>
        <!-- cookies dark version end -->
    @endif

    @stack('modal')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/magnific-popup.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/jquery.validate.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/viewport.jquery.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/main.js') }}"></script>

    @stack('script-lib')

    {{-- lgxCopyText: universal copy helper. execCommand first so it works on
         insecure origins (LAN IP over HTTP, where navigator.clipboard is
         undefined), async clipboard API as fallback, manual-select last. --}}
    <script>
        window.lgxCopyText = window.lgxCopyText || function (text, btn, doneMsg) {
            // double-fire guard: one physical click copies once, toasts once
            if (btn && btn.__lgxCopying) return;
            if (btn) btn.__lgxCopying = true;
            var done = function () {
                if (window.iziToast) iziToast.success({ title: 'Copied', message: doneMsg || 'Copied to clipboard.', position: 'topRight', timeout: 2200 });
                if (btn && window.jQuery) {
                    var $b = window.jQuery(btn), h = $b.html();
                    $b.html('<i class="las la-check"></i>');
                    setTimeout(function () { $b.html(h); }, 1400);
                }
            };
            var fail = function () {
                if (window.iziToast) iziToast.warning({ title: 'Copy manually', message: 'Text selected - press Ctrl+C to copy.', position: 'topRight', timeout: 3500 });
            };
            try {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0.01';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                ta.setSelectionRange(0, ta.value.length);
                var ok = false;
                try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
                document.body.removeChild(ta);
                if (ok) { done(); return; }
            } catch (e) {}
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done, function () { if (btn) btn.__lgxCopying = false; fail(); });
                return;
            }
            fail();
            setTimeout(function () { if (btn) btn.__lgxCopying = false; }, 700);
        };
    </script>

    @include('partials.notify')

    @php echo loadExtension('tawk-chat') @endphp

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        "use strict";
        (function($) {

            $(".langSel").on("change", function() {
                window.location.href = "{{ url('/') }}/change/" + $(this).val();
            });


            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            $('.policy').on('click', function() {
                $.get(`{{ route('cookie.accept') }}`, function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            $('form').on('submit', function() {
                if ($(this).valid()) {
                    $(':submit', this).attr('disabled', 'disabled');
                }
            });


            $.each($('.select2'), function() {
                $(this)
                    .wrap(`<div class="position-relative w-100"></div>`)
                    .select2({
                        dropdownParent: $(this).parent(),
                        width: "100%"
                    });
            });

            $.each($('.select2-auto-tokenize'), function() {
                $(this)
                    .wrap(`<div class="position-relative w-100"></div>`)
                    .select2({
                        tags: true,
                        tokenSeparators: [','],
                        dropdownParent: $(this).parent(),
                    });
            });


            var inputElements = $('[type=text],[type=password],select,textarea');

            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input:not([type=checkbox]):not([type=hidden]), select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox' && element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }
            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

        })(jQuery);
    </script>


    <!-- Navigation feel: prefetch pages on hover so menu clicks open
         instantly instead of showing a full reload -->
    <script>
        (function () {
            "use strict";
            var prefetched = {};
            function prefetch(url) {
                if (prefetched[url]) return;
                prefetched[url] = true;
                var link = document.createElement("link");
                link.rel = "prefetch";
                link.href = url;
                document.head.appendChild(link);
            }
            function consider(el) {
                if (!el || el.tagName !== "A") return;
                if (el.target === "_blank" || el.hasAttribute("download")) return;
                var url = el.href.split("#")[0];
                if (!url || url.indexOf(location.origin) !== 0) return;
                if (url === location.href.split("#")[0]) return;
                prefetch(url);
            }
            document.addEventListener("mouseover", function (e) { consider(e.target.closest("a")); }, { passive: true });
            document.addEventListener("touchstart", function (e) { consider(e.target.closest("a")); }, { passive: true });
        })();
    </script>
</body>

</html>
