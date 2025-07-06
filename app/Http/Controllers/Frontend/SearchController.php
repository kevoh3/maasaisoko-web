<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
	
	//Get data for search
	public function getSearchData(Request $request){
		$lan = glan();
		$search = $request->search;
		$category = $request->category;

		if($category !=''){
			$datalist = DB::table('products')
				->join('users', 'products.user_id', '=', 'users.id')
				->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
				->where('products.is_publish', '=', 1)
				->where('users.status_id', '=', 1)
				->where('products.lan', '=', $lan)
				->where('products.cat_id', '=', $category)
				->where(function ($query) use ($search){
					$query->where('products.title', 'like', '%'.$search.'%')
						->orWhere('products.slug', 'like', '%'.$search.'%')
						->orWhere('products.sale_price', 'like', '%'.$search.'%')
						->orWhere('products.sku', 'like', '%'.$search.'%')
						->orWhere('users.shop_name', 'like', '%'.$search.'%');
				})
				->orderBy('products.id', 'desc')
				->paginate(20);
		}else{
			$datalist = DB::table('products')
				->join('users', 'products.user_id', '=', 'users.id')
				->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
				->where('products.is_publish', '=', 1)
				->where('users.status_id', '=', 1)
				->where('products.lan', '=', $lan)
				->where(function ($query) use ($search){
					$query->where('products.title', 'like', '%'.$search.'%')
						->orWhere('products.slug', 'like', '%'.$search.'%')
						->orWhere('products.sale_price', 'like', '%'.$search.'%')
						->orWhere('products.sku', 'like', '%'.$search.'%')
						->orWhere('users.shop_name', 'like', '%'.$search.'%');
				})
				->orderBy('products.id', 'desc')
				->paginate(20);
		}
		
		for($i=0; $i<count($datalist); $i++){
			$Reviews = getReviews($datalist[$i]->id);
			$datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
			$datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
			$datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
		}
		
		return view('frontend.search', compact('datalist'));
	}
}
