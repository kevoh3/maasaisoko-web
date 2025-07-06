@extends('layouts.frontend')

@section('title', __('Wishlist'))
@php $gtext = gtext(); @endphp

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

@section('content')

<main class="main">
	<!-- Page Breadcrumb -->
	<div class="breadcrumb-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('Wishlist') }}</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6">
					<div class="page-title">
						<h1>{{ __('Wishlist') }}</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Page Breadcrumb/ -->
	
	<!-- Inner Section -->
	<section class="inner-section inner-section-bg">
		<div class="container">
			<div class="row">
				@if(session('shopping_wishlist'))
				<div class="col-xl-12">
					<div class="table-responsive shopping-cart">
						<table class="table">
							<thead>
								<tr>
									<th class="text-center">{{ __('Remove') }}</th>
									<th>{{ __('Image') }}</th>
									<th>{{ __('Product') }}</th>
									<th>{{ __('Sold By') }}</th>
									<th class="text-center">{{ __('Price') }}</th>
									<th class="text-center">{{ __('View') }}</th>
								</tr>
							</thead>
							<tbody>
								
								@foreach (session('shopping_wishlist') as $row)
								@php
									$pro_price = $row['price'];

									if($gtext['currency_position'] == 'left'){ 
										$price = $gtext['currency_icon'].NumberFormat($pro_price); 
									}else{
										$price = NumberFormat($pro_price).$gtext['currency_icon'];  
									}
								@endphp
								<tr id="row_delete_{{ $row['id'] }}">
									<td class="text-center pro-remove-w" data-title="{{ __('Remove') }}:">
										<a data-id="{{ $row['id'] }}" id="removetowishlist_{{ $row['id'] }}" onclick="onRemoveToWishlist({{ $row['id'] }})" href="javascript:void(0);" class="pro-remove"><i class="bi bi-x-lg"></i></a>
									</td>
									<td class="pro-image-w">
										<div class="pro-image">
											<a href="{{ route('frontend.product', [$row['id'], str_slug($row['name'])]) }}">
												<img src="{{ asset('public/media/'.$row['thumbnail']) }}" alt="{{ $row['name'] }}">
											</a>
										</div>
									</td>
									<td data-title="{{ __('Product') }}:">
										<span class="pro-name"><a href="{{ route('frontend.product', [$row['id'], str_slug($row['name'])]) }}">{{ $row['name'] }}</a></span>
									</td>
									<td class="pro-store-w" data-title="{{ __('Sold By') }}:">
										<a href="{{ route('frontend.stores', [$row['seller_id'], str_slug($row['store_name'])]) }}">{{ $row['store_name'] }}</a>
									</td>
									<td class="text-center pro-price-w" data-title="{{ __('Price') }}:">
										<span class="pro-price">{{ $price }}</span>
									</td>
									<td class="text-center pro-addtocart-w" data-title="{{ __('View') }}:">
										<div class="pro-addtocart">
											<a class="btn theme-btn cart" href="{{ route('frontend.product', [$row['id'], str_slug($row['name'])]) }}">{{ __('View') }}</a>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				@else
				<div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 col-xl-4 offset-xl-4 col-xxl-4 offset-xxl-4">
					<div class="empty_card">
						<div class="empty_img">
							<img src="{{ asset('public/frontend/images/empty.png') }}" />
						</div>
						<h3>{{ __('Your cart is empty!') }}</h3>
					</div>
				</div>
				@endif
			</div>
		</div>
	</section>
	<!-- /Inner Section/ -->
</main>

@endsection

@push('scripts')
<script src="{{asset('public/frontend/pages/wishlist.js')}}"></script>
@endpush	