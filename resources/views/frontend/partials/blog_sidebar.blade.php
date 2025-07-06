
<div class="sidebar">

	<div class="widget-card">
		<div class="widget-title">{{ __('Blog Categories') }}</div>
		<div class="widget-body">
			<ul class="widget-list">
				@php $BlogCategoryListForFilter = BlogCategoryListForFilter(); @endphp
				@foreach ($BlogCategoryListForFilter as $row)
				<li>
					<div class="desc">
						<a href="{{ route('frontend.blog-category', [$row->id, $row->slug]) }}">{{ $row->name }}</a>
					</div>
					<div class="count">{{ $row->TotalProduct }}</div>
				</li>
				@endforeach
			</ul>
		</div>
	</div>

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
