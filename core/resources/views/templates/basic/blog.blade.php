@extends('Template::layouts.master')
@section('content')
    <section class="blog py-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @include('Template::partials.blog')
            </div>

            @if ($blogs->hasPages())
                <div class="mt-4">
                    {{ paginateLinks($blogs) }}
                </div>
            @endif
        </div>
    </section>
@endsection
