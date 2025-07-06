<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class, 'index']);

//Home
Route::get('/', [App\Http\Controllers\Frontend\HomeFrontendController::class, 'homePageLoad'])->name('frontend.home');

Route::get('/search', [App\Http\Controllers\Frontend\SearchController::class, 'getSearchData'])->name('frontend.search');

Route::get('/contact/{id}/{title}', [App\Http\Controllers\Frontend\ContactController::class, 'getContactData'])->name('frontend.contact');
Route::post('/frontend/sentMessage', [App\Http\Controllers\Frontend\ContactController::class, 'sentMessage'])->name('frontend.sentMessage');

Route::get('/page/{id}/{title}', [App\Http\Controllers\Frontend\PageController::class, 'getPage'])->name('frontend.page');

//Product category
Route::get('/product-category/{id}/{title}', [App\Http\Controllers\Frontend\ProductCategoryController::class, 'getProductCategoryPage'])->name('frontend.product-category');
Route::get('/frontend/getProductCategoryGrid', [App\Http\Controllers\Frontend\ProductCategoryController::class, 'getProductCategoryGrid'])->name('frontend.getProductCategoryGrid');

//Blog
Route::get('/blog', [App\Http\Controllers\Frontend\BlogController::class, 'getBlogPage'])->name('frontend.blog');
Route::get('/blog-category/{id}/{title}', [App\Http\Controllers\Frontend\BlogController::class, 'getBlogCategoryPage'])->name('frontend.blog-category');
Route::get('/article/{id}/{title}', [App\Http\Controllers\Frontend\BlogController::class, 'getArticlePage'])->name('frontend.article');

//Brand
Route::get('/brand/{id}/{title}', [App\Http\Controllers\Frontend\BrandController::class, 'getBrandPage'])->name('frontend.brand');
Route::get('/frontend/getBrandGrid', [App\Http\Controllers\Frontend\BrandController::class, 'getBrandGrid'])->name('frontend.getBrandGrid');

//Product
Route::get('/product/{id}/{title}', [App\Http\Controllers\Frontend\ProductController::class, 'getProductPage'])->name('frontend.product');
Route::get('/frontend/getProductReviewsGrid', [App\Http\Controllers\Frontend\ProductController::class, 'getProductReviewsGrid'])->name('frontend.getProductReviewsGrid');

//Add to cart
Route::get('/frontend/add_to_cart/{id}/{qty}', [App\Http\Controllers\Frontend\CartController::class, 'AddToCart'])->name('frontend.add_to_cart');
Route::get('/frontend/view_cart', [App\Http\Controllers\Frontend\CartController::class, 'ViewCart'])->name('frontend.view_cart');
Route::get('/frontend/remove_to_cart/{rowid}', [App\Http\Controllers\Frontend\CartController::class, 'RemoveToCart'])->name('frontend.remove_to_cart');
Route::get('/cart', [App\Http\Controllers\Frontend\CartController::class, 'getCart'])->name('frontend.cart');
Route::get('/frontend/viewcart_data', [App\Http\Controllers\Frontend\CartController::class, 'getViewCartData'])->name('frontend.getViewCartData');

//Wishlist
Route::get('/frontend/add_to_wishlist/{id}', [App\Http\Controllers\Frontend\CartController::class, 'addToWishlist'])->name('frontend.add_to_wishlist');
Route::get('/wishlist', [App\Http\Controllers\Frontend\CartController::class, 'getWishlist'])->name('frontend.wishlist');
Route::get('/frontend/remove_to_wishlist/{rowid}', [App\Http\Controllers\Frontend\CartController::class, 'RemoveToWishlist'])->name('frontend.remove_to_wishlist');
Route::get('/frontend/count_wishlist', [App\Http\Controllers\Frontend\CartController::class, 'countWishlist'])->name('frontend.countWishlist');

//Customer Authentication
Route::get('/user/login', [App\Http\Controllers\Backend\CustomerAuthController::class, 'LoadLogin'])->name('frontend.login');
Route::post('/user/customer-login', [App\Http\Controllers\Backend\CustomerAuthController::class, 'CustomerLogin'])->name('frontend.customer-login');
Route::get('/user/register', [App\Http\Controllers\Backend\CustomerAuthController::class, 'LoadRegister'])->name('frontend.register');
Route::post('/user/customer-register', [App\Http\Controllers\Backend\CustomerAuthController::class, 'CustomerRegister'])->name('frontend.customer-register');
Route::get('/user/reset', [App\Http\Controllers\Backend\CustomerAuthController::class, 'LoadReset'])->name('frontend.reset');
Route::post('/user/resetPassword', [App\Http\Controllers\Backend\CustomerAuthController::class, 'resetPassword'])->name('frontend.resetPassword');
Route::post('/user/resetPasswordUpdate', [App\Http\Controllers\Backend\CustomerAuthController::class, 'resetPasswordUpdate'])->name('frontend.resetPasswordUpdate');

//Seller Authentication
Route::get('/seller/register', [App\Http\Controllers\Backend\SellerController::class, 'LoadSellerRegister'])->name('frontend.seller-register');
Route::post('/seller/seller-register', [App\Http\Controllers\Backend\SellerController::class, 'SellerRegister'])->name('frontend.sellerRegister');
Route::post('/frontend/hasShopSlug', [App\Http\Controllers\Backend\SellerController::class, 'hasShopSlug'])->name('frontend.hasShopSlug');

//My Dashboard
Route::get('/user/my-dashboard', [App\Http\Controllers\Frontend\MyDashboardController::class, 'LoadMyDashboard'])->name('frontend.my-dashboard')->middleware('auth');
Route::get('/user/my-orders', [App\Http\Controllers\Frontend\MyDashboardController::class, 'LoadMyOrders'])->name('frontend.my-orders')->middleware('auth');
Route::get('/user/my-profile', [App\Http\Controllers\Frontend\MyDashboardController::class, 'LoadMyProfile'])->name('frontend.my-profile')->middleware('auth');
Route::post('/user/UpdateProfile', [App\Http\Controllers\Frontend\MyDashboardController::class, 'UpdateProfile'])->name('frontend.UpdateProfile')->middleware('auth');
Route::get('/user/change-password', [App\Http\Controllers\Frontend\MyDashboardController::class, 'LoadChangePassword'])->name('frontend.change-password')->middleware('auth');
Route::post('/user/ChangePassword', [App\Http\Controllers\Frontend\MyDashboardController::class, 'ChangePassword'])->name('frontend.ChangePassword')->middleware('auth');
Route::get('/order-details/{id}/{order_no}', [App\Http\Controllers\Frontend\MyDashboardController::class, 'MyOrderDetails'])->name('frontend.order-details');

//Checkout
Route::get('/checkout', [App\Http\Controllers\Frontend\CheckoutFrontController::class, 'LoadCheckout'])->name('frontend.checkout');
Route::post('/frontend/make_order', [App\Http\Controllers\Frontend\CheckoutFrontController::class, 'LoadMakeOrder'])->name('frontend.make_order');
Route::get('/thank', [App\Http\Controllers\Frontend\CheckoutFrontController::class, 'LoadThank'])->name('frontend.thank');

//PayPal
Route::get('/PayPalpayment-cancel', [App\Http\Controllers\Frontend\CheckoutFrontController::class, 'PayPalPaymentCancel'])->name('cancel.PayPalPayment');
Route::get('/PayPalpayment-success', [App\Http\Controllers\Frontend\CheckoutFrontController::class, 'PayPalPaymentSuccess'])->name('success.PayPalPayment');

//Order Tracking
Route::get('/order-tracking', [App\Http\Controllers\Frontend\OrderTrackingController::class, 'getOrderTracking'])->name('frontend.order-tracking');

//Order Invoice
Route::get('/order-invoice/{id}/{order_no}', [App\Http\Controllers\Frontend\OrderInvoiceController::class, 'getOrderInvoice'])->name('frontend.order-invoice');

//Reviews
Route::post('/frontend/saveReviews', [App\Http\Controllers\Frontend\ReviewsController::class, 'saveReviews'])->name('frontend.saveReviews');

//Subscribe
Route::post('/frontend/saveSubscriber', [App\Http\Controllers\Backend\NewslettersController::class, 'saveSubscriberData'])->name('frontend.saveSubscriber');
Route::post('/frontend/subscribePopupOff', [App\Http\Controllers\Backend\NewslettersController::class, 'subscribePopupOff'])->name('frontend.subscribePopupOff');

//Stores
Route::get('/stores/{id}/{title}', [App\Http\Controllers\Frontend\StoresController::class, 'getStoresData'])->name('frontend.stores');
Route::get('/frontend/getStoresGrid', [App\Http\Controllers\Frontend\StoresController::class, 'getStoresGrid'])->name('frontend.getStoresGrid');

Route::prefix('backend')->group(function(){

	//Not Found Page
	Route::get('/notfound', [App\Http\Controllers\HomeController::class, 'notFoundPage'])->name('backend.notfound')->middleware('auth');

	//Dashboard
	Route::get('/dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'getDashboardData'])->name('backend.dashboard')->middleware(['auth','is_admin']);

	//Orders
	Route::get('/orders', [App\Http\Controllers\Backend\OrdersController::class, 'getOrdersPageLoad'])->name('backend.orders')->middleware(['auth','is_admin']);
	Route::get('/getOrdersTableData', [App\Http\Controllers\Backend\OrdersController::class, 'getOrdersTableData'])->name('backend.getOrdersTableData')->middleware(['auth','is_admin']);
	Route::post('/bulkActionOrders', [App\Http\Controllers\Backend\OrdersController::class, 'bulkActionOrders'])->name('backend.bulkActionOrders')->middleware(['auth','is_admin']);
	Route::get('/order/{id}', [App\Http\Controllers\Backend\OrdersController::class, 'getOrderData'])->name('backend.order')->middleware(['auth','is_admin']);
	Route::post('/updateOrderStatus', [App\Http\Controllers\Backend\OrdersController::class, 'updateOrderStatus'])->name('backend.updateOrderStatus')->middleware(['auth','is_admin']);
	Route::get('/getPaymentOrderStatusData', [App\Http\Controllers\Backend\OrdersController::class, 'getPaymentOrderStatusData'])->name('backend.getPaymentOrderStatusData')->middleware(['auth','is_admin']);
	Route::post('/deleteOrder', [App\Http\Controllers\Backend\OrdersController::class, 'deleteOrder'])->name('backend.deleteOrder')->middleware(['auth','is_admin']);
	
	//Transactions
	Route::get('/transactions', [App\Http\Controllers\Backend\TransactionController::class, 'getTransactionsPageLoad'])->name('backend.transactions')->middleware(['auth','is_admin']);
	Route::get('/getTransactionsTableData', [App\Http\Controllers\Backend\TransactionController::class, 'getTransactionsTableData'])->name('backend.getTransactionsTableData')->middleware(['auth','is_admin']);

	//Newsletters
	Route::get('/subscribe-settings', [App\Http\Controllers\Backend\NewslettersController::class, 'getSubscribeSettings'])->name('backend.subscribe-settings')->middleware(['auth','is_admin']);
	Route::post('/SubscribePopupUpdate', [App\Http\Controllers\Backend\NewslettersController::class, 'SubscribePopupUpdate'])->name('backend.SubscribePopupUpdate')->middleware(['auth','is_admin']);
	Route::get('/mailchimp-settings', [App\Http\Controllers\Backend\NewslettersController::class, 'getMailChimpSettings'])->name('backend.mailchimp-settings')->middleware(['auth','is_admin']);
	Route::post('/MailChimpSettingsUpdate', [App\Http\Controllers\Backend\NewslettersController::class, 'MailChimpSettingsUpdate'])->name('backend.MailChimpSettingsUpdate')->middleware(['auth','is_admin']);
	Route::get('/subscribers', [App\Http\Controllers\Backend\NewslettersController::class, 'getSubscribers'])->name('backend.subscribers')->middleware(['auth','is_admin']);
	Route::get('/getSubscriberTableData', [App\Http\Controllers\Backend\NewslettersController::class, 'getSubscriberTableData'])->name('backend.getSubscriberTableData')->middleware(['auth','is_admin']);
	Route::post('/saveSubscriberData', [App\Http\Controllers\Backend\NewslettersController::class, 'saveSubscriberData'])->name('backend.saveSubscriberData')->middleware(['auth','is_admin']);
	Route::post('/getSubscriberById', [App\Http\Controllers\Backend\NewslettersController::class, 'getSubscriberById'])->name('backend.getSubscriberById')->middleware(['auth','is_admin']);
	Route::post('/deleteSubscriber', [App\Http\Controllers\Backend\NewslettersController::class, 'deleteSubscriber'])->name('backend.deleteSubscriber')->middleware(['auth','is_admin']);
		
	//languages Page
	Route::get('/languages', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguagePageLoad'])->name('backend.languages')->middleware(['auth','is_admin']);
	Route::get('/getLanguagesTableData', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguagesTableData'])->name('backend.getLanguagesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveLanguagesData', [App\Http\Controllers\Backend\LanguagesController::class, 'saveLanguagesData'])->name('backend.saveLanguagesData')->middleware(['auth','is_admin']);
	Route::post('/getLanguageById', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguageById'])->name('backend.getLanguageById')->middleware(['auth','is_admin']);
	Route::post('/deleteLanguage', [App\Http\Controllers\Backend\LanguagesController::class, 'deleteLanguage'])->name('backend.deleteLanguage')->middleware(['auth','is_admin']);
	
	//Language Keywords Page
	Route::get('/language-keywords', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguageKeywordsPageLoad'])->name('backend.language-keywords')->middleware(['auth','is_admin']);
	Route::get('/getLanguageKeywordsTableData', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguageKeywordsTableData'])->name('backend.getLanguageKeywordsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveLanguageKeywordsData', [App\Http\Controllers\Backend\LanguagesController::class, 'saveLanguageKeywordsData'])->name('backend.saveLanguageKeywordsData')->middleware(['auth','is_admin']);
	Route::post('/getLanguageKeywordsById', [App\Http\Controllers\Backend\LanguagesController::class, 'getLanguageKeywordsById'])->name('backend.getLanguageKeywordsById')->middleware(['auth','is_admin']);
	Route::post('/deleteLanguageKeywords', [App\Http\Controllers\Backend\LanguagesController::class, 'deleteLanguageKeywords'])->name('backend.deleteLanguageKeywords')->middleware(['auth','is_admin']);
	
	//Customers Page
	Route::get('/customers', [App\Http\Controllers\Backend\CustomerController::class, 'getCustomersPageLoad'])->name('backend.customers')->middleware(['auth','is_admin']);
	Route::get('/getCustomersTableData', [App\Http\Controllers\Backend\CustomerController::class, 'getCustomersTableData'])->name('backend.getCustomersTableData')->middleware(['auth','is_admin']);
	Route::post('/saveCustomersData', [App\Http\Controllers\Backend\CustomerController::class, 'saveCustomersData'])->name('backend.saveCustomersData')->middleware(['auth','is_admin']);
	Route::post('/getCustomerById', [App\Http\Controllers\Backend\CustomerController::class, 'getCustomerById'])->name('backend.getCustomerById')->middleware(['auth','is_admin']);
	Route::post('/deleteCustomer', [App\Http\Controllers\Backend\CustomerController::class, 'deleteCustomer'])->name('backend.deleteCustomer')->middleware(['auth','is_admin']);
	Route::post('/bulkActionCustomers', [App\Http\Controllers\Backend\CustomerController::class, 'bulkActionCustomers'])->name('backend.bulkActionCustomers')->middleware(['auth','is_admin']);

	//Sellers Page
	Route::get('/sellers', [App\Http\Controllers\Backend\SellerController::class, 'getSellersPageLoad'])->name('backend.sellers')->middleware(['auth','is_admin']);
	Route::get('/getSellersTableData', [App\Http\Controllers\Backend\SellerController::class, 'getSellersTableData'])->name('backend.getSellersTableData')->middleware(['auth','is_admin']);
	Route::post('/saveSellersData', [App\Http\Controllers\Backend\SellerController::class, 'saveSellersData'])->name('backend.saveSellersData')->middleware(['auth','is_admin']);
	Route::post('/getSellerById', [App\Http\Controllers\Backend\SellerController::class, 'getSellerById'])->name('backend.getSellerById')->middleware(['auth','is_admin']);
	Route::post('/deleteSeller', [App\Http\Controllers\Backend\SellerController::class, 'deleteSeller'])->name('backend.deleteSeller')->middleware(['auth','is_admin']);
	Route::post('/bulkActionSellers', [App\Http\Controllers\Backend\SellerController::class, 'bulkActionSellers'])->name('backend.bulkActionSellers')->middleware(['auth','is_admin']);
	Route::post('/saveBankInformationData', [App\Http\Controllers\Backend\SellerController::class, 'saveBankInformationData'])->name('backend.saveBankInformationData')->middleware(['auth','is_admin']);
	
	//Users Page
	Route::get('/users', [App\Http\Controllers\Backend\UsersController::class, 'getUsersPageLoad'])->name('backend.users')->middleware(['auth','is_admin']);
	Route::get('/getUsersTableData', [App\Http\Controllers\Backend\UsersController::class, 'getUsersTableData'])->name('backend.getUsersTableData')->middleware(['auth','is_admin']);
	Route::post('/saveUsersData', [App\Http\Controllers\Backend\UsersController::class, 'saveUsersData'])->name('backend.saveUsersData')->middleware(['auth','is_admin']);
	Route::post('/getUserById', [App\Http\Controllers\Backend\UsersController::class, 'getUserById'])->name('backend.getUserById')->middleware(['auth','is_admin']);
	Route::post('/deleteUser', [App\Http\Controllers\Backend\UsersController::class, 'deleteUser'])->name('backend.deleteUser')->middleware(['auth','is_admin']);
	Route::post('/bulkActionUsers', [App\Http\Controllers\Backend\UsersController::class, 'bulkActionUsers'])->name('backend.bulkActionUsers')->middleware(['auth','is_admin']);

	//Profile Page
	Route::get('/profile', [App\Http\Controllers\Backend\UsersController::class, 'getProfilePageLoad'])->name('backend.profile')->middleware(['auth','is_admin']);
	Route::post('/profileUpdate', [App\Http\Controllers\Backend\UsersController::class, 'profileUpdate'])->name('backend.profileUpdate')->middleware(['auth','is_admin']);

	//Media Page
	Route::get('/media', [App\Http\Controllers\Backend\MediaController::class, 'getMediaPageLoad'])->name('backend.media')->middleware(['auth','is_admin']);
	Route::post('/getMediaById', [App\Http\Controllers\Backend\MediaController::class, 'getMediaById'])->name('backend.getMediaById')->middleware(['auth','is_admin']);
	Route::post('/mediaUpdate', [App\Http\Controllers\Backend\MediaController::class, 'mediaUpdate'])->name('backend.mediaUpdate')->middleware(['auth','is_admin']);
	Route::post('/onMediaDelete', [App\Http\Controllers\Backend\MediaController::class, 'onMediaDelete'])->name('backend.onMediaDelete')->middleware(['auth','is_admin']);
	Route::get('/getGlobalMediaData', [App\Http\Controllers\Backend\MediaController::class, 'getGlobalMediaData'])->name('backend.getGlobalMediaData')->middleware(['auth','is_admin']);
	Route::get('/getMediaPaginationData', [App\Http\Controllers\Backend\MediaController::class, 'getMediaPaginationData'])->name('backend.getMediaPaginationData')->middleware(['auth','is_admin']);
	
	//Menu Page
	Route::get('/menu', [App\Http\Controllers\Backend\MenuController::class, 'getMenuPageLoad'])->name('backend.menu')->middleware(['auth','is_admin']);
	Route::get('/getMenuTableData', [App\Http\Controllers\Backend\MenuController::class, 'getMenuTableData'])->name('backend.getMenuTableData')->middleware(['auth','is_admin']);
	Route::post('/saveMenuData', [App\Http\Controllers\Backend\MenuController::class, 'saveMenuData'])->name('backend.saveMenuData')->middleware(['auth','is_admin']);
	Route::post('/getMenuById', [App\Http\Controllers\Backend\MenuController::class, 'getMenuById'])->name('backend.getMenuById')->middleware(['auth','is_admin']);
	Route::post('/deleteMenu', [App\Http\Controllers\Backend\MenuController::class, 'deleteMenu'])->name('backend.deleteMenu')->middleware(['auth','is_admin']);
	Route::post('/bulkActionMenu', [App\Http\Controllers\Backend\MenuController::class, 'bulkActionMenu'])->name('backend.bulkActionMenu')->middleware(['auth','is_admin']);

	//Menu Builder Page
	Route::get('/menu-builder/{lan}/{id}', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getMenuBuilderPageLoad'])->name('backend.menu-builder')->middleware(['auth','is_admin']);
	Route::get('/getPageMenuBuilderData', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getPageMenuBuilderData'])->name('backend.getPageMenuBuilderData')->middleware(['auth','is_admin']);
	Route::get('/getBrandMenuBuilderData', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getBrandMenuBuilderData'])->name('backend.getBrandMenuBuilderData')->middleware(['auth','is_admin']);
	Route::get('/getProductMenuBuilderData', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getProductMenuBuilderData'])->name('backend.getProductMenuBuilderData')->middleware(['auth','is_admin']);
	Route::get('/getProductCategoryMenuBuilderData', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getProductCategoryMenuBuilderData'])->name('backend.getProductCategoryMenuBuilderData')->middleware(['auth','is_admin']);
	Route::get('/getBlogCategoryMenuBuilderData', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getBlogCategoryMenuBuilderData'])->name('backend.getBlogCategoryMenuBuilderData')->middleware(['auth','is_admin']);
	Route::post('/SaveParentMenu', [App\Http\Controllers\Backend\MenuBuilderController::class, 'SaveParentMenu'])->name('backend.SaveParentMenu')->middleware(['auth','is_admin']);
	Route::get('/ajaxMakeMenuList', [App\Http\Controllers\Backend\MenuBuilderController::class, 'ajaxMakeMenuList'])->name('backend.ajaxMakeMenuList')->middleware(['auth','is_admin']);
	Route::post('/UpdateMenuSettings', [App\Http\Controllers\Backend\MenuBuilderController::class, 'UpdateMenuSettings'])->name('backend.UpdateMenuSettings')->middleware(['auth','is_admin']);
	Route::post('/deleteParentChildMenu', [App\Http\Controllers\Backend\MenuBuilderController::class, 'deleteParentChildMenu'])->name('backend.deleteParentChildMenu')->middleware(['auth','is_admin']);
	Route::post('/getMegaMenuTitleById', [App\Http\Controllers\Backend\MenuBuilderController::class, 'getMegaMenuTitleById'])->name('backend.getMegaMenuTitleById')->middleware(['auth','is_admin']);
	Route::post('/UpdateMegaMenuTitle', [App\Http\Controllers\Backend\MenuBuilderController::class, 'UpdateMegaMenuTitle'])->name('backend.UpdateMegaMenuTitle')->middleware(['auth','is_admin']);
	Route::post('/UpdateSortableMenuList', [App\Http\Controllers\Backend\MenuBuilderController::class, 'UpdateSortableMenuList'])->name('backend.UpdateSortableMenuList')->middleware(['auth','is_admin']);

	//Page
	Route::get('/page', [App\Http\Controllers\Backend\PageController::class, 'getAllPageData'])->name('backend.page')->middleware(['auth','is_admin']);
	Route::get('/getPagePaginationData', [App\Http\Controllers\Backend\PageController::class, 'getPagePaginationData'])->name('backend.getPagePaginationData')->middleware(['auth','is_admin']);
	Route::post('/getPageById', [App\Http\Controllers\Backend\PageController::class, 'getPageById'])->name('backend.getPageById')->middleware(['auth','is_admin']);
	Route::post('/deletePage', [App\Http\Controllers\Backend\PageController::class, 'deletePage'])->name('backend.deletePage')->middleware(['auth','is_admin']);
	Route::post('/bulkActionPage', [App\Http\Controllers\Backend\PageController::class, 'bulkActionPage'])->name('backend.bulkActionPage')->middleware(['auth','is_admin']);
	Route::post('/hasPageTitleSlug', [App\Http\Controllers\Backend\PageController::class, 'hasPageTitleSlug'])->name('backend.hasPageTitleSlug')->middleware(['auth','is_admin']);
	Route::post('/savePageData', [App\Http\Controllers\Backend\PageController::class, 'savePageData'])->name('backend.savePageData')->middleware(['auth','is_admin']);
	
	//Contact
	Route::get('/contact', [App\Http\Controllers\Backend\ContactController::class, 'getContactData'])->name('backend.contact')->middleware(['auth','is_admin']);
	Route::get('/getContactPaginationData', [App\Http\Controllers\Backend\ContactController::class, 'getContactPaginationData'])->name('backend.getContactPaginationData')->middleware(['auth','is_admin']);
	Route::post('/getContactById', [App\Http\Controllers\Backend\ContactController::class, 'getContactById'])->name('backend.getContactById')->middleware(['auth','is_admin']);
	Route::post('/deleteContact', [App\Http\Controllers\Backend\ContactController::class, 'deleteContact'])->name('backend.deleteContact')->middleware(['auth','is_admin']);
	Route::post('/bulkActionContact', [App\Http\Controllers\Backend\ContactController::class, 'bulkActionContact'])->name('backend.bulkActionContact')->middleware(['auth','is_admin']);
	Route::post('/saveContactData', [App\Http\Controllers\Backend\ContactController::class, 'saveContactData'])->name('backend.saveContactData')->middleware(['auth','is_admin']);
	
	//Products
	Route::get('/products', [App\Http\Controllers\Backend\ProductsController::class, 'getProductsPageLoad'])->name('backend.products')->middleware(['auth','is_admin']);
	Route::get('/getProductsTableData', [App\Http\Controllers\Backend\ProductsController::class, 'getProductsTableData'])->name('backend.getProductsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveProductsData', [App\Http\Controllers\Backend\ProductsController::class, 'saveProductsData'])->name('backend.saveProductsData')->middleware(['auth','is_admin']);
	Route::post('/deleteProducts', [App\Http\Controllers\Backend\ProductsController::class, 'deleteProducts'])->name('backend.deleteProducts')->middleware(['auth','is_admin']);
	Route::post('/bulkActionProducts', [App\Http\Controllers\Backend\ProductsController::class, 'bulkActionProducts'])->name('backend.bulkActionProducts')->middleware(['auth','is_admin']);
	Route::post('/hasProductSlug', [App\Http\Controllers\Backend\ProductsController::class, 'hasProductSlug'])->name('backend.hasProductSlug')->middleware(['auth','is_admin']);
	//Update
	Route::get('/product/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getProductPageData'])->name('backend.product')->middleware(['auth','is_admin']);
	Route::post('/updateProductsData', [App\Http\Controllers\Backend\ProductsController::class, 'updateProductsData'])->name('backend.updateProductsData')->middleware(['auth','is_admin']);
	
	//Manage Stock
	Route::get('/manage-stock', [App\Http\Controllers\Backend\InventoryController::class, 'getManageStockPageLoad'])->name('backend.manage-stock')->middleware(['auth','is_admin']);
	Route::get('/getManageStockTableData', [App\Http\Controllers\Backend\InventoryController::class, 'getManageStockTableData'])->name('backend.getManageStockTableData')->middleware(['auth','is_admin']);
	Route::post('/getProductById', [App\Http\Controllers\Backend\InventoryController::class, 'getProductById'])->name('backend.getProductById')->middleware(['auth','is_admin']);
	Route::post('/saveManageStockData', [App\Http\Controllers\Backend\InventoryController::class, 'saveManageStockData'])->name('backend.saveManageStockData')->middleware(['auth','is_admin']);

	//Price
	Route::get('/price/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getPricePageData'])->name('backend.price')->middleware(['auth','is_admin']);
	Route::post('/savePriceData', [App\Http\Controllers\Backend\ProductsController::class, 'savePriceData'])->name('backend.savePriceData')->middleware(['auth','is_admin']);
	
	//Inventory
	Route::get('/inventory/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getInventoryPageData'])->name('backend.inventory')->middleware(['auth','is_admin']);
	Route::post('/saveInventoryData', [App\Http\Controllers\Backend\ProductsController::class, 'saveInventoryData'])->name('backend.saveInventoryData')->middleware(['auth','is_admin']);
	
	//Product Images
	Route::get('/product-images/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getProductImagesPageData'])->name('backend.product-images')->middleware(['auth','is_admin']);
	Route::get('/getProductImagesTableData', [App\Http\Controllers\Backend\ProductsController::class, 'getProductImagesTableData'])->name('backend.getProductImagesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveProductImagesData', [App\Http\Controllers\Backend\ProductsController::class, 'saveProductImagesData'])->name('backend.saveProductImagesData')->middleware(['auth','is_admin']);
	Route::post('/deleteProductImages', [App\Http\Controllers\Backend\ProductsController::class, 'deleteProductImages'])->name('backend.deleteProductImages')->middleware(['auth','is_admin']);
	
	//Variations
	Route::get('/variations/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getVariationsPageData'])->name('backend.variations')->middleware(['auth','is_admin']);
	Route::post('/saveVariationsData', [App\Http\Controllers\Backend\ProductsController::class, 'saveVariationsData'])->name('backend.saveVariationsData')->middleware(['auth','is_admin']);
	
	//Product SEO
	Route::get('/product-seo/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getProductSEOPageData'])->name('backend.product-seo')->middleware(['auth','is_admin']);
	Route::post('/saveProductSEOData', [App\Http\Controllers\Backend\ProductsController::class, 'saveProductSEOData'])->name('backend.saveProductSEOData')->middleware(['auth','is_admin']);
		
	//Related Products
	Route::get('/related-products/{id}', [App\Http\Controllers\Backend\ProductsController::class, 'getRelatedProductsPageData'])->name('backend.related-products')->middleware(['auth','is_admin']);
	Route::get('/getProductListForRelatedTableData', [App\Http\Controllers\Backend\ProductsController::class, 'getProductListForRelatedTableData'])->name('backend.getProductListForRelatedTableData')->middleware(['auth','is_admin']);
	Route::get('/getRelatedProductTableData', [App\Http\Controllers\Backend\ProductsController::class, 'getRelatedProductTableData'])->name('backend.getRelatedProductTableData')->middleware(['auth','is_admin']);
	Route::post('/saveRelatedProductsData', [App\Http\Controllers\Backend\ProductsController::class, 'saveRelatedProductsData'])->name('backend.saveRelatedProductsData')->middleware(['auth','is_admin']);
	Route::post('/deleteRelatedProduct', [App\Http\Controllers\Backend\ProductsController::class, 'deleteRelatedProduct'])->name('backend.deleteRelatedProduct')->middleware(['auth','is_admin']);
		
	//Product Categories
	Route::get('/product-categories', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'getProductCategoriesPageLoad'])->name('backend.product-categories')->middleware(['auth','is_admin']);
	Route::get('/getProductCategoriesTableData', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'getProductCategoriesTableData'])->name('backend.getProductCategoriesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveProductCategoriesData', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'saveProductCategoriesData'])->name('backend.saveProductCategoriesData')->middleware(['auth','is_admin']);
	Route::post('/getProductCategoriesById', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'getProductCategoriesById'])->name('backend.getProductCategoriesById')->middleware(['auth','is_admin']);
	Route::post('/deleteProductCategories', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'deleteProductCategories'])->name('backend.deleteProductCategories')->middleware(['auth','is_admin']);
	Route::post('/bulkActionProductCategories', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'bulkActionProductCategories'])->name('backend.bulkActionProductCategories')->middleware(['auth','is_admin']);
	Route::post('/hasProductCategorySlug', [App\Http\Controllers\Backend\Pro_categoriesController::class, 'hasProductCategorySlug'])->name('backend.hasProductCategorySlug')->middleware(['auth','is_admin']);
			
	//Blog Categories
	Route::get('/blog-categories', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'getBlogCategoriesPageLoad'])->name('backend.blog-categories')->middleware(['auth','is_admin']);
	Route::get('/getBlogCategoriesTableData', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'getBlogCategoriesTableData'])->name('backend.getBlogCategoriesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveBlogCategoriesData', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'saveBlogCategoriesData'])->name('backend.saveBlogCategoriesData')->middleware(['auth','is_admin']);
	Route::post('/getBlogCategoriesById', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'getBlogCategoriesById'])->name('backend.getBlogCategoriesById')->middleware(['auth','is_admin']);
	Route::post('/deleteBlogCategories', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'deleteBlogCategories'])->name('backend.deleteBlogCategories')->middleware(['auth','is_admin']);
	Route::post('/bulkActionBlogCategories', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'bulkActionBlogCategories'])->name('backend.bulkActionBlogCategories')->middleware(['auth','is_admin']);
	Route::post('/hasBlogCategorySlug', [App\Http\Controllers\Backend\Blog_categoriesController::class, 'hasBlogCategorySlug'])->name('backend.hasBlogCategorySlug')->middleware(['auth','is_admin']);
		
	//Blog
	Route::get('/blog', [App\Http\Controllers\Backend\BlogController::class, 'getBlogPageLoad'])->name('backend.blog')->middleware(['auth','is_admin']);
	Route::get('/getBlogTableData', [App\Http\Controllers\Backend\BlogController::class, 'getBlogTableData'])->name('backend.getBlogTableData')->middleware(['auth','is_admin']);
	Route::post('/saveBlogData', [App\Http\Controllers\Backend\BlogController::class, 'saveBlogData'])->name('backend.saveBlogData')->middleware(['auth','is_admin']);
	Route::post('/getBlogById', [App\Http\Controllers\Backend\BlogController::class, 'getBlogById'])->name('backend.getBlogById')->middleware(['auth','is_admin']);
	Route::post('/deleteBlog', [App\Http\Controllers\Backend\BlogController::class, 'deleteBlog'])->name('backend.deleteBlog')->middleware(['auth','is_admin']);
	Route::post('/bulkActionBlog', [App\Http\Controllers\Backend\BlogController::class, 'bulkActionBlog'])->name('backend.bulkActionBlog')->middleware(['auth','is_admin']);
	Route::post('/hasBlogSlug', [App\Http\Controllers\Backend\BlogController::class, 'hasBlogSlug'])->name('backend.hasBlogSlug')->middleware(['auth','is_admin']);
	
	//Brands
	Route::get('/brands', [App\Http\Controllers\Backend\BrandsController::class, 'getBrandsPageLoad'])->name('backend.brands')->middleware(['auth','is_admin']);
	Route::get('/getBrandsTableData', [App\Http\Controllers\Backend\BrandsController::class, 'getBrandsTableData'])->name('backend.getBrandsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveBrandsData', [App\Http\Controllers\Backend\BrandsController::class, 'saveBrandsData'])->name('backend.saveBrandsData')->middleware(['auth','is_admin']);
	Route::post('/getBrandsById', [App\Http\Controllers\Backend\BrandsController::class, 'getBrandsById'])->name('backend.getBrandsById')->middleware(['auth','is_admin']);
	Route::post('/deleteBrands', [App\Http\Controllers\Backend\BrandsController::class, 'deleteBrands'])->name('backend.deleteBrands')->middleware(['auth','is_admin']);
	Route::post('/bulkActionBrands', [App\Http\Controllers\Backend\BrandsController::class, 'bulkActionBrands'])->name('backend.bulkActionBrands')->middleware(['auth','is_admin']);

	//Review & Ratings
	Route::get('/review', [App\Http\Controllers\Backend\ReviewsController::class, 'getReviewRatingsPageLoad'])->name('backend.review')->middleware(['auth','is_admin']);
	Route::get('/getReviewRatingsTableData', [App\Http\Controllers\Backend\ReviewsController::class, 'getReviewRatingsTableData'])->name('backend.getReviewRatingsTableData')->middleware(['auth','is_admin']);
	Route::post('/deleteReviewRatings', [App\Http\Controllers\Backend\ReviewsController::class, 'deleteReviewRatings'])->name('backend.deleteReviewRatings')->middleware(['auth','is_admin']);
	Route::post('/bulkActionReviewRatings', [App\Http\Controllers\Backend\ReviewsController::class, 'bulkActionReviewRatings'])->name('backend.bulkActionReviewRatings')->middleware(['auth','is_admin']);

	//Shipping
	Route::get('/shipping', [App\Http\Controllers\Backend\ShippingController::class, 'getShippingPageLoad'])->name('backend.shipping')->middleware(['auth','is_admin']);
	Route::get('/getShippingTableData', [App\Http\Controllers\Backend\ShippingController::class, 'getShippingTableData'])->name('backend.getShippingTableData')->middleware(['auth','is_admin']);
	Route::post('/saveShippingData', [App\Http\Controllers\Backend\ShippingController::class, 'saveShippingData'])->name('backend.saveShippingData')->middleware(['auth','is_admin']);
	Route::post('/getShippingById', [App\Http\Controllers\Backend\ShippingController::class, 'getShippingById'])->name('backend.getShippingById')->middleware(['auth','is_admin']);
	Route::post('/deleteShipping', [App\Http\Controllers\Backend\ShippingController::class, 'deleteShipping'])->name('backend.deleteShipping')->middleware(['auth','is_admin']);
	Route::post('/bulkActionShipping', [App\Http\Controllers\Backend\ShippingController::class, 'bulkActionShipping'])->name('backend.bulkActionShipping')->middleware(['auth','is_admin']);

	//Collections
	Route::get('/collections', [App\Http\Controllers\Backend\CollectionsController::class, 'getCollectionsPageLoad'])->name('backend.collections')->middleware(['auth','is_admin']);
	Route::get('/getCollectionsTableData', [App\Http\Controllers\Backend\CollectionsController::class, 'getCollectionsTableData'])->name('backend.getCollectionsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveCollectionsData', [App\Http\Controllers\Backend\CollectionsController::class, 'saveCollectionsData'])->name('backend.saveCollectionsData')->middleware(['auth','is_admin']);
	Route::post('/getCollectionsById', [App\Http\Controllers\Backend\CollectionsController::class, 'getCollectionsById'])->name('backend.getCollectionsById')->middleware(['auth','is_admin']);
	Route::post('/deleteCollections', [App\Http\Controllers\Backend\CollectionsController::class, 'deleteCollections'])->name('backend.deleteCollections')->middleware(['auth','is_admin']);
	Route::post('/bulkActionCollections', [App\Http\Controllers\Backend\CollectionsController::class, 'bulkActionCollections'])->name('backend.bulkActionCollections')->middleware(['auth','is_admin']);
	
	//Attributes
	Route::get('/attributes', [App\Http\Controllers\Backend\AttributesController::class, 'getAttributesPageLoad'])->name('backend.attributes')->middleware(['auth','is_admin']);
	Route::get('/getAttributesTableData', [App\Http\Controllers\Backend\AttributesController::class, 'getAttributesTableData'])->name('backend.getAttributesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveAttributesData', [App\Http\Controllers\Backend\AttributesController::class, 'saveAttributesData'])->name('backend.saveAttributesData')->middleware(['auth','is_admin']);
	Route::post('/getAttributesById', [App\Http\Controllers\Backend\AttributesController::class, 'getAttributesById'])->name('backend.getAttributesById')->middleware(['auth','is_admin']);
	Route::post('/deleteAttributes', [App\Http\Controllers\Backend\AttributesController::class, 'deleteAttributes'])->name('backend.deleteAttributes')->middleware(['auth','is_admin']);
	Route::post('/bulkActionAttributes', [App\Http\Controllers\Backend\AttributesController::class, 'bulkActionAttributes'])->name('backend.bulkActionAttributes')->middleware(['auth','is_admin']);
	
	//Labels
	Route::get('/labels', [App\Http\Controllers\Backend\LabelsController::class, 'getLabelsPageLoad'])->name('backend.labels')->middleware(['auth','is_admin']);
	Route::get('/getLabelsTableData', [App\Http\Controllers\Backend\LabelsController::class, 'getLabelsTableData'])->name('backend.getLabelsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveLabelsData', [App\Http\Controllers\Backend\LabelsController::class, 'saveLabelsData'])->name('backend.saveLabelsData')->middleware(['auth','is_admin']);
	Route::post('/getLabelsById', [App\Http\Controllers\Backend\LabelsController::class, 'getLabelsById'])->name('backend.getLabelsById')->middleware(['auth','is_admin']);
	Route::post('/deleteLabels', [App\Http\Controllers\Backend\LabelsController::class, 'deleteLabels'])->name('backend.deleteLabels')->middleware(['auth','is_admin']);
	Route::post('/bulkActionLabels', [App\Http\Controllers\Backend\LabelsController::class, 'bulkActionLabels'])->name('backend.bulkActionLabels')->middleware(['auth','is_admin']);
	
	//Coupons
	Route::get('/coupons', [App\Http\Controllers\Backend\CouponsController::class, 'getCouponsPageLoad'])->name('backend.coupons')->middleware(['auth','is_admin']);
	Route::get('/getCouponsTableData', [App\Http\Controllers\Backend\CouponsController::class, 'getCouponsTableData'])->name('backend.getCouponsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveCouponsData', [App\Http\Controllers\Backend\CouponsController::class, 'saveCouponsData'])->name('backend.saveCouponsData')->middleware(['auth','is_admin']);
	Route::post('/getCouponsById', [App\Http\Controllers\Backend\CouponsController::class, 'getCouponsById'])->name('backend.getCouponsById')->middleware(['auth','is_admin']);
	Route::post('/deleteCoupons', [App\Http\Controllers\Backend\CouponsController::class, 'deleteCoupons'])->name('backend.deleteCoupons')->middleware(['auth','is_admin']);
	Route::post('/bulkActionCoupons', [App\Http\Controllers\Backend\CouponsController::class, 'bulkActionCoupons'])->name('backend.bulkActionCoupons')->middleware(['auth','is_admin']);

	//Tax
	Route::get('/tax', [App\Http\Controllers\Backend\TaxesController::class, 'getTaxPageLoad'])->name('backend.tax')->middleware(['auth','is_admin']);
	Route::post('/saveTaxData', [App\Http\Controllers\Backend\TaxesController::class, 'saveTaxData'])->name('backend.saveTaxData')->middleware(['auth','is_admin']);

	//Currency
	Route::get('/currency', [App\Http\Controllers\Backend\CurrencyController::class, 'getCurrencyPageLoad'])->name('backend.currency')->middleware(['auth','is_admin']);
	Route::post('/saveCurrencyData', [App\Http\Controllers\Backend\CurrencyController::class, 'saveCurrencyData'])->name('backend.saveCurrencyData')->middleware(['auth','is_admin']);

	//Slider
	Route::get('/slider', [App\Http\Controllers\Backend\HomeSliderController::class, 'getSliderPageLoad'])->name('backend.slider')->middleware(['auth','is_admin']);
	Route::get('/getSliderTableData', [App\Http\Controllers\Backend\HomeSliderController::class, 'getSliderTableData'])->name('backend.getSliderTableData')->middleware(['auth','is_admin']);
	Route::post('/saveSliderData', [App\Http\Controllers\Backend\HomeSliderController::class, 'saveSliderData'])->name('backend.saveSliderData')->middleware(['auth','is_admin']);
	Route::post('/getSliderById', [App\Http\Controllers\Backend\HomeSliderController::class, 'getSliderById'])->name('backend.getSliderById')->middleware(['auth','is_admin']);
	Route::post('/deleteSlider', [App\Http\Controllers\Backend\HomeSliderController::class, 'deleteSlider'])->name('backend.deleteSlider')->middleware(['auth','is_admin']);
	Route::post('/bulkActionSlider', [App\Http\Controllers\Backend\HomeSliderController::class, 'bulkActionSlider'])->name('backend.bulkActionSlider')->middleware(['auth','is_admin']);

	//Offer Ads
	Route::get('/offer-ads', [App\Http\Controllers\Backend\Offer_adsController::class, 'getOfferAdsPageLoad'])->name('backend.offer-ads')->middleware(['auth','is_admin']);
	Route::get('/getOfferAdsTableData', [App\Http\Controllers\Backend\Offer_adsController::class, 'getOfferAdsTableData'])->name('backend.getOfferAdsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveOfferAdsData', [App\Http\Controllers\Backend\Offer_adsController::class, 'saveOfferAdsData'])->name('backend.saveOfferAdsData')->middleware(['auth','is_admin']);
	Route::post('/getOfferAdsById', [App\Http\Controllers\Backend\Offer_adsController::class, 'getOfferAdsById'])->name('backend.getOfferAdsById')->middleware(['auth','is_admin']);
	Route::post('/deleteOfferAds', [App\Http\Controllers\Backend\Offer_adsController::class, 'deleteOfferAds'])->name('backend.deleteOfferAds')->middleware(['auth','is_admin']);
	Route::post('/bulkActionOfferAds', [App\Http\Controllers\Backend\Offer_adsController::class, 'bulkActionOfferAds'])->name('backend.bulkActionOfferAds')->middleware(['auth','is_admin']);

	//Countries
	Route::get('/countries', [App\Http\Controllers\Backend\CountriesController::class, 'getCountriesPageLoad'])->name('backend.countries')->middleware(['auth','is_admin']);
	Route::get('/getCountriesTableData', [App\Http\Controllers\Backend\CountriesController::class, 'getCountriesTableData'])->name('backend.getCountriesTableData')->middleware(['auth','is_admin']);
	Route::post('/saveCountriesData', [App\Http\Controllers\Backend\CountriesController::class, 'saveCountriesData'])->name('backend.saveCountriesData')->middleware(['auth','is_admin']);
	Route::post('/getCountryById', [App\Http\Controllers\Backend\CountriesController::class, 'getCountryById'])->name('backend.getCountryById')->middleware(['auth','is_admin']);
	Route::post('/deleteCountry', [App\Http\Controllers\Backend\CountriesController::class, 'deleteCountry'])->name('backend.deleteCountry')->middleware(['auth','is_admin']);
	Route::post('/bulkActionCountry', [App\Http\Controllers\Backend\CountriesController::class, 'bulkActionCountry'])->name('backend.bulkActionCountry')->middleware(['auth','is_admin']);
	
	//Page Variation
	Route::get('/page-variation', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getPageVariation'])->name('backend.page-variation')->middleware(['auth','is_admin']);
	Route::post('/savePageVariation', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'savePageVariation'])->name('backend.savePageVariation')->middleware(['auth','is_admin']);

	//Home Video Section
	Route::get('/home-video', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsHomeVideo'])->name('backend.home-video')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsHomeVideo', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsHomeVideo'])->name('backend.saveThemeOptionsHomeVideo')->middleware(['auth','is_admin']);

	//Section Manage
	Route::get('/section-manage', [App\Http\Controllers\Backend\SectionManageController::class, 'getSectionManagePageLoad'])->name('backend.section-manage')->middleware(['auth','is_admin']);
	Route::get('/getSectionManageTableData', [App\Http\Controllers\Backend\SectionManageController::class, 'getSectionManageTableData'])->name('backend.getSectionManageTableData')->middleware(['auth','is_admin']);
	Route::post('/saveSectionManageData', [App\Http\Controllers\Backend\SectionManageController::class, 'saveSectionManageData'])->name('backend.saveSectionManageData')->middleware(['auth','is_admin']);
	Route::post('/getSectionManageById', [App\Http\Controllers\Backend\SectionManageController::class, 'getSectionManageById'])->name('backend.getSectionManageById')->middleware(['auth','is_admin']);
	Route::post('/deleteSectionManage', [App\Http\Controllers\Backend\SectionManageController::class, 'deleteSectionManage'])->name('backend.deleteSectionManage')->middleware(['auth','is_admin']);
	Route::post('/bulkActionSectionManage', [App\Http\Controllers\Backend\SectionManageController::class, 'bulkActionSectionManage'])->name('backend.bulkActionSectionManage')->middleware(['auth','is_admin']);
	
	//Theme Logo
	Route::get('/theme-options', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsPageLoad'])->name('backend.theme-options')->middleware(['auth','is_admin']);
	Route::post('/saveThemeLogo', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeLogo'])->name('backend.saveThemeLogo')->middleware(['auth','is_admin']);
	
	//Theme Options Header
	Route::get('/theme-options-header', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsHeaderPageLoad'])->name('backend.theme-options-header')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsHeader', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsHeader'])->name('backend.saveThemeOptionsHeader')->middleware(['auth','is_admin']);
	
	//Language Switcher
	Route::get('/language-switcher', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getLanguageSwitcher'])->name('backend.language-switcher')->middleware(['auth','is_admin']);
	Route::post('/saveLanguageSwitcher', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveLanguageSwitcher'])->name('backend.saveLanguageSwitcher')->middleware(['auth','is_admin']);
	
	//Theme Options Footer
	Route::get('/theme-options-footer', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsFooterPageLoad'])->name('backend.theme-options-footer')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsFooter', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsFooter'])->name('backend.saveThemeOptionsFooter')->middleware(['auth','is_admin']);
	
	//Custom css
	Route::get('/custom-css', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getCustomCSSPageLoad'])->name('backend.custom-css')->middleware(['auth','is_admin']);
	Route::post('/saveCustomCSS', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveCustomCSS'])->name('backend.saveCustomCSS')->middleware(['auth','is_admin']);
	
	//Custom js
	Route::get('/custom-js', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getCustomJSPageLoad'])->name('backend.custom-js')->middleware(['auth','is_admin']);
	Route::post('/saveCustomJS', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveCustomJS'])->name('backend.saveCustomJS')->middleware(['auth','is_admin']);
	
	//Theme Options Color
	Route::get('/theme-options-color', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsColorPageLoad'])->name('backend.theme-options-color')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsColor', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsColor'])->name('backend.saveThemeOptionsColor')->middleware(['auth','is_admin']);
	
	//Theme Options SEO
	Route::get('/theme-options-seo', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsSEOPageLoad'])->name('backend.theme-options-seo')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsSEO', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsSEO'])->name('backend.saveThemeOptionsSEO')->middleware(['auth','is_admin']);
	
	//Theme Options Facebook
	Route::get('/theme-options-facebook', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsFacebookPageLoad'])->name('backend.theme-options-facebook')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsFacebook', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsFacebook'])->name('backend.saveThemeOptionsFacebook')->middleware(['auth','is_admin']);
	
	//Theme Options Facebook Pixel
	Route::get('/theme-options-facebook-pixel', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsFacebookPixelLoad'])->name('backend.theme-options-facebook-pixel')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsFacebookPixel', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsFacebookPixel'])->name('backend.saveThemeOptionsFacebookPixel')->middleware(['auth','is_admin']);
	
	//Theme Options Twitter
	Route::get('/theme-options-twitter', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsTwitterPageLoad'])->name('backend.theme-options-twitter')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsTwitter', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsTwitter'])->name('backend.saveThemeOptionsTwitter')->middleware(['auth','is_admin']);
	
	//Theme Options Google Analytics
	Route::get('/google-analytics', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getGoogleAnalytics'])->name('backend.google-analytics')->middleware(['auth','is_admin']);
	Route::post('/saveGoogleAnalytics', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveGoogleAnalytics'])->name('backend.saveGoogleAnalytics')->middleware(['auth','is_admin']);
	
	//Theme Options Google Tag Manager
	Route::get('/google-tag-manager', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getGoogleTagManager'])->name('backend.google-tag-manager')->middleware(['auth','is_admin']);
	Route::post('/saveGoogleTagManager', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveGoogleTagManager'])->name('backend.saveGoogleTagManager')->middleware(['auth','is_admin']);
	
	//Theme Options Whatsapp
	Route::get('/theme-options-whatsapp', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getThemeOptionsWhatsappPageLoad'])->name('backend.theme-options-whatsapp')->middleware(['auth','is_admin']);
	Route::post('/saveThemeOptionsWhatsapp', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveThemeOptionsWhatsapp'])->name('backend.saveThemeOptionsWhatsapp')->middleware(['auth','is_admin']);
	
	//Cookie Consent
	Route::get('/cookie-consent', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'getCookieConsent'])->name('backend.cookie-consent')->middleware(['auth','is_admin']);
	Route::post('/saveCookieConsent', [App\Http\Controllers\Backend\ThemeOptionsController::class, 'saveCookieConsent'])->name('backend.saveCookieConsent')->middleware(['auth','is_admin']);

	//Social Media
	Route::get('/social-media', [App\Http\Controllers\Backend\SocialMediasController::class, 'getSocialMediaPageLoad'])->name('backend.social-media')->middleware(['auth','is_admin']);
	Route::get('/getSocialMediaTableData', [App\Http\Controllers\Backend\SocialMediasController::class, 'getSocialMediaTableData'])->name('backend.getSocialMediaTableData')->middleware(['auth','is_admin']);
	Route::post('/saveSocialMediaData', [App\Http\Controllers\Backend\SocialMediasController::class, 'saveSocialMediaData'])->name('backend.saveSocialMediaData')->middleware(['auth','is_admin']);
	Route::post('/getSocialMediaById', [App\Http\Controllers\Backend\SocialMediasController::class, 'getSocialMediaById'])->name('backend.getSocialMediaById')->middleware(['auth','is_admin']);
	Route::post('/deleteSocialMedia', [App\Http\Controllers\Backend\SocialMediasController::class, 'deleteSocialMedia'])->name('backend.deleteSocialMedia')->middleware(['auth','is_admin']);
	Route::post('/bulkActionSocialMedia', [App\Http\Controllers\Backend\SocialMediasController::class, 'bulkActionSocialMedia'])->name('backend.bulkActionSocialMedia')->middleware(['auth','is_admin']);

	//General Page
	Route::get('/general', [App\Http\Controllers\Backend\SettingsController::class, 'getGeneralPageLoad'])->name('backend.general')->middleware(['auth','is_admin']);
	Route::post('/GeneralSettingUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'GeneralSettingUpdate'])->name('backend.GeneralSettingUpdate')->middleware(['auth','is_admin']);
	
	//Theme Register
	Route::get('/theme-register', [App\Http\Controllers\Backend\SettingsController::class, 'loadThemeRegisterPage'])->name('backend.theme-register')->middleware(['auth','is_admin']);
	Route::get('/getPcodeData', [App\Http\Controllers\Backend\SettingsController::class, 'getPcodeData'])->name('backend.getPcodeData')->middleware(['auth','is_admin']);
	Route::post('/CodeVerified', [App\Http\Controllers\Backend\SettingsController::class, 'CodeVerified'])->name('backend.CodeVerified')->middleware(['auth','is_admin']);
	Route::post('/deletePcode', [App\Http\Controllers\Backend\SettingsController::class, 'deletePcode'])->name('backend.deletePcode')->middleware(['auth','is_admin']);
	
	//Google Recaptcha
	Route::get('/google-recaptcha', [App\Http\Controllers\Backend\SettingsController::class, 'loadGoogleRecaptchaPage'])->name('backend.google-recaptcha')->middleware(['auth','is_admin']);
	Route::post('/GoogleRecaptchaUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'GoogleRecaptchaUpdate'])->name('backend.GoogleRecaptchaUpdate')->middleware(['auth','is_admin']);
	
	//Google Map
	Route::get('/google-map', [App\Http\Controllers\Backend\SettingsController::class, 'loadGoogleMapPage'])->name('backend.google-map')->middleware(['auth','is_admin']);
	Route::post('/GoogleMapUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'GoogleMapUpdate'])->name('backend.GoogleMapUpdate')->middleware(['auth','is_admin']);

	//Mail Settings
	Route::get('/mail-settings', [App\Http\Controllers\Backend\SettingsController::class, 'loadMailSettingsPage'])->name('backend.mail-settings')->middleware(['auth','is_admin']);
	Route::post('/saveMailSettings', [App\Http\Controllers\Backend\SettingsController::class, 'saveMailSettings'])->name('backend.saveMailSettings')->middleware(['auth','is_admin']);
	
	//Payment methods
	Route::get('/payment-methods', [App\Http\Controllers\Backend\SettingsController::class, 'loadPaymentMethodsPage'])->name('backend.payment-methods')->middleware(['auth','is_admin']);
	Route::post('/StripeSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'StripeSettingsUpdate'])->name('backend.StripeSettingsUpdate')->middleware(['auth','is_admin']);
	Route::post('/PaypalSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'PaypalSettingsUpdate'])->name('backend.PaypalSettingsUpdate')->middleware(['auth','is_admin']);
	Route::post('/RazorpaySettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'RazorpaySettingsUpdate'])->name('backend.RazorpaySettingsUpdate')->middleware(['auth','is_admin']);
	Route::post('/MollieSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'MollieSettingsUpdate'])->name('backend.MollieSettingsUpdate')->middleware(['auth','is_admin']);
	Route::post('/CODSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'CODSettingsUpdate'])->name('backend.CODSettingsUpdate')->middleware(['auth','is_admin']);
	Route::post('/BankSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'BankSettingsUpdate'])->name('backend.BankSettingsUpdate')->middleware(['auth','is_admin']);
	
	//Media Settings
	Route::get('/media-settings', [App\Http\Controllers\Backend\SettingsController::class, 'loadMediaSettingsPage'])->name('backend.media-settings')->middleware(['auth','is_admin']);
	Route::get('/getMediaSettingsTableData', [App\Http\Controllers\Backend\SettingsController::class, 'getMediaSettingsTableData'])->name('backend.getMediaSettingsTableData')->middleware(['auth','is_admin']);
	Route::post('/getMediaSettingsById', [App\Http\Controllers\Backend\SettingsController::class, 'getMediaSettingsById'])->name('backend.getMediaSettingsById')->middleware(['auth','is_admin']);
	Route::post('/MediaSettingsUpdate', [App\Http\Controllers\Backend\SettingsController::class, 'MediaSettingsUpdate'])->name('backend.MediaSettingsUpdate')->middleware(['auth','is_admin']);
	
	//All File Upload
	Route::post('/FileUpload', [App\Http\Controllers\Backend\UploadController::class, 'FileUpload'])->name('backend.FileUpload')->middleware(['auth','is_admin']);
	Route::post('/MediaUpload', [App\Http\Controllers\Backend\UploadController::class, 'MediaUpload'])->name('backend.MediaUpload')->middleware(['auth','is_admin']);
	
	//All Combo
	Route::post('/getTimezoneList', [App\Http\Controllers\Backend\ComboController::class, 'getTimezoneList'])->name('backend.getTimezoneList')->middleware(['auth','is_admin']);
	Route::post('/getUserStatusList', [App\Http\Controllers\Backend\ComboController::class, 'getUserStatusList'])->name('backend.getUserStatusList')->middleware(['auth','is_admin']);
	Route::post('/getUserRolesList', [App\Http\Controllers\Backend\ComboController::class, 'getUserRolesList'])->name('backend.getUserRolesList')->middleware(['auth','is_admin']);
	Route::post('/getStatusList', [App\Http\Controllers\Backend\ComboController::class, 'getStatusList'])->name('backend.getStatusList')->middleware(['auth','is_admin']);
	Route::post('/getCategoryList', [App\Http\Controllers\Backend\ComboController::class, 'getCategoryList'])->name('backend.getCategoryList')->middleware(['auth','is_admin']);
	Route::post('/getBrandList', [App\Http\Controllers\Backend\ComboController::class, 'getBrandList'])->name('backend.getBrandList')->middleware(['auth','is_admin']);
	
	//Orders Excel/CSV Export
	Route::get('/orders-excel-export', [App\Http\Controllers\Backend\OrdersExportController::class, 'OrdersExcelExport'])->name('backend.orders-excel-export')->middleware(['auth','is_admin']);
	Route::get('/orders-csv-export', [App\Http\Controllers\Backend\OrdersExportController::class, 'OrdersCSVExport'])->name('backend.orders-csv-export')->middleware(['auth','is_admin']);
	
	//Transactions Excel/CSV Export
	Route::get('/transactions-excel-export', [App\Http\Controllers\Backend\TransactionsExportController::class, 'TransactionsExcelExport'])->name('backend.transactions-excel-export')->middleware(['auth','is_admin']);
	Route::get('/transactions-csv-export', [App\Http\Controllers\Backend\TransactionsExportController::class, 'TransactionsCSVExport'])->name('backend.transactions-csv-export')->middleware(['auth','is_admin']);
	
	//Withdrawals
	Route::get('/withdrawals', [App\Http\Controllers\Backend\WithdrawalController::class, 'getWithdrawalsPageLoad'])->name('backend.withdrawals')->middleware(['auth','is_admin']);
	Route::get('/getWithdrawalsTableData', [App\Http\Controllers\Backend\WithdrawalController::class, 'getWithdrawalsTableData'])->name('backend.getWithdrawalsTableData')->middleware(['auth','is_admin']);
	Route::post('/saveWithdrawalsData', [App\Http\Controllers\Backend\WithdrawalController::class, 'saveWithdrawalsData'])->name('backend.saveWithdrawalsData')->middleware(['auth','is_admin']);
	Route::post('/getWithdrawalById', [App\Http\Controllers\Backend\WithdrawalController::class, 'getWithdrawalById'])->name('backend.getWithdrawalById')->middleware(['auth','is_admin']);
	Route::post('/deleteWithdrawal', [App\Http\Controllers\Backend\WithdrawalController::class, 'deleteWithdrawal'])->name('backend.deleteWithdrawal')->middleware(['auth','is_admin']);
	
	Route::post('/saveScreenshot', [App\Http\Controllers\Backend\WithdrawalController::class, 'saveScreenshot'])->name('backend.saveScreenshot')->middleware(['auth','is_admin']);
	Route::post('/getScreenshotById', [App\Http\Controllers\Backend\WithdrawalController::class, 'getScreenshotById'])->name('backend.getScreenshotById')->middleware(['auth','is_admin']);
	Route::post('/deleteScreenshotById', [App\Http\Controllers\Backend\WithdrawalController::class, 'deleteScreenshotById'])->name('backend.deleteScreenshotById')->middleware(['auth','is_admin']);

	//Seller Settings
	Route::get('/seller-settings', [App\Http\Controllers\Backend\SellerSettingsController::class, 'getSellerSettingsPageLoad'])->name('backend.seller-settings')->middleware(['auth','is_admin']);
	Route::post('/SellerSettingsSave', [App\Http\Controllers\Backend\SellerSettingsController::class, 'SellerSettingsSave'])->name('backend.SellerSettingsSave')->middleware(['auth','is_admin']);

});

Route::prefix('seller')->group(function(){
	
	//Dashboard
	Route::get('/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'getDashboardData'])->name('seller.dashboard')->middleware(['auth','is_seller']);
	
	//Withdrawals
	Route::get('/withdrawals', [App\Http\Controllers\Seller\WithdrawalController::class, 'getWithdrawalsPageLoad'])->name('seller.withdrawals')->middleware(['auth','is_seller']);
	Route::get('/getWithdrawalsTableData', [App\Http\Controllers\Seller\WithdrawalController::class, 'getWithdrawalsTableData'])->name('seller.getWithdrawalsTableData')->middleware(['auth','is_seller']);
	Route::post('/saveWithdrawalsData', [App\Http\Controllers\Seller\WithdrawalController::class, 'saveWithdrawalsData'])->name('seller.saveWithdrawalsData')->middleware(['auth','is_seller']);
	Route::post('/getWithdrawalById', [App\Http\Controllers\Seller\WithdrawalController::class, 'getWithdrawalById'])->name('seller.getWithdrawalById')->middleware(['auth','is_seller']);
	Route::post('/getCurrentBalanceBySellerId', [App\Http\Controllers\Seller\WithdrawalController::class, 'getCurrentBalanceBySellerId'])->name('seller.getCurrentBalanceBySellerId')->middleware(['auth','is_seller']);
	Route::post('/getScreenshotById', [App\Http\Controllers\Seller\WithdrawalController::class, 'getScreenshotById'])->name('seller.getScreenshotById')->middleware(['auth','is_seller']);
	
	//Settings Page
	Route::get('/settings', [App\Http\Controllers\Seller\SellerSettingsController::class, 'getSellerSettingsPageLoad'])->name('seller.settings')->middleware(['auth','is_seller']);
	Route::post('/saveSellersData', [App\Http\Controllers\Seller\SellerSettingsController::class, 'saveSellersData'])->name('seller.saveSellersData')->middleware(['auth','is_seller']);
	Route::post('/saveBankInformationData', [App\Http\Controllers\Seller\SellerSettingsController::class, 'saveBankInformationData'])->name('seller.saveBankInformationData')->middleware(['auth','is_seller']);
	Route::post('/hasShopSlug', [App\Http\Controllers\Seller\SellerSettingsController::class, 'hasShopSlug'])->name('seller.hasShopSlug')->middleware(['auth','is_seller']);
	
	//Review & Ratings
	Route::get('/review', [App\Http\Controllers\Seller\ReviewsSellerController::class, 'getReviewRatingsPageLoad'])->name('seller.review')->middleware(['auth','is_seller']);
	Route::get('/getReviewRatingsTableData', [App\Http\Controllers\Seller\ReviewsSellerController::class, 'getReviewRatingsTableData'])->name('seller.getReviewRatingsTableData')->middleware(['auth','is_seller']);
	Route::post('/deleteReviewRatings', [App\Http\Controllers\Seller\ReviewsSellerController::class, 'deleteReviewRatings'])->name('seller.deleteReviewRatings')->middleware(['auth','is_seller']);
	Route::post('/bulkActionReviewRatings', [App\Http\Controllers\Seller\ReviewsSellerController::class, 'bulkActionReviewRatings'])->name('seller.bulkActionReviewRatings')->middleware(['auth','is_seller']);

	//Orders
	Route::get('/orders', [App\Http\Controllers\Seller\OrdersSellerController::class, 'getOrdersPageLoad'])->name('seller.orders')->middleware(['auth','is_seller']);
	Route::get('/getOrdersTableData', [App\Http\Controllers\Seller\OrdersSellerController::class, 'getOrdersTableData'])->name('seller.getOrdersTableData')->middleware(['auth','is_seller']);
	Route::get('/order/{id}', [App\Http\Controllers\Seller\OrdersSellerController::class, 'getOrderData'])->name('seller.order')->middleware(['auth','is_seller']);
	Route::post('/updateOrderStatus', [App\Http\Controllers\Seller\OrdersSellerController::class, 'updateOrderStatus'])->name('seller.updateOrderStatus')->middleware(['auth','is_seller']);
	Route::get('/getPaymentOrderStatusData', [App\Http\Controllers\Seller\OrdersSellerController::class, 'getPaymentOrderStatusData'])->name('seller.getPaymentOrderStatusData')->middleware(['auth','is_seller']);
	Route::post('/deleteOrder', [App\Http\Controllers\Seller\OrdersSellerController::class, 'deleteOrder'])->name('seller.deleteOrder')->middleware(['auth','is_seller']);
	
	//Orders Excel/CSV Export
	Route::get('/orders-excel-export', [App\Http\Controllers\Seller\OrdersSellerExportController::class, 'OrdersExcelExport'])->name('seller.orders-excel-export')->middleware(['auth','is_seller']);
	Route::get('/orders-csv-export', [App\Http\Controllers\Seller\OrdersSellerExportController::class, 'OrdersCSVExport'])->name('seller.orders-csv-export')->middleware(['auth','is_seller']);
	
	//Products
	Route::get('/products', [App\Http\Controllers\Seller\ProductsController::class, 'getProductsPageLoad'])->name('seller.products')->middleware(['auth','is_seller']);
	Route::get('/getProductsTableData', [App\Http\Controllers\Seller\ProductsController::class, 'getProductsTableData'])->name('seller.getProductsTableData')->middleware(['auth','is_seller']);
	Route::post('/saveProductsData', [App\Http\Controllers\Seller\ProductsController::class, 'saveProductsData'])->name('seller.saveProductsData')->middleware(['auth','is_seller']);
	Route::post('/deleteProducts', [App\Http\Controllers\Seller\ProductsController::class, 'deleteProducts'])->name('seller.deleteProducts')->middleware(['auth','is_seller']);
	Route::post('/bulkActionProducts', [App\Http\Controllers\Seller\ProductsController::class, 'bulkActionProducts'])->name('seller.bulkActionProducts')->middleware(['auth','is_seller']);
	Route::post('/hasProductSlug', [App\Http\Controllers\Seller\ProductsController::class, 'hasProductSlug'])->name('seller.hasProductSlug')->middleware(['auth','is_seller']);
	//Update
	Route::get('/product/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getProductPageData'])->name('seller.product')->middleware(['auth','is_seller']);
	Route::post('/updateProductsData', [App\Http\Controllers\Seller\ProductsController::class, 'updateProductsData'])->name('seller.updateProductsData')->middleware(['auth','is_seller']);

	//Price
	Route::get('/price/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getPricePageData'])->name('seller.price')->middleware(['auth','is_seller']);
	Route::post('/savePriceData', [App\Http\Controllers\Seller\ProductsController::class, 'savePriceData'])->name('seller.savePriceData')->middleware(['auth','is_seller']);
	
	//Inventory
	Route::get('/inventory/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getInventoryPageData'])->name('seller.inventory')->middleware(['auth','is_seller']);
	Route::post('/saveInventoryData', [App\Http\Controllers\Seller\ProductsController::class, 'saveInventoryData'])->name('seller.saveInventoryData')->middleware(['auth','is_seller']);
	
	//Product Images
	Route::get('/product-images/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getProductImagesPageData'])->name('seller.product-images')->middleware(['auth','is_seller']);
	Route::get('/getProductImagesTableData', [App\Http\Controllers\Seller\ProductsController::class, 'getProductImagesTableData'])->name('seller.getProductImagesTableData')->middleware(['auth','is_seller']);
	Route::post('/saveProductImagesData', [App\Http\Controllers\Seller\ProductsController::class, 'saveProductImagesData'])->name('seller.saveProductImagesData')->middleware(['auth','is_seller']);
	Route::post('/deleteProductImages', [App\Http\Controllers\Seller\ProductsController::class, 'deleteProductImages'])->name('seller.deleteProductImages')->middleware(['auth','is_seller']);
	
	//Variations
	Route::get('/variations/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getVariationsPageData'])->name('seller.variations')->middleware(['auth','is_seller']);
	Route::post('/saveVariationsData', [App\Http\Controllers\Seller\ProductsController::class, 'saveVariationsData'])->name('seller.saveVariationsData')->middleware(['auth','is_seller']);
		
	//Related Products
	Route::get('/related-products/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getRelatedProductsPageData'])->name('seller.related-products')->middleware(['auth','is_seller']);
	Route::get('/getProductListForRelatedTableData', [App\Http\Controllers\Seller\ProductsController::class, 'getProductListForRelatedTableData'])->name('seller.getProductListForRelatedTableData')->middleware(['auth','is_seller']);
	Route::get('/getRelatedProductTableData', [App\Http\Controllers\Seller\ProductsController::class, 'getRelatedProductTableData'])->name('seller.getRelatedProductTableData')->middleware(['auth','is_seller']);
	Route::post('/saveRelatedProductsData', [App\Http\Controllers\Seller\ProductsController::class, 'saveRelatedProductsData'])->name('seller.saveRelatedProductsData')->middleware(['auth','is_seller']);
	Route::post('/deleteRelatedProduct', [App\Http\Controllers\Seller\ProductsController::class, 'deleteRelatedProduct'])->name('seller.deleteRelatedProduct')->middleware(['auth','is_seller']);
	
	//Product SEO
	Route::get('/product-seo/{id}', [App\Http\Controllers\Seller\ProductsController::class, 'getProductSEOPageData'])->name('seller.product-seo')->middleware(['auth','is_seller']);
	Route::post('/saveProductSEOData', [App\Http\Controllers\Seller\ProductsController::class, 'saveProductSEOData'])->name('seller.saveProductSEOData')->middleware(['auth','is_seller']);

	//All File Upload
	Route::post('/MediaUpload', [App\Http\Controllers\Backend\UploadController::class, 'MediaUpload'])->name('seller.MediaUpload')->middleware(['auth','is_seller']);

	//All Combo
	Route::post('/getTimezoneList', [App\Http\Controllers\Backend\ComboController::class, 'getTimezoneList'])->name('seller.getTimezoneList')->middleware(['auth','is_seller']);
	Route::post('/getUserStatusList', [App\Http\Controllers\Backend\ComboController::class, 'getUserStatusList'])->name('seller.getUserStatusList')->middleware(['auth','is_seller']);
	Route::post('/getUserRolesList', [App\Http\Controllers\Backend\ComboController::class, 'getUserRolesList'])->name('seller.getUserRolesList')->middleware(['auth','is_seller']);
	Route::post('/getStatusList', [App\Http\Controllers\Backend\ComboController::class, 'getStatusList'])->name('seller.getStatusList')->middleware(['auth','is_seller']);
	Route::post('/getCategoryList', [App\Http\Controllers\Backend\ComboController::class, 'getCategoryList'])->name('seller.getCategoryList')->middleware(['auth','is_seller']);
	Route::post('/getBrandList', [App\Http\Controllers\Backend\ComboController::class, 'getBrandList'])->name('seller.getBrandList')->middleware(['auth','is_seller']);

});

