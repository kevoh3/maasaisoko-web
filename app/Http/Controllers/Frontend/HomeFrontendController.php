<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Slider;
use App\Models\Pro_category;
use App\Models\Offer_ad;
use App\Models\Brand;
use App\Models\Tp_option;
use App\Models\Section_manage;

class HomeFrontendController extends Controller
{
	//Get Frontend Data
    public function homePageLoad(Request $request)
	{
		$lan = glan();
		
		$PageVariation = PageVariation();
		
		//Home Page 1
		if($PageVariation['home_variation'] == 'home_1'){
			//Home Page Section 1
			$section1 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_1')->where('is_publish', '=', 1)->first();
			if($section1 ==''){
				$section1_array =  array();
				$section1_array['image'] = '';
				$section1_array['is_publish'] = 2;
				$section1 = json_decode(json_encode($section1_array));
			}
			
			//Home Page Section 2
			$section2 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_2')->where('is_publish', '=', 1)->first();
			if($section2 ==''){
				$section2_array =  array();
				$section2_array['title'] = '';
				$section2_array['desc'] = '';
				$section2_array['is_publish'] = 2;
				$section2 = json_decode(json_encode($section2_array));
			}
			
			//Home Page Section 3
			$section3 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_3')->where('is_publish', '=', 1)->first();
			if($section3 ==''){
				$section3_array =  array();
				$section3_array['title'] = '';
				$section3_array['desc'] = '';
				$section3_array['is_publish'] = 2;
				$section3 = json_decode(json_encode($section3_array));
			}

			//Home Page Section 4
			$section4 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_4')->where('is_publish', '=', 1)->first();
			if($section4 ==''){
				$section4_array =  array();
				$section4_array['title'] = '';
				$section4_array['desc'] = '';
				$section4_array['image'] = '';
				$section4_array['is_publish'] = 2;
				$section4 = json_decode(json_encode($section4_array));
			}
			
			//Home Page Section 5
			$section5 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_5')->where('is_publish', '=', 1)->first();
			if($section5 ==''){
				$section5_array =  array();
				$section5_array['title'] = '';
				$section5_array['desc'] = '';
				$section5_array['image'] = '';
				$section5_array['is_publish'] = 2;
				$section5 = json_decode(json_encode($section5_array));
			}
			
			//Home Page Section 6
			$section6 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_6')->where('is_publish', '=', 1)->first();
			if($section6 ==''){
				$section6_array =  array();
				$section6_array['title'] = '';
				$section6_array['desc'] = '';
				$section6_array['image'] = '';
				$section6_array['is_publish'] = 2;
				$section6 = json_decode(json_encode($section6_array));
			}
			
			//Home Page Section 7
			$section7 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_7')->where('is_publish', '=', 1)->first();
			if($section7 ==''){
				$section7_array =  array();
				$section7_array['title'] = '';
				$section7_array['desc'] = '';
				$section7_array['image'] = '';
				$section7_array['is_publish'] = 2;
				$section7 = json_decode(json_encode($section7_array));
			}

			//Home Page Section 8
			$section8 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_8')->where('is_publish', '=', 1)->first();
			if($section8 ==''){
				$section8_array =  array();
				$section8_array['title'] = '';
				$section8_array['desc'] = '';
				$section8_array['image'] = '';
				$section8_array['is_publish'] = 2;
				$section8 = json_decode(json_encode($section8_array));
			}
			
			//Home Page Section 9
			$section9 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_9')->where('is_publish', '=', 1)->first();
			if($section9 ==''){
				$section9_array =  array();
				$section9_array['title'] = '';
				$section9_array['desc'] = '';
				$section9_array['image'] = '';
				$section9_array['is_publish'] = 2;
				$section9 = json_decode(json_encode($section9_array));
			}
			
			//Home Page Section 10
			$section10 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_10')->where('is_publish', '=', 1)->first();
			if($section10 ==''){
				$section10_array =  array();
				$section10_array['title'] = '';
				$section10_array['desc'] = '';
				$section10_array['image'] = '';
				$section10_array['is_publish'] = 2;
				$section10 = json_decode(json_encode($section10_array));
			}
			
			//Home Page Section 11
			$section11 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_11')->where('is_publish', '=', 1)->first();
			if($section11 ==''){
				$section11_array =  array();
				$section11_array['title'] = '';
				$section11_array['desc'] = '';
				$section11_array['image'] = '';
				$section11_array['is_publish'] = 2;
				$section11 = json_decode(json_encode($section11_array));
			}

			//Home Page Section 12
			$section12 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_12')->where('is_publish', '=', 1)->first();
			if($section12 ==''){
				$section12_array =  array();
				$section12_array['title'] = '';
				$section12_array['desc'] = '';
				$section12_array['image'] = '';
				$section12_array['is_publish'] = 2;
				$section12 = json_decode(json_encode($section12_array));
			}
			
			//Home Page Section 13
			$section13 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_13')->where('is_publish', '=', 1)->first();
			if($section13 ==''){
				$section13_array =  array();
				$section13_array['title'] = '';
				$section13_array['desc'] = '';
				$section13_array['image'] = '';
				$section13_array['is_publish'] = 2;
				$section13 = json_decode(json_encode($section13_array));
			}
			
			//Home Page Section 14
			$section14 = Section_manage::where('manage_type', '=', 'home_1')->where('section', '=', 'section_14')->where('is_publish', '=', 1)->first();
			if($section14 ==''){
				$section14_array =  array();
				$section14_array['title'] = '';
				$section14_array['desc'] = '';
				$section14_array['image'] = '';
				$section14_array['is_publish'] = 2;
				$section14 = json_decode(json_encode($section14_array));
			}
			
			//Slider
			$slider = Slider::where('slider_type', '=', 'home_1')->where('is_publish', '=', 1)->orderBy('id', 'desc')->get();

			//Product Category
			$pro_category = Pro_category::where('is_publish', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 1 (For Homepage 1)
			$offer_ad_position1 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position1_home1')->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 2 (For Homepage 1)
			$offer_ad_position2 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position2_home1')->orderBy('id', 'desc')->offset(0)->limit(1)->get();
			
			//Home Video Section
			$hv_data = Tp_option::where('option_name', 'home-video')->get();
			$id_home_video = '';
			foreach ($hv_data as $row){
				$id_home_video = $row->id;
			}

			$home_video = array();
			if($id_home_video != ''){
				$hvData = json_decode($hv_data);
				$dataObj = json_decode($hvData[0]->option_value);
				
				$home_video['title'] = $dataObj->title;
				$home_video['short_desc'] = $dataObj->short_desc;
				$home_video['url'] = $dataObj->url;
				$home_video['video_url'] = $dataObj->video_url;
				$home_video['button_text'] = $dataObj->button_text;
				$home_video['target'] = $dataObj->target;
				$home_video['image'] = $dataObj->image;
				$home_video['is_publish'] = $dataObj->is_publish;
			}else{
				$home_video['title'] = '';
				$home_video['short_desc'] = '';
				$home_video['url'] = '';
				$home_video['video_url'] = '';
				$home_video['button_text'] = '';
				$home_video['target'] = '';
				$home_video['image'] = '';
				$home_video['is_publish'] = '2';
			}

			//Brand
			$brand = Brand::where('is_publish', '=', 1)->where('is_featured', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Popular Products
			$pp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_featured = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			
			$popular_products = DB::select($pp_sql);
			for($i=0; $i<count($popular_products); $i++){
				$Reviews = getReviews($popular_products[$i]->id);
				$popular_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$popular_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$popular_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//New Products
			$np_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 8;";
			$new_products = DB::select($np_sql);
			
			for($i=0; $i<count($new_products); $i++){
				$Reviews = getReviews($new_products[$i]->id);
				$new_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$new_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$new_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Selling
			$top_sql = "SELECT COUNT(c.product_id) TotalSell, a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN order_items c ON a.id = c.product_id
			INNER JOIN order_masters d ON c.order_master_id = d.id
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			AND d.order_status_id = 4
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalSell DESC
			LIMIT 8;";

			$top_selling = DB::select($top_sql);
			
			for($i=0; $i<count($top_selling); $i++){
				$Reviews = getReviews($top_selling[$i]->id);
				$top_selling[$i]->TotalReview = $Reviews[0]->TotalReview;
				$top_selling[$i]->TotalRating = $Reviews[0]->TotalRating;
				$top_selling[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Trending Products
			$tp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.collection_id = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 8;";
			$trending_products = DB::select($tp_sql);
			
			for($i=0; $i<count($trending_products); $i++){
				$Reviews = getReviews($trending_products[$i]->id);
				$trending_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$trending_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$trending_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Rated
			$tr_sql = "SELECT a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url, 
			COUNT(c.id) TotalReview, SUM(IFNULL(c.rating, 0)) TotalRating, (SUM(IFNULL(c.rating, 0))/COUNT(c.id))*20 ReviewPercentage
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN reviews c ON a.id = c.item_id
			WHERE a.is_publish = 1
			AND a.lan = '".$lan."'
			AND c.rating = 5
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalReview DESC
			LIMIT 8;";
			$top_rated = DB::select($tr_sql);
			
			//Deals Of The Day
			$dofd_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_discount = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC;";
			$deals_products = DB::select($dofd_sql);
			
			for($i=0; $i<count($deals_products); $i++){
				$Reviews = getReviews($deals_products[$i]->id);
				$deals_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$deals_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$deals_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
		
		//Home Page 2
		}elseif($PageVariation['home_variation'] == 'home_2'){
			//Home Page Section 1
			$section1 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_1')->where('is_publish', '=', 1)->first();
			if($section1 ==''){
				$section1_array =  array();
				$section1_array['image'] = '';
				$section1_array['is_publish'] = 2;
				$section1 = json_decode(json_encode($section1_array));
			}
			
			//Home Page Section 2
			$section2 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_2')->where('is_publish', '=', 1)->first();
			if($section2 ==''){
				$section2_array =  array();
				$section2_array['title'] = '';
				$section2_array['desc'] = '';
				$section2_array['is_publish'] = 2;
				$section2 = json_decode(json_encode($section2_array));
			}
			
			//Home Page Section 3
			$section3 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_3')->where('is_publish', '=', 1)->first();
			if($section3 ==''){
				$section3_array =  array();
				$section3_array['title'] = '';
				$section3_array['desc'] = '';
				$section3_array['is_publish'] = 2;
				$section3 = json_decode(json_encode($section3_array));
			}

			//Home Page Section 4
			$section4 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_4')->where('is_publish', '=', 1)->first();
			if($section4 ==''){
				$section4_array =  array();
				$section4_array['title'] = '';
				$section4_array['desc'] = '';
				$section4_array['image'] = '';
				$section4_array['is_publish'] = 2;
				$section4 = json_decode(json_encode($section4_array));
			}
			
			//Home Page Section 5
			$section5 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_5')->where('is_publish', '=', 1)->first();
			if($section5 ==''){
				$section5_array =  array();
				$section5_array['title'] = '';
				$section5_array['desc'] = '';
				$section5_array['image'] = '';
				$section5_array['is_publish'] = 2;
				$section5 = json_decode(json_encode($section5_array));
			}
			
			//Home Page Section 6
			$section6 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_6')->where('is_publish', '=', 1)->first();
			if($section6 ==''){
				$section6_array =  array();
				$section6_array['title'] = '';
				$section6_array['desc'] = '';
				$section6_array['image'] = '';
				$section6_array['is_publish'] = 2;
				$section6 = json_decode(json_encode($section6_array));
			}
			
			//Home Page Section 7
			$section7 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_7')->where('is_publish', '=', 1)->first();
			if($section7 ==''){
				$section7_array =  array();
				$section7_array['title'] = '';
				$section7_array['desc'] = '';
				$section7_array['image'] = '';
				$section7_array['is_publish'] = 2;
				$section7 = json_decode(json_encode($section7_array));
			}
			
			//Home Page Section 8
			$section8 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_8')->where('is_publish', '=', 1)->first();
			if($section8 ==''){
				$section8_array =  array();
				$section8_array['title'] = '';
				$section8_array['desc'] = '';
				$section8_array['image'] = '';
				$section8_array['is_publish'] = 2;
				$section8 = json_decode(json_encode($section8_array));
			}
			
			//Home Page Section 9
			$section9 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_9')->where('is_publish', '=', 1)->first();
			if($section9 ==''){
				$section9_array =  array();
				$section9_array['title'] = '';
				$section9_array['desc'] = '';
				$section9_array['image'] = '';
				$section9_array['is_publish'] = 2;
				$section9 = json_decode(json_encode($section9_array));
			}
			
			//Home Page Section 10
			$section10 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_10')->where('is_publish', '=', 1)->first();
			if($section10 ==''){
				$section10_array =  array();
				$section10_array['title'] = '';
				$section10_array['desc'] = '';
				$section10_array['image'] = '';
				$section10_array['is_publish'] = 2;
				$section10 = json_decode(json_encode($section10_array));
			}
			
			//Home Page Section 11
			$section11 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_11')->where('is_publish', '=', 1)->first();
			if($section11 ==''){
				$section11_array =  array();
				$section11_array['title'] = '';
				$section11_array['desc'] = '';
				$section11_array['image'] = '';
				$section11_array['is_publish'] = 2;
				$section11 = json_decode(json_encode($section11_array));
			}

			//Home Page Section 12
			$section12 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_12')->where('is_publish', '=', 1)->first();
			if($section12 ==''){
				$section12_array =  array();
				$section12_array['title'] = '';
				$section12_array['desc'] = '';
				$section12_array['image'] = '';
				$section12_array['is_publish'] = 2;
				$section12 = json_decode(json_encode($section12_array));
			}
			
			//Home Page Section 13
			$section13 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_13')->where('is_publish', '=', 1)->first();
			if($section13 ==''){
				$section13_array =  array();
				$section13_array['title'] = '';
				$section13_array['desc'] = '';
				$section13_array['image'] = '';
				$section13_array['is_publish'] = 2;
				$section13 = json_decode(json_encode($section13_array));
			}
			
			//Home Page Section 14
			$section14 = Section_manage::where('manage_type', '=', 'home_2')->where('section', '=', 'section_14')->where('is_publish', '=', 1)->first();
			if($section14 ==''){
				$section14_array =  array();
				$section14_array['title'] = '';
				$section14_array['desc'] = '';
				$section14_array['image'] = '';
				$section14_array['is_publish'] = 2;
				$section14 = json_decode(json_encode($section14_array));
			}
			
			//Slider
			$slider = Slider::where('slider_type', '=', 'home_2')->where('is_publish', '=', 1)->orderBy('id', 'desc')->get();

			//Product Category
			$pro_category = Pro_category::where('is_publish', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 1 (For Homepage 1)
			$offer_ad_position1 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position1_home1')->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 2 (For Homepage 1)
			$offer_ad_position2 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position2_home1')->orderBy('id', 'desc')->offset(0)->limit(1)->get();
			
			//Home Video Section
			$hv_data = Tp_option::where('option_name', 'home-video')->get();
			$id_home_video = '';
			foreach ($hv_data as $row){
				$id_home_video = $row->id;
			}

			$home_video = array();
			if($id_home_video != ''){
				$hvData = json_decode($hv_data);
				$dataObj = json_decode($hvData[0]->option_value);
				
				$home_video['title'] = $dataObj->title;
				$home_video['short_desc'] = $dataObj->short_desc;
				$home_video['url'] = $dataObj->url;
				$home_video['video_url'] = $dataObj->video_url;
				$home_video['button_text'] = $dataObj->button_text;
				$home_video['target'] = $dataObj->target;
				$home_video['image'] = $dataObj->image;
				$home_video['is_publish'] = $dataObj->is_publish;
			}else{
				$home_video['title'] = '';
				$home_video['short_desc'] = '';
				$home_video['url'] = '';
				$home_video['video_url'] = '';
				$home_video['button_text'] = '';
				$home_video['target'] = '';
				$home_video['image'] = '';
				$home_video['is_publish'] = '2';
			}

			//Brand
			$brand = Brand::where('is_publish', '=', 1)->where('is_featured', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Popular Products
			$pp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_featured = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			
			$popular_products = DB::select($pp_sql);
			for($i=0; $i<count($popular_products); $i++){
				$Reviews = getReviews($popular_products[$i]->id);
				$popular_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$popular_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$popular_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//New Products
			$np_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";

			$new_products = DB::select($np_sql);
			
			for($i=0; $i<count($new_products); $i++){
				$Reviews = getReviews($new_products[$i]->id);
				$new_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$new_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$new_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Selling
			$top_sql = "SELECT COUNT(c.product_id) TotalSell, a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN order_items c ON a.id = c.product_id
			INNER JOIN order_masters d ON c.order_master_id = d.id
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			AND d.order_status_id = 4
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalSell DESC
			LIMIT 15;";

			$top_selling = DB::select($top_sql);
			for($i=0; $i<count($top_selling); $i++){
				$Reviews = getReviews($top_selling[$i]->id);
				$top_selling[$i]->TotalReview = $Reviews[0]->TotalReview;
				$top_selling[$i]->TotalRating = $Reviews[0]->TotalRating;
				$top_selling[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Trending Products
			$tp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.collection_id = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$trending_products = DB::select($tp_sql);
			
			for($i=0; $i<count($trending_products); $i++){
				$Reviews = getReviews($trending_products[$i]->id);
				$trending_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$trending_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$trending_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Rated
			$tr_sql = "SELECT a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url, 
			COUNT(c.id) TotalReview, SUM(IFNULL(c.rating, 0)) TotalRating, (SUM(IFNULL(c.rating, 0))/COUNT(c.id))*20 ReviewPercentage
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN reviews c ON a.id = c.item_id
			WHERE a.is_publish = 1
			AND a.lan = '".$lan."'
			AND c.rating = 5
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalReview DESC
			LIMIT 15;";
			$top_rated = DB::select($tr_sql);
			
			//Deals Of The Day
			$dofd_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_discount = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC;";
			$deals_products = DB::select($dofd_sql);
			
			for($i=0; $i<count($deals_products); $i++){
				$Reviews = getReviews($deals_products[$i]->id);
				$deals_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$deals_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$deals_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
		
		//Home Page 3
		}elseif($PageVariation['home_variation'] == 'home_3'){
			//Home Page Section 1
			$section1 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_1')->where('is_publish', '=', 1)->first();
			if($section1 ==''){
				$section1_array =  array();
				$section1_array['image'] = '';
				$section1_array['is_publish'] = 2;
				$section1 = json_decode(json_encode($section1_array));
			}
			
			//Home Page Section 2
			$section2 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_2')->where('is_publish', '=', 1)->first();
			if($section2 ==''){
				$section2_array =  array();
				$section2_array['title'] = '';
				$section2_array['desc'] = '';
				$section2_array['is_publish'] = 2;
				$section2 = json_decode(json_encode($section2_array));
			}
			
			//Home Page Section 3
			$section3 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_3')->where('is_publish', '=', 1)->first();
			if($section3 ==''){
				$section3_array =  array();
				$section3_array['title'] = '';
				$section3_array['desc'] = '';
				$section3_array['is_publish'] = 2;
				$section3 = json_decode(json_encode($section3_array));
			}

			//Home Page Section 4
			$section4 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_4')->where('is_publish', '=', 1)->first();
			if($section4 ==''){
				$section4_array =  array();
				$section4_array['title'] = '';
				$section4_array['desc'] = '';
				$section4_array['image'] = '';
				$section4_array['is_publish'] = 2;
				$section4 = json_decode(json_encode($section4_array));
			}
			
			//Home Page Section 5
			$section5 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_5')->where('is_publish', '=', 1)->first();
			if($section5 ==''){
				$section5_array =  array();
				$section5_array['title'] = '';
				$section5_array['desc'] = '';
				$section5_array['image'] = '';
				$section5_array['is_publish'] = 2;
				$section5 = json_decode(json_encode($section5_array));
			}
			
			//Home Page Section 6
			$section6 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_6')->where('is_publish', '=', 1)->first();
			if($section6 ==''){
				$section6_array =  array();
				$section6_array['title'] = '';
				$section6_array['desc'] = '';
				$section6_array['image'] = '';
				$section6_array['is_publish'] = 2;
				$section6 = json_decode(json_encode($section6_array));
			}
			
			//Home Page Section 7
			$section7 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_7')->where('is_publish', '=', 1)->first();
			if($section7 ==''){
				$section7_array =  array();
				$section7_array['title'] = '';
				$section7_array['desc'] = '';
				$section7_array['image'] = '';
				$section7_array['is_publish'] = 2;
				$section7 = json_decode(json_encode($section7_array));
			}
			
			//Home Page Section 8
			$section8 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_8')->where('is_publish', '=', 1)->first();
			if($section8 ==''){
				$section8_array =  array();
				$section8_array['title'] = '';
				$section8_array['desc'] = '';
				$section8_array['image'] = '';
				$section8_array['is_publish'] = 2;
				$section8 = json_decode(json_encode($section8_array));
			}
			
			//Home Page Section 9
			$section9 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_9')->where('is_publish', '=', 1)->first();
			if($section9 ==''){
				$section9_array =  array();
				$section9_array['title'] = '';
				$section9_array['desc'] = '';
				$section9_array['image'] = '';
				$section9_array['is_publish'] = 2;
				$section9 = json_decode(json_encode($section9_array));
			}
			
			//Home Page Section 10
			$section10 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_10')->where('is_publish', '=', 1)->first();
			if($section10 ==''){
				$section10_array =  array();
				$section10_array['title'] = '';
				$section10_array['desc'] = '';
				$section10_array['image'] = '';
				$section10_array['is_publish'] = 2;
				$section10 = json_decode(json_encode($section10_array));
			}
			
			//Home Page Section 11
			$section11 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_11')->where('is_publish', '=', 1)->first();
			if($section11 ==''){
				$section11_array =  array();
				$section11_array['title'] = '';
				$section11_array['desc'] = '';
				$section11_array['image'] = '';
				$section11_array['is_publish'] = 2;
				$section11 = json_decode(json_encode($section11_array));
			}

			//Home Page Section 12
			$section12 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_12')->where('is_publish', '=', 1)->first();
			if($section12 ==''){
				$section12_array =  array();
				$section12_array['title'] = '';
				$section12_array['desc'] = '';
				$section12_array['image'] = '';
				$section12_array['is_publish'] = 2;
				$section12 = json_decode(json_encode($section12_array));
			}
			
			//Home Page Section 13
			$section13 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_13')->where('is_publish', '=', 1)->first();
			if($section13 ==''){
				$section13_array =  array();
				$section13_array['title'] = '';
				$section13_array['desc'] = '';
				$section13_array['image'] = '';
				$section13_array['is_publish'] = 2;
				$section13 = json_decode(json_encode($section13_array));
			}
			
			//Home Page Section 14
			$section14 = Section_manage::where('manage_type', '=', 'home_3')->where('section', '=', 'section_14')->where('is_publish', '=', 1)->first();
			if($section14 ==''){
				$section14_array =  array();
				$section14_array['title'] = '';
				$section14_array['desc'] = '';
				$section14_array['image'] = '';
				$section14_array['is_publish'] = 2;
				$section14 = json_decode(json_encode($section14_array));
			}

			//Slider
			$slider = Slider::where('slider_type', '=', 'home_3')->where('is_publish', '=', 1)->orderBy('id', 'desc')->get();

			//Product Category
			$pro_category = Pro_category::where('is_publish', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 1 (For Homepage 1)
			$offer_ad_position1 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position1_home1')->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 2 (For Homepage 1)
			$offer_ad_position2 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position2_home1')->orderBy('id', 'desc')->offset(0)->limit(1)->get();
			
			//Home Video Section
			$hv_data = Tp_option::where('option_name', 'home-video')->get();
			$id_home_video = '';
			foreach ($hv_data as $row){
				$id_home_video = $row->id;
			}

			$home_video = array();
			if($id_home_video != ''){
				$hvData = json_decode($hv_data);
				$dataObj = json_decode($hvData[0]->option_value);
				
				$home_video['title'] = $dataObj->title;
				$home_video['short_desc'] = $dataObj->short_desc;
				$home_video['url'] = $dataObj->url;
				$home_video['video_url'] = $dataObj->video_url;
				$home_video['button_text'] = $dataObj->button_text;
				$home_video['target'] = $dataObj->target;
				$home_video['image'] = $dataObj->image;
				$home_video['is_publish'] = $dataObj->is_publish;
			}else{
				$home_video['title'] = '';
				$home_video['short_desc'] = '';
				$home_video['url'] = '';
				$home_video['video_url'] = '';
				$home_video['button_text'] = '';
				$home_video['target'] = '';
				$home_video['image'] = '';
				$home_video['is_publish'] = '2';
			}

			//Brand
			$brand = Brand::where('is_publish', '=', 1)->where('is_featured', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Popular Products
			$pp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_featured = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$popular_products = DB::select($pp_sql);
			for($i=0; $i<count($popular_products); $i++){
				$Reviews = getReviews($popular_products[$i]->id);
				$popular_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$popular_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$popular_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//New Products
			$np_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$new_products = DB::select($np_sql);
			
			for($i=0; $i<count($new_products); $i++){
				$Reviews = getReviews($new_products[$i]->id);
				$new_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$new_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$new_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Selling
			$top_sql = "SELECT COUNT(c.product_id) TotalSell, a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN order_items c ON a.id = c.product_id
			INNER JOIN order_masters d ON c.order_master_id = d.id
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			AND d.order_status_id = 4
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalSell DESC
			LIMIT 15;";
			$top_selling = DB::select($top_sql);
			
			for($i=0; $i<count($top_selling); $i++){
				$Reviews = getReviews($top_selling[$i]->id);
				$top_selling[$i]->TotalReview = $Reviews[0]->TotalReview;
				$top_selling[$i]->TotalRating = $Reviews[0]->TotalRating;
				$top_selling[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Trending Products
			$tp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.collection_id = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$trending_products = DB::select($tp_sql);
			
			for($i=0; $i<count($trending_products); $i++){
				$Reviews = getReviews($trending_products[$i]->id);
				$trending_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$trending_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$trending_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Rated
			$tr_sql = "SELECT a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url, 
			COUNT(c.id) TotalReview, SUM(IFNULL(c.rating, 0)) TotalRating, (SUM(IFNULL(c.rating, 0))/COUNT(c.id))*20 ReviewPercentage
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN reviews c ON a.id = c.item_id
			WHERE a.is_publish = 1
			AND a.lan = '".$lan."'
			AND c.rating = 5
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalReview DESC
			LIMIT 15;";
			$top_rated = DB::select($tr_sql);
			
			//Deals Of The Day
			$dofd_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_discount = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC;";
			$deals_products = DB::select($dofd_sql);
			
			for($i=0; $i<count($deals_products); $i++){
				$Reviews = getReviews($deals_products[$i]->id);
				$deals_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$deals_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$deals_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
		
		//Home Page 4
		}elseif($PageVariation['home_variation'] == 'home_4'){
			//Home Page Section 1
			$section1 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_1')->where('is_publish', '=', 1)->first();
			if($section1 ==''){
				$section1_array =  array();
				$section1_array['image'] = '';
				$section1_array['is_publish'] = 2;
				$section1 = json_decode(json_encode($section1_array));
			}
			
			//Home Page Section 2
			$section2 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_2')->where('is_publish', '=', 1)->first();
			if($section2 ==''){
				$section2_array =  array();
				$section2_array['title'] = '';
				$section2_array['desc'] = '';
				$section2_array['is_publish'] = 2;
				$section2 = json_decode(json_encode($section2_array));
			}
			
			//Home Page Section 3
			$section3 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_3')->where('is_publish', '=', 1)->first();
			if($section3 ==''){
				$section3_array =  array();
				$section3_array['title'] = '';
				$section3_array['desc'] = '';
				$section3_array['is_publish'] = 2;
				$section3 = json_decode(json_encode($section3_array));
			}

			//Home Page Section 4
			$section4 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_4')->where('is_publish', '=', 1)->first();
			if($section4 ==''){
				$section4_array =  array();
				$section4_array['title'] = '';
				$section4_array['desc'] = '';
				$section4_array['image'] = '';
				$section4_array['is_publish'] = 2;
				$section4 = json_decode(json_encode($section4_array));
			}
			
			//Home Page Section 5
			$section5 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_5')->where('is_publish', '=', 1)->first();
			if($section5 ==''){
				$section5_array =  array();
				$section5_array['title'] = '';
				$section5_array['desc'] = '';
				$section5_array['image'] = '';
				$section5_array['is_publish'] = 2;
				$section5 = json_decode(json_encode($section5_array));
			}
			
			//Home Page Section 6
			$section6 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_6')->where('is_publish', '=', 1)->first();
			if($section6 ==''){
				$section6_array =  array();
				$section6_array['title'] = '';
				$section6_array['desc'] = '';
				$section6_array['image'] = '';
				$section6_array['is_publish'] = 2;
				$section6 = json_decode(json_encode($section6_array));
			}
			
			//Home Page Section 7
			$section7 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_7')->where('is_publish', '=', 1)->first();
			if($section7 ==''){
				$section7_array =  array();
				$section7_array['title'] = '';
				$section7_array['desc'] = '';
				$section7_array['image'] = '';
				$section7_array['is_publish'] = 2;
				$section7 = json_decode(json_encode($section7_array));
			}
			
			//Home Page Section 8
			$section8 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_8')->where('is_publish', '=', 1)->first();
			if($section8 ==''){
				$section8_array =  array();
				$section8_array['title'] = '';
				$section8_array['desc'] = '';
				$section8_array['image'] = '';
				$section8_array['is_publish'] = 2;
				$section8 = json_decode(json_encode($section8_array));
			}
			
			//Home Page Section 9
			$section9 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_9')->where('is_publish', '=', 1)->first();
			if($section9 ==''){
				$section9_array =  array();
				$section9_array['title'] = '';
				$section9_array['desc'] = '';
				$section9_array['image'] = '';
				$section9_array['is_publish'] = 2;
				$section9 = json_decode(json_encode($section9_array));
			}
			
			//Home Page Section 10
			$section10 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_10')->where('is_publish', '=', 1)->first();
			if($section10 ==''){
				$section10_array =  array();
				$section10_array['title'] = '';
				$section10_array['desc'] = '';
				$section10_array['image'] = '';
				$section10_array['is_publish'] = 2;
				$section10 = json_decode(json_encode($section10_array));
			}
			
			//Home Page Section 11
			$section11 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_11')->where('is_publish', '=', 1)->first();
			if($section11 ==''){
				$section11_array =  array();
				$section11_array['title'] = '';
				$section11_array['desc'] = '';
				$section11_array['image'] = '';
				$section11_array['is_publish'] = 2;
				$section11 = json_decode(json_encode($section11_array));
			}
			
			//Home Page Section 12
			$section12 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_12')->where('is_publish', '=', 1)->first();
			if($section12 ==''){
				$section12_array =  array();
				$section12_array['title'] = '';
				$section12_array['desc'] = '';
				$section12_array['image'] = '';
				$section12_array['is_publish'] = 2;
				$section12 = json_decode(json_encode($section12_array));
			}
			
			//Home Page Section 13
			$section13 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_13')->where('is_publish', '=', 1)->first();
			if($section13 ==''){
				$section13_array =  array();
				$section13_array['title'] = '';
				$section13_array['desc'] = '';
				$section13_array['image'] = '';
				$section13_array['is_publish'] = 2;
				$section13 = json_decode(json_encode($section13_array));
			}
			
			//Home Page Section 14
			$section14 = Section_manage::where('manage_type', '=', 'home_4')->where('section', '=', 'section_14')->where('is_publish', '=', 1)->first();
			if($section14 ==''){
				$section14_array =  array();
				$section14_array['title'] = '';
				$section14_array['desc'] = '';
				$section14_array['image'] = '';
				$section14_array['is_publish'] = 2;
				$section14 = json_decode(json_encode($section14_array));
			}
			
			//Slider
			$slider = array();
			$slider['slider1'] = Slider::where('slider_type', '=', 'home_4')->where('is_publish', '=', 1)->orderBy('id', 'desc')->offset(0)->limit(1)->get();
			$slider['slider2'] = Slider::where('slider_type', '=', 'home_4')->where('is_publish', '=', 1)->orderBy('id', 'desc')->offset(1)->limit(4)->get();

			//Product Category
			$pro_category = Pro_category::where('is_publish', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 1 (For Homepage 1)
			$offer_ad_position1 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position1_home1')->orderBy('id', 'desc')->get();
			
			//Offer & Ads - Position 2 (For Homepage 1)
			$offer_ad_position2 = Offer_ad::where('is_publish', '=', 1)->where('offer_ad_type', '=', 'position2_home1')->orderBy('id', 'desc')->offset(0)->limit(1)->get();
			
			//Home Video Section
			$hv_data = Tp_option::where('option_name', 'home-video')->get();
			$id_home_video = '';
			foreach ($hv_data as $row){
				$id_home_video = $row->id;
			}

			$home_video = array();
			if($id_home_video != ''){
				$hvData = json_decode($hv_data);
				$dataObj = json_decode($hvData[0]->option_value);
				
				$home_video['title'] = $dataObj->title;
				$home_video['short_desc'] = $dataObj->short_desc;
				$home_video['url'] = $dataObj->url;
				$home_video['video_url'] = $dataObj->video_url;
				$home_video['button_text'] = $dataObj->button_text;
				$home_video['target'] = $dataObj->target;
				$home_video['image'] = $dataObj->image;
				$home_video['is_publish'] = $dataObj->is_publish;
			}else{
				$home_video['title'] = '';
				$home_video['short_desc'] = '';
				$home_video['url'] = '';
				$home_video['video_url'] = '';
				$home_video['button_text'] = '';
				$home_video['target'] = '';
				$home_video['image'] = '';
				$home_video['is_publish'] = '2';
			}

			//Brand
			$brand = Brand::where('is_publish', '=', 1)->where('is_featured', '=', 1)->where('lan', '=', $lan)->orderBy('id', 'desc')->get();
			
			//Popular Products
			$pp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_featured = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$popular_products = DB::select($pp_sql);
			
			for($i=0; $i<count($popular_products); $i++){
				$Reviews = getReviews($popular_products[$i]->id);
				$popular_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$popular_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$popular_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//New Products
			$np_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$new_products = DB::select($np_sql);
			
			for($i=0; $i<count($new_products); $i++){
				$Reviews = getReviews($new_products[$i]->id);
				$new_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$new_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$new_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Selling
			$top_sql = "SELECT COUNT(c.product_id) TotalSell, a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN order_items c ON a.id = c.product_id
			INNER JOIN order_masters d ON c.order_master_id = d.id
			WHERE a.is_publish = 1 
			AND a.lan = '".$lan."'
			AND d.order_status_id = 4
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalSell DESC
			LIMIT 15;";
			$top_selling = DB::select($top_sql);
			
			for($i=0; $i<count($top_selling); $i++){
				$Reviews = getReviews($top_selling[$i]->id);
				$top_selling[$i]->TotalReview = $Reviews[0]->TotalReview;
				$top_selling[$i]->TotalRating = $Reviews[0]->TotalRating;
				$top_selling[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Trending Products
			$tp_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.collection_id = 1
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC LIMIT 15;";
			$trending_products = DB::select($tp_sql);
			
			for($i=0; $i<count($trending_products); $i++){
				$Reviews = getReviews($trending_products[$i]->id);
				$trending_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$trending_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$trending_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}
			
			//Top Rated
			$tr_sql = "SELECT a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url, 
			COUNT(c.id) TotalReview, SUM(IFNULL(c.rating, 0)) TotalRating, (SUM(IFNULL(c.rating, 0))/COUNT(c.id))*20 ReviewPercentage
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			INNER JOIN reviews c ON a.id = c.item_id
			WHERE a.is_publish = 1
			AND a.lan = '".$lan."'
			AND c.rating = 5
			GROUP BY a.id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id, b.shop_url
			ORDER BY TotalReview DESC
			LIMIT 15;";
			$top_rated = DB::select($tr_sql);
			
			//Deals Of The Day
			$dofd_sql = "SELECT a.id, a.brand_id, a.title, a.slug, a.f_thumbnail, a.sale_price, a.old_price, a.end_date, a.is_discount, b.shop_name, b.id seller_id, b.shop_url
			FROM products a
			INNER JOIN users b ON a.user_id = b.id AND b.status_id = 1
			WHERE a.is_publish = 1 
			AND a.is_discount = 1 
			AND a.lan = '".$lan."'
			ORDER BY a.id DESC;";
			$deals_products = DB::select($dofd_sql);
			
			for($i=0; $i<count($deals_products); $i++){
				$Reviews = getReviews($deals_products[$i]->id);
				$deals_products[$i]->TotalReview = $Reviews[0]->TotalReview;
				$deals_products[$i]->TotalRating = $Reviews[0]->TotalRating;
				$deals_products[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
			}			
		}

        return view('frontend.home', compact(
			'section1', 
			'section2', 
			'section3', 
			'section4', 
			'section5', 
			'section6',
			'section7',
			'section8', 
			'section9', 
			'section10', 
			'section11',
			'section12',
			'section13',
			'section14',
			'slider', 
			'pro_category', 
			'offer_ad_position1', 
			'offer_ad_position2', 
			'home_video', 
			'brand', 
			'popular_products', 
			'new_products', 
			'top_selling', 
			'trending_products', 
			'top_rated', 
			'deals_products'
		));
    }
}
