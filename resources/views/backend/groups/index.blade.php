@extends('layouts.backend')

@section('title', __('Groups'))

@section('content')
    <!-- main Section -->
    <div class="main-body">
        <div class="container-fluid">
            @php $vipc = vipc(); @endphp
            @if($vipc['bkey'] == 0)
                @include('backend.partials.vipc')
            @else
                <div class="row mt-25">
                    <div class="col-lg-12">
                        <div class="card" id="list-panel">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>{{ __('Sellers') }}</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="float-right">
                                            <a onClick="onFormPanel()" href="javascript:void(0);" class="btn blue-btn btn-form float-right"><i class="fa fa-plus"></i> {{ __('Add New') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Data grid-->
                            <div class="card-body">
                                <div class="row mb-10">
                                    <div class="col-lg-12">
                                        <div class="group-button">
{{--                                            <button id="orderstatus_0" type="button" onclick="onDataViewByStatus(0)" class="btn btn-theme orderstatus active">All ({{ $Counts['all'] }})</button>--}}
{{--                                            <button id="orderstatus_1" type="button" onclick="onDataViewByStatus(1)" class="btn btn-theme orderstatus">{{ __('Active') }} ({{ $Counts['active'] }})</button>--}}
{{--                                            <button id="orderstatus_2" type="button" onclick="onDataViewByStatus(2)" class="btn btn-theme orderstatus">{{ __('Inactive') }} ({{ $Counts['inactive'] }})</button>--}}
                                            <button id="orderstatus_0" onclick="onDataViewByStatus('0')" class="btn btn-theme orderstatus">All ({{ $Counts['all'] }})</button>
                                            <button id="orderstatus_active" onclick="onDataViewByStatus('active')" class="btn btn-theme orderstatus">Active ({{ $Counts['active'] }})</button>
                                            <button id="orderstatus_pending" onclick="onDataViewByStatus('pending')" class="btn btn-theme orderstatus">Pending({{ $Counts['pending'] }})</button>
                                            <button id="orderstatus_suspended" onclick="onDataViewByStatus('suspended')" class="btn btn-theme orderstatus">Suspended({{ $Counts['suspended'] }})</button>
                                            <button id="orderstatus_inactive" onclick="onDataViewByStatus('inactive')" class="btn btn-theme orderstatus">Inactive ({{ $Counts['inactive'] }})</button>
                                        </div>
                                        <input type="hidden" id="view_by_status" value="0">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group bulk-box">
                                            <select id="bulk-action" class="form-control">
                                                <option value="">{{ __('Select Action') }}</option>
                                                <option value="active">{{ __('Active') }}</option>
                                                <option value="inactive">{{ __('Inactive') }}</option>
                                                <option value="delete">{{ __('Delete Permanently') }}</option>
                                            </select>
                                            <button type="submit" onClick="onBulkAction()" class="btn bulk-btn">{{ __('Apply') }}</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-5">
                                        <div class="form-group search-box">
                                            <input id="search" name="search" type="text" class="form-control" placeholder="{{ __('Search') }}...">
                                            <button type="submit" onClick="onSearch()" class="btn search-btn">{{ __('Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="tp_datalist">
                                    @include('backend.groups.partials.group_table')
                                </div>
                            </div>
                            <!--/Data grid/-->
                        </div>

                        <div class="dnone" id="form-panel">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <span>{{ __('Group') }}</span>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="float-right">
                                                        <a onClick="onListPanel()" href="javascript:void(0);" class="btn warning-btn btn-list float-right dnone"><i class="fa fa-reply"></i> {{ __('Back to List') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/Data Entry Form-->
                                        {{-- ========= Group Create / Edit Form ========= --}}
                                        <div class="card-body">
                                            <ul class="nav nav-tabs mb-15" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#g_details" role="tab">{{ __('Details') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#g_contact" role="tab">{{ __('Contact') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#g_kyc" role="tab">{{ __('KYC') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#g_banking" role="tab">{{ __('Banking') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#g_digital" role="tab">{{ __('Digital') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#g_verification" role="tab">{{ __('Verification') }}</a>
                                                </li>
                                            </ul>

                                            <form id="GroupEntry_formId" data-validate="parsley" novalidate>
                                                @csrf
                                                <input type="hidden" id="RecordId" name="id" />

                                                <div class="tab-content">
                                                    {{-- Details --}}
                                                    <div class="tab-pane fade show active" id="g_details" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="name">{{ __('Group / Organisation Name') }} <span class="red">*</span></label>
                                                                    <input type="text" name="name" id="name" class="form-control parsley-validated" data-required="true" placeholder="{{ __('e.g. Umoja Self Help Group') }}">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="registration_number">{{ __('Registration / Certificate Number') }}</label>
                                                                    <input type="text" name="registration_number" id="registration_number" class="form-control" placeholder="{{ __('e.g. CPR/2019/123456 or BN/2024/123456') }}">
                                                                </div>

                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="type">{{ __('Type') }}</label>
                                                                        <select name="type" id="type" class="chosen-select form-control">
                                                                            <option value="">{{ __('Select Type') }}</option>
                                                                            <option value="company">{{ __('Company') }}</option>
                                                                            <option value="association">{{ __('Association') }}</option>
                                                                            <option value="cooperative">{{ __('Co-operative / SACCO') }}</option>
                                                                            <option value="cbo">{{ __('CBO') }}</option>
                                                                            <option value="ngo">{{ __('NGO') }}</option>
                                                                            <option value="self_help">{{ __('Self Help Group') }}</option>
                                                                            <option value="church">{{ __('Church / Faith-based') }}</option>
                                                                            <option value="school">{{ __('School / Institution') }}</option>
                                                                            <option value="other">{{ __('Other') }}</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label for="industry">{{ __('Industry / Sector') }}</label>
                                                                        <input type="text" name="industry" id="industry" class="form-control" placeholder="{{ __('e.g. Retail, Farming, Crafts') }}">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Logo --}}
                                                            <div class="col-md-4">
                                                                <label>{{ __('Logo') }}</label>
                                                                <div class="tp-upload-field">
                                                                    <input type="text" name="logo" id="logo_thumbnail" class="form-control" readonly>
                                                                    <a id="on_thumbnail" href="javascript:void(0);" class="tp-upload-btn">
                                                                        <i class="fa fa-window-restore"></i> {{ __('Browse') }}
                                                                    </a>
                                                                </div>
                                                                <em>{{ __('Recommended image size: 200x200') }}</em>
                                                                <div id="remove_photo_thumbnail" class="select-image" style="display:none;">
                                                                    <div class="inner-image" id="view_photo_thumbnail"></div>
                                                                    <a onClick="onMediaImageRemove()" class="media-image-remove" href="javascript:void(0);">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Contact --}}
                                                    <div class="tab-pane fade" id="g_contact" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="contact_person">{{ __('Contact Person') }}</label>
                                                                    <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="{{ __('e.g. Jane Wambui') }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="phone">{{ __('Phone') }} <span class="red">*</span></label>
                                                                    <input type="text" name="phone" id="phone" class="form-control parsley-validated" data-required="true" placeholder="{{ __('e.g. 07XXXXXXXX') }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="email">{{ __('Email') }}</label>
                                                                    <input type="email" name="email" id="email" class="form-control" placeholder="group@example.com">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="address">{{ __('Address') }}</label>
                                                                    <input type="text" name="address" id="address" class="form-control" placeholder="{{ __('Street, Building, etc.') }}">
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="county">{{ __('County') }}</label>
                                                                        <input type="text" name="county" id="county" class="form-control" placeholder="{{ __('e.g. Nairobi') }}">
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label for="sub_county">{{ __('Sub-County') }}</label>
                                                                        <input type="text" name="sub_county" id="sub_county" class="form-control" placeholder="{{ __('e.g. Kasarani') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- KYC --}}
                                                    <div class="tab-pane fade" id="g_kyc" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="kra_pin">{{ __('KRA PIN') }}</label>
                                                                    <input type="text" name="kra_pin" id="kra_pin" class="form-control" placeholder="{{ __('e.g. A123456789B') }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="business_permit_number">{{ __('Business Permit No.') }}</label>
                                                                    <input type="text" name="business_permit_number" id="business_permit_number" class="form-control" placeholder="{{ __('e.g. BP/2025/XXXXXX') }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="certificate_of_incorporation">{{ __('Certificate of Incorporation No.') }}</label>
                                                                    <input type="text" name="certificate_of_incorporation" id="certificate_of_incorporation" class="form-control" placeholder="{{ __('e.g. CPR/2019/XXXXXX') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="tax_compliance_certificate">{{ __('Tax Compliance Certificate No.') }}</label>
                                                                    <input type="text" name="tax_compliance_certificate" id="tax_compliance_certificate" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="status">{{ __('Status') }} <span class="red">*</span></label>
                                                                    <select name="status" id="status" class="chosen-select form-control parsley-validated" data-required="true">
                                                                        <option value="pending">{{ __('Pending') }}</option>
                                                                        <option value="active">{{ __('Active') }}</option>
                                                                        <option value="suspended">{{ __('Suspended') }}</option>
                                                                        <option value="inactive">{{ __('Inactive') }}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="verified_status">{{ __('Verification Status') }}</label>
                                                                    <select name="verified_status" id="verified_status" class="chosen-select form-control">
                                                                        <option value="pending">{{ __('Pending') }}</option>
                                                                        <option value="verified">{{ __('Verified') }}</option>
                                                                        <option value="rejected">{{ __('Rejected') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Banking --}}
                                                    <div class="tab-pane fade" id="g_banking" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bank_name">{{ __('Bank Name') }}</label>
                                                                    <input type="text" name="bank_name" id="bank_name" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bank_branch">{{ __('Bank Branch') }}</label>
                                                                    <input type="text" name="bank_branch" id="bank_branch" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="bank_account_number">{{ __('Account Number') }}</label>
                                                                    <input type="text" name="bank_account_number" id="bank_account_number" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Digital --}}
                                                    <div class="tab-pane fade" id="g_digital" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="website">{{ __('Website') }}</label>
                                                                    <input type="text" name="website" id="website" class="form-control" placeholder="https://">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="social_media">{{ __('Social Media') }}</label>
                                                                    <input type="text" name="social_media" id="social_media" class="form-control" placeholder="{{ __('e.g. https://facebook.com/yourpage') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Verification --}}
                                                    <div class="tab-pane fade" id="g_verification" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label for="verified_notes">{{ __('Verification Notes (Internal)') }}</label>
                                                                <textarea name="verified_notes" id="verified_notes" rows="3" class="form-control" placeholder="{{ __('Any notes regarding manual verification or review…') }}"></textarea>
                                                                <small class="opacity50">
                                                                    {{ __('“Verified By” and “Approved By” are set automatically by workflow/actions, no need to fill them here.') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row tabs-footer mt-15">
                                                    <div class="col-lg-12">
                                                        <a id="submit-form" href="javascript:void(0);" class="btn blue-btn mr-10">{{ __('Save') }}</a>
                                                        <a onClick="onListPanel()" href="javascript:void(0);" class="btn warning-btn btn-list dnone">
                                                            <i class="fa fa-reply"></i> {{ __('Back to List') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        {{-- ========= /Group Create / Edit Form ========= --}}
                                        <!--/Data Entry Form-->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card mb-15">
                                        <div class="card-body">
                                            <div class="seller_card">
                                                <h5><strong>{{ __('Joined At') }}</strong> <span class="float-right" id="created_at"></span></h5>
                                                <h6><strong>{{ __('Status') }}</strong> <span id="seller_status" class="float-right"></span></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="status-card bg-grad-5 mb-15">
                                        <div class="status-text">
                                            <div class="status-name opacity50">{{ __('Current Balance') }}</div>
                                            <h2 class="status-count" id="Current_Balance"></h2>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                            <path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                                        </svg>
                                    </div>

                                    <div class="status-card bg-grad-10 mb-15">
                                        <div class="status-text">
                                            <div class="status-name opacity50">{{ __('Total Withdraw') }}</div>
                                            <h2 class="status-count" id="WithdrawalBalance"></h2>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                            <path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                                        </svg>
                                    </div>

                                    <div class="status-card bg-grad-9 mb-15">
                                        <div class="status-text">
                                            <div class="status-name opacity50">{{ __('Total Sold') }}</div>
                                            <h2 class="status-count" id="OrderBalance"></h2>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                            <path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                                        </svg>
                                    </div>

                                    <div class="status-card bg-grad-4 mb-15">
                                        <div class="status-text">
                                            <div class="status-name opacity50">{{ __('Total Products') }}</div>
                                            <h2 class="status-count" id="TotalProducts"></h2>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                            <path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- /main Section -->

    <!--Global Media-->
    @include('backend.partials.global_media')
    <!--/Global Media/-->

@endsection

@push('scripts')
    <!-- css/js -->
    <script type="text/javascript">
        var media_type = 'Thumbnail';
        var TEXT = [];
        TEXT['Do you really want to edit this record'] = "{{ __('Do you really want to edit this record') }}";
        TEXT['Do you really want to delete this record'] = "{{ __('Do you really want to delete this record') }}";
        TEXT['Do you really want to active this records'] = "{{ __('Do you really want to active this records') }}";
        TEXT['Do you really want to inactive this records'] = "{{ __('Do you really want to inactive this records') }}";
        TEXT['Do you really want to delete this records'] = "{{ __('Do you really want to delete this records') }}";
        TEXT['Please select action'] = "{{ __('Please select action') }}";
        TEXT['Please select record'] = "{{ __('Please select record') }}";
        TEXT['Active'] = "{{ __('Active') }}";
        TEXT['Inactive'] = "{{ __('Inactive') }}";
    </script>
    <script src="{{asset('public/backend/pages/group.js')}}"></script>
    <script src="{{asset('public/backend/pages/global-media.js')}}"></script>
@endpush
