@extends('Template::layouts.master')
@section('content')
<section class="py-60">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    @php
                        echo @$cookie?->data_values?->description ?? '';
                    @endphp
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
