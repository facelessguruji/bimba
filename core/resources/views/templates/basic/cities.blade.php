@extends('Template::layouts.master')
@section('content')
    <section class="section-bg  py-120">
        <div class="container">
            <div class="row g-3 justify-content-center">
                @include('Template::partials.city')
            </div>

            @if ($cities->hasPages())
                {{ paginateLinks($cities) }}
            @endif
        </div>
    </section>
@endsection
