<!-- Tell us what you need -->
<div class="card custom--card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">@lang('Tell Us What You Need')</h5>
    </div>
    <div class="card-body">
        <form action="{{ isset($leadListing) ? route('lead.listing', $leadListing->id) : route('lead.submit') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Your Name')</label>
                        <input type="text" name="name" class="form--control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Email')</label>
                        <input type="email" name="email" class="form--control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Phone (optional)')</label>
                        <input type="text" name="phone" class="form--control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form--label">@lang('What are you looking for?')</label>
                        <textarea name="message" class="form--control" rows="3" required
                            placeholder="@lang('Describe the business or service you need - we will match you')"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit Request')</button>
                </div>
            </div>
        </form>
    </div>
</div>
