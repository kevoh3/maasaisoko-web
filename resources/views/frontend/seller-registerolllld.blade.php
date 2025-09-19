@extends('layouts.frontend')

@section('title', __('Register'))

@php
    $gtext = gtext();

    // --- Safe fallbacks if controller didn't pass data ---
    // Country: default to Kenya id
    $countryId = $countryId
        ?? \App\Models\Country::where('country_name','Kenya')->value('id');

    // Geo level: 'county' id
    $countyLevelId = \Illuminate\Support\Facades\DB::table('geo_levels')
        ->whereIn('name', ['county','County'])->value('id');

    // Counties: id + name (if not provided)
    $counties = $counties
        ?? \Illuminate\Support\Facades\DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $countyLevelId)
            ->orderBy('name')
            ->get(['id','name']);

    // Optional collections
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

                            {{-- We will show toasts instead of inline alerts --}}

                            <form id="sellerWizardForm" class="form" method="POST" action="{{ route('frontend.sellerRegister') }}" enctype="multipart/form-data" novalidate>
                                @csrf
                                {{-- Global hidden --}}
                                <input type="hidden" name="geo_unit_id" id="geo_unit_id" value="">
                                <input type="hidden" name="geo_path" id="geo_path" value="">
                                <input type="hidden" name="save_mode" id="save_mode" value="">

                                {{-- Progress --}}
                                <div class="form-section mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="section-title m-0">{{ __('Seller Onboarding') }}</div>
                                        <div id="wizStepText" class="small text-muted">{{ __('Step 1 of 4') }}</div>
                                    </div>
                                    <div class="progress mt-2 theme-progress" style="height:8px;">
                                        <div id="wizProgress" class="progress-bar " role="progressbar" style="width: 25%;"></div>
                                    </div>
                                </div>

                                {{-- STEP 1: Registration --}}
                                <fieldset class="form-section wiz-step" data-step="1">
                                    <legend class="section-title">{{ __('1) Registration') }}</legend>
                                    <div class="row">
                                        {{-- Full Name / Business Name --}}
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
                                        {{-- Mobile (+254) --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Mobile Number (+254)') }}</label>
                                                <input type="tel" name="phone" class="form-control" placeholder="+2547XXXXXXXX" required pattern="^\+2547\d{8}$" value="{{ old('phone') }}">
                                                <small class="text-muted">{{ __('Format: +2547XXXXXXXX') }}</small>
                                            </div>
                                        </div>
                                        {{-- Physical Address --}}
                                        <div class="col-md-6">
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
                                    </div>
                                </fieldset>

                                {{-- STEP 2: Verification --}}
                                <fieldset class="form-section wiz-step d-none" data-step="2">
                                    <legend class="section-title">{{ __('2) Verification') }}</legend>
                                    <div class="row">
                                        {{-- National ID / Passport Number --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label id="doc_label">{{ __('National ID / Passport Number') }}</label>
                                                <input type="text" name="document_number" class="form-control" required value="{{ old('document_number') }}">
                                                <small id="doc_help" class="form-text text-muted"></small>
                                            </div>
                                        </div>
                                        {{-- Attach copy (ID/Passport) --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Attach Copy (ID/Passport)') }}</label>
                                                <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                            </div>
                                        </div>

                                        {{-- Business License / Certificate of Incorporation --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Business License / Certificate of Incorporation (attach)') }}</label>
                                                <input type="file" name="business_license_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                        </div>

                                        {{-- KRA PIN --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('KRA PIN (Tax ID)') }}</label>
                                                <input type="text" name="kra_pin" class="form-control" placeholder="A123456789B" value="{{ old('kra_pin') }}">
                                            </div>
                                        </div>

                                        {{-- Brand authorization (optional) --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Brand Authorization (if selling branded products)') }}</label>
                                                <input type="file" name="brand_auth_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                        </div>

                                        {{-- Contact person --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Contact Person (Name)') }}</label>
                                                <input type="text" name="contact_person_name" class="form-control" value="{{ old('contact_person_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Contact Person (Phone)') }}</label>
                                                <input type="tel" name="contact_person_phone" class="form-control" placeholder="+2547XXXXXXXX" pattern="^\+2547\d{8}$" value="{{ old('contact_person_phone') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 3: Settlement / Payment --}}
                                <fieldset class="form-section wiz-step d-none" data-step="3">
                                    <legend class="section-title">{{ __('3) Settlement / Payment') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Bank Name') }}</label>
                                                <input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Branch') }}</label>
                                                <input type="text" name="bank_branch" class="form-control" required value="{{ old('bank_branch') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Account Name') }}</label>
                                                <input type="text" name="account_name" class="form-control" required value="{{ old('account_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Account Number') }}</label>
                                                <input type="text" name="account_number" class="form-control" required value="{{ old('account_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('SWIFT Code') }}</label>
                                                <input type="text" name="swift_code" class="form-control" value="{{ old('swift_code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Mobile Money Number (Optional)') }}</label>
                                                <input type="tel" name="mobile_money" class="form-control" placeholder="+2547XXXXXXXX" pattern="^\+2547\d{8}$" value="{{ old('mobile_money') }}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 4: Store Info --}}
                                <fieldset class="form-section wiz-step d-none" data-step="4">
                                    <legend class="section-title">{{ __('4) Store Information') }}</legend>
                                    <div class="row">
                                        {{-- Store Logo --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Store Logo') }}</label>
                                                <input type="file" name="store_logo" class="form-control" accept=".jpg,.jpeg,.png">
                                            </div>
                                        </div>
                                        {{-- Store Banner --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Store Banner') }}</label>
                                                <input type="file" name="store_banner" class="form-control" accept=".jpg,.jpeg,.png">
                                            </div>
                                        </div>
                                        {{-- Category --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Store Category') }}</label>
                                                <select name="store_category_id" class="form-control" required>
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach($storeCategories as $sc)
                                                        <option value="{{ $sc->id }}" @selected(old('store_category_id')==$sc->id)>{{ $sc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{-- Short description --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Short Store Description') }}</label>
                                                <textarea name="store_description" class="form-control" rows="3" required>{{ old('store_description') }}</textarea>
                                            </div>
                                        </div>
                                        {{-- Shipping Methods --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Shipping Methods') }}</label>
                                                <div class="d-flex gap-3 flex-wrap">
                                                    <label><input type="checkbox" name="shipping_methods[]" value="local_pickup"> {{ __('Local Pickup') }}</label>
                                                    <label><input type="checkbox" name="shipping_methods[]" value="within_county"> {{ __('Within County Courier') }}</label>
                                                    <label><input type="checkbox" name="shipping_methods[]" value="nationwide"> {{ __('Nationwide Courier') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notice & Agreements --}}
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
    {{-- SweetAlert2 for toasts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if($gtext['is_recaptcha'] == 1)
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    @endif
    <script>
        // --- Toasts on redirect (success/fail/validation) ---
        (function () {
            function fireToast(icon, title) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: title,
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
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
                if (flash.errors && flash.errors.length) {
                    fireToast('error', flash.errors[0]);
                }
            });
        })();
    </script>

    <script>
        // Optional slug helper (safe-guarded)
        (function(){
            const el = document.getElementById('shop_url');
            if (!el) return;
            el.addEventListener('blur', function(){
                const val = (el.value || '').trim(); if (!val) return;
                if (typeof base_url === 'undefined' || typeof $ === 'undefined') return;
                $.ajax({
                    type : 'POST',
                    url: base_url + '/frontend/hasShopSlug',
                    data: 'shop_url='+val,
                    success: function (response) { if (response && response.slug) el.value = response.slug; }
                });
            });
        })();

        // Classification helper (guard missing elements)
        (function () {
            const classification = document.getElementById('classification');
            const docLabel = document.getElementById('doc_label');
            const docHelp  = document.getElementById('doc_help');
            const compliance = document.getElementById('compliance_section'); // may not exist

            function setTxt(el, txt){ if(el) el.textContent = txt; }

            function applyClassificationUI(value) {
                switch (value) {
                    case 'individual':
                        setTxt(docLabel, '{{ __("National ID / Passport Number") }}');
                        setTxt(docHelp,  '{{ __("Enter your National ID or Passport number.") }}');
                        if (compliance) compliance.style.display = 'none';
                        break;
                    case 'company':
                        setTxt(docLabel, '{{ __("Company Registration/Certificate Number") }}');
                        setTxt(docHelp,  '{{ __("e.g. CPR/20XX/XXXXXX as on your certificate of incorporation.") }}');
                        if (compliance) compliance.style.display = '';
                        break;
                    default:
                        setTxt(docLabel, '{{ __("National ID / Passport Number") }}');
                        setTxt(docHelp,  '');
                        if (compliance) compliance.style.display = 'none';
                }
            }
            if (classification) {
                applyClassificationUI(classification.value || '');
                classification.addEventListener('change', (e)=> applyClassificationUI(e.target.value));
            }
        })();

        // Wizard + Cascader + Draft
        (function(){
            // --- Wizard ---
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
                if (prevBtn) prevBtn.disabled = (i===0);
                if (nextBtn) nextBtn.classList.toggle('d-none', i===steps.length-1);
                if (submitBtn) {
                    submitBtn.classList.toggle('d-none', i!==steps.length-1);
                    submitBtn.disabled = !(agree && agree.checked);
                }
                const pct = ((i+1)/steps.length)*100;
                if (progress) progress.style.width = pct+'%';
                if (stepText) stepText.textContent = `{{ __('Step') }} ${i+1} {{ __('of') }} ${steps.length}`;
                idx = i;
            }
            function validateStep(i){
                const fs = steps[i];
                if (!fs) return true;
                const required = fs.querySelectorAll('[required]');
                for (const el of required){
                    if ((el.type==='checkbox' || el.type==='radio') && !el.checked) { el.focus(); return false; }
                    if (!(el.type==='checkbox' || el.type==='radio')) {
                        if (!el.value || el.value.trim()==='') { el.focus(); return false; }
                    }
                    if (el.pattern){
                        const re = new RegExp(el.pattern);
                        if (!re.test(el.value)) { el.focus(); return false; }
                    }
                }
                // Step 1 requires a location
                if (i===0){
                    const geoId = document.getElementById('geo_unit_id')?.value;
                    if (!geoId){ alert("{{ __('Please choose your county/constituency/ward.') }}"); return false; }
                }
                return true;
            }
            if (nextBtn) nextBtn.addEventListener('click', ()=>{ if(validateStep(idx)) show(idx+1); });
            if (prevBtn) prevBtn.addEventListener('click', ()=> show(idx-1));
            if (agree && submitBtn) agree.addEventListener('change', ()=> submitBtn.disabled = !agree.checked);

            // prevent double submit + show "Submitting…" state
            if (form) form.addEventListener('submit', function () {
                if (submitBtn){ submitBtn.disabled = true; submitBtn.textContent = '{{ __("Submitting…") }}'; }
                if (nextBtn){ nextBtn.disabled = true; }
                if (saveDraftBtn){ saveDraftBtn.disabled = true; }
            });

            show(0);

            // Save as Draft (removes constraints temporarily and submit with flag)
            if (saveDraftBtn) saveDraftBtn.addEventListener('click', ()=>{
                const flag = document.getElementById('save_mode');
                if (flag) flag.value = 'draft';
                // Temporarily drop required/pattern to bypass browser validation
                const conEls = form.querySelectorAll('[required],[pattern]');
                conEls.forEach(el=>{
                    if (el.hasAttribute('required')) el.setAttribute('data-was-required','1');
                    if (el.hasAttribute('pattern')) el.setAttribute('data-was-pattern', el.getAttribute('pattern'));
                    el.removeAttribute('required'); el.removeAttribute('pattern');
                });
                // immediate "saving draft" toast (optional)
                if (window.Swal) {
                    Swal.fire({toast:true, icon:'info', title:'{{ __("Saving draft…") }}', position:'top-end', showConfirmButton:false, timer:1800});
                }
                form.submit();
            });

            // --- Location Cascader ---
            const cascader   = document.getElementById('geoCascader');
            if (!cascader) return;
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
            const childrenURL= "{{ route('geo.children') }}";

            let currentCounty = null;       // {id,name}
            let currentConstituency = null; // {id,name}

            function open(){ cascader.classList.add('show'); }
            function close(){ cascader.classList.remove('show'); }
            if (trigger) trigger.addEventListener('click', (e)=>{ e.stopPropagation(); cascader.classList.toggle('show'); });
            if (closeBtn) closeBtn.addEventListener('click', close);
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
                    const res = await fetch(`${childrenURL}?${params.toString()}`, { headers:{'X-Requested-With':'XMLHttpRequest'} });
                    if(!res.ok) return [];
                    return await res.json();
                }catch(e){ return []; }
            }

            // County: mouseover loads constituencies; click selects county
            colCounties.querySelectorAll('.county').forEach(li=>{
                li.addEventListener('mouseover', async ()=>{
                    currentCounty = { id: li.dataset.id, name: li.dataset.name };
                    currentConstituency = null;
                    clearCol(colConst, "{{ __('Loading…') }}");
                    clearCol(colWards, "{{ __('Hover a constituency…') }}");
                    const arr = await fetchChildren(li.dataset.id);
                    if (arr.length) fillList(colConst, arr, 'constituency'); else clearCol(colConst, "{{ __('No constituencies') }}");
                });
                li.addEventListener('click', ()=>{
                    const name = li.dataset.name;
                    if (geoInput) geoInput.value = li.dataset.id;
                    if (geoPath)  geoPath.value  = name;
                    if (geoText)  geoText.textContent = name;
                    if (geoHelp)  geoHelp.textContent = name;
                    close();
                });
            });

            // Constituency: mouseover loads wards (delegation). click selects constituency
            listConst.addEventListener('mouseover', async (e)=>{
                const t = e.target.closest('.constituency'); if(!t) return;
                currentConstituency = { id: t.dataset.id, name: t.dataset.name };
                clearCol(colWards, "{{ __('Loading…') }}");
                const arr = await fetchChildren(t.dataset.id);
                if (arr.length) fillList(colWards, arr, 'ward'); else clearCol(colWards, "{{ __('No wards') }}");
            });

            listConst.addEventListener('click', (e)=>{
                const t = e.target.closest('.constituency'); if(!t) return;
                currentConstituency = { id: t.dataset.id, name: t.dataset.name };
                const name = [currentCounty?.name, currentConstituency?.name].filter(Boolean).join(' / ');
                if (geoInput) geoInput.value = t.dataset.id;
                if (geoPath)  geoPath.value  = name;
                if (geoText)  geoText.textContent = name;
                if (geoHelp)  geoHelp.textContent = name;
                close();
            });

            // Ward click selects ward
            listWards.addEventListener('click', (e)=>{
                const t = e.target.closest('.ward'); if(!t) return;
                const name = [currentCounty?.name, currentConstituency?.name, t.dataset.name].filter(Boolean).join(' / ');
                if (geoInput) geoInput.value = t.dataset.id;
                if (geoPath)  geoPath.value  = name;
                if (geoText)  geoText.textContent = name;
                if (geoHelp)  geoHelp.textContent = name;
                close();
            });
        })();
    </script>
@endpush
