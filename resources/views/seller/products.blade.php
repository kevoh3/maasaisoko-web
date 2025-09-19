@extends('layouts.backend')

@section('title', __('Products'))

@section('content')
    <!-- main Section -->
    <div class="main-body">
        <div class="container-fluid">
            @php $vipc = vipc(); @endphp
            @if($vipc['bkey'] == 0)
                @include('seller.partials.vipc')
            @else
                <div class="row mt-25">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <span>{{ __('Products') }}</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="float-right">
                                            <a onClick="onFormPanel()" href="javascript:void(0);" class="btn blue-btn btn-form float-right"><i class="fa fa-plus"></i> {{ __('Add New') }}</a>
                                            <a onClick="onListPanel()" href="javascript:void(0);" class="btn warning-btn btn-list float-right dnone"><i class="fa fa-reply"></i> {{ __('Back to List') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Data grid-->
                            <div id="list-panel" class="card-body">
                                <div class="row mb-10">
                                    <div class="col-md-3">
                                        <div class="form-group mb-10">
                                            <select name="language_code" id="language_code" class="chosen-select form-control">
                                                <option value="0" selected="selected">{{ __('All Language') }}</option>
                                                @foreach($languageslist as $row)
                                                    <option value="{{ $row->language_code }}">
                                                        {{ $row->language_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-10">
                                            <select name="category_id" id="category_id" class="chosen-select form-control">
                                                <option value="0" selected="selected">{{ __('All Category') }}</option>
                                                @foreach($categorylist as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-10">
                                            <select name="brand_id" id="brand_id" class="chosen-select form-control">
                                                <option value="all" selected="selected">{{ __('All Brand') }}</option>
                                                <option value="0">No Brand</option>
                                                @foreach($brandlist as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group bulk-box">
                                            <select id="bulk-action" class="form-control">
                                                <option value="">{{ __('Select Action') }}</option>
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
                                    @include('seller.partials.products_table')
                                </div>
                            </div>
                            <!-- Data Entry Form -->
                            <div id="form-panel" class="card-body dnone">
                                <form id="DataEntry_formId" novalidate>
                                    @csrf

                                    @php
                                        $defaultLangCode = glan() ?? ($languageslist[0]->language_code ?? 'en');
                                        $defaultLangName = optional(collect($languageslist)->firstWhere('language_code',$defaultLangCode))->language_name
                                                          ?? strtoupper($defaultLangCode);

                                        $parents = collect($categorylist)->whereNull('parent_id');
                                    @endphp

                                    {{-- Hidden language actually submitted --}}
{{--                                    <input type="hidden" name="lan" id="lan" value="{{ $defaultLangCode }}">--}}

                                    {{-- Product Name --}}
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="title">{{ __('Product Name') }} <span class="red">*</span></label>
                                                <input type="text" name="title" id="title" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="slug">{{ __('Slug') }} <span class="red">*</span></label>
                                                <input type="text" name="slug" id="slug" class="form-control" required readonly>
                                                <small class="text-muted">{{ __('Auto-generated from the product name (you can refine later).') }}</small>
                                            </div>
                                        </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Language') }}</label>
                                                    <input type="text" class="form-control" value="{{ $defaultLangName }}" readonly>
                                                </div>

                                    </div>

                                    {{-- Language (read-only display), Category, Subcategory, Brand --}}


                                        {{-- Parent Category (to load subcats) --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="parent_category">{{ __('Category') }} <span class="red">*</span></label>
                                                <select id="parent_category" class="form-control">
                                                    <option value="">{{ __('Select Category') }}</option>
                                                    @foreach($parents as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">{{ __('Pick a category to see its subcategories.') }}</small>
                                            </div>
                                        </div>

                                        {{-- Subcategory (what we submit as categoryid) --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subcategory">{{ __('Subcategory') }} <span class="red d-inline-block align-middle" id="sub_required">*</span></label>
                                                <select id="subcategory" class="form-control" disabled>
                                                    <option value="">{{ __('Select Subcategory') }}</option>
                                                </select>
                                                <small id="sub_help" class="text-muted d-block"></small>
                                            </div>
                                        </div>

                                        {{-- Brand --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="brandid">{{ __('Brand') }} <span class="red">*</span></label>
                                                <select name="brandid" id="brandid" class="form-control" required>
                                                    <option value="0">{{ __('No Brand') }}</option>
                                                    @foreach($brandlist as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden field your controller expects --}}
                                    <input type="hidden" name="categoryid" id="categoryid" required>

                                    {{-- Auth + Record --}}
                                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="RecordId" id="RecordId">

                                    <div class="row tabs-footer mt-15">
                                        <div class="col-lg-12 d-flex gap-2">
                                            <a id="submit-form" href="javascript:void(0);" class="btn blue-btn mr-10">{{ __('Save') }}</a>
                                            <a onClick="onListPanel()" href="javascript:void(0);" class="btn danger-btn">{{ __('Cancel') }}</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /Data Entry Form/ -->

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
        var TEXT = [];
        TEXT['Do you really want to edit this record'] = "{{ __('Do you really want to edit this record') }}";
        TEXT['Do you really want to delete this record'] = "{{ __('Do you really want to delete this record') }}";
        TEXT['Do you really want to publish this records'] = "{{ __('Do you really want to publish this records') }}";
        TEXT['Do you really want to draft this records'] = "{{ __('Do you really want to draft this records') }}";
        TEXT['Do you really want to delete this records'] = "{{ __('Do you really want to delete this records') }}";
        TEXT['Please select action'] = "{{ __('Please select action') }}";
        TEXT['Please select record'] = "{{ __('Please select record') }}";
        TEXT['All Category'] = "{{ __('All Category') }}";
        TEXT['All Brand'] = "{{ __('All Brand') }}";



        (function(){
            // --- Slug from Title (read-only), ensure uniqueness via endpoint ---
            const titleEl = document.getElementById('title');
            const slugEl  = document.getElementById('slug');

            function toSlug(s){
            return (s||'')
            .toString()
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
        }

            async function ensureUniqueSlug(slug){
            try {
            const res = await fetch("{{ url('/seller/hasProductSlug') }}", {
            method: 'POST',
            headers: {
            'Content-Type':'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
            body: new URLSearchParams({ slug })
        });
            const data = await res.json();
            if (data && data.slug) slugEl.value = data.slug;
        } catch(e) {/* silent */}
        }

            titleEl.addEventListener('blur', function(){
            const s = toSlug(titleEl.value);
            if (!s) return;
            slugEl.value = s;
            ensureUniqueSlug(s);
        });

            // --- Category → Subcategory dynamic loading ---
            const parentSel = document.getElementById('parent_category');
            const subSel    = document.getElementById('subcategory');
            const catHidden = document.getElementById('categoryid');
            const helpEl    = document.getElementById('sub_help');
            const childrenURL = "{{ route('seller-manage.category.children') }}";

            function chosenUpdate(el){
            if (window.jQuery && jQuery.fn.chosen) jQuery(el).trigger('chosen:updated');
        }

            function resetSubToParent(pid){
            subSel.innerHTML = '';
            if (pid) {
            const opt = document.createElement('option');
            opt.value = pid;
            opt.textContent = "{{ __('(Same as Category)') }}";
            subSel.appendChild(opt);
            helpEl.textContent = "{{ __('No subcategories — the product will use the selected category.') }}";
            catHidden.value = pid; // submit the parent
        } else {
            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = "{{ __('Select Subcategory') }}";
            subSel.appendChild(ph);
            helpEl.textContent = '';
            catHidden.value = '';
        }
            chosenUpdate(subSel);
        }

            parentSel.addEventListener('change', async function(){
            const pid = this.value;
            if (!pid) { resetSubToParent(''); return; }

            // pre-set to parent until we fetch children
            resetSubToParent(pid);

            try{
            const res = await fetch(`${childrenURL}?parent_id=${encodeURIComponent(pid)}`, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
        });
            const kids = await res.json();
            if (Array.isArray(kids) && kids.length){
            subSel.innerHTML = '';
            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = "{{ __('Select Subcategory') }}";
            subSel.appendChild(ph);

            kids.forEach(k=>{
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = k.name;
            subSel.appendChild(opt);
        });
            helpEl.textContent = "{{ __('Pick a subcategory (or leave blank to use the parent)') }}";
            catHidden.value = pid; // default still parent unless they pick a child
            chosenUpdate(subSel);
        } else {
            // no children
            resetSubToParent(pid);
        }
        }catch(e){
            // on error fallback to parent
            resetSubToParent(pid);
        }
        });

            // when subcategory changes, prefer child; blank means parent
            subSel.addEventListener('change', function(){
            const child = this.value;
            const parent = parentSel.value;
            catHidden.value = child || parent || '';
        });

            // --- Submit: keep your existing handler behavior ---
            document.getElementById('submit-form').addEventListener('click', function(){
            // make sure categoryid is set correctly if user never touched subcat
            if (!catHidden.value) {
            catHidden.value = parentSel.value || '';
        }
            if (typeof OnFormSubmit === 'function') return OnFormSubmit();
            document.getElementById('DataEntry_formId').submit();
        });

        })();

    </script>
    <script src="{{asset('public/backend/pages/products_seller.js')}}"></script>
@endpush
