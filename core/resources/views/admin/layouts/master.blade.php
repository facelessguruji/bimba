<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ gs()->siteName($pageTitle ?? '') }}</title>

    <link rel="shortcut icon" type="image/png" href="{{siteFavicon()}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{asset('assets/global/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/app.css')}}?v=34">

    {{-- Shared LeadxGPT design system: loads after the admin theme so the
         token bridge + admin surface overrides win the cascade. --}}
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-ui.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-polish.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-mobile.css') }}?v=34">

    @stack('style')

    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-contrast.css') }}?v=34">
    {{-- Agent UIUX: pure-aesthetic conformance layer, always last --}}
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-uiux.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxgpt-front.css') }}?v=34">
    <link rel="stylesheet" href="{{ asset('assets/global/css/leadxos.css') }}?v=34">
</head>
<body class="lgx-admin">
@yield('content')




<script src="{{asset('assets/global/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>

@include('partials.notify')
@stack('script-lib')

<script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>

<script src="{{asset('assets/global/js/select2.min.js')}}"></script>
<script src="{{asset('assets/admin/js/app.js')}}"></script>
<script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>

{{-- LOAD NIC EDIT --}}
<script>
    "use strict";
    bkLib.onDomLoaded(function() {
        $( ".nicEdit" ).each(function( index ) {
            $(this).attr("id","nicEditor"+index);
            new nicEditor({fullPanel : true}).panelInstance('nicEditor'+index,{hasPanel : true});
        });
    });
    (function($){
        $( document ).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain',function(){
            $('.nicEdit-main').focus();
        });

        $('.breadcrumb-nav-open').on('click', function() {
            $(this).toggleClass('active');
            $('.breadcrumb-nav').toggleClass('active');
        });

        $('.breadcrumb-nav-close').on('click', function() {
            $('.breadcrumb-nav').removeClass('active');
        });

        if($('.topTap').length){
            $('.breadcrumb-nav-open').removeClass('d-none');
        }
    })(jQuery);
</script>

{{-- .confirmationBtn buttons are handled by the x-confirmation-modal
     component (components/confirmation-modal.blade.php), which opens the
     confirmation dialog and POSTs data-action. Do not bind another global
     .confirmationBtn handler here: an old dead handler (preventDefault +
     return) used to sit in front of the modal one and swallow nothing but
     created the impression of a second implementation. --}}

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
@stack('script')
</body>

</html>
