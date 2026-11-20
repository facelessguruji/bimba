
@extends('Template::layouts.user')
@section('content')
    <div class="feature-grid py-120">
        <div class="container">
            <div class="user-profile-wrapper">
                <div class="profile-info">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="proifle-image-preview">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, null, true) }}" alt="@lang('profile-image')">
                            </div>
                            <ul class="caption-list-two mt-3">
                                <li>
                                    <span class="caption">@lang('Username')</span>
                                    <span class="value">{{ $user->username }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Email')</span>
                                    <span class="value">{{ $user->email }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Mobile')</span>
                                    <span class="value">+{{ $user->mobile }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Country')</span>
                                    <span class="value">{{$user->country_name}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="profile-form">
                    <div class="card custom--card">
                        <div class="card-body">
                            <form class="register" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('First Name')</label>
                                            <input class="form--control control-two" name="firstname" type="text" value="{{ $user->firstname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Last Name')</label>
                                            <input class="form--control control-two" name="lastname" type="text" value="{{ $user->lastname }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Address')</label>
                                        <input type="text" class="form-control form--control" name="address" value="{{$user->address}}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('State')</label>
                                        <input type="text" class="form-control form--control" name="state" value="{{$user->state}}">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Zip Code')</label>
                                        <input type="text" class="form-control form--control" name="zip" value="{{$user->zip}}">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('City')</label>
                                        <input type="text" class="form-control form--control" name="city" value="{{$user->city}}">
                                    </div>

                                    <div class="col">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Website URL')</label>
                                            <input class="form--control control-two" name="website" value="{{ $user->website }}" type='text' />
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Image')</label>
                                            <input class="form-control form--control" id="imageUpload" name="image" type='file' accept=".png, .jpg, .jpeg" />

                                            <small><i class="la la-info-circle"></i> @lang('Files'):
                                                <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b> @lang('Resized: 400x400px')
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-md btn--base w-100" type="submit">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .user-profile-wrapper {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .profile-info {
            width: 320px;
        }

        .profile-form {
            width: calc(100% - 360px);
        }

        @media(max-width:767px) {
            .user-profile-wrapper {
                gap: 10px;
            }

            .profile-info {
                width: 380px;
            }

            .profile-form {
                width: 380px;
            }
        }

        @media(max-width:590px) {
            .profile-info {
                width: 300px;
            }

            .profile-form {
                width: 300px;
            }
        }

        .proifle-image-preview {
            text-align: center;
        }

        .proifle-image-preview img {
            width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 5px;
        }

        .caption-list-two {
            padding: 0;
        }

        .caption-list li {
            display: flex;
            flex-wrap: wrap;
            padding: 0.625rem 0;
            font-size: 0.9375rem;
            border-bottom: 1px dashed #e5e5e5;
        }

        .caption-list li:first-child {
            padding-top: 0;
        }

        .caption-list li:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .caption-list li .caption {
            width: 30%;
            font-family: "Exo", cursive;
            font-weight: 700;
            font-size: 0.875rem;
            position: relative;
        }

        @media (max-width: 480px) {
            .caption-list li .caption {
                width: 35%;
            }
        }

        .caption-list li .caption::after {
            position: absolute;
            content: ':';
            top: 0;
            right: 0;
        }

        .caption-list li .value {
            width: 70%;
            padding-left: 0.9375rem;
        }

        @media (max-width: 480px) {
            .caption-list li .value {
                width: 65%;
            }
        }

        .caption-list-two {
            padding: 0.625rem 0.9375rem;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
        }

        .caption-list-two li {
            font-weight: 500;
            color: #373e4a;
            font-size: 0.875rem;
            padding: 0.5rem 0;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px dashed #cccccc;
        }

        .caption-list-two li:first-child {
            padding-top: 0;
        }

        .caption-list-two li:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .caption-list-two li .caption {
            font-weight: 700;
        }

        @media (max-width: 480px) {
            .caption-list-two li span {
                width: 100%;
            }

            .caption-list-two li .value {
                margin-top: 3px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        $("#imageUpload").on('change', function() {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('.proifle-image-preview img').attr('src', e.target.result)
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
@endpush
