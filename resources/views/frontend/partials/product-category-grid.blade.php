@php $gtext = gtext(); @endphp
<div class="row">
	@if(count($datalist)>0)
	@foreach ($datalist as $row)
		@if(($category_variation == 'left_sidebar') || ($category_variation == 'right_sidebar'))
		<div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-4">
		@else
		<div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
		@endif
		
			<div class="item-card">
				<div class="item-image">
					@if(($row->is_discount == 1) && ($row->old_price !=''))
						@php 
							$discount = number_format((($row->old_price - $row->sale_price)*100)/$row->old_price);
						@endphp
					<span class="item-label">{{ $discount }}% {{ __('Off') }}</span>
					@endif
					<a href="{{ route('frontend.product', [$row->id, $row->slug]) }}">
						<img src="{{ asset('public/media/'.$row->f_thumbnail) }}" alt="{{ $row->title }}" />
					</a>
				</div>
				<div class="item-title">
					<a href="{{ route('frontend.product', [$row->id, $row->slug]) }}">{{ str_limit($row->title) }}</a>
				</div>
				<div class="rating-wrap">
					<div class="stars-outer">
						<div class="stars-inner" style="width:{{ $row->ReviewPercentage }}%;"></div>
					</div>
					<span class="rating-count">({{ $row->TotalReview }})</span>
				</div>
				<div class="item-sold">
					{{ __('Sold By') }} <a href="{{ route('frontend.stores', [$row->seller_id, str_slug($row->shop_url)]) }}">{{ str_limit($row->shop_name) }}</a>
				</div>
				<div class="item-pric-card">
					@if($row->sale_price != '')
						@if($gtext['currency_position'] == 'left')
						<div class="new-price">{{ $gtext['currency_icon'] }}{{ NumberFormat($row->sale_price) }}</div>
						@else
						<div class="new-price">{{ NumberFormat($row->sale_price) }}{{ $gtext['currency_icon'] }}</div>
						@endif
					@endif
					@if(($row->is_discount == 1) && ($row->old_price !=''))
						@if($gtext['currency_position'] == 'left')
						<div class="old-price">{{ $gtext['currency_icon'] }}{{ NumberFormat($row->old_price) }}</div>
						@else
						<div class="old-price">{{ NumberFormat($row->old_price) }}{{ $gtext['currency_icon'] }}</div>
						@endif
					@endif
				</div>
				<div class="item-card-bottom">
					<a data-id="{{ $row->id }}" href="javascript:void(0);" class="btn add-to-cart addtocart">{{ __('Add To Cart') }}</a>
					<ul class="item-cart-list">
						<li><a class="addtowishlist" data-id="{{ $row->id }}" href="javascript:void(0);"><i class="bi bi-heart"></i></a></li>
						<li><a href="{{ route('frontend.product', [$row->id, $row->slug]) }}"><i class="bi bi-eye"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	@endforeach
	@else
	<div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 col-xl-4 offset-xl-4 col-xxl-4 offset-xxl-4">
		<div class="empty_card">
			<div class="empty_img">
				<img src="{{ asset('public/frontend/images/empty.png') }}" />
			</div>
			<h3>{{ __('Oops! No product found.') }}</h3>
		</div>
	</div>
	@endif
</div>
<div class="row mt-15">
	<div class="col-lg-12">
		{{ $datalist->links() }}
	</div>
</div>