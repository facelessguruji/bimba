@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Create Company Event') <small class="text-muted">(@lang('visible to registered affiliates only'))</small></h5>
                </div>
                <form action="{{ route('admin.events.internal.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>@lang('Title') *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Description') *</label>
                            <textarea name="description" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Category') *</label>
                                    <select name="category_id" class="form-control" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Country') *</label>
                                    <select name="country_id" class="form-control" required>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
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
                                                <option value="{{ $city->id }}">{{ $city->name }} ({{ $country->name }})</option>
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
                                    <input type="text" name="venue" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Start Time') *</label>
                                    <input type="datetime-local" name="start_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('End Time') *</label>
                                    <input type="datetime-local" name="end_time" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Registration Price (0 = free)')</label>
                                    <input type="number" name="price" class="form-control" min="0" step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Cover Image')</label>
                                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn--primary">@lang('Create Event')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
