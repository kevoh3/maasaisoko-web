@extends('layouts.backend')

@section('title', __('Product'))

@section('content')
    <!-- main Section -->
    <div class="main-body">
        <div class="container-fluid">
            @php $vipc = vipc(); @endphp
            @if($vipc['bkey'] == 0)
                @include('seller.partials.vipc')
            @else
                @php
                    // We have $categorylist with all categories (including parent_id)
                    $parents = collect($categorylist)->whereNull('parent_id');

                    $currentCat     = collect($categorylist)->firstWhere('id', $datalist['cat_id']);
                    $currentParent  = $currentCat ? $currentCat->parent_id : null;

                    // If the product is already on a parent category (no parent_id), we treat its own id as the parent
                    $selectedParentId = $currentParent ?: ($currentCat->id ?? null);
                @endphp
            <h>WAntam</h>
                <div class="row mt-25">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        {{ __('Product') }}
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="float-right">
                                            <a href="{{ route('seller-manage.products') }}" class="btn warning-btn"><i class="fa fa-reply"></i> {{ __('Back to List') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body tabs-area p-0">
                                @include('seller.partials.product_tabs_nav')
                                <div class="tabs-body">
                                    <!--Data Entry Form-->
                                    <form novalidate="" data-validate="parsley" id="DataEntry_formId">
                                        <div class="row">
{{--                                            <div class="col-lg-6">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label for="lan">{{ __('Language') }}<span class="red">*</span></label>--}}
{{--                                                    <select name="lan" id="lan" class="chosen-select form-control">--}}
{{--                                                        @foreach($languageslist as $row)--}}
{{--                                                            <option {{ $row->language_code == $datalist['lan'] ? "selected=selected" : '' }} value="{{ $row->language_code }}">--}}
{{--                                                                {{ $row->language_name }}--}}
{{--                                                            </option>--}}
{{--                                                        @endforeach--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="col-lg-6"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="product_name">{{ __('Product Name') }}<span class="red">*</span></label>
                                                    <input value="{{ $datalist['title'] }}" type="text" name="title" id="product_name" class="form-control parsley-validated" data-required="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="slug">{{ __('Slug') }}<span class="red">*</span></label>
                                                    <input value="{{ $datalist['slug'] }}" type="text" name="slug" id="slug" class="form-control parsley-validated" data-required="true" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="short_desc">{{ __('Short Description') }}</label>
                                                    <textarea name="short_desc" id="short_desc" class="form-control" rows="2">{{ $datalist['short_desc'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group tpeditor">
                                                    <label for="description">{{ __('Product Content') }}</label>
                                                    <textarea name="description" id="description" class="form-control" rows="4">{{ $datalist['description'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
{{--                                        <div class="row">--}}
{{--                                            <div class="col-lg-6">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label for="brand_id">{{ __('Brand') }}<span class="red">*</span></label>--}}
{{--                                                    <select name="brand_id" id="brand_id" class="chosen-select form-control">--}}
{{--                                                        <option value="0">No Brand</option>--}}
{{--                                                        @foreach($brandlist as $row)--}}
{{--                                                            <option {{ $row->id == $datalist['brand_id'] ? "selected=selected" : '' }} value="{{ $row->id }}">--}}
{{--                                                                {{ $row->name }}--}}
{{--                                                            </option>--}}
{{--                                                        @endforeach--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-lg-6">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label for="cat_id">{{ __('Category') }}<span class="red">*</span></label>--}}
{{--                                                    <select name="cat_id" id="cat_id" class="chosen-select form-control">--}}
{{--                                                        <option value="" selected="selected">{{ __('Select Category') }}</option>--}}
{{--                                                        @foreach($categorylist as $row)--}}
{{--                                                            <option {{ $row->id == $datalist['cat_id'] ? "selected=selected" : '' }} value="{{ $row->id }}">--}}
{{--                                                                {{ $row->name }}--}}
{{--                                                            </option>--}}
{{--                                                        @endforeach--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="brand_id">{{ __('Brand') }}<span class="red">*</span></label>
                                                    <select name="brand_id" id="brand_id" class="chosen-select form-control">
                                                        <option value="0">No Brand</option>
                                                        @foreach($brandlist as $row)
                                                            <option {{ $row->id == $datalist['brand_id'] ? 'selected=selected' : '' }} value="{{ $row->id }}">
                                                                {{ $row->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Parent Category (drives the subcategory list) --}}
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="parent_category">{{ __('Category') }}<span class="red">*</span></label>
                                                    <select id="parent_category" class="chosen-select form-control">
                                                        <option value="">{{ __('Select Category') }}</option>
                                                        @foreach($parents as $p)
                                                            <option value="{{ $p->id }}" {{ (int)$p->id === (int)$selectedParentId ? 'selected' : '' }}>
                                                                {{ $p->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">{{ __('Pick a category to see its subcategories.') }}</small>
                                                </div>
                                            </div>

                                            {{-- Subcategory (optional; if none, we’ll submit parent) --}}
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="subcategory">{{ __('Subcategory') }}<span class="red d-inline-block align-middle">*</span></label>
                                                    <select id="subcategory" class="chosen-select form-control" {{ $currentParent ? '' : 'disabled' }}>
                                                        <option value="">{{ __('Select Subcategory') }}</option>
                                                        {{-- Options will be filled by JS. If no children, we add a “(Same as Category)” option. --}}
                                                    </select>
                                                    <small id="sub_help" class="text-muted d-block"></small>
                                                </div>
                                            </div>
                                            {{-- Hidden field actually submitted to controller (it reads "cat_id") --}}
                                            <input type="hidden" name="cat_id" id="cat_id" value="{{ (int)$datalist['cat_id'] }}">
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="tax_id">{{ __('Tax') }}<span class="red">*</span></label>
                                                    <select name="tax_id" id="tax_id" class="chosen-select form-control">
                                                        @foreach($taxlist as $row)
                                                            <option {{ $row->id == $datalist['tax_id'] ? "selected=selected" : '' }} value="{{ $row->id }}">
                                                                {{ $row->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="sale_price">{{ __('Sale Price') }}<span class="red">*</span></label>
                                                    <input value="{{ $datalist['sale_price'] }}" name="sale_price" id="sale_price" type="text" class="form-control parsley-validated" data-required="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="variation_size">{{ __('Unit') }}<span class="red">*</span></label>
                                                    <select name="variation_size" id="variation_size" class="chosen-select form-control">
                                                        @foreach($unitlist as $row)
                                                            <option {{ $row->name == $datalist['variation_size'] ? "selected=selected" : '' }} value="{{ $row->name }}">
                                                                {{ $row->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="is_featured">{{ __('Is Popular') }}</label>
                                                    <select name="is_featured" id="is_featured" class="chosen-select form-control">
                                                        <option {{ 1 == $datalist['is_featured'] ? "selected=selected" : '' }} value="1">{{ __('YES') }}</option>
                                                        <option {{ 0 == $datalist['is_featured'] ? "selected=selected" : '' }} value="0">{{ __('NO') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="collection_id">{{ __('Is Gift Item') }}</label>
                                                    <select name="collection_id" id="collection_id" class="chosen-select form-control">
                                                        <option {{ 1 == $datalist['collection_id'] ? "selected=selected" : '' }} value="1">{{ __('YES') }}</option>
                                                        <option {{ 0 == $datalist['collection_id'] ? "selected=selected" : '' }} value="0">{{ __('NO') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="f_thumbnail_thumbnail"><span class="red">*</span> {{ __('Featured image') }}</label>
                                                    <div class="file_up">
                                                        <input type="text" name="f_thumbnail" id="f_thumbnail_thumbnail" value="{{ $datalist['f_thumbnail'] }}" class="form-control parsley-validated" data-required="true" readonly>
                                                        <div class="file_browse_box">
                                                            <input type="file" name="load_image" id="load_image" class="file_browse">
                                                            <label for="load_image" class="file_browse_icon"><i class="fa fa-window-restore"></i>{{ __('Browse') }}</label>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">Recommended image size width: 400px and height: 400px.</small>
                                                    <div id="remove_f_thumbnail" class="select-image dnone">
                                                        <div class="inner-image" id="view_thumbnail_image"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6"></div>
                                        </div>

                                        <input value="{{ $datalist['id'] }}" type="text" name="RecordId" id="RecordId" class="dnone">
                                        <div class="row tabs-footer mt-15">
                                            <div class="col-lg-12">
                                                <a id="submit-form" href="javascript:void(0);" class="btn blue-btn">{{ __('Save') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!--/Data Entry Form/-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- /main Section -->

@endsection

@push('scripts')
    <!-- css/js -->
    <script type="text/javascript">
        var media_type = 'Product_Thumbnail';
        var strIds = "{{ $datalist['category_ids'] }}";
        if(strIds !=''){
            var idsArr = strIds.split(",");
            $("#category_ids").val(idsArr).trigger("chosen:updated");
        }

        var f_thumbnail = "{{ $datalist['f_thumbnail'] }}";
        if(f_thumbnail == ''){
            $("#remove_f_thumbnail").hide();
            $("#f_thumbnail_thumbnail").html('');
        }
        if(f_thumbnail != ''){
            $("#remove_f_thumbnail").show();
            $("#view_thumbnail_image").html('<img src="'+public_path+'/media/'+f_thumbnail+'">');
        }

        var TEXT = [];
        TEXT['Select Category'] = "{{ __('Select Category') }}";
        TEXT['Sorry only you can upload jpg, png and gif file type'] = "{{ __('Sorry only you can upload jpg, png and gif file type') }}";


            (function () {
            // Endpoint to fetch children
            const childrenURL = "{{ route('seller-manage.category.children') }}";

            const parentSel = document.getElementById('parent_category');
            const subSel    = document.getElementById('subcategory');
            const helpEl    = document.getElementById('sub_help');
            const catHidden = document.getElementById('cat_id');

            // Current values from the Blade
            const currentCatId     = parseInt("{{ (int) $datalist['cat_id'] }}", 10) || null;
            const currentParentId  = parseInt("{{ (int) $selectedParentId }}", 10) || null;

            function chosenUpdate(el){
            if (window.jQuery && jQuery.fn.chosen) jQuery(el).trigger('chosen:updated');
        }

            function setHiddenTo(value){
            catHidden.value = value || '';
        }

            // When a parent is selected, load children
            async function loadChildrenForParent(pid, preselectChildId = null){
            subSel.innerHTML = '';
            if (!pid) {
            subSel.disabled = true;
            subSel.appendChild(new Option("{{ __('Select Subcategory') }}", ''));
            helpEl.textContent = '';
            setHiddenTo('');
            chosenUpdate(subSel);
            return;
        }

            // Default the hidden field to parent until user picks a child
            setHiddenTo(pid);

            try {
            const res = await fetch(`${childrenURL}?parent_id=${encodeURIComponent(pid)}`, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
        });
            const kids = await res.json();

            if (Array.isArray(kids) && kids.length) {
            subSel.disabled = false;
            subSel.appendChild(new Option("{{ __('Select Subcategory') }}", ''));
            kids.forEach(k => subSel.appendChild(new Option(k.name, k.id)));
            helpEl.textContent = "{{ __('Pick a subcategory (or leave blank to use the parent)') }}";

            // Preselect if editing a child
            if (preselectChildId) {
            subSel.value = String(preselectChildId);
            setHiddenTo(preselectChildId);
        }

            chosenUpdate(subSel);
        } else {
            // No children ⇒ add a synthetic option “(Same as Category)”
            subSel.disabled = false;
            subSel.appendChild(new Option("{{ __('(Same as Category)') }}", pid));
            subSel.value = String(pid);
            helpEl.textContent = "{{ __('No subcategories — the product will use the selected category.') }}";
            setHiddenTo(pid);
            chosenUpdate(subSel);
        }
        } catch (e) {
            // On error, degrade gracefully to parent
            subSel.disabled = false;
            subSel.appendChild(new Option("{{ __('(Same as Category)') }}", pid));
            subSel.value = String(pid);
            helpEl.textContent = "{{ __('No subcategories — the product will use the selected category.') }}";
            setHiddenTo(pid);
            chosenUpdate(subSel);
        }
        }

            // Init on page load:
            // If current category is a child, `currentParentId` is its parent; otherwise parent==currentCatId
            if (currentParentId) {
            // Make sure parent select reflects it (Blade did this), then load kids and preselect current child if applicable
            loadChildrenForParent(currentParentId, (currentParentId !== currentCatId ? currentCatId : null));
        }

            // Parent change
            parentSel.addEventListener('change', function () {
            const pid = this.value ? parseInt(this.value, 10) : null;
            loadChildrenForParent(pid);
        });

            // Subcategory change — prefer child; blank means the parent
            subSel.addEventListener('change', function () {
            const child = this.value;
            const parent = parentSel.value;
            setHiddenTo(child || parent || '');
        });

            // Safety: when clicking Save, if cat_id somehow empty, fall back to current parent
            const saveBtn = document.getElementById('submit-form');
            if (saveBtn) {
            saveBtn.addEventListener('click', function(){
            if (!catHidden.value) {
            catHidden.value = parentSel.value || '';
        }
        });
        }
        })();


    </script>
    <link href="{{asset('public/backend/editor/summernote-lite.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/backend/editor/summernote-lite.min.js')}}"></script>
    <script src="{{asset('public/backend/pages/product_seller.js')}}"></script>
@endpush
