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

        /* Review table */
        .review-grid {border:1px solid #eee;border-radius:10px;overflow:hidden}
        .review-grid .row{margin:0;border-top:1px solid #f1f2f4}
        .review-grid .row:first-child{border-top:none}
        .review-grid .cell{padding:10px 12px}
        .review-grid .label{background:#fafbfc;font-weight:600}
        canvas#sigPad{width:100%;max-width:560px;border:1px dashed #cbd5e1;border-radius:8px}
    </style>
@endpush

@section('content')
    <main class="main">
        <!-- Breadcrumb -->
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
                    <div class="col-lg-6"><div class="page-title"><h1>{{ __('Register') }}</h1></div></div>
                </div>
            </div>
        </div>

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
                            <p>Karibu! Thank you for choosing to partner with us. We are excited to
                                showcase your unique African Cultural artifacts to the world. To ensure a
                                seamless and compliant experience for both you and our customers, please
                                fill out this form in its entirety.
                            </p>
                            <hr>
                            <p>
                                Data Protection Clause: We are committed to protecting your privacy and
                                personal data in accordance with the Kenya Data Protection Act, 2019. The
                                information provided will be used solely for the purpose of merchant
                                verification, payment processing, and platform management.
                            </p>
                            <p>{{ __('Please fill in the information below') }}</p>

                            <form id="sellerWizardForm" class="form" method="POST" action="{{ route('frontend.sellerRegister') }}" enctype="multipart/form-data" novalidate>
                                @csrf
                                <input type="hidden" name="geo_unit_id" id="geo_unit_id" value="">
                                <input type="hidden" name="geo_path" id="geo_path" value="">
                                <input type="hidden" name="save_mode" id="save_mode" value="">
                                <input type="hidden" id="otp_ok" value="0">
                                <input type="hidden" name="signature_data" id="signature_data" value="">
                                <input type="hidden" name="current_step" id="current_step" value="{{ old('current_step', 1) }}">

                                {{-- Progress --}}
                                <div class="form-section mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="section-title m-0">{{ __('Seller Onboarding') }}</div>
                                        <div id="wizStepText" class="small text-muted">{{ __('Step 1 of 5') }}</div>
                                    </div>
                                    <div class="progress mt-2 theme-progress" style="height:8px;">
                                        <div id="wizProgress" class="progress-bar" role="progressbar" style="width: 20%;"></div>
                                    </div>
                                </div>

                                {{-- STEP 1: Register Seller --}}
                                <fieldset class="form-section wiz-step" data-step="1">
                                    <legend class="section-title">{{ __('1) Register Seller') }}</legend>
                                    <div class="row">
                                        {{-- Username --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Username') }}</label>
                                                <input type="text" name="username" class="form-control" minlength="4" pattern="[A-Za-z0-9._-]{4,}" placeholder="{{ __('at least 4 chars, letters & numbers') }}" required value="{{ old('username') }}">
                                            </div>
                                        </div>
                                        {{-- Passwords --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Password') }}</label>
                                                <input type="password" name="password" class="form-control" minlength="6" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Confirm Password') }}</label>
                                                <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                                            </div>
                                        </div>

                                        {{-- Phone + OTP --}}
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>{{ __('Phone Number') }}</label>
                                                <div class="d-flex gap-2 otp-row">
                                                    <input type="tel" id="shop_phone" name="shop_phone" class="form-control" placeholder="+254712345678 or 0712345678" required value="{{ old('shop_phone') }}">
                                                    <button type="button" class="btn btn-outline-primary" id="btnSendOtp">{{ __('Send Code') }}</button>
                                                </div>
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

                                        {{-- Seller Type --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Seller Type') }}</label>
                                                <select name="seller_type" id="seller_type" class="form-control" required>
                                                    <option value="">{{ __('Select type') }}</option>
                                                    <option value="sole_proprietor" @selected(old('seller_type')==='sole_proprietor')>{{ __('Sole Proprietor') }}</option>
                                                    <option value="partnership" @selected(old('seller_type')==='partnership')>{{ __('Partnership') }}</option>
                                                    <option value="company" @selected(old('seller_type')==='company')>{{ __('Company') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- Email --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Email') }}</label>
                                                <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}">
                                            </div>
                                        </div>
                                        {{-- Group optional --}}
                                        <div class="col-md-4">
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

                                        {{-- Primary Contact --}}
                                        <div class="col-md-12"><hr></div>
                                        <div class="col-md-12"><strong>{{ __('Primary Contact Person') }}</strong></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Full Name') }}</label>
                                                <input type="text" name="contact_person_name" class="form-control" value="{{ old('contact_person_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Designation') }}</label>
                                                <select name="contact_person_designation" class="form-control" required>
                                                    <option value="">{{ __('Select') }}</option>
                                                    <option value="proprietor">{{ __('Proprietor') }}</option>
                                                    <option value="director">{{ __('Director') }}</option>
                                                    <option value="manager">{{ __('Manager') }}</option>
                                                    <option value="agent">{{ __('Agent') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Mobile Phone Number') }}</label>
                                                <input type="tel" name="contact_person_phone" id="contact_person_phone" class="form-control" placeholder="+254..." required value="{{ old('contact_person_phone') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Email Address') }}</label>
                                                <input type="email" name="contact_person_email" class="form-control" value="{{ old('contact_person_email') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('ID/Passport Number') }}</label>
                                                <input type="text" name="contact_person_id" class="form-control" value="{{ old('contact_person_id') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Personal KRA PIN') }}</label>
                                                <input type="text" name="personal_kra_pin" class="form-control" placeholder="A123456789B" value="{{ old('personal_kra_pin') }}">
                                            </div>
                                        </div>

                                        {{-- Physical Address --}}
                                        <div class="col-md-12"><hr></div>
                                        <div class="col-md-12"><strong>{{ __('Physical Address') }}</strong></div>
                                        <div class="col-md-4">
                                            <div class="form-group"><label>{{ __('Building Name') }}</label><input type="text" name="address_building" class="form-control" value="{{ old('address_building') }}"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group"><label>{{ __('Street/Road') }}</label><input type="text" name="address_street" class="form-control" required value="{{ old('address_street') }}"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group"><label>{{ __('City/Town') }}</label><input type="text" name="address_city" class="form-control" required value="{{ old('address_city') }}"></div>
                                        </div>

                                        {{-- Location Cascader (County → Constituency → Ward) --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Location (County → Sub-County → Ward)') }}</label>
                                                <div class="cascader" id="geoCascader" data-country="{{ (int)$countryId }}">
                                                    <button type="button" class="btn btn-light cascader-trigger w-100 text-start">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <span id="geoTriggerText">{{ __('Choose County') }}</span>
                                                    </button>
                                                    <div class="cascader-panel" style="max-width:100%;">
                                                        <div class="cascader-cols">
                                                            <div class="cascader-col" id="col-counties" aria-label="Counties">
                                                                <ul class="cascader-list">
                                                                    @forelse($counties as $c)
                                                                        <li class="cascader-item county" data-id="{{ $c->id }}" data-name="{{ $c->name }}"><span class="name">{{ $c->name }}</span></li>
                                                                    @empty
                                                                        <li class="cascader-item"><span class="name text-muted">{{ __('No counties found') }}</span></li>
                                                                    @endforelse
                                                                </ul>
                                                            </div>
                                                            <div class="cascader-col" id="col-constits" aria-label="Constituencies">
                                                                <div class="cascader-empty">{{ __('Hover a county…') }}</div>
                                                                <ul class="cascader-list d-none"></ul>
                                                            </div>
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

                                        {{-- Postal --}}
                                        <div class="col-md-12"><hr></div>
                                        <div class="col-md-12"><strong>{{ __('Postal Address') }}</strong></div>
                                        <div class="col-md-4">
                                            <div class="form-group"><label>{{ __('P.O. Box') }}</label><input type="text" name="postal_box" class="form-control" value="{{ old('postal_box') }}"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group"><label>{{ __('Postal Code') }}</label><input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}"></div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 2: Documentation (conditional) --}}
                                <fieldset class="form-section wiz-step d-none" data-step="2">
                                    <legend class="section-title">{{ __('2) Seller Documentation') }}</legend>

                                    {{-- Company --}}
                                    <div id="docs_company" class="d-none">
                                        <div class="alert alert-secondary py-2 mb-3">{{ __('For Companies') }}</div>
                                        <div class="row">
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Certificate of Incorporation') }}</label><input type="file" name="doc_company_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('KRA Business PIN Certificate') }}</label><input type="file" name="doc_company_kra" class="form-control" accept=".pdf"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Single Business Permit (SBP)') }}</label><input type="file" name="doc_company_sbp" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Identification Documents (all directors)') }}</label><input type="file" name="doc_company_ids[]" class="form-control" accept=".pdf,.jpg,.jpeg" multiple></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Board Resolution authorizing contact') }}</label><input type="file" name="doc_company_board_resolution" class="form-control" accept=".pdf"></div></div>
                                        </div>
                                    </div>

                                    {{-- Partnership --}}
                                    <div id="docs_partnership" class="d-none">
                                        <div class="alert alert-secondary py-2 mb-3">{{ __('For Partnerships') }}</div>
                                        <div class="row">
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Business Name Registration Certificate (BRS)') }}</label><input type="file" name="doc_partner_brs" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Identification Documents (all partners)') }}</label><input type="file" name="doc_partner_ids[]" class="form-control" accept=".pdf,.jpg,.jpeg" multiple></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('KRA Business PIN') }}</label><input type="file" name="doc_partner_kra" class="form-control" accept=".pdf"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Single Business Permit (SBP)') }}</label><input type="file" name="doc_partner_sbp" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Partnership Agreement') }}</label><input type="file" name="doc_partner_agreement" class="form-control" accept=".pdf"></div></div>
                                        </div>
                                    </div>

                                    {{-- Sole Proprietor --}}
                                    <div id="docs_sole" class="">
                                        <div class="alert alert-secondary py-2 mb-3">{{ __('For Individuals / Sole Proprietor') }}</div>
                                        <div class="row">
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Business Name Registration Certificate (BRS)') }}</label><input type="file" name="doc_sole_brs" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Identification Document (ID/Passport)') }}</label><input type="file" name="doc_sole_id" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Personal KRA PIN') }}</label><input type="file" name="doc_sole_kra" class="form-control" accept=".pdf"></div></div>
                                            <div class="col-md-6"><div class="form-group"><label>{{ __('Single Business Permit (SBP)') }}</label><input type="file" name="doc_sole_sbp" class="form-control" accept=".pdf,.jpg,.jpeg"></div></div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- STEP 3: Settlement --}}
                                <fieldset class="form-section wiz-step d-none" data-step="3">
                                    <legend class="section-title">{{ __('3) Settlement / Bank Details') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Bank Account Name') }}</label><input type="text" name="account_name" class="form-control" required value="{{ old('account_name') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Bank Name') }}</label><input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Branch Name') }}</label><input type="text" name="bank_branch" class="form-control" required value="{{ old('bank_branch') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('SWIFT Code') }}</label><input type="text" name="swift_code" class="form-control" value="{{ old('swift_code') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Account Number') }}</label><input type="text" name="account_number" class="form-control" required value="{{ old('account_number') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Account Type') }}</label>
                                                <select name="account_type" class="form-control" required>
                                                    <option value="">{{ __('Select') }}</option>
                                                    <option value="current">{{ __('Current') }}</option>
                                                    <option value="savings">{{ __('Savings') }}</option>
                                                </select>
                                            </div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Mobile Money Number') }}</label><input type="tel" name="mobile_money" id="mobile_money" class="form-control" placeholder="+254..." value="{{ old('mobile_money') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Mobile Money Paybill / Pochi') }}</label><input type="text" name="mobile_money_paybill" class="form-control" value="{{ old('mobile_money_paybill') }}"></div></div>
                                    </div>
                                </fieldset>

                                {{-- STEP 4: Store Info --}}
                                <fieldset class="form-section wiz-step d-none" data-step="4">
                                    <legend class="section-title">{{ __('4) Store Information') }}</legend>
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Store Name (shown to customers)') }}</label><input type="text" name="shop_name" class="form-control" required value="{{ old('shop_name') }}"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Store Category') }}</label>
                                                <select name="store_category_id" class="form-control" required>
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach($storeCategories as $sc)
                                                        <option value="{{ $sc->id }}" @selected(old('store_category_id')==$sc->id)>{{ $sc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div></div>
                                        <div class="col-md-12"><div class="form-group"><label>{{ __('Store Description (short)') }}</label><textarea name="store_description" class="form-control" rows="3" required>{{ old('store_description') }}</textarea></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Store Logo') }}</label><input type="file" name="store_logo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Store Banner') }}</label><input type="file" name="store_banner" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx"></div></div>

                                        {{-- Store physical location split --}}
                                        <div class="col-md-12"><hr></div>
                                        <div class="col-md-12"><strong>{{ __('Store Location') }}</strong></div>
                                        <div class="col-md-3"><div class="form-group"><label>{{ __('City/Town') }}</label><input type="text" name="store_city" class="form-control" required></div></div>
                                        <div class="col-md-3"><div class="form-group"><label>{{ __('County') }}</label><input type="text" name="store_county" class="form-control" required></div></div>
                                        <div class="col-md-3"><div class="form-group"><label>{{ __('Sub-County') }}</label><input type="text" name="store_sub_county" class="form-control"></div></div>
                                        <div class="col-md-3"><div class="form-group"><label>{{ __('Ward') }}</label><input type="text" name="store_ward" class="form-control"></div></div>
                                        <div class="col-md-6"><div class="form-group"><label>{{ __('Store Geographic Coordinates (lat,long)') }}</label><input type="text" name="store_coords" class="form-control" placeholder="-1.286389,36.817223"></div></div>
                                    </div>
                                </fieldset>

                                {{-- STEP 5: Review + Declaration --}}
                                <fieldset class="form-section wiz-step d-none" data-step="5">
                                    <legend class="section-title">{{ __('5) Review Application') }}</legend>

                                    <div id="reviewBox" class="review-grid mb-3"></div>

                                    <div class="form-group form-check my-2">
                                        <input type="checkbox" id="agreeTerms" class="form-check-input" value="1" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            {!! __('I, :name, hereby declare that the information provided is true and complete. I agree to the :tac and the :ma.', [
                                                'name' => '<strong><span id="declName">'.e(old('contact_person_name')).'</span></strong>',
                                                'tac'  => '<a href="https://maasaisoko.co.ke/page/45/terms-and-conditions" target="_blank">Terms & Conditions</a>',
                                                'ma'   => '<a href="https://maasaisoko.co.ke/page/46/merchant-agreement" target="_blank">Merchant Agreement</a>',
                                            ]) !!}
                                        </label>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="d-block">{{ __('Digital Signature') }}</label>
                                            <canvas id="sigPad" height="180"></canvas>
                                            <div class="mt-2 d-flex gap-2">
                                                <button type="button" id="sigClear" class="btn btn-sm btn-outline-secondary">{{ __('Clear') }}</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="d-block">{{ __('Date') }}</label>
                                            <input type="date" name="declaration_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                        </div>
                                    </div>

                                    @if($gtext['is_recaptcha'] == 1)
                                        <div class="mt-3">
                                            <div class="g-recaptcha" data-sitekey="{{ $gtext['sitekey'] ?? '' }}"></div>
                                        </div>
                                    @endif
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
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
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

    {{-- Wizard + Cascader + Draft + OTP + Signature + Review --}}
    <script>
        (function(){
            const steps        = Array.from(document.querySelectorAll('.wiz-step'));
            const nextBtn      = document.getElementById('wizNext');
            const prevBtn      = document.getElementById('wizPrev');
            const submitBtn    = document.getElementById('wizSubmit');
            const saveDraftBtn = document.getElementById('wizSaveDraft');
            const progress     = document.getElementById('wizProgress');
            const stepText     = document.getElementById('wizStepText');
            const form         = document.getElementById('sellerWizardForm');
            const agree        = document.getElementById('agreeTerms');
            const stepField    = document.getElementById('current_step');

            // open on the step the server told us
            let idx = Math.max(1, parseInt(stepField.value || '1',10)) - 1;

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
                stepField.value = (idx+1);
                if (i===4 && typeof buildReview === 'function') buildReview();
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
                if (i===0){
                    const geoId = document.getElementById('geo_unit_id')?.value;
                    if (!geoId){ alert("{{ __('Please choose your county/constituency/ward.') }}"); return false; }
                    if (document.getElementById('otp_ok').value !== '1') {
                        alert("{{ __('Please verify your phone number via OTP before continuing.') }}");
                        return false;
                    }
                }
                return true;
            }

            // SAVE & CONTINUE → POST this step; server persists and returns with next step
            nextBtn.addEventListener('click', ()=>{
                if(!validateStep(idx)) return;
                document.getElementById('save_mode').value = 'submit';
                form.submit();
            });

            // BACK → just move UI locally
            prevBtn.addEventListener('click', ()=> show(idx-1));

            // SAVE DRAFT → POST this step as draft and remain
            saveDraftBtn.addEventListener('click', ()=>{
                document.getElementById('save_mode').value = 'draft';
                form.querySelectorAll('[required]').forEach(el=>{ el.setAttribute('data-was-required','1'); el.removeAttribute('required'); });
                if (window.Swal) Swal.fire({toast:true, icon:'info', title:'{{ __("Saving draft…") }}', position:'top-end', showConfirmButton:false, timer:1200});
                form.submit();
            });

            // Final submit: attach signature etc.
            form.addEventListener('submit', function () {
                const sigData = (window._sigPad && !window._sigPad.isEmpty()) ? window._sigPad.toDataURL('image/png') : '';
                document.getElementById('signature_data').value = sigData;
                submitBtn.disabled = true; submitBtn.textContent = '{{ __("Submitting…") }}';
                nextBtn.disabled = true; saveDraftBtn.disabled = true;
            });

            agree?.addEventListener('change', ()=> submitBtn.disabled = !agree.checked);

            /* --- OTP --- */
            const btnSendOtp   = document.getElementById('btnSendOtp');
            const btnVerifyOtp = document.getElementById('btnVerifyOtp');
            const inpPhone     = document.getElementById('shop_phone');
            const inpCode      = document.getElementById('otp_code');
            const otpHelp      = document.getElementById('otp_help');
            const otpOk        = document.getElementById('otp_ok');

            function setOtpNeutral(msg){ otpHelp.textContent = msg || ''; }
            function setOtpOK(msg){ otpOk.value = '1'; otpHelp.textContent = msg || ''; }
            function setOtpFail(msg){ otpOk.value = '0'; otpHelp.textContent = msg || ''; }

            btnSendOtp?.addEventListener('click', async ()=>{
                otpOk.value = '0';
                const phone = (inpPhone.value || '').trim();
                if (!phone){ setOtpFail('{{ __("Enter a phone number first.") }}'); inpPhone.focus(); return; }
                btnSendOtp.disabled = true; setOtpNeutral('{{ __("Requesting code…") }}');
                try{
                    const res = await fetch("{{ route('seller.otp.send') }}", {
                        method:'POST',
                        headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' },
                        body: new URLSearchParams({ phone })
                    });
                    const data = await res.json();
                    if (res.ok && data?.status === 'ok'){ setOtpNeutral('{{ __("Code sent. Please check your phone.") }}'); }
                    else { setOtpFail(data?.message || '{{ __("Could not send code.") }}'); }
                } catch{ setOtpFail('{{ __("Network error. Try again.") }}'); }
                finally { btnSendOtp.disabled = false; }
            });

            btnVerifyOtp?.addEventListener('click', async ()=>{
                const phone = (inpPhone.value || '').trim();
                const code  = (inpCode.value  || '').trim();
                if (!phone || !code){ setOtpFail('{{ __("Enter phone and the code you received.") }}'); if (!phone) inpPhone.focus(); else inpCode.focus(); return; }
                btnVerifyOtp.disabled = true; setOtpNeutral('{{ __("Verifying…") }}');
                try{
                    const res = await fetch("{{ route('seller.otp.verify') }}", {
                        method:'POST',
                        headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' },
                        body: new URLSearchParams({ phone, code })
                    });
                    const data = await res.json();
                    if (res.ok && data?.status === 'ok'){ setOtpOK('{{ __("Phone verified.") }}'); }
                    else { setOtpFail(data?.message || '{{ __("Invalid code.") }}'); }
                } catch{ setOtpFail('{{ __("Network error. Try again.") }}'); }
                finally { btnVerifyOtp.disabled = false; }
            });

            // --- GEO Cascader ---
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
            const geoChildrenURL = "{{ route('frontend.geo.children') }}"; // ?parent_id=
            function close(){ cascader.classList.remove('show'); }
            trigger.addEventListener('click', (e)=>{ e.stopPropagation(); cascader.classList.toggle('show'); });
            closeBtn.addEventListener('click', close);
            document.addEventListener('click', (e)=>{ if(!cascader.contains(e.target)) close(); });

            function clearCol(colEl, ph){
                colEl.querySelectorAll('ul.cascader-list li').forEach(li=>li.remove());
                colEl.querySelector('.cascader-empty')?.remove();
                const ul = colEl.querySelector('ul.cascader-list');
                ul.classList.add('d-none');
                const d = document.createElement('div'); d.className = 'cascader-empty'; d.textContent = ph; colEl.prepend(d);
            }
            function fillList(colEl, items, cls){
                colEl.querySelector('.cascader-empty')?.remove();
                const ul = colEl.querySelector('ul.cascader-list'); ul.classList.remove('d-none'); ul.innerHTML = '';
                items.forEach(it=>{
                    const li = document.createElement('li'); li.className = `cascader-item ${cls}`;
                    li.dataset.id = it.id; li.dataset.name = it.name;
                    li.innerHTML = `<span class="name">${it.name}</span>`; ul.appendChild(li);
                });
            }
            async function fetchChildren(parentId){
                try{
                    const params = new URLSearchParams({ parent_id: parentId });
                    const res = await fetch(`${geoChildrenURL}?${params.toString()}`, { headers:{'X-Requested-With':'XMLHttpRequest'} });
                    if(!res.ok) return []; return await res.json();
                }catch(e){ return []; }
            }
            colCounties.querySelectorAll('.county').forEach(li=>{
                li.addEventListener('mouseover', async ()=>{
                    clearCol(colConst, "{{ __('Loading…') }}");
                    clearCol(colWards, "{{ __('Hover a constituency…') }}");
                    const arr = await fetchChildren(li.dataset.id);
                    if (arr.length) fillList(colConst, arr, 'constituency'); else clearCol(colConst, "{{ __('No constituencies') }}");
                });
                li.addEventListener('click', ()=>{
                    const name = li.dataset.name;
                    geoInput.value = li.dataset.id; geoPath.value  = name; geoText.textContent = name; geoHelp.textContent = name; close();
                });
            });
            listConst.addEventListener('mouseover', async (e)=>{
                const t = e.target.closest('.constituency'); if(!t) return;
                clearCol(colWards, "{{ __('Loading…') }}");
                const arr = await fetchChildren(t.dataset.id);
                if (arr.length) fillList(colWards, arr, 'ward'); else clearCol(colWards, "{{ __('No wards') }}");
            });
            listConst.addEventListener('click', (e)=>{
                const t = e.target.closest('.constituency'); if(!t) return;
                const name = t.dataset.name;
                geoInput.value = t.dataset.id; geoPath.value  = name; geoText.textContent = name; geoHelp.textContent = name; close();
            });
            listWards.addEventListener('click', (e)=>{
                const t = e.target.closest('.ward'); if(!t) return;
                const name = t.dataset.name;
                geoInput.value = t.dataset.id; geoPath.value  = name; geoText.textContent = name; geoHelp.textContent = name; close();
            });

            // --- Documentation visibility by seller_type ---
            const sellerTypeSel = document.getElementById('seller_type');
            const docs = {
                sole: document.getElementById('docs_sole'),
                partnership: document.getElementById('docs_partnership'),
                company: document.getElementById('docs_company'),
            };
            function applyDocVisibility(){
                const v = sellerTypeSel.value;
                docs.sole.classList.add('d-none');
                docs.partnership.classList.add('d-none');
                docs.company.classList.add('d-none');
                if (v === 'sole_proprietor') docs.sole.classList.remove('d-none');
                else if (v === 'partnership') docs.partnership.classList.remove('d-none');
                else if (v === 'company') docs.company.classList.remove('d-none');
            }
            sellerTypeSel.addEventListener('change', applyDocVisibility);
            applyDocVisibility();

            // --- Signature pad ---
            const canvas = document.getElementById('sigPad');
            function resizeCanvas(){
                if (!canvas) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                const ctx = canvas.getContext("2d"); ctx.scale(ratio, ratio);
                if (window._sigPad) window._sigPad.clear();
            }
            if (canvas) {
                window._sigPad = new window.SignaturePad(canvas, { penColor: '#111' });
                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                document.getElementById('sigClear')?.addEventListener('click', ()=> window._sigPad.clear());
            }

            // --- Build review summary ---
            const formEl = document.getElementById('sellerWizardForm');
            function val(name){ const el = formEl.querySelector(`[name="${name}"]`); return el ? (el.type==='file' ? (el.files?.length? el.files[0].name : '') : el.value) : ''; }
            function buildRow(label, value){
                return `<div class="row">
                    <div class="col-12 col-md-4 cell label">${label}</div>
                    <div class="col-12 col-md-8 cell">${(value||'—')}</div>
                </div>`;
            }
            window.buildReview = function(){
                const decl = document.getElementById('declName');
                if (decl) decl.textContent = val('contact_person_name') || '';
                const html = [
                    '<h6 class="px-3 pt-3 m-0">{{ __("Account") }}</h6>',
                    buildRow('{{ __("Username") }}', val('username')),
                    buildRow('{{ __("Seller Type") }}', (function(){ const sel = document.getElementById('seller_type'); return sel.options[sel.selectedIndex]?.text || ''; })()),
                    buildRow('{{ __("Phone") }}', val('shop_phone')),
                    buildRow('{{ __("Email") }}', val('email')),

                    '<h6 class="px-3 pt-3">{{ __("Primary Contact") }}</h6>',
                    buildRow('{{ __("Name") }}', val('contact_person_name')),
                    buildRow('{{ __("Designation") }}', val('contact_person_designation')),
                    buildRow('{{ __("Mobile") }}', val('contact_person_phone')),
                    buildRow('{{ __("Email") }}', val('contact_person_email')),
                    buildRow('{{ __("ID/Passport") }}', val('contact_person_id')),
                    buildRow('{{ __("Personal KRA PIN") }}', val('personal_kra_pin')),

                    '<h6 class="px-3 pt-3">{{ __("Address") }}</h6>',
                    buildRow('{{ __("Building") }}', val('address_building')),
                    buildRow('{{ __("Street/Road") }}', val('address_street')),
                    buildRow('{{ __("City/Town") }}', val('address_city')),
                    buildRow('{{ __("County/Constituency/Ward") }}', document.getElementById('geo_path').value || document.getElementById('geoSelectedHelp').textContent),
                    buildRow('{{ __("P.O. Box") }}', val('postal_box')),
                    buildRow('{{ __("Postal Code") }}', val('postal_code')),

                    '<h6 class="px-3 pt-3">{{ __("Settlement") }}</h6>',
                    buildRow('{{ __("Account Name") }}', val('account_name')),
                    buildRow('{{ __("Bank / Branch") }}', `${val('bank_name')} / ${val('bank_branch')}`),
                    buildRow('{{ __("Account Number") }}', val('account_number')),
                    buildRow('{{ __("Account Type") }}', val('account_type')),
                    buildRow('{{ __("SWIFT") }}', val('swift_code')),
                    buildRow('{{ __("Mobile Money") }}', `${val('mobile_money')} ${val('mobile_money_paybill') ? ' / ' + val('mobile_money_paybill') : ''}`),

                    '<h6 class="px-3 pt-3">{{ __("Store") }}</h6>',
                    buildRow('{{ __("Store Name") }}', val('shop_name')),
                    buildRow('{{ __("Category") }}', (function(){ const s=formEl.querySelector('[name="store_category_id"]'); return s && s.options[s.selectedIndex]?.text; })()),
                    buildRow('{{ __("Description") }}', val('store_description')),
                    buildRow('{{ __("Location") }}', `${val('store_city')}, ${val('store_county')}${val('store_sub_county')? ', '+val('store_sub_county'):''}${val('store_ward')? ', '+val('store_ward'):''}`),
                    buildRow('{{ __("Coordinates") }}', val('store_coords')),
                ].join('');
                document.getElementById('reviewBox').innerHTML = html;
            };

            // Init on correct step
            show(idx);
        })();
    </script>
@endpush
