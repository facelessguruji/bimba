@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Edit Company Event')</h5>
                    <span class="badge {{ $event->status == \App\Constants\Status::ENABLE ? 'badge--success' : 'badge--warning' }}">
                        {{ $event->status == \App\Constants\Status::ENABLE ? __('Published') : __('Unpublished') }}
                    </span>
                </div>
                <form action="{{ route('admin.events.internal.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>@lang('Title') *</label>
                            <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Description') *</label>
                            <textarea name="description" class="form-control" rows="5" required>{{ $event->description }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Category') *</label>
                                    <select name="category_id" class="form-control" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Country') *</label>
                                    <select name="country_id" class="form-control" required>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ $event->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('City') *</label>
                                    <select name="city_id" class="form-control" required>
                                        @foreach ($countries as $country)
                                            @foreach ($country->cities as $city)
                                                <option value="{{ $city->id }}" {{ $event->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }} ({{ $country->name }})</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Venue') *</label>
                                    <input type="text" name="venue" class="form-control" value="{{ $event->venue }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Start Time') *</label>
                                    <input type="datetime-local" name="start_time" class="form-control" value="{{ optional($event->start_time)->format('Y-m-d\\TH:i') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('End Time') *</label>
                                    <input type="datetime-local" name="end_time" class="form-control" value="{{ optional($event->end_time)->format('Y-m-d\\TH:i') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Registration Price (0 = free)')</label>
                                    <input type="number" name="price" class="form-control" min="0" step="0.01" value="{{ $event->price }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Cover Image') <small class="text-muted">(@lang('leave empty to keep the current one'))</small></label>
                                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn--primary">@lang('Update Event')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
