
	<header class="header">
		<!--Top Header-->
		<div class="top-header">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						@if($gtext['is_publish'] == 1)
						<ul class="top-list-1">
							@if($gtext['address'] != '')
							<li><i class="bi bi-geo-alt"></i>{{ $gtext['address'] }}</li>
							@endif
							@if($gtext['phone'] != '')
							<li><i class="bi bi-telephone"></i>{{ $gtext['phone'] }}</li>
							@endif
						</ul>
						@endif
					</div>
					<div class="col-lg-6">
						<ul class="top-list">
							<li><a href="{{ route('frontend.order-tracking') }}"><i class="bi bi-geo"></i>{{ __('Order Tracking') }}</a></li>
							@auth
							<li>
								<div class="btn-group language-menu">
									<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										{{ Auth::user()->name }}
									</a>
									<ul class="dropdown-menu dropdown-menu-end">
										<li><a class="dropdown-item" href="{{ route('frontend.my-dashboard') }}">{{ __('My Dashboard') }}</a></li>
										<li><a class="dropdown-item" href="{{ route('logout') }}"
										onclick="event.preventDefault();
										document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
										<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
											@csrf
										</form>
										</li>
									</ul>
								</div>
							</li>
							@else
							@if (Route::has('frontend.register'))
							<li><a href="{{ route('frontend.register') }}"><i class="bi bi-person-plus"></i>{{ __('Register') }}</a></li>
							@endif
							@if (Route::has('login'))
							<li><a href="{{ route('frontend.login') }}"><i class="bi bi-person"></i>{{ __('Sign in') }}</a></li>
							@endif
							@endauth
							
							@if($gtext['is_language_switcher'] == 1)
							<li>
								@php echo language(); @endphp
							</li>
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div><!--/Top Header/-->
		
		<!--Desktop Header-->
		<div class="header-desktop">
			<div class="container">
				<div class="row">
					<div class="col-lg-3">
						<div class="logo">
							<a href="{{ url('/') }}">
								<img src="{{ $gtext['front_logo'] ? asset('public/media/'.$gtext['front_logo']) : asset('public/frontend/images/logo.png') }}" alt="logo">
							</a>
						</div>
					</div>
					<div class="col-lg-5">
						<form method="GET" action="{{ route('frontend.search') }}">
							<div class="search-card">
								<div class="search-box">
									<input name="search" type="text" class="form-control" placeholder="{{ __('Search for Products') }}..." required />
								</div>
								<div class="cat-select">
									<select class="form-select" name="category">
										<option value="">{{ __('All Categories') }}</option>
										@php echo CategoryListOption(); @endphp
									</select>
								</div>
								<div class="search-btn">
									<button type="submit" class="btn btn-search"><i class="bi bi-search"></i></button>
								</div>
							</div>
						</form>
					</div>
					<div class="col-lg-4">
						<ul class="head-round-icon">
							<li>
								<a href="{{ route('frontend.wishlist') }}">
									<i class="bi bi-heart"></i>{{ __('Wishlist') }}
									<span class="cart_count count_wishlist">0</span>
								</a>
							</li>
							<li class="shopingCart">
								<a href="javascript:void(0);" class="CartShowHide">
									<i class="bi bi-cart"></i>{{ __('Cart') }}
									<span class="cart_count total_qty">0</span>
								</a>
								<div class="shoping-cart-card headerShopingCart">
									<div class="empty_card has_item_empty">
										<div class="empty_img">
											<img src="{{ asset('public/frontend/images/empty.png') }}" />
										</div>
										<h3>{{ __('Your cart is empty!') }}</h3>
									</div>
									
									<div class="shoping-cart-body has_cart_item">
										<ul class="cart_list" id="tp_cart_data"></ul>
									</div>
									
									<div class="shoping-cart-footer has_cart_item">
										<p>{{ __('Subtotal') }}<span class="sub_total">0</span></p>
										<p>{{ __('Tax') }}<span class="tax">0</span></p>
										<h6>{{ __('Total') }}<span class="tp_total">0</span></h6>
										<a href="{{ route('frontend.cart') }}" class="btn view-cart-btn">{{ __('View Cart') }}</a>
										<a href="{{ route('frontend.checkout') }}" class="btn checkout-btn">{{ __('Checkout') }}</a>
									</div>
									
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div><!--/Desktop Header/-->
		
		<!--Mobile Header-->
		<div class="header-mobile" id="sticky-header">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="header-mobile-card">
							<div class="bars-search-card">
								<ul class="head-round-icon">
									<li class="off-canvas-btn">
										<a href="javascript:void(0);"><i class="bi bi-list"></i></a>
									</li>
									<li class="off-canvas-btn">
										<a href="javascript:void(0);"><i class="bi bi-search"></i></a>
									</li>
								</ul>
							</div>
							<div class="logo-card">
								<div class="logo">
									<a href="{{ url('/') }}">
										<img src="{{ $gtext['front_logo'] ? asset('public/media/'.$gtext['front_logo']) : asset('public/frontend/images/logo.png') }}" alt="logo">
									</a>
								</div>
							</div>
							<div class="head-round-card">
								<ul class="head-round-icon">
									<li>
										<a href="{{ route('frontend.wishlist') }}">
											<i class="bi bi-heart"></i>
											<span class="cart_count count_wishlist">0</span>
										</a>
									</li>
									<li class="shopingCart">
										<a href="javascript:void(0);" class="CartShowHide">
											<i class="bi bi-cart"></i>
											<span class="cart_count total_qty">0</span>
										</a>
										<div class="shoping-cart-card headerShopingCart">
											
											<div class="empty_card has_item_empty">
												<div class="empty_img">
													<img src="{{ asset('public/frontend/images/empty.png') }}" />
												</div>
												<h3>{{ __('Your cart is empty!') }}</h3>
											</div>
											
											<div class="shoping-cart-body has_cart_item">
												<ul class="cart_list" id="tp_cart_data_for_mobile"></ul>
											</div>
											
											<div class="shoping-cart-footer has_cart_item">
												<p>{{ __('Subtotal') }}<span class="sub_total">0</span></p>
												<p>{{ __('Tax') }}<span class="tax">0</span></p>
												<h6>{{ __('Total') }}<span class="tp_total">0</span></h6>
												<a href="{{ route('frontend.cart') }}" class="btn view-cart-btn">{{ __('View Cart') }}</a>
												<a href="{{ route('frontend.checkout') }}" class="btn checkout-btn">{{ __('Checkout') }}</a>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!--/Mobile Header/-->
		
		<!--Menu-->
		<div class="header-menu" id="sticky-menu">
			<div class="container">
				<div class="row">
					<div class="col-lg-3">
						<ul class="categories-wrap">
							<li>
								<a class="navCategoryListActive" href="javascript:void(0);">{{ __('Browse Categories') }}</a>
								<ul class="nav-category-list">
									@php echo CategoryMenuList(); @endphp
									<li><a href="javascript:void(0);" class="btn cat-more-btn catMoreBtnActive"><span class="onCatMoreBtn">{{ __('Show More') }}</span></a></li>
								</ul>
							</li>
						</ul>
					</div>
					<div class="col-lg-9">
						<div class="tp-mega-full">
							<div class="tp-menu align-self-center">
								<nav>
									<ul class="main-menu">
										@php echo HeaderMenuList('HeaderMenuListForDesktop'); @endphp
									</ul>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!--/Menu/-->
	</header>

	<!-- off-canvas menu start -->
	<aside class="mobile-menu-wrapper">
		<div class="off-canvas-overlay"></div>
		<div class="offcanvas-body">
			<div class="offcanvas-top">
				<div class="offcanvas-btn-close">
					<i class="bi bi-x-lg"></i>
				</div>
			</div>
			<div class="search-for-mobile">
				<form method="GET" action="{{ route('frontend.search') }}">
					<input name="search" type="text" class="form-control" placeholder="{{ __('Search for Products') }}..." required />
					<button type="submit" class="btn theme-btn"><i class="bi bi-search"></i>{{ __('Search') }}</button>
				</form>	
			</div>
			<div class="mobile-navigation">
				<nav>
					<ul class="mobile-menu">
						<li class="has-children-menu"><a href="#">{{ __('Browse Categories') }}</a>
							<ul class="dropdown">
								@php echo CategoryListForMobile(); @endphp
							</ul>
						</li>
						@php echo HeaderMenuList('HeaderMenuListForMobile'); @endphp
					</ul>
				</nav>
			</div>
		</div>
	</aside>
	<!-- /off-canvas menu start -->