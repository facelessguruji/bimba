@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="mb-4">@lang('KYC Form')</h3>
                @if ($form)
                    <div class="card custom--card">
                        <div class="card-body">
                            <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @foreach ($form->form_data as $item)
                                    <div class="form-group">
                                        <label class="form--label">{{ __(keyToTitle($item->label)) }}
                                            @if ($item->is_required == 'required') <span class="text-danger">*</span> @endif
                                        </label>

                                        @if ($item->type == 'text')
                                            <input type="text" name="{{ $item->label }}" class="form--control"
                                                value="{{ old($item->label) }}" {{ $item->is_required == 'required' ? 'required' : '' }}>
                                        @elseif ($item->type == 'number')
                                            <input type="number" name="{{ $item->label }}" class="form--control"
                                                value="{{ old($item->label) }}" {{ $item->is_required == 'required' ? 'required' : '' }}>
                                        @elseif ($item->type == 'textarea')
                                            <textarea name="{{ $item->label }}" class="form--control" rows="3"
                                                {{ $item->is_required == 'required' ? 'required' : '' }}>{{ old($item->label) }}</textarea>
                                        @elseif ($item->type == 'file')
                                            <input type="file" name="{{ $item->label }}" class="form--control"
                                                {{ $item->is_required == 'required' ? 'required' : '' }}>
                                            @if (isset($item->extensions) && $item->extensions)
                                                <small class="text-muted">@lang('Allowed'): {{ $item->extensions }}</small>
                                            @endif
                                        @elseif ($item->type == 'select' || $item->type == 'radio')
                                            <select name="{{ $item->label }}" class="form--control"
                                                {{ $item->is_required == 'required' ? 'required' : '' }}>
                                                @foreach ($item->options ?? [] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($item->type == 'checkbox')
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach ($item->options ?? [] as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="{{ $item->label }}[]" value="{{ $option }}"
                                                            id="{{ $item->label }}_{{ $loop->index }}"
                                                            @checked(is_array(old($item->label)) && in_array($option, old($item->label)))>
                                                        <label class="form-check-label" for="{{ $item->label }}_{{ $loop->index }}">{{ __($option) }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn--base w-100">@lang('Submit KYC Data')</button>
                            </form>
                        </div>
                    </div>
                @else
                    @include('Template::partials.data_not_found', ['data' => 'KYC form is not configured yet'])
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
