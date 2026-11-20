@extends('Template::layouts.master')
@section('content')
    @php
        $faqs = getContent('faq.element', orderById: true);
    @endphp

    <div class="faq-section py-120">
        <div class="container">
            @include('Template::partials.faq')
        </div>
    </div>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
