@php
    // the button link is admin-entered and may be a relative path like
    // "user/register"; make it absolute so it can never resolve relative
    // to a nested URL such as /listing/slug/1
    $ctaContent = getContent('cta.content', true);
    $ctaLink = trim((string) @$ctaContent->data_values->button_link);
    if ($ctaLink && !preg_match('~^(https?://|/|#|mailto:|tel:)~i', $ctaLink)) {
        $ctaLink = '/' . $ctaLink;
    }
@endphp

<section class="lgx-section">
    <div class="lgx-container">
        <div class="lgx-band">
            <div>
                <h2>@lang('List your business on LeadxGPT')</h2>
                <p>@lang('Create a free account, publish your business page and start receiving customer enquiries today.')</p>
            </div>
            <div class="lgx-band__act">
                <a class="btn-lgx btn-lgx-lg btn-lgx-white" href="{{ $ctaLink ?: route('user.register') }}">@lang('Join free')</a>
            </div>
        </div>
    </div>
</section>
