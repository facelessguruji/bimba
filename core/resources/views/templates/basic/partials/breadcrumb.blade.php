@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

@if (!Route::is('home'))
    <section class="breadcrumb bg-img"
        style="background-image: url({{ frontendImage('breadcrumb', @$breadcrumbContent->data_values->image, '1815x450') }});">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb__wrapper">
                        <h2 class="breadcrumb__title"> {{ __(@$pageTitle) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
