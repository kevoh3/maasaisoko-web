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
            /* wider panel for long names */
            min-width: 500px; width:100%; max-width: min(600px, 90vw);
            background:#fff; border:1px solid #e5e7eb; border-radius:10px;
            box-shadow:0 12px 28px rgba(0,0,0,.12);
            display:none; overflow:hidden; margin-top:6px;
        }
        .cascader.show .cascader-panel{display:flex; flex-direction:column}

        .cascader-cols{display:flex; width:100%}
        /* Give more space to county names */
        .cascader-col{flex: 0 0 42%; max-height:320px; overflow:auto; border-right:1px solid #f1f2f4}
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

        /* Scroll shadows hint */
        .cascader-col.scrolling::before,
        .cascader-col.scrolling::after{
            content:""; position:absolute; left:0; right:0; height:14px; pointer-events:none;
        }
        .cascader-col.scrolling::before{ top:0; box-shadow: inset 0 10px 10px -10px rgba(0,0,0,.25); }
        .cascader-col.scrolling::after{ bottom:0; box-shadow: inset 0 -10px 10px -10px rgba(0,0,0,.25); }

        /* Skeleton shimmer while loading */
        .cascader-skel{ padding:8px 12px; width:100% }
        .cascader-skel .bar{
            height:12px; margin:8px 0; border-radius:6px;
            background: linear-gradient(90deg,#f2f4f7, #eceff3, #f2f4f7);
            background-size: 200% 100%; animation: skel 1.1s linear infinite;
        }
        @keyframes skel{ to { background-position:-200% 0; } }

        @media (max-width: 420px){
            .cascader-panel{min-width: 100%;}
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

{{--                <div class="cascader" id="geoCascader" data-cat="{{ (int)$params['category_id'] }}">--}}
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

	<div class="widget-card">
		<div class="widget-title">{{ __('Categories') }}</div>
		<div class="widget-body">
{{--			<ul class="widget-list">--}}
{{--				@php $CategoryListForFilter = CategoryListForFilter(); @endphp--}}
{{--				@foreach ($CategoryListForFilter as $row)--}}
{{--				<li>--}}
{{--					<div class="icon">--}}
{{--						<a href="{{ route('frontend.product-category', [$row->id, $row->slug]) }}">--}}
{{--							<img src="{{ asset('public/media/'.$row->thumbnail) }}" alt="{{ $row->name }}" />--}}
{{--						</a>--}}
{{--					</div>--}}
{{--					<div class="desc">--}}
{{--						<a href="{{ route('frontend.product-category', [$row->id, $row->slug]) }}">{{ $row->name }}</a>--}}
{{--					</div>--}}
{{--					<div class="count">{{ $row->TotalProduct }}</div>--}}
{{--				</li>--}}
{{--				@endforeach--}}
{{--			</ul>--}}
            @php $CategoryTree = CategoryTreeForFilter(glan()); @endphp
            <ul class="widget-list cats-tree">
                @foreach ($CategoryTree as $row)
                    <li class="cat-parent">
                        <div class="rowitem">
                            <div class="icon">
                                <a href="{{ route('frontend.product-category', [$row['id'], $row['slug']]) }}">
                                    <img src="{{ asset('public/media/'.$row['thumbnail']) }}" alt="{{ $row['name'] }}" />
                                </a>
                            </div>
                            <div class="desc">
                                <a href="{{ route('frontend.product-category', [$row['id'], $row['slug']]) }}">{{ $row['name'] }}</a>
                            </div>
                            <div class="count">{{ $row['count'] }}</div>
                            @if(count($row['children'])) <i class="bi bi-chevron-right caret"></i> @endif
                        </div>

                        @if(count($row['children']))
                            <ul class="children">
                                @foreach ($row['children'] as $ch)
                                    <li>
                                        <a href="{{ route('frontend.product-category', [$ch['id'], $ch['slug']]) }}">{{ $ch['name'] }}</a>
                                        <span class="count">{{ $ch['count'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

        </div>
	</div>

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
				<a id="FilterByPrice" href="javascript:void(0);" class="btn theme-btn filter-btn"><i class="bi bi-funnel"></i> {{ __('Filter') }}</a>
			</div>
		</div>
	</div>
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

            const colConst   = document.getElementById('col-constits');
            const colWards   = document.getElementById('col-wards');
            const listConst  = colConst.querySelector('.cascader-list');
            const listWards  = colWards.querySelector('.cascader-list');

            function open(){ cascader.classList.add('show'); }
            function close(){ cascader.classList.remove('show'); }
            trigger.addEventListener('click', (e)=>{ e.stopPropagation(); cascader.classList.toggle('show'); });
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

                // UI: mark active and reset wards
                setActive(cascader, countyEl);
                clearCol(colConst, @json(__('Loading…')));
                clearCol(colWards, @json(__('Hover a constituency…')));

                try {
                    const kids = await fetchChildren(pid, countyCtrl);
                    // Ignore if user already hovered another county
                    if (pid !== lastCountyId) return;

                    if (Array.isArray(kids) && kids.length){
                        fillList(colConst, kids, 'constituency');
                    } else {
                        clearCol(colConst, @json(__('No constituencies')));
                    }
                } catch(e) {
                    // aborted or failed
                }
            }

            async function loadWards(cid, constituEl){
                lastConstitId = cid;
                if (constCtrl) constCtrl.abort();
                constCtrl = new AbortController();

                setActive(listConst, constituEl);
                clearCol(colWards, @json(__('Loading…')));
                try {
                    const kids = await fetchChildren(cid, constCtrl);
                    if (cid !== lastConstitId) return;
                    if (Array.isArray(kids) && kids.length){
                        fillList(colWards, kids, 'ward');
                    } else {
                        clearCol(colWards, @json(__('No wards')));
                    }
                } catch(e) {
                    // aborted or failed
                }
            }

            // ===== County hover/click =====
            cascader.querySelectorAll('.county').forEach(li=>{
                li.addEventListener('mouseenter', ()=> loadConstituencies(li.dataset.id, li));
                li.addEventListener('click', ()=>{
                    geoInput.value = li.dataset.id;
                    form.submit();
                });
            });

            // ===== Constituency hover/click (delegated) =====
            listConst.addEventListener('mouseenter', (e)=>{
                const t = e.target.closest('.constituency');
                if(!t) return;
                loadWards(t.dataset.id, t);
            }, true);

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

            // Optional: auto-open when a geo is already selected
            @if(!empty($selectedGeo))
            open();
            @endif
        })();
    </script>
@endpush
