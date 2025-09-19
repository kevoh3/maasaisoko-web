<ul class="tabs-nav">
	<li><a href="{{ route('seller-manage.product-manage', [$datalist['id']]) }}"><i class="fa fa-truck"></i>{{ __('Product') }}</a></li>
	<li><a href="{{ route('seller-manage.price', [$datalist['id']]) }}"><i class="fa fa-money"></i>{{ __('Discount Manage') }}</a></li>
	<li><a href="{{ route('seller-manage.inventory', [$datalist['id']]) }}"><i class="fa fa-balance-scale"></i>{{ __('Inventory') }}</a></li>
	<li><a href="{{ route('seller-manage.product-images', [$datalist['id']]) }}"><i class="fa fa-picture-o"></i>{{ __('Multiple Images') }}</a></li>
	<li class="dnone"><a href="{{ route('seller-manage.variations', [$datalist['id']]) }}"><i class="fa fa-hourglass-end"></i>{{ __('Variations') }}</a></li>
	<li><a href="{{ route('seller-manage.related-products', [$datalist['id']]) }}"><i class="fa fa-compass"></i>{{ __('Related Products') }}</a></li>
	<li><a href="{{ route('seller-manage.product-seo', [$datalist['id']]) }}"><i class="fa fa-rocket"></i>{{ __('SEO') }}</a></li>
</ul>
