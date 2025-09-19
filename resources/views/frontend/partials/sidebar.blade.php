@push('style')
    <style>
        /* ===== Categories tree (unchanged) ===== */
        .cats-tree{list-style:none;margin:0;padding:0}
        .cats-tree .cat-parent{position:relative}
        .cats-tree .rowitem{display:flex;align-items:center;gap:10px;padding:8px 6px;border-radius:6px}
        .cats-tree .rowitem:hover{background:#f7f8fa}
        .cats-tree .icon img{width:36px;height:36px;object-fit:cover;border-radius:4px}
        .cats-tree .desc a{text-decoration:none}
        .cats-tree .count{margin-left:auto;font-weight:600}
        .cats-tree .caret{margin-left:6px;opacity:.6}
        .cats-tree .children{display:none;margin:4px 0 8px 46px;padding-left:8px;border-left:2px solid #eee}
        .cats-tree .children li{display:flex;justify-content:space-between;align-items:center;padding:4px 0}
        .cats-tree .children .count{font-size:.9em;opacity:.8}
        @media (hover:hover){ .cats-tree .cat-parent:hover>.children{display:block} }
        @media (max-width:991.98px){ .cats-tree .children{display:block} }

        /* ===== Cascader ===== */
        .cascader{position:relative}
        .cascader-trigger{
            width:100%; text-align:left; display:flex; align-items:center; gap:.5rem;
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .cascader-trigger i{opacity:.7}
        .cascader-panel{
            position:absolute; z-index:20; left:0; top:100%;
            min-width:500px; width:100%; max-width:min(600px,90vw);
            background:#fff; border:1px solid #e5e7eb; border-radius:10px;
            box-shadow:0 12px 28px rgba(0,0,0,.12);
            display:none; overflow:hidden; margin-top:6px;
        }
        .cascader.show .cascader-panel{display:flex; flex-direction:column}

        .cascader-cols{display:flex; width:100%}
        .cascader-col{flex:0 0 42%; max-height:320px; overflow:auto; border-right:1px solid #f1f2f4; position:relative}
        .cascader-col:nth-child(2){flex-basis:33%}
        .cascader-col:nth-child(3){flex-basis:25%}
        .cascader-col:last-child{border-right:none}

        .cascader-head{
            position:sticky; top:0; z-index:1; background:#fff;
            border-bottom:1px solid #f1f2f4; padding:8px 10px; font-weight:600; font-size:.85rem;
        }
        .cascader-list{list-style:none; margin:0; padding:6px}
        .cascader-item{
            display:flex; justify-content:space-between; align-items:center;
            gap:10px; padding:8px 10px; border-radius:8px; cursor:pointer; outline:none;
        }
        .cascader-item:hover, .cascader-item:focus{background:#f7f8fa}
        .cascader-item.is-active{background:#eef6ff}
        .cascader-item .name{
            flex:1 1 auto; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .cascader-count{font-size:.85rem; opacity:.7; white-space:nowrap}
        .cascader-empty{padding:10px; color:#9aa1a9; font-size:.9rem}

        .cascader-footer{
            padding:8px 10px; background:#fafbfc; border-top:1px solid #f1f2f4;
            display:flex; justify-content:space-between; align-items:center
        }

        /* Skeleton shimmer while loading */
        .cascader-skel{ padding:8px 12px; width:100% }
        .cascader-skel .bar{
            height:12px; margin:8px 0; border-radius:6px;
            background: linear-gradient(90deg,#f2f4f7, #eceff3, #f2f4f7);
            background-size: 200% 100%; animation: skel 1.1s linear infinite;
        }
        @keyframes skel{ to { background-position:-200% 0; } }

        @media (max-width:420px){
            .cascader-panel{min-width:100%;}
            .cascader-cols{flex-direction:column}
            .cascader-col{flex-basis:auto; border-right:none; border-bottom:1px solid #f1f2f4; max-height:240px}
            .cascader-col:last-child{border-bottom:none}
        }
    </style>
@endpush

<div class="sidebar">
    <div class="widget-card">
        <div class="widget-title">{{ __('Location') }}</div>
        <div class="widget-body">
            <form id="geoFilterForm" method="GET" action="">
                {{-- preserve other filters except geo & page --}}
                @foreach(request()->except(['geo','page']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $vv)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <input type="hidden" name="geo" id="geoInput" value="{{ (int)($selectedGeo ?? 0) }}">

                <div class="cascader" id="geoCascader"
                     data-cat="{{ (int)($params['category_id'] ?? 0) }}"
                     data-brand="{{ (int)($params['brand_id'] ?? 0) }}">
                    <button type="button" class="btn btn-light cascader-trigger">
                        <i class="bi bi-geo-alt"></i>
                        <span>{{ !empty($selectedGeo) ? ($selectedPath ?? __('Selected location')) : __('All Kenya (choose county)') }}</span>
                    </button>

                    <div class="cascader-panel" role="dialog" aria-label="Location picker">
                        <div class="cascader-cols">
                            {{-- Counties --}}
                            <div class="cascader-col" id="col-counties" aria-label="Counties">
                                <div class="cascader-head">{{ __('Counties') }}</div>
                                <ul class="cascader-list" role="menu">
                                    @foreach($counties as $c)
                                        <li class="cascader-item county" role="menuitem" tabindex="0"
                                            data-id="{{ $c->id }}" data-name="{{ $c->name }}"
                                            title="{{ $c->name }}">
                                            <span class="name">{{ $c->name }}</span>
                                            @isset($c->product_count)
                                                <span class="cascader-count">{{ $c->product_count }}</span>
                                            @endisset
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Constituencies (dynamic) --}}
                            <div class="cascader-col" id="col-constits" aria-label="Constituencies">
                                <div class="cascader-head">{{ __('Constituencies') }}</div>
                                <div class="cascader-empty">{{ __('Hover a county…') }}</div>
                                <ul class="cascader-list d-none" role="menu"></ul>
                            </div>

                            {{-- Wards (dynamic) --}}
                            <div class="cascader-col" id="col-wards" aria-label="Wards">
                                <div class="cascader-head">{{ __('Wards') }}</div>
                                <div class="cascader-empty">{{ __('Hover a constituency…') }}</div>
                                <ul class="cascader-list d-none" role="menu"></ul>
                            </div>
                        </div>

                        <div class="cascader-footer">
                            <a href="{{ request()->fullUrlWithQuery(['geo'=>null,'page'=>null]) }}" class="btn btn-sm btn-link p-0">
                                {{ __('Clear location') }}
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="closeCascader">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Categories --}}
{{--    <div class="widget-card">--}}
{{--        <div class="widget-title">{{ __('Categories') }}</div>--}}
{{--        <div class="widget-body">--}}
{{--            @php $CategoryTree = CategoryTreeForFilter(glan()); @endphp--}}
{{--            <ul class="widget-list cats-tree">--}}
{{--                @foreach ($CategoryTree as $row)--}}
{{--                    <li class="cat-parent">--}}
{{--                        <div class="rowitem">--}}
{{--                            <div class="icon">--}}
{{--                                <a href="{{ route('frontend.product-category', [$row['id'], $row['slug']]) }}">--}}
{{--                                    <img src="{{ asset('public/media/'.$row['thumbnail']) }}" alt="{{ $row['name'] }}" />--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                            <div class="desc">--}}
{{--                                <a href="{{ route('frontend.product-category', [$row['id'], $row['slug']]) }}">{{ $row['name'] }}</a>--}}
{{--                            </div>--}}
{{--                            <div class="count">{{ $row['count'] }}</div>--}}
{{--                            @if(count($row['children'])) <i class="bi bi-chevron-right caret"></i> @endif--}}
{{--                        </div>--}}

{{--                        @if(count($row['children']))--}}
{{--                            <ul class="children">--}}
{{--                                @foreach ($row['children'] as $ch)--}}
{{--                                    <li>--}}
{{--                                        <a href="{{ route('frontend.product-category', [$ch['id'], $ch['slug']]) }}">{{ $ch['name'] }}</a>--}}
{{--                                        <span class="count">{{ $ch['count'] }}</span>--}}
{{--                                    </li>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}
{{--                        @endif--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="widget-card">
        <div class="widget-title">{{ __('Categories') }}</div>
        <div class="widget-body">
            <div class="cascader" id="catCascader">
                <button type="button" class="btn btn-light cascader-trigger">
                    <i class="bi bi-tags"></i>
                    <span id="catTriggerText">
                    {{ isset($metadata['name']) && $metadata['name'] ? $metadata['name'] : __('All Categories (choose)') }}
                </span>
                </button>

                <div class="cascader-panel" role="dialog" aria-label="Category picker">
                    <div class="cascader-cols">
                        {{-- Parents (top-level) --}}
                        <div class="cascader-col" id="cat-col-parents" aria-label="Parent categories">
                            <div class="cascader-head">{{ __('Categories') }}</div>
                            <ul class="cascader-list" role="menu" id="catParentsList"></ul>
                        </div>

                        {{-- Children --}}
                        <div class="cascader-col" id="cat-col-children" aria-label="Subcategories">
                            <div class="cascader-head">{{ __('Subcategories') }}</div>
                            <div class="cascader-empty">{{ __('Hover a category…') }}</div>
                            <ul class="cascader-list d-none" role="menu" id="catChildrenList"></ul>
                        </div>

                        {{-- Grandchildren (optional) --}}
                        <div class="cascader-col" id="cat-col-grand" aria-label="More">
                            <div class="cascader-head">{{ __('More') }}</div>
                            <div class="cascader-empty">{{ __('Hover a subcategory…') }}</div>
                            <ul class="cascader-list d-none" role="menu" id="catGrandList"></ul>
                        </div>
                    </div>

                    <div class="cascader-footer">
                        <a href="{{ request()->fullUrlWithQuery(['category_id'=>null,'page'=>null]) }}"
                           class="btn btn-sm btn-link p-0">{{ __('Clear category') }}</a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="catShowAll">{{ __('Show all') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="catClose">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Price --}}
    <div class="widget-card">
        <div class="widget-title">{{ __('Filter by Price') }}</div>
        <div class="widget-body">
            <div class="slider-range">
                <div id="slider-range"></div>
                <div class="price-range">
                    <div class="price-label">{{ __('Price Range') }}:</div>
                    <div class="price" id="amount"></div>
                </div>
                <input id="filter_min_price" type="hidden" value="0" />
                <input id="filter_max_price" type="hidden" />
                <a id="FilterByPrice" href="javascript:void(0);" class="btn theme-btn filter-btn">
                    <i class="bi bi-funnel"></i> {{ __('Filter') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Brands --}}
    <div class="widget-card">
        <div class="widget-title">{{ __('Brands') }}</div>
        <div class="widget-body">
            <ul class="widget-list">
                @php $BrandListForFilter = BrandListForFilter(); @endphp
                @foreach ($BrandListForFilter as $row)
                    <li>
                        <div class="icon">
                            <a href="{{ route('frontend.brand', [$row->id, str_slug($row->name)]) }}">
                                <img src="{{ asset('public/media/'.$row->thumbnail) }}" alt="{{ $row->name }}" />
                            </a>
                        </div>
                        <div class="desc">
                            <a href="{{ route('frontend.brand', [$row->id, str_slug($row->name)]) }}">{{ $row->name }}</a>
                        </div>
                        <div class="count">{{ $row->TotalProduct }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function(){
            const cascader   = document.getElementById('geoCascader');
            const panel      = cascader.querySelector('.cascader-panel');
            const trigger    = cascader.querySelector('.cascader-trigger');
            const closeBtn   = document.getElementById('closeCascader');
            const catId      = cascader.dataset.cat;
            const form       = document.getElementById('geoFilterForm');
            const geoInput   = document.getElementById('geoInput');
            const selectedGeo = parseInt(geoInput.value || '0', 10);

            const colConst   = document.getElementById('col-constits');
            const colWards   = document.getElementById('col-wards');
            const listCounties = document.querySelector('#col-counties .cascader-list');
            const listConst  = colConst.querySelector('.cascader-list');
            const listWards  = colWards.querySelector('.cascader-list');

            function open(){ cascader.classList.add('show'); }
            function close(){ cascader.classList.remove('show'); }
            trigger.addEventListener('click', (e)=>{
                e.stopPropagation();
                // If we already have a selected county, keep the panel focused on it
                if (!cascader.classList.contains('show')) {
                    open();
                    if (selectedGeo) collapseToSelectedCounty(selectedGeo);
                } else {
                    close();
                }
            });
            closeBtn.addEventListener('click', ()=> close());
            document.addEventListener('click', (e)=>{ if (!cascader.contains(e.target)) close(); });

            // ===== Utilities =====
            function clearCol(colEl, placeholder){
                colEl.querySelectorAll('ul.cascader-list li').forEach(li=>li.remove());
                colEl.querySelector('.cascader-empty')?.remove();
                const ul = colEl.querySelector('ul.cascader-list');
                ul.classList.add('d-none');
                const ph = document.createElement('div');
                ph.className = 'cascader-empty';
                ph.textContent = placeholder;
                colEl.prepend(ph);
            }
            function fillList(colEl, items, level){
                colEl.querySelector('.cascader-empty')?.remove();
                const ul = colEl.querySelector('ul.cascader-list');
                ul.classList.remove('d-none');
                ul.innerHTML = '';
                items.forEach(it=>{
                    const li = document.createElement('li');
                    li.className = `cascader-item ${level}`;
                    li.dataset.id = it.id;
                    li.dataset.name = it.name;
                    li.innerHTML = `<span class="name">${it.name}</span>${(it.count!==undefined? `<span class="cascader-count">${it.count}</span>`:'')}`;
                    ul.appendChild(li);
                });
            }
            function setActive(listRoot, el){
                listRoot.querySelectorAll('.cascader-item.is-active').forEach(x=>x.classList.remove('is-active'));
                if (el) el.classList.add('is-active');
            }

            // ===== Fetch with stale-response protection =====
            const childrenURL = @json(route('frontend.geo.children'));
            let countyCtrl = null, constCtrl = null;
            let lastCountyId = null, lastConstitId = null;

            async function fetchChildren(parentId, abortCtrl){
                const params = new URLSearchParams({ parent_id: parentId, cat_id: catId });
                const res = await fetch(`${childrenURL}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: abortCtrl?.signal
                });
                if(!res.ok) return [];
                return await res.json();
            }

            async function loadConstituencies(pid, countyEl){
                lastCountyId = pid;
                if (countyCtrl) countyCtrl.abort();
                countyCtrl = new AbortController();

                // UI: mark active and reset wards; ALSO clear any active constituency
                setActive(listCounties, countyEl);
                setActive(listConst, null);
                clearCol(colConst, @json(__('Loading…')));
                clearCol(colWards, @json(__('Hover a constituency…')));

                try {
                    const kids = await fetchChildren(pid, countyCtrl);
                    if (pid !== lastCountyId) return; // ignore stale
                    if (Array.isArray(kids) && kids.length){
                        fillList(colConst, kids, 'constituency');
                    } else {
                        clearCol(colConst, @json(__('No constituencies')));
                    }
                } catch(e) { /* aborted or failed */ }
            }

            async function loadWards(cid, constituEl){
                lastConstitId = cid;
                if (constCtrl) constCtrl.abort();
                constCtrl = new AbortController();

                setActive(listConst, constituEl);
                clearCol(colWards, @json(__('Loading…')));
                try {
                    const kids = await fetchChildren(cid, constCtrl);
                    if (cid !== lastConstitId) return; // ignore stale
                    if (Array.isArray(kids) && kids.length){
                        fillList(colWards, kids, 'ward');
                    } else {
                        clearCol(colWards, @json(__('No wards')));
                    }
                } catch(e) { /* aborted or failed */ }
            }

            // ===== Focus helpers (new) =====
            function collapseToSelectedCounty(id){
                // hide all counties except the selected one
                const lis = Array.from(listCounties.querySelectorAll('.county'));
                let selectedLi = null;
                lis.forEach(li=>{
                    if (parseInt(li.dataset.id,10) === parseInt(id,10)) {
                        selectedLi = li;
                        li.style.display = '';
                        li.classList.add('is-active');
                    } else {
                        li.classList.remove('is-active');
                        li.style.display = 'none';
                    }
                });
                if (selectedLi){
                    loadConstituencies(id, selectedLi);
                }
            }
            function restoreCountyList(){
                // show all counties again
                listCounties.querySelectorAll('.county').forEach(li=>{
                    li.style.display = '';
                    li.classList.remove('is-active');
                });
                clearCol(colConst, @json(__('Hover a county…')));
                clearCol(colWards, @json(__('Hover a constituency…')));
            }

            // ===== County hover/click =====
            listCounties.querySelectorAll('.county').forEach(li=>{
                li.addEventListener('mouseenter', ()=>{
                    // only load on hover if we are not collapsed to a selected county
                    const collapsed = Array.from(listCounties.querySelectorAll('.county'))
                        .some(x => x.style.display === 'none');
                    if (!collapsed) loadConstituencies(li.dataset.id, li);
                });
                li.addEventListener('click', ()=>{
                    // apply filter immediately
                    geoInput.value = li.dataset.id;
                    form.submit();
                });
            });

            // ===== Constituency hover/click (delegated) =====
            listConst.addEventListener('mouseover', (e)=>{
                const t = e.target.closest('.constituency');
                if(!t) return;
                loadWards(t.dataset.id, t);
            });
            listConst.addEventListener('click', (e)=>{
                const t = e.target.closest('.constituency');
                if(!t) return;
                geoInput.value = t.dataset.id;
                form.submit();
            });

            // ===== Ward click =====
            listWards.addEventListener('click', (e)=>{
                const t = e.target.closest('.ward');
                if(!t) return;
                geoInput.value = t.dataset.id;
                form.submit();
            });

            // ===== On county page: auto-collapse to that county and show children =====
            if (selectedGeo) {
                open();
                collapseToSelectedCounty(selectedGeo);
            }

            // (Optional) if you want "Clear location" to also restore the counties list,
            // add: onclick handler to that link to call restoreCountyList() before navigation.
        })();
        <script>
            (function(){
            const cascader   = document.getElementById('catCascader');
            const trigger    = cascader.querySelector('.cascader-trigger');
            const panel      = cascader.querySelector('.cascader-panel');
            const closeBtn   = document.getElementById('catClose');
            const showAllBtn = document.getElementById('catShowAll');
            const triggerTxt = document.getElementById('catTriggerText');

            const parentsCol = document.getElementById('cat-col-parents');
            const childCol   = document.getElementById('cat-col-children');
            const grandCol   = document.getElementById('cat-col-grand');

            const parentsUL  = document.getElementById('catParentsList');
            const childUL    = document.getElementById('catChildrenList');
            const grandUL    = document.getElementById('catGrandList');

            const CAT_CHILDREN_URL = window.CAT_CHILDREN_URL;
            const SELECTED_CAT_ID  = parseInt(window.SELECTED_CAT_ID || 0, 10);

            function open(){ cascader.classList.add('show'); }
            function close(){ cascader.classList.remove('show'); }
            trigger.addEventListener('click', (e)=>{ e.stopPropagation(); cascader.classList.toggle('show'); });
            closeBtn.addEventListener('click', ()=> close());
            document.addEventListener('click', (e)=>{ if (!cascader.contains(e.target)) close(); });

            function setEmpty(colEl, txt){
            colEl.querySelector('.cascader-empty')?.remove();
            const ul = colEl.querySelector('.cascader-list');
            ul.classList.add('d-none');
            const ph = document.createElement('div');
            ph.className = 'cascader-empty';
            ph.textContent = txt;
            colEl.prepend(ph);
        }
            function fillList(ul, colEl, items, itemClass){
            colEl.querySelector('.cascader-empty')?.remove();
            ul.classList.remove('d-none');
            ul.innerHTML = '';
            items.forEach(it=>{
            const li = document.createElement('li');
            li.className = `cascader-item ${itemClass}`;
            li.dataset.id = it.id;
            li.dataset.name = it.name;
            li.dataset.slug = it.slug || '';
            li.innerHTML = `<span class="name">${it.name}</span>${(it.count!==undefined? `<span class="cascader-count">${it.count}</span>`:'')}`;
            ul.appendChild(li);
        });
        }
            function setActive(listRoot, el){
            listRoot.querySelectorAll('.cascader-item.is-active').forEach(x=>x.classList.remove('is-active'));
            if (el) el.classList.add('is-active');
        }

            async function fetchKids(parentId, ctrl){
            const params = new URLSearchParams({ parent_id: parentId });
            const res = await fetch(`${CAT_CHILDREN_URL}?${params.toString()}`, {
            headers: { 'X-Requested-With':'XMLHttpRequest' },
            signal: ctrl?.signal
        });
            if(!res.ok) return [];
            return await res.json();
        }

            let abortParents = null, abortChild = null;
            async function loadParents(){
            if (abortParents) abortParents.abort();
            abortParents = new AbortController();
            setEmpty(parentsCol, @json(__('Loading…')));
            const parents = await fetchKids(0, abortParents);
            fillList(parentsUL, parentsCol, parents, 'cat-parent');
        }

            async function loadChildren(parentId, parentLi){
            if (abortChild) abortChild.abort();
            abortChild = new AbortController();

            setActive(parentsUL, parentLi);
            setEmpty(childCol, @json(__('Loading…')));
            setEmpty(grandCol, @json(__('Hover a subcategory…')));

            const kids = await fetchKids(parentId, abortChild);
            if (kids.length){
            fillList(childUL, childCol, kids, 'cat-child');
        } else {
            setEmpty(childCol, @json(__('No subcategories')));
        }
        }

            async function loadGrand(childId, childLi){
            setActive(childUL, childLi);
            setEmpty(grandCol, @json(__('Loading…')));
            const kids = await fetchKids(childId);
            if (kids.length){
            fillList(grandUL, grandCol, kids, 'cat-grand');
        } else {
            setEmpty(grandCol, @json(__('No more levels')));
        }
        }

            function goToCategory(id, slug){
            // your category route is /product-category/{id}/{title}
            const base = @json(url('/product-category'));
            const s = slug && slug.length ? slug : 'category';
            window.location.href = `${base}/${id}/${s}`;
        }

            // Hover → preview next level
            parentsUL.addEventListener('mouseenter', (e)=>{
            const li = e.target.closest('.cat-parent');
            if(!li) return;
            loadChildren(parseInt(li.dataset.id,10), li);
        }, true);
            childUL.addEventListener('mouseenter', (e)=>{
            const li = e.target.closest('.cat-child');
            if(!li) return;
            loadGrand(parseInt(li.dataset.id,10), li);
        }, true);

            // Click → navigate
            parentsUL.addEventListener('click', (e)=>{
            const li = e.target.closest('.cat-parent');
            if(!li) return;
            goToCategory(li.dataset.id, li.dataset.slug);
        });
            childUL.addEventListener('click', (e)=>{
            const li = e.target.closest('.cat-child');
            if(!li) return;
            goToCategory(li.dataset.id, li.dataset.slug);
        });
            grandUL.addEventListener('click', (e)=>{
            const li = e.target.closest('.cat-grand');
            if(!li) return;
            goToCategory(li.dataset.id, li.dataset.slug);
        });

            // Show all → reset to parents
            showAllBtn.addEventListener('click', async ()=>{
            triggerTxt.textContent = @json(__('All Categories (choose)'));
            await loadParents();
        });

            // Focus the panel to the selected category when you are on a category page
            async function focusToCategory(catId){
            if (!catId) return;
            const pl = parentsUL.querySelectorAll('.cat-parent');

            // case A: selected is a top-level category
            for (const li of pl) {
            if (parseInt(li.dataset.id,10) === catId) {
            triggerTxt.textContent = li.dataset.name;
            await loadChildren(catId, li);
            return;
        }
        }

            // case B: find the parent whose children include the selected id
            for (const li of pl) {
            const pid = parseInt(li.dataset.id,10);
            const kids = await fetchKids(pid);
            if (kids.find(k=>k.id === catId)) {
            triggerTxt.textContent = li.dataset.name;
            fillList(childUL, childCol, kids, 'cat-child');
            setActive(parentsUL, li);
            const childLi = Array.from(childUL.children).find(x => parseInt(x.dataset.id,10) === catId);
            if (childLi) setActive(childUL, childLi);
            await loadGrand(catId, childLi); // optional pre-load
            return;
        }
        }
        }

            // INIT
            (async ()=>{
            await loadParents();
            if (SELECTED_CAT_ID) {
            await focusToCategory(SELECTED_CAT_ID);
            open(); // auto-open when on a category page
        }
        })();
        })();
    </script>
@endpush
