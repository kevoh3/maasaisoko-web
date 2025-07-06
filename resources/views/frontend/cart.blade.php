@extends('layouts.frontend')

@section('title', __('Cart'))
@php 
$gtext = gtext();
$gtax = getTax();
@endphp

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
							<li class="breadcrumb-item active" aria-current="page">{{ __('Cart') }}</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6">
					<div class="page-title">
						<h1>{{ __('Cart') }}</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Page Breadcrumb/ -->
	
	<!-- Inner Section -->
	<section class="inner-section inner-section-bg">
		<div class="container">
			@if(session('shopping_cart'))
			<div class="row">
				<div class="col-xl-12">
					<div class="table-responsive shopping-cart">
						<table class="table">
							<thead>
								<tr>
									<th>{{ __('Image') }}</th>
									<th>{{ __('Product') }}</th>
									<th>{{ __('Sold By') }}</th>
									<th class="text-center">{{ __('Unit') }}</th>
									<th class="text-center">{{ __('Price') }}</th>
									<th class="text-center">{{ __('Quantity') }}</th>
									<th class="text-center">{{ __('Total') }}</th>
									<th class="text-center">{{ __('Remove') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach (session('shopping_cart') as $row)
								@php
								
									$pro_price = $row['price'];
									$pro_qty = $row['qty'];
									
									$total_Price = $row['price']*$row['qty'];
									
									if($gtext['currency_position'] == 'left'){ 
										$price = $gtext['currency_icon'].NumberFormat($pro_price); 
									}else{
										$price = NumberFormat($pro_price).$gtext['currency_icon'];  
									}

									if($gtext['currency_position'] == 'left'){
										$totalPrice = $gtext['currency_icon'].NumberFormat($total_Price);
									}else{
										$totalPrice = NumberFormat($total_Price).$gtext['currency_icon'];
									}
									
								@endphp
								<tr id="row_delete_{{ $row['id'] }}">
									<td class="pro-image-w">
										<div class="pro-image">
											<a href="{{ route('frontend.product', [$row['id'], str_slug($row['name'])]) }}">
												<img src="{{ asset('public/media/'.$row['thumbnail']) }}" alt="{{ $row['name'] }}">
											</a>
										</div>
									</td>
									<td class="pro-name-w" data-title="{{ __('Product') }}:">
										<span class="pro-name"><a href="{{ route('frontend.product', [$row['id'], str_slug($row['name'])]) }}">{{ $row['name'] }}</a></span>
									</td>
									<td class="pro-store-w" data-title="{{ __('Sold By') }}:">
										<a href="{{ route('frontend.stores', [$row['seller_id'], str_slug($row['store_name'])]) }}">{{ $row['store_name'] }}</a>
									</td>
									<td class="text-center pro-variation-w" data-title="{{ __('Unit') }}:">
										<span class="pro-variation">{{ $row['unit'] }}</span>
									</td>
									<td class="text-center pro-price-w" data-title="{{ __('Price') }}:">
										<span class="pro-price"><span class="pro-price">{{ $price }}</span></span>
									</td>
									<td class="text-center pro-quantity-w" data-title="{{ __('Quantity') }}:">
										<div class="pro-quantity">{{ $row['qty'] }}</div>
									</td>
									<td class="text-center pro-total-price-w" data-title="{{ __('Total') }}:">
										<span class="pro-total-price">{{ $totalPrice }}</span>
									</td>
									<td class="text-center pro-remove-w" data-title="Remove:">
										<a data-id="{{ $row['id'] }}" id="removetoviewcart_{{ $row['id'] }}" onclick="onRemoveToCart({{ $row['id'] }})" href="javascript:void(0);" class="pro-remove"><i class="bi bi-x-lg"></i></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-7"></div>
				<div class="col-lg-5 mt10">
					<div class="carttotals-card">
						<div class="carttotals-head">{{ __('Cart Total') }}</div>
						<div class="carttotals-body">
							<table class="table">
								<tbody>
									<tr><td><span class="title">{{ __('Price Total') }}</span><span class="price viewcart_price_total"></span></td></tr>
									<tr><td><span class="title">{{ __('Tax') }}</span><span class="price viewcart_tax"></span></td></tr>
									<tr><td><span class="title">{{ __('Subtotal') }}</span><span class="price viewcart_sub_total"></span></td></tr>
									<tr><td><span class="total">{{ __('Total') }}</span><span class="total-price viewcart_total"></span></td></tr>
								</tbody>
							</table>
							<a class="btn theme-btn mt10" href="{{ route('frontend.checkout') }}">{{ __('Proceed To CheckOut') }}</a>
						</div>
					</div>
				</div>
			</div>
			@else
			<div class="row">
				<div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 col-xl-4 offset-xl-4 col-xxl-4 offset-xxl-4">
					<div class="empty_card">
						<div class="empty_img">
							<img src="{{ asset('public/frontend/images/empty.png') }}" />
						</div>
						<h3>{{ __('Your cart is empty!') }}</h3>
					</div>
				</div>
			</div>
			@endif
		</div>
	</section>
	<!-- /Inner Section/ -->
</main>

@endsection

@push('scripts')
<script src="{{asset('public/frontend/pages/view_cart.js')}}"></script>
@endpush	