@extends('Template::layouts.master')
@section('content')

    @include('Template::sections.banner')

    {{-- page-builder section loop: the admin picks the order, every section
         blade guards its own data so a missing or renamed key can never
         fatal the homepage. @includeIf also tolerates unknown keys. --}}
    @php
        $homeSections = isset($sections->secs) && $sections->secs ? json_decode($sections->secs) : [];
    @endphp
    @if (!blank($homeSections))
        @foreach ($homeSections as $homeSection)
            @includeIf('Template::sections.' . $homeSection)
        @endforeach
    @endif

@endsection
