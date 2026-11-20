@php
    // heading stays admin-editable; the steps and services below are the
    // approved original copy and do not depend on any seed data
    $hwContent = getContent('how_work.content', true);

    $hwHeading = trim((string) @($hwContent->data_values->heading));
    if ($hwHeading === '') {
        $hwHeading = 'How LeadxGPT works';
    }
    $hwSub = trim((string) @($hwContent->data_values->subheading));
    $hwSub = ($hwSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $hwSub)) ? $hwSub : null;

    $hwSteps = [
        ['title' => 'List your business', 'desc' => 'Create your business page in minutes: photos, services, opening hours, location and contact details.'],
        ['title' => 'Get found',          'desc' => 'Customers discover you when they search on LeadxGPT and on search engines. Your page is built to rank.'],
        ['title' => 'Receive leads',      'desc' => 'Customer enquiries land straight in your inbox. Reply fast, win the job, and watch your rating grow.'],
    ];

    $hwServices = [
        ['icon' => 'las la-building',  'tone' => 'b', 'title' => 'Business Listings',  'desc' => 'Your full business page with gallery, map and hours'],
        ['icon' => 'las la-inbox',     'tone' => 'g', 'title' => 'Lead Generation',    'desc' => 'Real customer enquiries delivered to your inbox'],
        ['icon' => 'las la-shield-alt','tone' => 'v', 'title' => 'Trusted Profiles',   'desc' => 'Verified business badges build customer trust'],
        ['icon' => 'las la-star',      'tone' => 'a', 'title' => 'Reviews and Ratings','desc' => 'Collect and showcase genuine customer reviews'],
        ['icon' => 'las la-calendar',  'tone' => 'i', 'title' => 'Business Events',    'desc' => 'Promote openings, offers and local events'],
        ['icon' => 'las la-chart-line','tone' => 'b', 'title' => 'Growth Insights',    'desc' => 'See views, enquiries and response performance'],
    ];
@endphp

<section class="lgx-section">
    <div class="lgx-container">
        <div class="lgx-sec-head">
            <div>
                <h2>{{ __($hwHeading) }}</h2>
                @if ($hwSub)
                    <p>{{ __($hwSub) }}</p>
                @endif
            </div>
            <a class="lgx-sec-head__link" href="{{ route('user.register') }}">@lang('Join free')</a>
        </div>

        <div class="lgx-steps">
            @foreach ($hwSteps as $hwStep)
                <div class="lgx-step">
                    <span class="lgx-step__num">{{ $loop->iteration }}</span>
                    <h3>@lang($hwStep['title'])</h3>
                    <p>@lang($hwStep['desc'])</p>
                </div>
            @endforeach
        </div>

        <div class="lgx-serv">
            @foreach ($hwServices as $hwService)
                <div class="lgx-sv">
                    <span class="lgx-tile lgx-tile--sm lgx-tile--{{ $hwService['tone'] }}"><i class="{{ $hwService['icon'] }}"></i></span>
                    <div>
                        <b>@lang($hwService['title'])</b>
                        <span>@lang($hwService['desc'])</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
