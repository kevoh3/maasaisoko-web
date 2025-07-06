<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;

class CartController extends Controller
{
	//Add to Cart
	public function AddToCart($id, $qty){

		$res = array();
		$datalist = Product::where('id', $id)->first();
		$user = User::where('id', $datalist['user_id'])->first();

		$quantity = $qty == 0 ? 1 : $qty;
		$cart = session()->get('shopping_cart', []);
		
		if(isset($cart[$id])){
			$cart[$id]['qty'] = $cart[$id]['qty'] + $quantity;
		}else{
			$cart[$id] = [
				"id" => $datalist['id'],
				"name" => $datalist['title'],
				"qty" => $quantity,
				"price" => $datalist['sale_price'],
				"weight" => 0,
				"thumbnail" => $datalist['f_thumbnail'],
				"unit" => $datalist['variation_size'],
				"seller_id" => $datalist['user_id'],
				"seller_name" => $user['name'],
				"store_name" => $user['shop_name'],
				"store_logo" => $user['photo'],
				"store_url" => $user['shop_url'],
				"seller_email" => $user['email'],
				"seller_phone" => $user['phone'],
				"seller_address" => $user['address']
			];
		}

		session()->put('shopping_cart', $cart);

		$res['msgType'] = 'success';
		$res['msg'] = __('New Data Added Successfully');
		
		return response()->json($res);
	}
	
	//Add to Cart
	public function ViewCart(){
		$gtext = gtext();
		$gtax = getTax();
		$taxRate = $gtax['percentage'];
		$Path = asset('public/media');

		$ShoppingCartData = session()->get('shopping_cart');
		$count = 0;
		$Total_Price = 0;
		$Sub_Total = 0;
		$tax = 0;
		$total = 0;
		$items = '';
		if(session()->get('shopping_cart')){
			foreach ($ShoppingCartData as $row) {
				$count += $row['qty'];
				$Total_Price += $row['price']*$row['qty'];
				$Sub_Total += $row['price']*$row['qty'];
				
				if($gtext['currency_position'] == 'left'){
					$price = '<span id="product-quatity">'.$row['qty'].'</span> x '.$gtext['currency_icon'].$row['price']; 
				}else{
					$price = '<span id="product-quatity">'.$row['qty'].'</span> x '.$row['price'].$gtext['currency_icon']; 
				}
			
				$items .= '<li>
							<div class="cart-item-card">
								<a data-id="'.$row['id'].'" id="removetocart_'.$row['id'].'" onclick="onRemoveToCart('.$row['id'].')" href="javascript:void(0);" class="item-remove"><i class="bi bi-x"></i></a>
								<div class="cart-item-img">
									<img src="'.$Path.'/'.$row['thumbnail'].'" alt="'.$row['name'].'" />
								</div>
								<div class="cart-item-desc">
									<h6><a href="'.route('frontend.product', [$row['id'], str_slug($row['name'])]).'">'.$row['name'].'</a></h6>
									<p>'.$price.'</p>
								</div>
							</div>
						</li>';
			}
		}
		
		$TotalPrice = NumberFormat($Total_Price);
		$SubTotal = NumberFormat($Sub_Total);
		
		$TaxCal = ($Total_Price*$taxRate)/100;
		$tax = NumberFormat($TaxCal);
		
		$total = $Sub_Total+$TaxCal;
		$GrandTotal = NumberFormat($total);
		$discount = 0;
		
		$datalist = array();
		$datalist['items'] = $items;
		$datalist['total_qty'] = $count;
		if($gtext['currency_position'] == 'left'){
			$datalist['sub_total'] = $gtext['currency_icon'].$SubTotal;
			$datalist['tax'] = $gtext['currency_icon'].$tax;
			$datalist['price_total'] = $gtext['currency_icon'].$TotalPrice;
			$datalist['total'] = $gtext['currency_icon'].$GrandTotal;
		}else{
			$datalist['sub_total'] = $SubTotal.$gtext['currency_icon'];
			$datalist['tax'] = $tax.$gtext['currency_icon'];
			$datalist['price_total'] = $TotalPrice.$gtext['currency_icon'];
			$datalist['total'] = $GrandTotal.$gtext['currency_icon'];
		}

		return response()->json($datalist);
	}
	
	//Remove to Cart
	public function RemoveToCart($rowid){
		$res = array();

		$cart = session()->get('shopping_cart');
		if(isset($cart[$rowid])){
			unset($cart[$rowid]);
			session()->put('shopping_cart', $cart);
		}

		$res['msgType'] = 'success';
		$res['msg'] = __('Data Removed Successfully');
		
		return response()->json($res);
	}
	
    //get Cart
    public function getCart(){
        return view('frontend.cart');
    }
	
    //get Cart
    public function getViewCartData(){
		$gtext = gtext();
		$gtax = getTax();
		$taxRate = $gtax['percentage'];
		
		$ShoppingCartData = session()->get('shopping_cart');
		$count = 0;
		$Total_Price = 0;
		$Sub_Total = 0;
		$tax = 0;
		$total = 0;
		
		if(session()->get('shopping_cart')){
			foreach ($ShoppingCartData as $row) {
				$count += $row['qty'];
				$Total_Price += $row['price']*$row['qty'];
				$Sub_Total += $row['price']*$row['qty'];
			}
		}
		
		$TotalPrice = NumberFormat($Total_Price);
		$SubTotal = NumberFormat($Sub_Total);
		
		$TaxCal = ($Total_Price*$taxRate)/100;
		$tax = NumberFormat($TaxCal);
		
		$total = $SubTotal+$TaxCal;
		$GrandTotal = NumberFormat($total);
		$discount = 0;
		
		$datalist = array();
		$datalist['total_qty'] = $count;
		if($gtext['currency_position'] == 'left'){
			$datalist['sub_total'] = $gtext['currency_icon'].$SubTotal;
			$datalist['tax'] = $gtext['currency_icon'].$tax;
			$datalist['price_total'] = $gtext['currency_icon'].$TotalPrice;
			$datalist['total'] = $gtext['currency_icon'].$GrandTotal;
			$datalist['discount'] = $gtext['currency_icon'].$discount;
		}else{
			$datalist['sub_total'] = $SubTotal.$gtext['currency_icon'];
			$datalist['tax'] = $tax.$gtext['currency_icon'];
			$datalist['price_total'] = $TotalPrice.$gtext['currency_icon'];
			$datalist['total'] = $GrandTotal.$gtext['currency_icon'];
			$datalist['discount'] = $discount.$gtext['currency_icon'];
		}

		return response()->json($datalist);
    }
	
	//Add to Wishlist
	public function addToWishlist($id){

		$res = array();
		$datalist = Product::where('id', $id)->first();
		$user = User::where('id', $datalist['user_id'])->first();
		
		$quantity = 1;
		$cart = session()->get('shopping_wishlist', []);
		
		if(isset($cart[$id])){
			$cart[$id]['qty'] = $quantity;
		}else{
			$cart[$id] = [
				"id" => $datalist['id'],
				"name" => $datalist['title'],
				"qty" => $quantity,
				"price" => $datalist['sale_price'],
				"weight" => 0,
				"thumbnail" => $datalist['f_thumbnail'],
				"seller_id" => $datalist['user_id'],
				"seller_name" => $user['name'],
				"store_name" => $user['shop_name'],
				"store_logo" => $user['photo'],
				"store_url" => $user['shop_url'],
				"seller_email" => $user['email'],
				"seller_phone" => $user['phone'],
				"seller_address" => $user['address']
			];
		}

		session()->put('shopping_wishlist', $cart);

		$res['msgType'] = 'success';
		$res['msg'] = __('New Data Added Successfully');
		
		return response()->json($res);
	}
	
    //get Wishlist
    public function getWishlist(){
		return view('frontend.wishlist');
	}
	
	//Remove to Wishlist
	public function RemoveToWishlist($rowid){
		$res = array();
		
		$cart = session()->get('shopping_wishlist');
		if(isset($cart[$rowid])){
			unset($cart[$rowid]);
			session()->put('shopping_wishlist', $cart);
		}

		$res['msgType'] = 'success';
		$res['msg'] = __('Data Removed Successfully');
		
		return response()->json($res);
	}
	
	//Count to Wishlist
	public function countWishlist(){

		$ShoppingWishlistData = session()->get('shopping_wishlist');
		$count = 0;
		if(session()->get('shopping_wishlist')){
			foreach ($ShoppingWishlistData as $row) {
				$count++;
			}
		}
		
		return response()->json($count);
	}
}
