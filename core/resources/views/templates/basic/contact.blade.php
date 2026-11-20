@php
    $contactContent = getContent('contact_us.content', true);
    $contactElement = getContent('contact_us.element', false, null, true);
@endphp
@extends('Template::layouts.master')
@section('content')
    <section class="contact-bottom py-120">
        <div class="contact-top__shape-one">
            <img src="{{ asset(activeTemplate(true) . 'images/shapes/city-aside.png') }}">
        </div>
        <div class="contact-top__shape-two">
            <img src="{{ asset(activeTemplate(true) . 'images/shapes/city-aside.png') }}">
        </div>
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-5">
                    <div class="row gy-4 justify-content-center">
                        @foreach ($contactElement as $contact)
                            <div class="col-md-10">
                                <div class="contact-item">
                                    <div class="contact-item__top">
                                        <div class="contact-item__icon">@php echo @$contact->data_values->icon @endphp</div>
                                    </div>
                                    <div class="contact-item__content">
                                        <h5 class="contact-item__title"> {{ @$contact->data_values->title }} </h5>
                                        <p class="contact-item__desc">{{ @$contact->data_values->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contactus-form">
                        <h4 class="contact__title">{{ __(@$contactContent->data_values->heading) }}</h4>
                        <form action="" method="POST" autocomplete="off" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name" class="form--label">@lang('Your Name')</label>
                                        <input type="text" name="name" value="{{ is_string(old('name', $user?->fullname)) ? old('name', $user?->fullname) : '' }}" @if ($user && $user->profile_complete) readonly @endif required class="form--control" id="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="adress" class="form--label">@lang('Your Email') </label>
                                        <input type="email" name="email" value="{{ is_string(old('email', $user?->email)) ? old('email', $user?->email) : '' }}" @if ($user) readonly @endif required class="form--control" id="adress">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="subject" class="form--label">@lang('Subject') </label>
                                        <input type="text" name="subject" value="{{ is_string(old('subject')) ? old('subject') : '' }}" class="form--control" id="subject" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="message" class="form--label">@lang('Message') </label>
                                        <textarea class="form--control" name="message" placeholder="@lang('Type message')" id="message" required>{{ is_string(old('message')) ? old('message') : '' }} </textarea>
                                    </div>
                                </div>
                                <x-captcha />
                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <button type="submit" class=" btn btn--base"> @lang('Send Message') </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
