@extends('Template::layouts.user')
@section('content')
<section class="py-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h3 class="mb-4">@lang('Withdraw Preview')</h3>
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="{{ route('user.withdraw.submit') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <ul class="caption-list">
                                <li>
                                    <span class="caption">@lang('Method')</span>
                                    <span class="value">{{ $withdraw->method->name }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Requested Amount')</span>
                                    <span class="value">{{ showAmount($withdraw->amount) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Charge')</span>
                                    <span class="value">{{ showAmount($withdraw->charge) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Conversion Rate')</span>
                                    <span class="value">1 {{ __(gs('cur_text')) }} = {{ showAmount($withdraw->rate, currencyFormat: false) }} {{ $withdraw->currency }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('You Will Receive')</span>
                                    <span class="value">{{ showAmount($withdraw->final_amount, currencyFormat: false) }} {{ $withdraw->currency }}</span>
                                </li>
                            </ul>

                            @foreach ($withdraw->method->showData as $item)
                                {{-- each $item is an assoc array, see WithdrawMethod::showData() / FormProcessor::generate() --}}
                                <div class="form-group">
                                    <label class="form--label">{{ __(keyToTitle($item['label'] ?? $item['name'] ?? '')) }}
                                        @if (($item['is_required'] ?? '') == 'required') <span class="text-danger">*</span> @endif
                                    </label>
                                    @if (($item['type'] ?? '') == 'file')
                                        <input type="file" name="{{ $item['label'] ?? '' }}" class="form--control" {{ ($item['is_required'] ?? '') == 'required' ? 'required' : '' }}>
                                    @elseif (in_array(($item['type'] ?? ''), ['select', 'radio']))
                                        <select name="{{ $item['label'] ?? '' }}" class="form--control" {{ ($item['is_required'] ?? '') == 'required' ? 'required' : '' }}>
                                            @foreach ($item['options'] ?? [] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif (($item['type'] ?? '') == 'textarea')
                                        <textarea name="{{ $item['label'] ?? '' }}" class="form--control" rows="3" {{ ($item['is_required'] ?? '') == 'required' ? 'required' : '' }}></textarea>
                                    @else
                                        <input type="text" name="{{ $item['label'] ?? '' }}" class="form--control" {{ ($item['is_required'] ?? '') == 'required' ? 'required' : '' }}>
                                    @endif
                                </div>
                            @endforeach

                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label class="form--label">@lang('2FA Code')</label>
                                    <input type="text" name="authenticator_code" class="form--control"
                                        placeholder="@lang('Google Authenticator code')" required>
                                </div>
                            @endif

                            <button type="submit" class="btn btn--base w-100">@lang('Submit Withdraw Request')</button>
                        </form>
                        <small class="text-muted d-block mt-2">@lang('KYC must be approved and an active plan is required to withdraw.')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
