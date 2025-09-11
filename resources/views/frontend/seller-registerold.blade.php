@extends('layouts.frontend')

@section('title', __('Register'))
@php $gtext = gtext(); @endphp

@section('meta-content')
	<meta name="keywords" content="{{ $gtext['og_keywords'] }}" />
	<meta name="description" content="{{ $gtext['og_description'] }}" />
	<meta property="og:title" content="{{ $gtext['og_title'] }}" />
	<meta property="og:site_name" content="{{ $gtext['site_name'] }}" />
	<meta property="og:description" content="{{ $gtext['og_description'] }}" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="{{ url()->current() }}" />
	<meta property="og:image" content="{{ asset('public/media/'.$gtext['og_image']) }}" />
	<meta property="og:image:width" content="600" />
	<meta property="og:image:height" content="315" />
	@if($gtext['fb_publish'] == 1)
	<meta name="fb:app_id" property="fb:app_id" content="{{ $gtext['fb_app_id'] }}" />
	@endif
	<meta name="twitter:card" content="summary_large_image">
	@if($gtext['twitter_publish'] == 1)
	<meta name="twitter:site" content="{{ $gtext['twitter_id'] }}">
	<meta name="twitter:creator" content="{{ $gtext['twitter_id'] }}">
	@endif
	<meta name="twitter:url" content="{{ url()->current() }}">
	<meta name="twitter:title" content="{{ $gtext['og_title'] }}">
	<meta name="twitter:description" content="{{ $gtext['og_description'] }}">
	<meta name="twitter:image" content="{{ asset('public/media/'.$gtext['og_image']) }}">
@endsection

@section('header')
@include('frontend.partials.header')
@endsection

@section('content')
    <style>
        .form-section {
            background: #fff;
            border-radius: 14px;
            padding: 18px 18px 6px;
            margin-bottom: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.04);
        }
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 14px;
        }
    </style>

<main class="main">
	<!-- Page Breadcrumb -->
	<div class="breadcrumb-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('Register') }}</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6">
					<div class="page-title">
						<h1>{{ __('Register') }}</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Page Breadcrumb/ -->

	<!-- Inner Section -->
	<section class="inner-section inner-section-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="row mt10 mb5">
						<div class="col-md-12 text-center">
							<a href="{{ route('frontend.register') }}" class="btn white-btn text-initial mr10 mb5 font-bold">{{ __('I am a customer') }}</a>
							<a href="{{ route('frontend.seller-register') }}" class="btn white-btn text-initial mb5 font-bold active">{{ __('I am a seller') }}</a>
						</div>
					</div>
					<div class="register" >
						<h4>{{ __('Create an seller account') }}</h4>
						<p>{{ __('Please fill in the information below') }}</p>

						@if(Session::has('success'))
						<div class="alert alert-success">
							{{Session::get('success')}}
						</div>
						@endif
						@if(Session::has('fail'))
						<div class="alert alert-danger">
							{{Session::get('fail')}}
						</div>
						@endif

                        <form class="form" method="POST" action="{{ route('frontend.sellerRegister') }}" novalidate>
                            @csrf

                            {{-- Alerts --}}
                            @if(Session::has('success'))
                                <div class="alert alert-success">{{ Session::get('success') }}</div>
                            @endif
                            @if(Session::has('fail'))
                                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                            @endif

                            {{-- ACCOUNT --}}
                            <fieldset class="form-section">
                                <legend class="section-title">{{ __('Account Details') }}</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">{{ __('Full Name') }}</label>
                                            <input id="name" name="name" type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="{{ __('e.g. Jane Wambui') }}"
                                                   value="{{ old('name') }}" required autocomplete="name" />
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{ __('Email Address') }}</label>
                                            <input id="email" name="email" type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   placeholder="you@example.com"
                                                   value="{{ old('email') }}" required autocomplete="email" />
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label for="password">{{ __('Password') }}</label>
                                            <div class="input-group">
                                                <input id="password" name="password" type="password"
                                                       class="form-control @error('password') is-invalid @enderror"
                                                       placeholder="{{ __('Min. 8 characters') }}" required />
                                                <button type="button" class="btn btn-outline-secondary" id="togglePass">
                                                    {{ __('Show') }}
                                                </button>
                                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>
                                            <small class="form-text text-muted">{{ __('Use at least 8 characters with a number or symbol.') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                            <input id="password_confirmation" name="password_confirmation" type="password"
                                                   class="form-control" placeholder="{{ __('Re-enter your password') }}" required />
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- BUSINESS --}}
                            <fieldset class="form-section">
                                <legend class="section-title">{{ __('Business Details') }}</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shop_name">{{ __('Shop Name') }}</label>
                                            <input id="shop_name" name="shop_name" type="text"
                                                   class="form-control @error('shop_name') is-invalid @enderror"
                                                   placeholder="{{ __('e.g. Smart Store Clay City') }}"
                                                   value="{{ old('shop_name') }}" required />
                                            @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shop_phone">{{ __('Shop Phone') }}</label>
                                            <input id="shop_phone" name="shop_phone" type="tel"
                                                   class="form-control @error('shop_phone') is-invalid @enderror"
                                                   placeholder="{{ __('e.g. 07XXXXXXXX') }}"
                                                   value="{{ old('shop_phone') }}" required />
                                            @error('shop_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">{{ __('Use your business line for order and KYC verification.') }}</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="classification">{{ __('Classification') }}</label>
                                            <select name="classification" id="classification"
                                                    class="form-control @error('classification') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Classification') }}</option>
                                                <option value="individual" {{ old('classification') == 'individual' ? 'selected' : '' }}>{{ __('Individual') }}</option>
                                                <option value="company" {{ old('classification') == 'company' ? 'selected' : '' }}>{{ __('Company') }}</option>
                                                <option value="group" {{ old('classification') == 'group' ? 'selected' : '' }}>{{ __('Group/Association') }}</option>
                                            </select>
                                            @error('classification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="document_number" id="doc_label">{{ __('Document Number') }}</label>
                                            <input id="document_number" name="document_number" type="text"
                                                   class="form-control @error('document_number') is-invalid @enderror"
                                                   placeholder="{{ __('Enter Relevant Document Number') }}"
                                                   value="{{ old('document_number') }}" required />
                                            @error('document_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small id="doc_help" class="form-text text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
{{--                            <fieldset>--}}
{{--                                --}}{{-- NEW: Optional Group/Organisation selector --}}
{{--                                <div class="col-md-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="group_option">{{ __('Group / Organisation (Optional)') }}</label>--}}
{{--                                        <select name="group_option" id="group_option" class="form-control">--}}
{{--                                            <option value="">{{ __('None') }}</option>--}}
{{--                                            <option value="group" {{ old('group_option') == 'group' ? 'selected' : '' }}>{{ __('Group / Organisation') }}</option>--}}
{{--                                        </select>--}}
{{--                                        <small class="form-text text-muted">--}}
{{--                                            {{ __('Select only if you’re registering on behalf of a group or organisation.') }}--}}
{{--                                        </small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </fieldset>--}}
                            <fieldset class="form-section">
                                <legend class="section-title">{{ __('Group / Organisation (Optional)') }}</legend>
                                <div class="row">
                                    {{-- Toggle: none vs registering under a group --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="group_option">{{ __('Are you registering under a Group / Organisation?') }}</label>
                                            <select name="group_option" id="group_option" class="form-control @error('group_option') is-invalid @enderror">
                                                <option value="">{{ __('No') }}</option>
                                                <option value="group" {{ old('group_option') == 'group' ? 'selected' : '' }}>
                                                    {{ __('Yes, select a Group / Organisation') }}
                                                </option>
                                            </select>
                                            @error('group_option') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">
                                                {{ __('Choose this if you represent a registered group, organisation, association, or co-op.') }}
                                            </small>
                                        </div>
                                    </div>

                                    {{-- When Yes: show available groups --}}
                                    <div class="col-md-6" id="group_picker_wrap" style="display:none;">
                                        <div class="form-group">
                                            <label for="group_id">{{ __('Select Group / Organisation') }}</label>
                                            <select name="group_id" id="group_id" class="form-control @error('group_id') is-invalid @enderror" disabled>
                                                <option value="">{{ __('-- Select Group / Organisation --') }}</option>
                                                @forelse($groups as $g)
                                                    <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>
                                                        {{ $g->name }} @if($g->registration_number) ({{ $g->registration_number }}) @endif
                                                    </option>
                                                @empty
                                                    <option value="" disabled>{{ __('No active groups available') }}</option>
                                                @endforelse
                                            </select>
                                            @error('group_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">
                                                {{ __('Only active groups are listed. Can’t find yours? Contact support to add it.') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            {{-- COMPLIANCE (Optional) --}}
                            <fieldset class="form-section" id="compliance_section">
                                <legend class="section-title">{{ __('Compliance (Optional)') }}</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="business_registration_number">{{ __('Business Registration Number') }}</label>
                                            <input id="business_registration_number" name="business_registration_number" type="text"
                                                   class="form-control"
                                                   placeholder="{{ __('e.g. BN/2024/123456 or CPR/2019/123456') }}"
                                                   value="{{ old('business_registration_number') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kra_pin">{{ __('KRA PIN') }}</label>
                                            <input id="kra_pin" name="kra_pin" type="text"
                                                   class="form-control"
                                                   placeholder="{{ __('e.g. A123456789B') }}"
                                                   value="{{ old('kra_pin') }}" />
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    {{ __('Tip: For Individuals, you can skip these. For Companies/Groups, adding them speeds up approval.') }}
                                </small>
                            </fieldset>

                            {{-- CAPTCHA --}}
                            @if($gtext['is_recaptcha'] == 1)
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="{{ $gtext['sitekey'] }}"></div>
                                    @error('g-recaptcha-response') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- TERMS --}}
                            <div class="form-group form-check my-3">
                                <input type="checkbox" id="terms" name="terms"
                                       class="form-check-input @error('terms') is-invalid @enderror" value="1" required>
                                <label class="form-check-label" for="terms">
                                    {!! __('I agree to the') !!} <a href="https://maasaisoko.co.ke/page/45/terms-and-conditions" target="_blank">{{ __('Terms and Conditions') }}</a>
                                </label>
                                @error('terms') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" class="btn theme-btn full">{{ __('Register') }}</button>

                            @if (Route::has('frontend.reset'))
                                <h3 class="mt-3"><a href="{{ route('frontend.reset') }}">{{ __('Forgot your password?') }}</a></h3>
                            @endif
                            @if (Route::has('frontend.login'))
                                <h3><a href="{{ route('frontend.login') }}">{{ __('Back to login') }}</a></h3>
                            @endif
                        </form>

{{--                    @if (Route::has('frontend.reset'))--}}
{{--						<h3><a href="{{ route('frontend.reset') }}">{{ __('Forgot your password?') }}</a></h3>--}}
{{--						@endif--}}
{{--						@if (Route::has('frontend.login'))--}}
{{--						<h3><a href="{{ route('frontend.login') }}">{{ __('Back to login') }}</a></h3>--}}
{{--						@endif--}}

					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Inner Section/ -->
</main>

@endsection

@push('scripts')
@if($gtext['is_recaptcha'] == 1)
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
@endif
<script>
$("#shop_url").on("blur", function () {
	var shop_url = $("#shop_url").val();
	var str_name = shop_url.trim();
	var strLength = str_name.length;
	if(strLength>0){
		$.ajax({
			type : 'POST',
			url: base_url + '/frontend/hasShopSlug',
			data: 'shop_url='+shop_url,
			success: function (response) {
				var slug = response.slug;
				$("#shop_url").val(slug);
			}
		});
	}
});
(function () {
    // Password toggle
    const toggle = document.getElementById('togglePass');
    if (toggle) {
        toggle.addEventListener('click', function () {
            const input = document.getElementById('password');
            if (!input) return;
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            this.textContent = isPwd ? '{{ __("Hide") }}' : '{{ __("Show") }}';
        });
    }

    // Classification-driven UI
    const classification = document.getElementById('classification');
    const docLabel = document.getElementById('doc_label');
    const docHelp  = document.getElementById('doc_help');
    const compliance = document.getElementById('compliance_section');

    function applyClassificationUI(value) {
        switch (value) {
            case 'individual':
                docLabel.textContent = '{{ __("Document Number (ID/Passport)") }}';
                docHelp.textContent  = '{{ __("Enter your National ID or Passport number.") }}';
                if (compliance) compliance.style.display = 'none';
                break;
            case 'company':
                docLabel.textContent = '{{ __("Company Registration/Certificate Number") }}';
                docHelp.textContent  = '{{ __("e.g. CPR/20XX/XXXXXX as on your certificate of incorporation.") }}';
                if (compliance) compliance.style.display = '';
                break;
            case 'group':
                docLabel.textContent = '{{ __("Group/Association Registration Number") }}';
                docHelp.textContent  = '{{ __("Number as issued by the registrar or relevant authority.") }}';
                if (compliance) compliance.style.display = '';
                break;
            default:
                docLabel.textContent = '{{ __("Document Number") }}';
                docHelp.textContent  = '';
                if (compliance) compliance.style.display = 'none';
        }
    }

    if (classification) {
        applyClassificationUI(classification.value || '');
        classification.addEventListener('change', function (e) {
            applyClassificationUI(e.target.value);
        });
    }
})();
(function () {
    const groupOption = document.getElementById('group_option');
    const groupWrap   = document.getElementById('group_picker_wrap');
    const groupIdSel  = document.getElementById('group_id');

    function toggleGroupPicker() {
        const show = groupOption && groupOption.value === 'group';
        if (groupWrap) groupWrap.style.display = show ? '' : 'none';
        if (groupIdSel) {
            groupIdSel.disabled = !show;
            // If you want it required only when visible:
            if (show) { groupIdSel.setAttribute('required', 'required'); }
            else { groupIdSel.removeAttribute('required'); groupIdSel.value = ''; }
        }
    }

    toggleGroupPicker();
    if (groupOption) groupOption.addEventListener('change', toggleGroupPicker);
})();
</script>
@endpush
