@extends('layouts.frontend')

@section('title', $seller_data->shop_name)
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
							<li class="breadcrumb-item active" aria-current="page">{{ $seller_data->shop_name }}</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6">
					<div class="page-title">
						<h1>{{ $seller_data->shop_name }}</h1>
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
				<div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2 col-xxl-8 offset-xxl-2">
					<div class="stores-card">
						<div class="store-logo">
							@if($seller_data->photo == '')
							<span class="text">{{ sub_str($seller_data->shop_name, 0,1) }}</span>
							@else
							<img src="{{ asset('public/media/'.$seller_data->photo) }}" alt="{{ $seller_data->shop_name }}"/>
							@endif
							
						</div>
						<div class="desc">
							<h3 class="store-name">{{ $seller_data->shop_name }}</h3>
							<h6 class="since">{{ __('Since') }} {{ date('Y', strtotime($seller_data->created_at)) }}</h6>
							<div class="rating-wrap">
								<div class="stars-outer">
									<div class="stars-inner" style="width:{{ $SellerReview['ReviewPercentage'] }}%;"></div>
								</div>
								<span class="rating-count">({{ $SellerReview['TotalReview'] }})</span>
							</div>
							<ul class="info">
								<li><i class="bi bi-telephone"></i>{{ $seller_data->phone }}</li>
								<li><i class="bi bi-envelope"></i>{{ $seller_data->email }}</li>
								<li><i class="bi bi-geo-alt"></i>{{ $seller_data->address }}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			@if($seller_variation == 'left_sidebar')
			<div class="row">
				<div class="col-lg-3">
					@include('frontend.partials.sidebar')
				</div>
				<div class="col-lg-9">
			@elseif($seller_variation == 'right_sidebar')
			<div class="row">
				<div class="col-lg-9">
			@endif
			
				<div class="filter-card">
					<div class="row">
						<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
							<div class="filter_select">
								<select name="num" id="num" class="form-select form-select-sm">
									<option value="">{{ __('Showing') }}</option>
									@if(($seller_variation == 'left_sidebar') || ($seller_variation == 'right_sidebar'))
									<option value="9">9</option>
									<option value="15">15</option>
									<option value="24">24</option>
									@else
									<option value="12">12</option>
									<option value="20">20</option>
									<option value="28">28</option>
									@endif
								</select>
							</div>
						</div>
						<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
							<div class="sort_by_select">
								<select name="sortby" id="sortby" class="form-select form-select-sm">
									<option value="default_sorting" selected="">{{ __('Default') }}</option>
									<option value="date_asc">Oldest</option>
									<option value="date_desc">Newest</option>
									<option value="name_asc">Name: A-Z</option>
									<option value="name_desc">Name : Z-A</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				
				<div id="tp_datalist">
					@include('frontend.partials.stores-grid')
				</div>
				
			@if($seller_variation == 'left_sidebar')
				</div>
			</div>
			@elseif($seller_variation == 'right_sidebar')
				</div>
				<div class="col-lg-3">
					@include('frontend.partials.sidebar')
				</div>
			</div>
			@endif
		</div>
	</section>
	<!-- /Inner Section/ -->
</main>

@endsection

@push('scripts')
<script type="text/javascript">
var seller_id = "{{ isset($params) ? $params['seller_id'] : 0 }}";
</script>
<script src="{{asset('public/frontend/pages/stores.js')}}"></script>
@endpush	