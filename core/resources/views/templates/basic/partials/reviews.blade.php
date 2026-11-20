@foreach ($reviews as $review)
    <div class="comment-list__item">
        <div class="comment-list__item-wrapper">
            <div class="d-flex gap-2">
                <div class="comment-list__thumb">
                    <img class="fit-image" src="{{ getImage(getFilePath('userProfile') . '/' . $review->user->image, null, true) }}">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <h5 class="comment-list__name"> {{ $review->user->username }} </h5>
                    <span class="comment-list__time mb-0">{{ $review->created_at->format('d F, Y') }} </span>
                </div>
            </div>

            <div class="comment-list__content d-flex justify-content-between">
                <div class="rating-list rating-star">
                    @php echo showRatingStars($review->rating) @endphp
                </div>
            </div>
        </div>

        <p class="comment-list__desc">
            {{ $review->review }}
        </p>
    </div>
@endforeach
