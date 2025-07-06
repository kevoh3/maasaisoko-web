<main class="main {{ $PageVariation['home_variation'] }}">
<!-- Home Slider -->
	@if($section1->is_publish == 1)
	<section class="slider-section">
		<div class="slider-screen h1-height" style="background-image: url({{ $section1->image ? asset('public/media/'.$section1->image) : '' }});">
			<div class="home-slider owl-carousel">
				@foreach ($slider as $row)
				@php $aRow = json_decode($row->desc); @endphp
				<!-- Slider Item -->
				<div class="single-slider">
					<div class="container">
						<div class="row">
							<div class="order-1 col-sm-12 order-sm-1 col-md-6 order-md-0 col-lg-5 order-lg-0">
								<div class="slider-content">
									<h1>{{ $row->title }}</h1>
									@if($aRow->sub_title != '')
									<p class="relative">{{ $aRow->sub_title }}</p>
									@endif
									
									@if($aRow->button_text != '')
									<a href="{{ $row->url }}" class="btn theme-btn" {{ $aRow->target =='' ? '' : "target=".$aRow->target }}>{{ $aRow->button_text }}</a>
									@endif
									
									@if($aRow->layer_image_1 != '')
									<div class="h1-layer2 layer-bounce2">
										<img src="{{ asset('public/media/'.$aRow->layer_image_1) }}" alt="{{ $row->title }}" />
									</div>
									@endif
								</div>
							</div>
							<div class="order-0 col-sm-12 order-sm-0 col-md-6 order-md-1 col-lg-7 order-lg-1">
								@if($aRow->layer_image_2 != '')
								<div class="h1-layer3 layer-bounce3">
									<img src="{{ asset('public/media/'.$aRow->layer_image_2) }}" alt="{{ $row->title }}" />
								</div>
								@endif
								<div class="h1-layer1 layer-bounce1">
									<img src="{{ asset('public/media/'.$row->image) }}" alt="{{ $row->title }}" />
								</div>
								@if($aRow->layer_image_3 != '')
								<div class="h1-layer4 layer-bounce4">
									<img src="{{ asset('public/media/'.$aRow->layer_image_3) }}" alt="{{ $row->title }}" />
								</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<!-- /Slider Item/ -->
				@endforeach
			</div>
		</div>
	</section>
	@endif
	<!-- /Home Slider/ -->
	
	<!-- Featured Categories -->
	@if($section2->is_publish == 1)
	<section class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-heading">
						@if($section2->desc !='')
						<h5>{{ $section2->desc }}</h5>
						@endif
						
						@if($section2->title !='')
						<h2>{{ $section2->title }}</h2>
						@endif
					</div>
				</div>
			</div>
			<div class="row owl-carousel caro-common featured-categories">
				@foreach ($pro_category as $row)
				<div class="col-lg-12">
					<div class="featured-card">
						<div class="featured-image">
							<img src="{{ asset('public/media/'.$row->thumbnail) }}" alt="{{ $row->name }}" />
						</div>
						<div class="featured-title">
							<a href="{{ route('frontend.product-category', [$row->id, $row->slug]) }}">{{ $row->name }}</a>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</section>
	@endif
	<!-- /Featured Categories/ -->
		
	<!-- Popular Products -->
	@if($section3->is_publish == 1)
	<section class="section product-section">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="section-heading">
						@if($section3->desc !='')
						<h5>{{ $section3->desc }}</h5>
						@endif
						
						@if($section3->title !='')
						<h2>{{ $section3->title }}</h2>
						@endif
					</div>
				</div>
			</div>
			<div class="row owl-carousel caro-common category-carousel">
			
				@foreach ($popular_products as $row)
				<div class="col-lg-12">
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
			</div>
		</div>
	</section>
	@endif
	<!-- /Popular Products/ -->
	
	<!-- Offer Section -->
	@if($section4->is_publish == 1)
	@if(count($offer_ad_position1)>0)
	<section class="section offer-section" style="background-image: url({{ $section4->image ? asset('public/media/'.$section4->image) : '' }});">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-heading">
						@if($section4->desc !='')
						<h5>{{ $section4->desc }}</h5>
						@endif
						
						@if($section4->title !='')
						<h2>{{ $section4->title }}</h2>
						@endif
					</div>
				</div>
			</div>

			<div class="row">
				@foreach ($offer_ad_position1 as $row)
				@php $aRow = json_decode($row->desc); @endphp
				<div class="col-lg-4">
					<div class="offer-card" style="background:{{ $aRow->bg_color == '' ? '#daeac5' : $aRow->bg_color }};">
						@if($aRow->text_1 != '')
						<div class="offer-heading">
							<h2>{{ $aRow->text_1 }}</h2>
						</div>
						@endif
						@if($aRow->text_1 != '')
						<div class="offer-body">
							<p>{{ $aRow->text_2 }}</p>
						</div>
						@endif
						<div class="offer-footer">
							@if($aRow->button_text != '')
							<a href="{{ $row->url }}" class="btn theme-btn" {{ $aRow->target =='' ? '' : "target=".$aRow->target }}>{{ $aRow->button_text }}</a>
							@endif
							<div class="offer-image">
								<img src="{{ asset('public/media/'.$row->image) }}" alt="{{ $aRow->text_1 }}" />
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</section>
	@endif
	@endif
	<!-- /Offer Section/ -->

	<!-- Products Section -->
	@if($section5->is_publish == 1)
	<section class="section product-section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-heading">
						@if($section5->desc !='')
						<h5>{{ $section5->desc }}</h5>
						@endif
						
						@if($section5->title !='')
						<h2>{{ $section5->title }}</h2>
						@endif
					</div>
				</div>
			</div>
		
			<div class="row mb25">
				<div class="col-lg-12">
					<ul class="nav nav-pills tp-tabs" id="pills-tab" role="tablist">
						@if($section8->is_publish == 1)
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="pills-newProducts-tab" data-bs-toggle="pill" data-bs-target="#pills-newProducts" type="button" role="tab" aria-controls="pills-newProducts" aria-selected="true">{{ $section8->title }}</button>
						</li>
						@endif
						@if($section9->is_publish == 1)
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="pills-topSelling-tab" data-bs-toggle="pill" data-bs-target="#pills-topSelling" type="button" role="tab" aria-controls="pills-topSelling" aria-selected="false">{{ $section9->title }}</button>
						</li>
						@endif
						@if($section10->is_publish == 1)
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="pills-trendingProducts-tab" data-bs-toggle="pill" data-bs-target="#pills-trendingProducts" type="button" role="tab" aria-controls="pills-trendingProducts" aria-selected="false">{{ $section10->title }}</button>
						</li>
						@endif
						@if($section11->is_publish == 1)
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="pills-topRated-tab" data-bs-toggle="pill" data-bs-target="#pills-topRated" type="button" role="tab" aria-controls="pills-topRated" aria-selected="false">{{ $section11->title }}</button>
						</li>
						@endif
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="tab-content" id="pills-tabContent">
						<!--New Products-->
						@if($section8->is_publish == 1)
						<div class="tab-pane fade show active" id="pills-newProducts" role="tabpanel" aria-labelledby="pills-newProducts-tab">
							<div class="row">
								@foreach ($new_products as $row)
								<div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
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
							</div>
						</div>
						@endif
						<!--/New Products/-->
					
						<!--Top Selling-->
						@if($section9->is_publish == 1)
						<div class="tab-pane fade" id="pills-topSelling" role="tabpanel" aria-labelledby="pills-topSelling-tab">
							<div class="row">
								@foreach ($top_selling as $row)
								<div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
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
							</div>
						</div>
						@endif
						<!--/Top Selling/-->
						
						<!--Trending Products-->
						@if($section10->is_publish == 1)
						<div class="tab-pane fade" id="pills-trendingProducts" role="tabpanel" aria-labelledby="pills-trendingProducts-tab">
							<div class="row">
								@foreach ($trending_products as $row)
								<div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
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
							</div>
						</div>
						@endif
						<!--/Trending Products/-->
						
						<!--Top Rated-->
						@if($section11->is_publish == 1)
						<div class="tab-pane fade" id="pills-topRated" role="tabpanel" aria-labelledby="pills-topRated-tab">
							<div class="row">
								@foreach ($top_rated as $row)
								<div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 col-xxl-3">
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
							</div>
						</div>
						@endif
						<!--/Top Rated/-->
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif
	<!-- /Products Section/ -->
	
	<!-- Video Section -->
	@if($home_video['is_publish'] == 1)
	<section class="section video-section" style="background-image: url({{ asset('public/media/'.$home_video['image']) }});">
		<div class="container">
			<div class="row justify-content-start">
				<div class="col-xl-7 text-center">
					<div class="video-card">
						<a href="{{ $home_video['video_url'] }}" class="play-icon popup-video">
							<i class="bi bi-play-fill"></i>
						</a>
					</div>
				</div>
				<div class="col-xl-5">
					<div class="video-desc">
						<h1>{{ $home_video['title'] }}</h1>
						@if($home_video['short_desc'] !='')
						<p>{{ $home_video['short_desc'] }}</p>
						@endif
						<a href="{{ $home_video['url'] }}" {{ $home_video['target'] =='' ? '' : "target=".$home_video['target'] }} class="btn theme-btn">{{ $home_video['button_text'] }}</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif
	<!-- /Video Section/ -->
	
	<!-- Deals Section -->
	@if($section6->is_publish == 1)
	<section class="section deals-section">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="section-heading">
						@if($section6->desc !='')
						<h5>{{ $section6->desc }}</h5>
						@endif
						
						@if($section6->title !='')
						<h2>{{ $section6->title }}</h2>
						@endif
					</div>
				</div>
			</div>
			<div class="row">
				@if(count($offer_ad_position2)>0)
				<div class="order-1 col-sm-12 order-sm-1 col-md-4 order-md-1 col-lg-3 order-lg-0">
					@foreach ($offer_ad_position2 as $row)
					@php $aRow = json_decode($row->desc); @endphp
					<div class="deals-card" style="background:{{ $aRow->bg_color == '' ? '#d7eabf' : $aRow->bg_color }};">
						<img src="{{ asset('public/media/'.$row->image) }}" alt="{{ $aRow->text_1 }}" />
						@if($aRow->text_1 != '')
						<div class="deals-desc">{{ $aRow->text_1 }}</div>
						@endif
						<div class="deals-bottom">
							@if($aRow->button_text != '')
							<a href="{{ $row->url }}" class="btn theme-btn" {{ $aRow->target =='' ? '' : "target=".$aRow->target }}>{{ $aRow->button_text }}</a>
							@endif
						</div>
					</div>
					@endforeach
				</div>
				<div class="order-0 col-sm-12 order-sm-0 col-md-8 order-md-0 col-lg-9 order-lg-1">
				@else
				<div class="order-0 col-sm-12 order-sm-0 col-md-12 order-md-0 col-lg-12 order-lg-1">
				@endif
					<div class="row owl-carousel caro-common deals-carousel">
						@foreach ($deals_products as $row)
						<div class="col-lg-12">
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
									@if(($row->is_discount == 1) && ($row->end_date !=''))
									<div class="deals-countdown-card">
										<div data-countdown="{{ date('Y/m/d', strtotime($row->end_date)) }}" class="deals-countdown"></div>
									</div>
									@endif
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
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif
	<!-- /Deals Section/ -->
</main>