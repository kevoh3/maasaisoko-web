
<div class="sidebar">

	<div class="widget-card">
		<div class="widget-title">{{ __('Categories') }}</div>
		<div class="widget-body">
			<ul class="widget-list">
				@php $CategoryListForFilter = CategoryListForFilter(); @endphp
				@foreach ($CategoryListForFilter as $row)
				<li>
					<div class="icon">
						<a href="{{ route('frontend.product-category', [$row->id, $row->slug]) }}">
							<img src="{{ asset('public/media/'.$row->thumbnail) }}" alt="{{ $row->name }}" />
						</a>
					</div>
					<div class="desc">
						<a href="{{ route('frontend.product-category', [$row->id, $row->slug]) }}">{{ $row->name }}</a>
					</div>
					<div class="count">{{ $row->TotalProduct }}</div>
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
