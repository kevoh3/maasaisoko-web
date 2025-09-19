@extends('layouts.frontend')

@section('title', __('Register'))

@php
    $gtext = gtext();

    // Safe fallbacks if controller didn’t pass them
    $countryId = $countryId
        ?? \App\Models\Country::where('country_name','Kenya')->value('id');

    $countyLevelId = \Illuminate\Support\Facades\DB::table('geo_levels')
        ->whereIn('name', ['county','County'])->value('id');

    $counties = $counties
        ?? \Illuminate\Support\Facades\DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $countyLevelId)
            ->orderBy('name')
            ->get(['id','name']);

    $groups = $groups ?? collect();
    $storeCategories = $storeCategories ?? collect();
@endphp

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

@push('style')
    <style>
        .form-section{background:#fff;border-radius:14px;padding:18px 18px 6px;margin-bottom:16px;box-shadow:0 2px 10px rgba(0,0,0,.04)}
        .section-title{font-size:1.05rem;font-weight:700;margin-bottom:14px}
        .d-none{display:none!important}

        /* Cascader */
        .cascader{position:relative}
        .cascader-trigger{min-height:42px;border:1px solid #e5e7eb;border-radius:8px;background:#fff}
        .cascader-panel{position:absolute;z-index:9999;left:0;top:100%;width:100%;max-height:360px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 16px 30px rgba(0,0,0,.1);display:none;overflow:hidden;margin-top:6px}
        .cascader.show .cascader-panel{display:flex}
        .cascader-cols{display:flex;width:100%}
        .cascader-col{flex:1 1 33.3%;min-width:0;max-height:320px;overflow:auto;border-right:1px solid #f1f2f4}
        .cascader-col:last-child{border-right:none}
        .cascader-list{list-style:none;margin:0;padding:6px}
        .cascader-item{display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border-radius:6px;cursor:pointer}
        .cascader-item:hover{background:#f7f8fa}
        .cascader-item .name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90%}
        .cascader-empty{padding:10px;color:#9aa1a9;font-size:.9rem}
        .cascader-footer{padding:8px 10px;background:#fafbfc;border-top:1px solid #f1f2f4;width:100%;display:flex;justify-content:space-between;align-items:center}

        .otp-row .btn{white-space:nowrap}
        .is-valid { border-color:#22c55e !important; }
        .is-invalid { border-color:#ef4444 !important; }
    </style>
@endpush

@section('content')
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

                        <div class="register">
                            <h4>{{ __('Create a seller account') }}</h4>
                            <p>{{ __('Please fill in the information below') }}</p>

                            <form id="sellerWizardForm" class="form" method="POST" action="{{ route('frontend.sellerRegister') }}" enctype="multipart/form-data" novalidate>
                                @csrf
                                <input type="hidden" name="geo_unit_id" id="geo_unit_id" value="">
                                <input type="hidden" name="geo_path" id="geo_path" value="">
                                <input type="hidden" name="save_mode" id="save_mode" value="">
                                <input type="hidden" id="otp_ok" value="0">

                                {{-- Progress --}}
                                <div class="form-section mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="section-title m-0">{{ __('Seller Onboarding') }}</div>
                                        <div id="wizStepText" class="small text-muted">{{ __('Step 1 of 4') }}</div>
                                    </div>
                                    <div class="progress mt-2 theme-progress" style="height:8px;">
                                        <div id="wizProgress" class="progress-bar" role="progressbar" style="width: 25%;"></div>
                                    </div>
                                </div>

                                {{-- STEP 1 --}}
                                <fieldset class="form-section wiz-step" data-step="1">
                                    <legend class="section-title">{{ __('1) Registration') }}</legend>
                                    <div class="row">
                                        {{-- Full / Business Name --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Full Name / Business Name') }}</label>
                                                <input type="text" name="name" class="form-control" placeholder="{{ __('e.g. Jane Wambui / Savannah Crafts Ltd') }}" required value="{{ old('name') }}">
                                            </div>
                                        </div>
                                        {{-- Email --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Email Address') }}</label>
                                                <input type="email" name="email" class="form-control" placeholder="you@example.com" required value="{{ old('email') }}">
                                            </div>
                                        </div>

                                        {{-- Shop Phone + OTP --}}
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>{{ __('Shop Phone (Mobile)') }}</label>
                                                <div class="d-flex gap-2 otp-row">
                                                    <input
                                                        type="tel"
                                                        id="shop_phone"
                                                        name="shop_phone"
                                                        class="form-control"
                                                        placeholder="+254712345678 or 0712345678"
                                                        required
                                                        value="{{ old('shop_phone') }}"
                                                    >
                                                    <button type="button" class="btn btn-outline-primary" id="btnSendOtp">
                                                        {{ __('Request OTP') }}
                                                    </button>
                                                </div>
                                                <small class="text-muted">
                                                    {{ __('Accepts +2547/+2541, 07/01, or 7/1 formats. We’ll format it as +2547XXXXXXXX or +2541XXXXXXXX.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Verification Code') }}</label>
                                                <div class="d-flex gap-2 otp-row">
                                                    <input type="text" id="otp_code" class="form-control" placeholder="123456">
                                                    <button type="button" class="btn btn-outline-success" id="btnVerifyOtp">{{ __('Verify') }}</button>
                                                </div>
                                                <small id="otp_help" class="text-muted"></small>
                                            </div>
                                        </div>

                                        {{-- Physical Address --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Physical Address') }}</label>
                                                <input type="text" name="address_line" class="form-control" placeholder="{{ __('House/Building, Street/Road') }}" required value="{{ old('address_line') }}">
                                            </div>
                                        </div>

                                        {{-- Location Cascader --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Location (County → Constituency → Ward)') }}</label>
                                                <div class="cascader" id="geoCascader" data-country="{{ (int)$countryId }}">
                                                    <button type="button" class="btn btn-light cascader-trigger w-100 text-start">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <span id="geoTriggerText">{{ __('Choose County') }}</span>
                                                    </button>

                                                    <div class="cascader-panel" style="max-width:100%;">
                                                        <div class="cascader-cols">
                                                            {{-- Counties --}}
                                                            <div class="cascader-col" id="col-counties" aria-label="Counties">
                                                                <ul class="cascader-list">
                                                                    @forelse($counties as $c)
                                                                        <li class="cascader-item county" data-id="{{ $c->id }}" data-name="{{ $c->name }}" title="{{ $c->name }}">
                                                                            <span class="name">{{ $c->name }}</span>
                                                                        </li>
                                                                    @empty
                                                                        <li class="cascader-item"><span class="name text-muted">{{ __('No counties found') }}</span></li>
                                                                    @endforelse
                                                                </ul>
                                                            </div>
                                                            {{-- Constituencies --}}
                                                            <div class="cascader-col" id="col-constits" aria-label="Constituencies">
                                                                <div class="cascader-empty">{{ __('Hover a county…') }}</div>
                                                                <ul class="cascader-list d-none"></ul>
                                                            </div>
                                                            {{-- Wards --}}
                                                            <div class="cascader-col" id="col-wards" aria-label="Wards">
                                                                <div class="cascader-empty">{{ __('Hover a constituency…') }}</div>
                                                                <ul class="cascader-list d-none"></ul>
                                                            </div>
                                                        </div>
                                                        <div class="cascader-footer">
                                                            <span class="small text-muted">{{ __('Select the most specific unit you can (ward if possible).') }}</span>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="closeCascader">{{ __('Close') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-muted" id="geoSelectedHelp"></small>
                                            </div>
                                        </div>

                                        {{-- Store/Shop Name --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Preferred Store/Shop Name') }}</label>
                                                <input type="text" name="shop_name" class="form-control" placeholder="{{ __('e.g. Smart Store Clay City') }}" required value="{{ old('shop_name') }}">
                                            </div>
                                        </div>

                                        {{-- Business Type --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Business Type') }}</label>
                                                <select name="classification" id="classification" class="form-control" required>
                                                    <option value="">{{ __('Select type') }}</option>
                                                    <option value="individual" @selected(old('classification')==='individual')>{{ __('Individual') }}</option>
                                                    <option value="company" @selected(old('classification')==='company')>{{ __('Company') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Group (optional) --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Group / Organisation (Optional)') }}</label>
                                                <select name="group_id" class="form-control">
                                                    <option value="">{{ __('None') }}</option>
                                                    @foreach($groups as $g)
                                                        <option value="{{ $g->id }}" @selected(old('group_id')==$g->id)>{{ $g->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Account password --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Account Password') }}</label>
                                                <input type="password" name="password" class="form-control" required>
                                                <small class="text-muted">{{ __('Min 6 characters') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Confirm Password') }}</label>
                                                <input type="password" name="password_confirmation" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 2 --}}
                                <fieldset class="form-section wiz-step d-none" data-step="2">
                                    <legend class="section-title">{{ __('2) Verification') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label id="doc_label">{{ __('National ID / Passport Number') }}</label>
                                                <input type="text" name="document_number" class="form-control" required value="{{ old('document_number') }}">
                                                <small id="doc_help" class="form-text text-muted"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Attach Copy (ID/Passport)') }}</label>
                                                <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Business License / Certificate of Incorporation (attach)') }}</label>
                                                <input type="file" name="business_license_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('KRA PIN (Tax ID)') }}</label>
                                                <input type="text" name="kra_pin" class="form-control" placeholder="A123456789B" value="{{ old('kra_pin') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Brand Authorization (if selling branded products)') }}</label>
                                                <input type="file" name="brand_auth_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Contact Person (Name)') }}</label>
                                                <input type="text" name="contact_person_name" class="form-control" value="{{ old('contact_person_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Contact Person (Phone)') }}</label>
                                                <input
                                                    type="tel"
                                                    name="contact_person_phone"
                                                    id="contact_person_phone"
                                                    class="form-control"
                                                    placeholder="+254712345678 or 0712345678"
                                                    required
                                                    value="{{ old('contact_person_phone') }}"
                                                >
                                                <small class="text-muted">
                                                    {{ __('Accepts +2547/+2541, 07/01, or 7/1 formats. We’ll format it as +2547XXXXXXXX or +2541XXXXXXXX.') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 3 --}}
                                <fieldset class="form-section wiz-step d-none" data-step="3">
                                    <legend class="section-title">{{ __('3) Settlement / Payment') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Bank Name') }}</label>
                                                <input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name') }}">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Branch') }}</label>
                                                <input type="text" name="bank_branch" class="form-control" required value="{{ old('bank_branch') }}">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Account Name') }}</label>
                                                <input type="text" name="account_name" class="form-control" required value="{{ old('account_name') }}">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Account Number') }}</label>
                                                <input type="text" name="account_number" class="form-control" required value="{{ old('account_number') }}">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('SWIFT Code') }}</label>
                                                <input type="text" name="swift_code" class="form-control" value="{{ old('swift_code') }}">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Mobile Money Number (Optional)') }}</label>
                                                <input
                                                    type="tel"
                                                    name="mobile_money"
                                                    id="mobile_money"
                                                    class="form-control"
                                                    placeholder="+254712345678 or 0712345678"
                                                    value="{{ old('mobile_money') }}"
                                                >
                                                <small class="text-muted">
                                                    {{ __('Accepts +2547/+2541, 07/01, or 7/1 formats. We’ll format it as +2547XXXXXXXX or +2541XXXXXXXX.') }}
                                                </small>
                                            </div></div>
                                    </div>
                                </fieldset>

                                {{-- STEP 4 --}}
                                <fieldset class="form-section wiz-step d-none" data-step="4">
                                    <legend class="section-title">{{ __('4) Store Information') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Store Logo') }}</label>
                                                <input type="file" name="store_logo" class="form-control" accept=".jpg,.jpeg,.png">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Store Banner') }}</label>
                                                <input type="file" name="store_banner" class="form-control" accept=".jpg,.jpeg,.png">
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group">
                                                <label>{{ __('Store Category') }}</label>
                                                <select name="store_category_id" class="form-control" required>
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach($storeCategories as $sc)
                                                        <option value="{{ $sc->id }}" @selected(old('store_category_id')==$sc->id)>{{ $sc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div></div>
                                        <div class="col-md-12"><div class="form-group">
                                                <label>{{ __('Short Store Description') }}</label>
                                                <textarea name="store_description" class="form-control" rows="3" required>{{ old('store_description') }}</textarea>
                                            </div></div>
                                        <div class="col-md-12"><div class="form-group">
                                                <label>{{ __('Shipping Methods') }}</label>
                                                <div class="d-flex gap-3 flex-wrap">
                                                    <label><input type="checkbox" name="shipping_methods[]" value="local_pickup"> {{ __('Local Pickup') }}</label>
                                                    <label><input type="checkbox" name="shipping_methods[]" value="within_county"> {{ __('Within County Courier') }}</label>
                                                    <label><input type="checkbox" name="shipping_methods[]" value="nationwide"> {{ __('Nationwide Courier') }}</label>
                                                </div>
                                            </div></div>
                                    </div>

                                    <div class="alert alert-info">
                                        {{ __('Note: Your documents will be verified. You will be notified when your store can start uploading/updating products.') }}
                                    </div>

                                    <div class="form-group form-check my-2">
                                        <input type="checkbox" id="agreeTerms" class="form-check-input" value="1" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            {!! __('I agree to the <a href="https://maasaisoko.co.ke/page/45/terms-and-conditions" target="_blank">Terms & Conditions</a> and the <a href="https://maasaisoko.co.ke/page/46/merchant-agreement" target="_blank">Merchant Agreement</a>.') !!}
                                        </label>
                                    </div>
                                </fieldset>

                                {{-- Wizard Controls --}}
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="wizPrev" disabled>{{ __('Back') }}</button>
                                        <button type="button" class="btn btn-light" id="wizSaveDraft">{{ __('Save as Draft') }}</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn theme-btn" id="wizNext">{{ __('Save & Continue') }}</button>
                                        <button type="submit" class="btn theme-btn d-none" id="wizSubmit" disabled>{{ __('Submit for Review') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Inner Section/ -->
    </main>
@endsection

@push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if($gtext['is_recaptcha'] == 1)
            <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        @endif

        {{-- Flash toasts --}}
        <script>
            (function () {
                function fireToast(icon, title) {
                    Swal.fire({ toast:true, position:'top-end', icon, title, showConfirmButton:false, timer:5000, timerProgressBar:true });
                }
                document.addEventListener('DOMContentLoaded', function () {
                    const flash = {
                        success: @json(session('success')),
                        fail: @json(session('fail')),
                        status: @json(session('status')),
                        errors: @json($errors->any() ? $errors->all() : [])
                    };
                    if (flash.success) fireToast('success', flash.success);
                    if (flash.fail)    fireToast('error',  flash.fail);
                    if (flash.status)  fireToast('info',   flash.status);
                    if (flash.errors && flash.errors.length) fireToast('error', flash.errors[0]);
                });
            })();
        </script>

        {{-- Forgiving KE phone validator/normalizer --}}
        <script>
            /** Accepts: +2547XXXXXXXX, 2547XXXXXXXX, 07XXXXXXXX, 7XXXXXXXXX (spaces/dashes/dots/() allowed) */
            const KE_PHONE_RE = /^(?:\+?254|0)?\s*7\d(?:[\s\-\.\)]?\d){7}$/;

            /** Strip everything except digits, keep a single leading + if present */
            function softSanitizePhone(raw) {
                if (!raw) return '';
                raw = raw.trim();
                if (raw.startsWith('+')) {
                    return '+' + raw.slice(1).replace(/[^\d]/g, '');
                }
                return raw.replace(/[^\d]/g, '');
            }

            /** Validate Kenyan mobile in a forgiving way */
            function isValidKEPhone(raw) {
                if (!raw) return false;
                const s = raw.replace(/[^\d+]/g, '');
                return KE_PHONE_RE.test(s);
            }

            /** Normalize to E.164: +2547XXXXXXXX */
            function normalizeKEPhone(raw) {
                if (!raw) return '';
                let s = softSanitizePhone(raw);

                if (s.startsWith('+2547') && s.length === 13) return s;        // +2547XXXXXXXX
                if (s.startsWith('2547')  && s.length === 12) return '+' + s;  // 2547XXXXXXXX
                if (s.startsWith('07')    && s.length === 10) return '+254' + s.slice(1); // 07XXXXXXXX
                if (s.length === 9 && s.startsWith('7')) return '+254' + s;    // 7XXXXXXXXX

                if (isValidKEPhone(s)) {
                    const digits = s.replace(/[^\d]/g, '');
                    if (digits.startsWith('07') && digits.length === 10) return '+254' + digits.slice(1);
                    if (digits.startsWith('2547') && digits.length === 12) return '+' + digits;
                }
                return s;
            }
        </script>

        {{-- Classification helper --}}
        <script>
            (function () {
                const classification = document.getElementById('classification');
                const docLabel = document.getElementById('doc_label');
                const docHelp  = document.getElementById('doc_help');

                function setTxt(el, txt){ if(el) el.textContent = txt; }
                function applyClassificationUI(value) {
                    switch (value) {
                        case 'individual':
                            setTxt(docLabel, '{{ __("National ID / Passport Number") }}');
                            setTxt(docHelp,  '{{ __("Enter your National ID or Passport number.") }}');
                            break;
                        case 'company':
                            setTxt(docLabel, '{{ __("Company Registration/Certificate Number") }}');
                            setTxt(docHelp,  '{{ __("e.g. CPR/20XX/XXXXXX as on your certificate of incorporation.") }}');
                            break;
                        default:
                            setTxt(docLabel, '{{ __("National ID / Passport Number") }}');
                            setTxt(docHelp,  '');
                    }
                }
                if (classification) {
                    applyClassificationUI(classification.value || '');
                    classification.addEventListener('change', (e)=> applyClassificationUI(e.target.value));
                }
            })();
        </script>

        {{-- Wizard + Cascader + Draft + OTP (fixed) --}}
        <script>
            (function(){
                const steps = Array.from(document.querySelectorAll('.wiz-step'));
                const nextBtn = document.getElementById('wizNext');
                const prevBtn = document.getElementById('wizPrev');
                const submitBtn = document.getElementById('wizSubmit');
                const saveDraftBtn = document.getElementById('wizSaveDraft');
                const progress = document.getElementById('wizProgress');
                const stepText = document.getElementById('wizStepText');
                const form = document.getElementById('sellerWizardForm');
                const agree = document.getElementById('agreeTerms');

                let idx = 0;
                function show(i){
                    if (i < 0 || i >= steps.length) return;
                    steps.forEach((s,k)=> s.classList.toggle('d-none', k!==i));
                    prevBtn.disabled = (i===0);
                    nextBtn.classList.toggle('d-none', i===steps.length-1);
                    submitBtn.classList.toggle('d-none', i!==steps.length-1);
                    submitBtn.disabled = !(agree && agree.checked);
                    const pct = ((i+1)/steps.length)*100;
                    progress.style.width = pct+'%';
                    stepText.textContent = `{{ __('Step') }} ${i+1} {{ __('of') }} ${steps.length}`;
                    idx = i;
                }
                function validateStep(i){
                    const fs = steps[i]; if (!fs) return true;
                    const required = fs.querySelectorAll('[required]');
                    for (const el of required){
                        if ((el.type==='checkbox' || el.type==='radio') && !el.checked) { el.focus(); return false; }
                        if (!(el.type==='checkbox' || el.type==='radio')) {
                            if (!el.value || el.value.trim()==='') { el.focus(); return false; }
                        }
                    }

                    // Step-specific
                    const shopPhone    = document.getElementById('shop_phone');
                    const contactPhone = document.getElementById('contact_person_phone');
                    const mobileMoney  = document.getElementById('mobile_money');

                    if (i===0){
                        if (!shopPhone || !isValidKEPhone(shopPhone.value)) {
                            alert("{{ __('Please enter a valid Kenyan mobile number for Shop Phone.') }}");
                            shopPhone && shopPhone.focus();
                            return false;
                        }
                        const geoId = document.getElementById('geo_unit_id')?.value;
                        if (!geoId){ alert("{{ __('Please choose your county/constituency/ward.') }}"); return false; }
                        if (document.getElementById('otp_ok').value !== '1') {
                            alert("{{ __('Please verify your phone number via OTP before continuing.') }}");
                            return false;
                        }
                    }
                    if (i===1){
                        if (!contactPhone || !isValidKEPhone(contactPhone.value)) {
                            alert("{{ __('Please enter a valid Kenyan mobile number for Contact Person.') }}");
                            contactPhone && contactPhone.focus();
                            return false;
                        }
                    }
                    if (i===2){
                        if (mobileMoney && mobileMoney.value.trim() && !isValidKEPhone(mobileMoney.value)) {
                            alert("{{ __('Please enter a valid Kenyan mobile number for Mobile Money or leave it blank.') }}");
                            mobileMoney.focus();
                            return false;
                        }
                    }
                    return true;
                }
                nextBtn.addEventListener('click', ()=>{ if(validateStep(idx)) show(idx+1); });
                prevBtn.addEventListener('click', ()=> show(idx-1));
                agree?.addEventListener('change', ()=> submitBtn.disabled = !agree.checked);

                form.addEventListener('submit', function () {
                    submitBtn.disabled = true; submitBtn.textContent = '{{ __("Submitting…") }}';
                    nextBtn.disabled = true;
                    saveDraftBtn.disabled = true;
                });

                show(0);

                // Save Draft (remove required for draft submit)
                saveDraftBtn.addEventListener('click', ()=>{
                    const flag = document.getElementById('save_mode');
                    flag.value = 'draft';
                    const conEls = form.querySelectorAll('[required]');
                    conEls.forEach(el=>{
                        el.setAttribute('data-was-required','1');
                        el.removeAttribute('required');
                    });
                    if (window.Swal) Swal.fire({toast:true, icon:'info', title:'{{ __("Saving draft…") }}', position:'top-end', showConfirmButton:false, timer:1800});
                    form.submit();
                });

                /* --- OTP (no red border on Request OTP) --- */
                const btnSendOtp   = document.getElementById('btnSendOtp');
                const btnVerifyOtp = document.getElementById('btnVerifyOtp');
                const inpPhone     = document.getElementById('shop_phone');
                const inpCode      = document.getElementById('otp_code');
                const otpHelp      = document.getElementById('otp_help');
                const otpOk        = document.getElementById('otp_ok');

                // Normalize phones on blur for nice UX
                const inpCPPhone = document.getElementById('contact_person_phone');
                const inpMM      = document.getElementById('mobile_money');
                [inpPhone, inpCPPhone, inpMM].forEach(el=>{
                    if (!el) return;
                    el.addEventListener('blur', ()=>{
                        if (!el.value.trim()) return;
                        el.value = normalizeKEPhone(el.value);
                    });
                });

                function clearOtpClasses(){
                    inpPhone.classList.remove('is-valid','is-invalid');
                }
                function markValid(msg){
                    otpOk.value = '1';
                    clearOtpClasses();
                    inpPhone.classList.add('is-valid');
                    otpHelp.textContent = msg || '';
                }
                function markInvalid(msg){
                    otpOk.value = '0';
                    clearOtpClasses();
                    inpPhone.classList.add('is-invalid');
                    otpHelp.textContent = msg || '';
                }

                btnSendOtp?.addEventListener('click', async ()=>{
                    // neutral state on send
                    otpOk.value = '0';
                    clearOtpClasses();
                    otpHelp.textContent = '';

                    const raw = (inpPhone.value || '').trim();
                    if (!raw){
                        markInvalid('{{ __("Enter a phone number first.") }}');
                        inpPhone.focus();
                        return;
                    }
                    if (!isValidKEPhone(raw)){
                        markInvalid('{{ __("That doesn’t look like a valid Kenyan mobile number.") }}');
                        inpPhone.focus();
                        return;
                    }

                    const normalized = normalizeKEPhone(raw);
                    inpPhone.value = normalized;

                    btnSendOtp.disabled = true;
                    otpHelp.textContent = '{{ __("Requesting code…") }}';

                    try{
                        const res = await fetch("{{ route('seller.otp.send') }}", {
                            method:'POST',
                            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' },
                            body: new URLSearchParams({ phone: normalized })
                        });
                        const data = await res.json();

                        if (res.ok && data?.status === 'ok'){
                            // stay neutral (no red/green) until user verifies
                            otpHelp.textContent = '{{ __("Code sent. Please check your phone.") }}';
                        } else {
                            markInvalid(data?.message || '{{ __("Could not send code.") }}');
                        }
                    } catch(e){
                        markInvalid('{{ __("Network error. Try again.") }}');
                    } finally {
                        btnSendOtp.disabled = false;
                    }
                });

                btnVerifyOtp?.addEventListener('click', async ()=>{
                    clearOtpClasses();
                    otpHelp.textContent = '';

                    const phone = (inpPhone.value || '').trim();
                    const code  = (inpCode.value  || '').trim();

                    if (!phone || !code){
                        markInvalid('{{ __("Enter phone and the code you received.") }}');
                        if (!phone) inpPhone.focus(); else inpCode.focus();
                        return;
                    }
                    if (!isValidKEPhone(phone)){
                        markInvalid('{{ __("That doesn’t look like a valid Kenyan mobile number.") }}');
                        inpPhone.focus();
                        return;
                    }

                    const normalized = normalizeKEPhone(phone);
                    inpPhone.value = normalized;

                    btnVerifyOtp.disabled = true;
                    otpHelp.textContent = '{{ __("Verifying…") }}';

                    try{
                        const res = await fetch("{{ route('seller.otp.verify') }}", {
                            method:'POST',
                            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' },
                            body: new URLSearchParams({ phone: normalized, code })
                        });
                        const data = await res.json();

                        if (res.ok && data?.status === 'ok'){
                            markValid('{{ __("Phone verified.") }}');
                        } else {
                            markInvalid(data?.message || '{{ __("Invalid code.") }}');
                        }
                    } catch(e){
                        markInvalid('{{ __("Network error. Try again.") }}');
                    } finally {
                        btnVerifyOtp.disabled = false;
                    }
                });

                // --- GEO Cascader (uses query-param route: frontend.geo.children) ---
                const cascader   = document.getElementById('geoCascader');
                const trigger    = cascader.querySelector('.cascader-trigger');
                const colCounties= document.getElementById('col-counties');
                const colConst   = document.getElementById('col-constits');
                const colWards   = document.getElementById('col-wards');
                const listConst  = colConst.querySelector('.cascader-list');
                const listWards  = colWards.querySelector('.cascader-list');
                const geoInput   = document.getElementById('geo_unit_id');
                const geoPath    = document.getElementById('geo_path');
                const geoText    = document.getElementById('geoTriggerText');
                const geoHelp    = document.getElementById('geoSelectedHelp');
                const closeBtn   = document.getElementById('closeCascader');

                const geoChildrenURL = "{{ route('frontend.geo.children') }}"; // query param route (?parent_id=)

                function close(){ cascader.classList.remove('show'); }
                trigger.addEventListener('click', (e)=>{ e.stopPropagation(); cascader.classList.toggle('show'); });
                closeBtn.addEventListener('click', close);
                document.addEventListener('click', (e)=>{ if(!cascader.contains(e.target)) close(); });

                function clearCol(colEl, ph){
                    colEl.querySelectorAll('ul.cascader-list li').forEach(li=>li.remove());
                    colEl.querySelector('.cascader-empty')?.remove();
                    const ul = colEl.querySelector('ul.cascader-list');
                    ul.classList.add('d-none');
                    const d = document.createElement('div');
                    d.className = 'cascader-empty';
                    d.textContent = ph;
                    colEl.prepend(d);
                }
                function fillList(colEl, items, cls){
                    colEl.querySelector('.cascader-empty')?.remove();
                    const ul = colEl.querySelector('ul.cascader-list');
                    ul.classList.remove('d-none');
                    ul.innerHTML = '';
                    items.forEach(it=>{
                        const li = document.createElement('li');
                        li.className = `cascader-item ${cls}`;
                        li.dataset.id = it.id; li.dataset.name = it.name;
                        li.innerHTML = `<span class="name">${it.name}</span>`;
                        ul.appendChild(li);
                    });
                }
                async function fetchChildren(parentId){
                    try{
                        const params = new URLSearchParams({ parent_id: parentId });
                        const res = await fetch(`${geoChildrenURL}?${params.toString()}`, { headers:{'X-Requested-With':'XMLHttpRequest'} });
                        if(!res.ok) return [];
                        return await res.json();
                    }catch(e){ return []; }
                }

                // county hover -> constituencies; click selects
                colCounties.querySelectorAll('.county').forEach(li=>{
                    li.addEventListener('mouseover', async ()=>{
                        clearCol(colConst, "{{ __('Loading…') }}");
                        clearCol(colWards, "{{ __('Hover a constituency…') }}");
                        const arr = await fetchChildren(li.dataset.id);
                        if (arr.length) fillList(colConst, arr, 'constituency'); else clearCol(colConst, "{{ __('No constituencies') }}");
                    });
                    li.addEventListener('click', ()=>{
                        const name = li.dataset.name;
                        geoInput.value = li.dataset.id;
                        geoPath.value  = name;
                        geoText.textContent = name;
                        geoHelp.textContent = name;
                        close();
                    });
                });

                // constituency hover -> wards; click selects
                listConst.addEventListener('mouseover', async (e)=>{
                    const t = e.target.closest('.constituency'); if(!t) return;
                    clearCol(colWards, "{{ __('Loading…') }}");
                    const arr = await fetchChildren(t.dataset.id);
                    if (arr.length) fillList(colWards, arr, 'ward'); else clearCol(colWards, "{{ __('No wards') }}");
                });
                listConst.addEventListener('click', (e)=>{
                    const t = e.target.closest('.constituency'); if(!t) return;
                    const name = t.dataset.name;
                    geoInput.value = t.dataset.id;
                    geoPath.value  = name;
                    geoText.textContent = name;
                    geoHelp.textContent = name;
                    close();
                });

                // ward click selects
                listWards.addEventListener('click', (e)=>{
                    const t = e.target.closest('.ward'); if(!t) return;
                    const name = t.dataset.name;
                    geoInput.value = t.dataset.id;
                    geoPath.value  = name;
                    geoText.textContent = name;
                    geoHelp.textContent = name;
                    close();
                });
            })();
        </script>
    @endpush

