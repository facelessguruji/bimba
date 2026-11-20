@php
    $blogContent = getContent('blog.content', true);
    $blogs = getContent('blog.element', false, 6);

    $blogHeading = trim((string) @(($blogContent->data_values->heading ?? '')));
    if ($blogHeading === '') {
        $blogHeading = 'Latest articles';
    }
    $blogSub = trim((string) @(($blogContent->data_values->subheading ?? '')));
    $blogSub = ($blogSub !== '' && !preg_match('/lorem[\s,]*ipsum/i', $blogSub)) ? $blogSub : null;
@endphp

@if (!blank($blogs))
    <section class="lgx-section lgx-section--tint">
        <div class="lgx-container">
            <div class="lgx-sec-head">
                <div>
                    <h2>{{ __($blogHeading) }}</h2>
                    @if ($blogSub)
                        <p>{{ __($blogSub) }}</p>
                    @endif
                </div>
                <a class="lgx-sec-head__link" href="{{ route('blog') }}">@lang('All articles')</a>
            </div>

            <div class="lgx-posts">
                @foreach ($blogs as $blog)
                    @php
                        $blogImage = trim((string) @(($blog->data_values->image ?? '')));
                        $blogExcerpt = strLimit(strip_tags(@($blog->data_values->description_nic ?? '')), 90);
                    @endphp
                    <article class="lgx-post">
                        <a class="lgx-post__th" href="{{ route('blog.details', @$blog->slug) }}" aria-label="{{ strLimit(__(($blog->data_values->title ?? '')), 60) }}">
                            @if ($blogImage !== '')
                                <img src="{{ frontendImage('blog', ($blog->data_values->image ?? ''), '435x260', thumb: true) }}"
                                     alt="{{ strLimit(__(($blog->data_values->title ?? '')), 60) }}" loading="lazy">
                            @else
                                <span class="lgx-post__ph"><i class="las la-newspaper"></i></span>
                            @endif
                        </a>
                        <div class="lgx-post__bd">
                            <h3><a href="{{ route('blog.details', @$blog->slug) }}">{{ strLimit(__(($blog->data_values->title ?? '')), 58) }}</a></h3>
                            @if ($blogExcerpt)
                                <p>{{ $blogExcerpt }}</p>
                            @endif
                            <a class="lgx-post__more" href="{{ route('blog.details', $blog->slug) }}">@lang('Read article') <i class="las la-arrow-right"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
