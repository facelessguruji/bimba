@extends('Template::layouts.master')
@section('content')
    <section class="blog-detials py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog-details">
                        <div class="blog-details__thumb">
                            <img class="fit-image" src="{{ frontendImage('blog', @$blog->data_values->image, '870x520') }}" alt="@lang('image')">
                        </div>
                        <div class="blog-details__content">

                            <h3 class="listing-details__title"> {{ __(@$blog->data_values->title) }} </h3>

                            <p class="listing-details__desc"> @php echo @$blog->data_values->description_nic @endphp</p>

                            <div class="blog-details__share d-flex align-items-center mt-4 flex-wrap">
                                <h5 class="social-share__title me-sm-3 d-inline-block mb-0 me-1">@lang('Share This')</h5>
                                <ul class="social-list">
                                    <li class="social-list__item"><a class="social-list__link flex-center" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="fab fa-facebook-f"></i></a>
                                    </li>
                                    <li class="social-list__item"><a class="social-list__link flex-center" href="https://twitter.com/intent/tweet?text={{ urlencode(__(@$blog->data_values->title)) }}&amp;url={{ urlencode(url()->current()) }}"> <i class="fab fa-twitter"></i></a>
                                    </li>
                                    <li class="social-list__item"><a class="social-list__link flex-center" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"> <i class="fab fa-linkedin-in"></i></a>
                                    </li>
                                    <li class="social-list__item">
                                        <a class="social-list__link flex-center" href="mailto:?subject={{ urlencode(__(@$blog->data_values->title)) }}&amp;body={{ urlencode(url()->current()) }}">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Comments Start -->
                            <ul class="comment-list mt-4">
                                @push('fbComment')
                                    @php echo loadExtension('fb-comment') @endphp
                                @endpush
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <!-- ============================= Blog Details Sidebar Start ======================== -->
                    <div class="blog-sidebar-wrapper">
                        <div class="blog-sidebar">
                            <h5 class="blog-sidebar__title"> @lang('Latest Blog') </h5>
                            @foreach ($latestPosts as $blog)
                                <div class="latest-blog">
                                    <div class="latest-blog__thumb">
                                        <a href="{{ route('blog.details', $blog->slug), }}">
                                            <img class="fit-image" src="{{ frontendImage('blog', @$blog->data_values->image, '350x210', thumb:true) }}"></a>
                                    </div>
                                    <div class="latest-blog__content">
                                        <h6 class="latest-blog__title"><a href="{{ route('blog.details', $blog->slug), }}">{{ __(strLimit(@$blog->data_values->title ?? '', 70)) }}</a>
                                        </h6>
                                        <span class="latest-blog__date fs-13"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- ============================= Blog Details Sidebar End ======================== -->
                </div>
            </div>
        </div>
    </section>
@endsection
