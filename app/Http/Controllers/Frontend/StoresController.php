<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tp_option;

class StoresController extends Controller
{
	//Get data for stores
	public function getStoresData($seller_id, $title){
		$lan = glan();
		
		$params = array('seller_id' => $seller_id);
		
		$sData = Tp_option::where('option_name', '=', 'page_variation')->first();
		if($sData !=''){
			$dataObj = json_decode($sData['option_value']);
			$seller_variation = $dataObj->seller_variation;
		}else{
			$seller_variation = 'left_sidebar';
		}
		
		if(($seller_variation == 'left_sidebar') || ($seller_variation == 'right_sidebar')){
			$num = 12;
		}else{
			$num = 16;
		}
		
		$seller_data = DB::table('users')
			->select('id', 'email', 'shop_name', 'phone', 'address', 'photo', 'created_at')
			->where('status_id', '=', 1)
			->where('id', '=', $seller_id)
			->first();
			
		$SellerReview = array('TotalReview' => 0, 'TotalRating' => 0, 'ReviewPercentage' => 0);
		$aReview = getReviewsBySeller($seller_id);
		$SellerReview['TotalReview'] = $aReview[0]->TotalReview;
		$SellerReview['TotalRating'] = $aReview[0]->TotalRating;
		$SellerReview['ReviewPercentage'] = number_format($aReview[0]->ReviewPercentage);
		
		$datalist = DB::table('products')
			->join('users', 'products.user_id', '=', 'users.id')
			->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
			->where('products.is_publish', '=', 1)
			->where('users.status_id', '=', 1)
			->where('products.lan', '=', $lan)
			->where('products.user_id', '=', $seller_id)
			->orderBy('products.id', 'desc')
			->paginate($num);

		for($i=0; $i<count($datalist); $i++){
			$Reviews = getReviews($datalist[$i]->id);
			$datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
			$datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
			$datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
		}

		return view('frontend.stores', compact('params', 'seller_variation', 'seller_data', 'SellerReview', 'datalist'));
	}

	//Get data for Stores Pagination
	public function getStoresGrid(Request $request){
		$lan = glan();
		
		$seller_id = $request->seller_id;
		$min_price = $request->min_price == '' ? 0 : $request->min_price;
		$max_price = $request->max_price;
		
		$sData = Tp_option::where('option_name', '=', 'page_variation')->first();
		if($sData !=''){
			$dataObj = json_decode($sData['option_value']);
			$seller_variation = $dataObj->seller_variation;
		}else{
			$seller_variation = 'left_sidebar';
		}
		
		if($request->num !=''){
			$num = $request->num;
		}else{
			if(($seller_variation == 'left_sidebar') || ($seller_variation == 'right_sidebar')){
				$num = 9;
			}else{
				$num = 12;
			}
		}
		
		$field_name = 'id';
		$order_name = 'desc';
		if($request->sortby !=''){
			if($request->sortby == 'date_asc'){
				$field_name = 'created_at';
				$order_name = 'asc';
			}elseif($request->sortby == 'date_desc'){
				$field_name = 'created_at';
				$order_name = 'desc';
			}elseif($request->sortby == 'name_asc'){
				$field_name = 'title';
				$order_name = 'asc';
			}elseif($request->sortby == 'name_desc'){
				$field_name = 'title';
				$order_name = 'desc';
			}
		}else{
			$field_name = 'id';
			$order_name = 'desc';
		}
		
		if($request->ajax()){
			if($max_price !=''){
				$datalist = DB::table('products')
					->join('users', 'products.user_id', '=', 'users.id')
					->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
					->where('products.is_publish', '=', 1)
					->where('users.status_id', '=', 1)
					->where('products.lan', '=', $lan)
					->where('products.user_id', '=', $seller_id)
					->whereBetween('products.sale_price', [$min_price, $max_price])
					->orderBy('products.'.$field_name, $order_name)
					->paginate($num);
			}else{
				$datalist = DB::table('products')
					->join('users', 'products.user_id', '=', 'users.id')
					->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
					->where('products.is_publish', '=', 1)
					->where('users.status_id', '=', 1)
					->where('products.lan', '=', $lan)
					->where('products.user_id', '=', $seller_id)
					->orderBy('products.'.$field_name, $order_name)
					->paginate($num);
			}
			
			for($i=0; $i<count($datalist); $i++){
				$Reviews = getReviews($datalist[$i]->id);
				$datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
				$datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
				$datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			return view('frontend.partials.stores-grid', compact('seller_variation', 'datalist'))->render();
		}
	}	
	
}
