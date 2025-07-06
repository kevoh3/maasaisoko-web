<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Pro_category;
use App\Models\Product;
use App\Models\Tp_option;

class ProductCategoryController extends Controller
{
	
    //get Product Category Page
    public function getProductCategoryPage($id, $title){
		
		$params = array('category_id' => $id);
		
		$mdata = Pro_category::where('id', '=', $id)->where('is_publish', '=', 1)->first();
		if($mdata !=''){
			$metadata = $mdata;
		}else{
			$metadata = array(
				'id' => '',
				'name' => '',
				'slug' => '',
				'thumbnail' => '',
				'subheader_image' => '',
				'description' => '',
				'lan' => '',
				'parent_id' => '',
				'is_subheader' => '',
				'is_publish' => '',
				'og_title' => '',
				'og_image' => '',
				'og_description' => '',
				'og_keywords' => ''
			);
		}
		
		$sData = Tp_option::where('option_name', '=', 'page_variation')->first();
		if($sData !=''){
			$dataObj = json_decode($sData['option_value']);
			$category_variation = $dataObj->category_variation;
		}else{
			$category_variation = 'left_sidebar';
		}
		
		if(($category_variation == 'left_sidebar') || ($category_variation == 'right_sidebar')){
			$num = 9;
		}else{
			$num = 12;
		}
		
		$datalist = DB::table('products')
			->join('users', 'products.user_id', '=', 'users.id')
			->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
			->where('products.is_publish', '=', 1)
			->where('users.status_id', '=', 1)
			->where('products.cat_id', '=', $id)
			->orderBy('products.id','desc')
			->paginate($num);

		for($i=0; $i<count($datalist); $i++){
			$Reviews = getReviews($datalist[$i]->id);
			$datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
			$datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
			$datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
		}

        return view('frontend.product-category', compact('params', 'metadata', 'category_variation', 'datalist'));
    }
	
	//Get data for Product Category Pagination
	public function getProductCategoryGrid(Request $request){

		$id = $request->cat_id;
		$min_price = $request->min_price == '' ? 0 : $request->min_price;
		$max_price = $request->max_price;
		
		$sData = Tp_option::where('option_name', '=', 'page_variation')->first();
		if($sData !=''){
			$dataObj = json_decode($sData['option_value']);
			$category_variation = $dataObj->category_variation;
		}else{
			$category_variation = 'left_sidebar';
		}
			
		if($request->num !=''){
			$num = $request->num;
		}else{
			if(($category_variation == 'left_sidebar') || ($category_variation == 'right_sidebar')){
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
					->where('products.cat_id', '=', $id)
					->whereBetween('products.sale_price', [$min_price, $max_price])
					->orderBy('products.'.$field_name, $order_name)
					->paginate($num);
			}else{
				$datalist = DB::table('products')
					->join('users', 'products.user_id', '=', 'users.id')
					->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
					->where('products.is_publish', '=', 1)
					->where('users.status_id', '=', 1)
					->where('products.cat_id', '=', $id)
					->orderBy('products.'.$field_name, $order_name)
					->paginate($num);
			}
			
 			for($i=0; $i<count($datalist); $i++){
				$Reviews = getReviews($datalist[$i]->id);
				$datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
				$datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
				$datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			return view('frontend.partials.product-category-grid', compact('category_variation', 'datalist'))->render();
		}
	}
}
