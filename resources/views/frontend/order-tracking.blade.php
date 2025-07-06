@extends('layouts.frontend')

@section('title', __('Order Tracking'))
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
							<li class="breadcrumb-item active" aria-current="page">{{ __('Order Tracking') }}</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6">
					<div class="page-title">
						<h1>{{ __('Order Tracking') }}</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Page Breadcrumb/ -->
	
	<!-- Inner Section -->
	<section class="inner-section inner-section-bg">
		<div class="container">
			<div class="row mb20">
				<div class="col-lg-12">
					<div class="register shadow_bg_off">
						<h4>{{ __('Order Tracking') }}</h4>
						<p>{{ __('Tracking your order status') }}</p>
						<form class="form" method="GET" action="{{ route('frontend.order-tracking') }}">
							<div class="form-group">
								<input name="order_no" type="text" class="form-control" placeholder="{{ __('Order#') }}" required />
							</div>
							<div class="form-group">
								<input name="email" type="email" class="form-control" placeholder="{{ __('Email Address') }}" required />
							</div>
							<input type="submit" class="btn theme-btn" value="{{ __('Find') }}">
						</form>
					</div>
				</div>
			</div>

			<div class="my_card mb40">
				@if (count($datalist)>0)
				
				@foreach($masterData as $mdata)
				@php 
					if($mdata->order_status_id == 1){
						$awaiting = "check";
						$processing = "x";
						$pickup = "x";
						$completed = "x";
					} elseif($mdata->order_status_id == 2) {
						$awaiting = "check";
						$processing = "check";
						$pickup = "x";
						$completed = "x";
					} elseif($mdata->order_status_id == 3) {
						$awaiting = "check";
						$processing = "check";
						$pickup = "check";
						$completed = "x";
					} elseif($mdata->order_status_id == 4) {
						$awaiting = "check";
						$processing = "check";
						$pickup = "check";
						$completed = "check";
					}
				@endphp
				<div class="row">
					<div class="col-lg-12">
						<div class="order_tracking_card">
							<h4 class="tracking_order_no">{{ __('Order#') }}: {{ $mdata->order_no }} </h4>
							<ul class="order_track">
								<li>
									<span class="order_track_item {{ $awaiting }}"><i class="bi bi-{{ $awaiting }}"></i></span>
									<strong>{{ $order_status_list[1] }}</strong>
								</li>
								<li>
									<span class="order_track_item {{ $processing }}"><i class="bi bi-{{ $processing }}"></i></span>
									<strong>{{ $order_status_list[2] }}</strong>
								</li>
								<li>
									<span class="order_track_item {{ $pickup }}"><i class="bi bi-{{ $pickup }}"></i></span>
									<strong>{{ $order_status_list[3] }}</strong>
								</li>
								<li>
									<span class="order_track_item {{ $completed }}"><i class="bi bi-{{ $completed }}"></i></span>
									<strong>{{ $order_status_list[4] }}</strong>
								</li>
							</ul>
						</div>
					</div>
				</div>
				@endforeach
				
				<div class="row">
					<div class="col-lg-12">
						@foreach($masterData as $mdata)
						<div class="row mb10">
							<div class="col-lg-6 mb10">
								<h5>{{ __('BILL TO') }}:</h5>
								<p class="mb5"><strong>{{ $mdata->customer_name }}</strong></p>
								<p class="mb5">{{ $mdata->customer_address }}</p>
								<p class="mb5">{{ $mdata->city }}, {{ $mdata->state }}, {{ $mdata->country }}</p>
								<p class="mb5">{{ $mdata->customer_email }}</p>
								<p class="mb5">{{ $mdata->customer_phone }}</p>
							</div>
							<div class="col-lg-6 mb10 order_status">
								<p class="mb5"><strong>{{ __('Order#') }}</strong>: {{ $mdata->order_no }}</p>
								<p class="mb5"><strong>{{ __('Order Date') }}</strong>: {{ date('d-m-Y', strtotime($mdata->created_at)) }}</p>
								<p class="mb5"><strong>{{ __('Payment Method') }}</strong>: {{ $mdata->method_name }}</p>
								<p class="mb5"><strong>{{ __('Payment Status') }}</strong>: <span class="status_btn pstatus_{{ $mdata->payment_status_id }}">{{ $mdata->pstatus_name }}</span></p>
								<p class="mb5"><strong>{{ __('Order Status') }}</strong>: <span class="status_btn ostatus_{{ $mdata->order_status_id }}">{{ $mdata->ostatus_name }}</span></p>
							</div>
						</div>
						@endforeach
						<div class="row mt15">
							<div class="col-lg-12">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>{{ __('Image') }}</th>
												<th>{{ __('Product') }}</th>
												<th>{{ __('Variation') }}</th>
												<th class="text-center">{{ __('Price') }}</th>
												<th class="text-center">{{ __('Quantity') }}</th>
												<th class="text-center">{{ __('Total') }}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($datalist as $row)
											@php
												if($gtext['currency_position'] == 'left'){
													$price = $gtext['currency_icon'].NumberFormat($row->price);
													$total_price = $gtext['currency_icon'].NumberFormat($row->total_price);
												}else{
													$price = NumberFormat($row->price).$gtext['currency_icon'];
													$total_price = NumberFormat($row->total_price).$gtext['currency_icon'];
												}

												if($row->variation_size == '0'){
													$size = '';
												}else{
													$size = $row->quantity.' '.$row->variation_size;
												}
											@endphp
											<tr>
												<td class="pro-image-w">
													<div class="pro-image">
														<a href="{{ route('frontend.product', [$row->id, str_slug($row->title)]) }}">
															<img src="{{ asset('public/media/'.$row->f_thumbnail) }}" alt="{{ $row->title }}" />
														</a>
													</div>
												</td>
												<td class="pro-name-w">
													<span class="pro-name"><a href="{{ route('frontend.product', [$row->id, str_slug($row->title)]) }}">{{ $row->title }}</a></span>
												</td>
												<td class="text-left">@php echo $size; @endphp</td>
												<td class="text-center">{{ $price }}</td>
												<td class="text-center">{{ $row->quantity }}</td>
												<td class="text-center">{{ $total_price }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-4 mt10"></div>
							<div class="col-lg-3"></div>
							<div class="col-lg-5 mt10">
								<div class="carttotals-card">
									<div class="carttotals-body">
										<table class="table">
											<tbody>
											@foreach($masterData as $mdata)
												@php	
													$total_amount_shipping_fee = $mdata->total_amount+$mdata->shipping_fee+$mdata->tax;

													if($gtext['currency_position'] == 'left'){
														$shipping_fee = $gtext['currency_icon'].NumberFormat($mdata->shipping_fee);
														$tax = $gtext['currency_icon'].NumberFormat($mdata->tax);
														$subtotal = $gtext['currency_icon'].NumberFormat($mdata->total_amount);
														$total_amount = $gtext['currency_icon'].NumberFormat($total_amount_shipping_fee);
														
													}else{
														$shipping_fee = NumberFormat($mdata->shipping_fee).$gtext['currency_icon'];
														$tax = NumberFormat($mdata->tax).$gtext['currency_icon'];
														$subtotal = NumberFormat($mdata->total_amount).$gtext['currency_icon'];
														$total_amount = NumberFormat($total_amount_shipping_fee).$gtext['currency_icon'];
													}
												@endphp
												
												<tr><td><span class="title">{{ __('Shipping Fee') }}<br>({{ $mdata->shipping_title }})</span><span class="price">{{ $shipping_fee }}</span></td></tr>
												<tr><td><span class="title">{{ __('Tax') }}</span><span class="price">{{ $tax }}</span></td></tr>
												<tr><td><span class="title">{{ __('Subtotal') }}</span><span class="price">{{ $subtotal }}</span></td></tr>
												<tr><td><span class="total">{{ __('Total') }}</span><span class="total-price">{{ $total_amount }}</span></td></tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@else
				<div class="row">
					<div class="col-lg-12">
						<h5 class="text-center red" style="display: {{$isfind}};">{{ __('No data available') }}</h5>
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
@endpush	