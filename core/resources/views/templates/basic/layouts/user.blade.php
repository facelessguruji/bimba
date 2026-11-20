@extends('Template::layouts.app')
@section('panel')
    <div class="lx-dash">

        <div class="lx-dash__overlay" id="lxDashOverlay"></div>

        <aside class="lx-dash__sidebar" id="lxDashSidebar">
            @include('Template::partials.user_sidebar')
        </aside>

        <div class="lx-dash__wrapper">
            <header class="lx-dash__topbar">
                <button type="button" class="lx-dash__burger" id="lxDashBurger" aria-label="@lang('Open menu')">
                    <i class="las la-bars"></i>
                </button>

                <div class="lx-dash__title">
                    <h4 class="mb-0">{{ __($pageTitle ?? 'Dashboard') }}</h4>
                </div>

                <div class="lx-dash__actions">
                    <a href="{{ route('user.listing.create') }}" class="btn btn--base btn--sm d-none d-sm-inline-flex" title="@lang('Post New Listing')">
                        <i class="las la-plus"></i> <span class="d-none d-lg-inline">@lang('New Listing')</span>
                    </a>
                    <a href="{{ url('/') }}" class="lx-dash__site" title="@lang('View Site')">
                        <i class="las la-globe"></i> <span class="d-none d-md-inline">@lang('View Site')</span>
                    </a>
                    <div class="dropdown">
                        <button class="lx-dash__avatar" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, null, true) }}" alt="{{ auth()->user()->username }}">
                            <i class="las la-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('user.profile.setting') }}"><i class="las la-user-cog"></i> @lang('Profile Setting')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.change.password') }}"><i class="las la-key"></i> @lang('Change Password')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.twofactor') }}"><i class="las la-shield-alt"></i> @lang('2FA Security')</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                                <form action="{{ route('user.logout') }}" method="POST">@csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="las la-sign-out-alt"></i> @lang('Logout')</button>
                                                </form>
                                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="lx-dash__content">
                @yield('content')
            </main>
        </div>

    </div>
@endsection

@push('style')
    <style>
        html, body, .lx-dash__sidebar { scrollbar-width: none; -ms-overflow-style: none; }
        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        .lx-dash__sidebar::-webkit-scrollbar { display: none; width: 0; height: 0; }
    </style>
@endpush

@push('script')
    <script>
        (function () {
            var sidebar = document.getElementById('lxDashSidebar');
            var overlay = document.getElementById('lxDashOverlay');
            var burger  = document.getElementById('lxDashBurger');
            var close   = document.getElementById('lxSideClose');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (burger) burger.addEventListener('click', openSidebar);
            if (close) close.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            sidebar.addEventListener('click', function (e) {
                if (window.innerWidth < 992 && e.target.closest('a')) {
                    closeSidebar();
                }
            });
        })();
    </script>
@endpush
