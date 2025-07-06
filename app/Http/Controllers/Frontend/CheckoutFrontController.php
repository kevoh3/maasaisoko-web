<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order_master;
use App\Models\Order_item;
use App\Models\Country;
use App\Models\Shipping;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Services\PayPalService;

use Razorpay\Api\Api;
use Mollie\Laravel\Facades\Mollie;

class CheckoutFrontController extends Controller
{
	
    protected $PayPalClient;

    public function __construct(PayPalService $PayPalClient)
    {
        $this->PayPalClient = $PayPalClient;
    }
	
    public function LoadCheckout()
    {
		$country_list = Country::where('is_publish', '=', 1)->orderBy('country_name', 'ASC')->get();
		$shipping_list = Shipping::where('is_publish', '=', 1)->get();
				
        return view('frontend.checkout', compact('country_list', 'shipping_list'));
    }
	
    public function LoadThank()
    {	
        return view('frontend.thank');
    }
	
    public function LoadMakeOrder(Request $request)
    {
		$res = array();
		$gtext = gtext();
		$gtax = getTax();
		$tax_rate = $gtax['percentage'];

		Session::forget('pt_payment_error');
		
		$base_url = url('/');
		
		$CartDataList = session()->get('shopping_cart');
		
		$total_qty = 0;
		$TotalPrice = 0;

		if(session()->get('shopping_cart')){
			foreach ($CartDataList as $row) {
				$total_qty += $row['qty'];
				$TotalPrice += $row['price']*$row['qty'];
			}
		}
		
		$TaxCal = ($TotalPrice*$tax_rate)/100;
		$total_amount = $TotalPrice+$TaxCal;
		
		if($total_qty == 0){
			$res['msgType'] = 'error';
			$res['msg'] = array('oneError' => array(__('Oops! Your order is failed. Please product add to cart.')));
			return response()->json($res);
		}
		
		$CustomerId = '';
		
		$newaccount = $request->input('new_account');
		
		if ($newaccount == 'true' || $newaccount == 'on') {
			$new_account = 1;
		}else {
			$new_account = 0;
		}

		$payment_method_id = $request->input('payment_method');
		$shipping_method_id = $request->input('shipping_method');

		if($new_account == 1){
			
			$validator = Validator::make($request->all(),[
				'name' => 'required',
				'phone' => 'required',
				'country' => 'required',
				'state' => 'required',
				'zip_code' => 'required',
				'city' => 'required',
				'address' => 'required',
				'payment_method' => 'required',
				'shipping_method' => 'required',
				'email' => 'required|email|unique:users',
				'password' => 'required|confirmed',
			]);

			if(!$validator->passes()){
				$res['msgType'] = 'error';
				$res['msg'] = $validator->errors()->toArray();
				return response()->json($res);
			}

			$userData = array(
				'name' => $request->input('name'),
				'email' => $request->input('email'),
				'phone' => $request->input('phone'),
				'address' => $request->input('address'),
				'state' => $request->input('state'),
				'zip_code' => $request->input('zip_code'),
				'city' => $request->input('city'),
				'password' => Hash::make($request->input('password')),
				'bactive' => base64_encode($request->input('password')),
				'status_id' => 1,
				'role_id' => 2
			);
			
			$CustomerId = User::create($userData)->id;
			
		}else{
			
			$validator = Validator::make($request->all(),[
				'name' => 'required',
				'email' => 'required',
				'phone' => 'required',
				'country' => 'required',
				'state' => 'required',
				'zip_code' => 'required',
				'city' => 'required',
				'address' => 'required',
				'payment_method' => 'required',
				'shipping_method' => 'required'
			]);
			
			if(!$validator->passes()){
				$res['msgType'] = 'error';
				$res['msg'] = $validator->errors()->toArray();
				return response()->json($res);
			}

			$CustomerId = $request->input('customer_id');
		}
		
		if($CustomerId == '') {
			$customer_id = NULL;
		}else {
			$customer_id = $CustomerId;
		}
		
		$shipping_list = Shipping::where('id', '=', $shipping_method_id)->where('is_publish', '=', 1)->get();
		$shipping_title = NULL;
		$shipping_fee = NULL;
		foreach ($shipping_list as $row){
			$shipping_title = $row->title;
			$shipping_fee = comma_remove($row->shipping_fee);
		}

		$UniqueDataArray = array();
		$key = 0;
		foreach($CartDataList as $row){
			
			$UniqueDataArray[$key] = $row['seller_id'];
			
			$key++;
		}
		
		$UniqueDataList = array_unique($UniqueDataArray);
		
		$MasterData = array();
		$OrderNoArr = array();
		
		$i = 1;
		foreach($UniqueDataList as $row){
			
			$random_code = random_int(100000, 999999);
			
			$order_no = 'ORD-'.$random_code.$i;
			$OrderNoArr[] = $order_no;
			
			$seller_id = $row;
			$data = array(
				'order_no' => $order_no,
				'customer_id' => $customer_id,
				'seller_id' => $seller_id,
				'payment_method_id' => $payment_method_id,
				'payment_status_id' => 2,
				'order_status_id' => 1,
				'shipping_title' => $shipping_title,
				'shipping_fee' => $shipping_fee,
				'name' => $request->input('name'),
				'email' => $request->input('email'),
				'phone' => $request->input('phone'),
				'country' => $request->input('country'),
				'state' => $request->input('state'),
				'zip_code' => $request->input('zip_code'),
				'city' => $request->input('city'),
				'address' => $request->input('address'),
				'comments' => $request->input('comments')
			);
			
			$order_master_id = Order_master::create($data)->id;
			
			$i++;
			
			$MasterData[$seller_id] = $order_master_id;
		}

		//set order master ids into session
		Session::put('order_master_ids', $MasterData);

		$index = 0;
		$total_tax = 0;
		foreach($CartDataList as $row){

			$seller_id = $row['seller_id'];
			$order_master_id = $MasterData[$seller_id];
			
			$total_price = $row['price']*$row['qty'];
			
			$total_tax = (($total_price*$tax_rate)/100);
			
			$OrderItemData = array(
				'order_master_id' => $order_master_id,
				'customer_id' => $customer_id,
				'seller_id' => $seller_id,
				'product_id' => $row['id'],
				'variation_size' => $row['unit'],
				'quantity' => comma_remove($row['qty']),
				'price' => comma_remove($row['price']),
				'total_price' => comma_remove($total_price),
				'tax' => comma_remove($total_tax)
			);
			
			Order_item::create($OrderItemData);
			
			$index++;
		}
		
		if($index>0){
			$intent = '';
			
			$sellerCount = 0;
			
			$OrderNoStr = implode(', ', $OrderNoArr);
			$total_qty = comma_remove($total_qty);
			$description = 'Total Quantity:'.$total_qty.', Order No:'. $OrderNoStr;

			$sellerCount = count($UniqueDataList);
		
			if($shipping_fee ==''){
				$shippingFee = 0; 
			}else{
				$shippingFee = $sellerCount * $shipping_fee;
			}
			
			$t_amount = comma_remove($total_amount);
			
			$totalAmount = $t_amount + $shippingFee;

			//Stripe
			if($payment_method_id == 3){
				if($gtext['stripe_isenable'] == 1){
					$stripe_secret = $gtext['stripe_secret'];
					
					// Enter Your Stripe Secret
					\Stripe\Stripe::setApiKey($stripe_secret);
							
					$amount = $totalAmount;
					$amount *= 100;
					$amount = (int) $amount;
					if($gtext['stripe_currency'] !=''){
						$currency = $gtext['stripe_currency'];
					}else{
						$currency = 'usd';
					}
					
					$payment_intent = \Stripe\PaymentIntent::create([
						'amount' => $amount,
						'currency' => $currency,
						'description' => $description,
						'payment_method_types' => ['card']
					]);
					$intent = $payment_intent->client_secret;
				}
				
			//Paypal
			}elseif($payment_method_id == 4){
				
				if($gtext['isenable_paypal'] == 1){
					
					$PayPalData = [
						'intent' => 'CAPTURE',
						"application_context" => [
							"return_url" => route('success.PayPalPayment'),
							"cancel_url" => route('cancel.PayPalPayment'),
						],
					   "purchase_units" => [
							0 => [
								"amount" => [
									"currency_code" => $gtext['paypal_currency'],
									"value" => "{$totalAmount}",
								],
								"description" => $description
							]
						]
					];

					$accessToken = $this->PayPalClient->generateAccessToken();
					$PayPalResponse = $this->PayPalClient->createOrder($accessToken, $PayPalData);
					
					if (isset($PayPalResponse['id']) && $PayPalResponse['id'] != null){
						foreach ($PayPalResponse['links'] as $links) {
							if ($links['rel'] == 'approve') {
								$redirect_url = $links['href'];
								break;
							}
						}
						
						if(isset($redirect_url)) {
							$intent = $redirect_url;
						}
					}else{
						
						Order_item::whereIn('order_master_id', $MasterData)->delete();
						Order_master::whereIn('id', $MasterData)->delete();
						
						$res['msgType'] = 'error';
						$res['msg'] = array('oneError' => array(__('Unknown error occurred')));
						return response()->json($res);
					}
				}
			
			//Razorpay
			}elseif($payment_method_id == 5){
				$intent = '';
				
				if($gtext['isenable_razorpay'] == 1){
					
					$razorpay_payment_id = $request->input('razorpay_payment_id');
					
					if($razorpay_payment_id == ''){
						$res['msgType'] = 'error';
						$res['msg'] = array('oneError' => array(__('Payment failed')));
						return response()->json($res);
					}
			
					$razorpay_key_id = $gtext['razorpay_key_id'];
					$razorpay_key_secret = $gtext['razorpay_key_secret'];
					
					$api = new Api($razorpay_key_id, $razorpay_key_secret);
					
					$payment = $api->payment->fetch($razorpay_payment_id);

					if(!empty($razorpay_payment_id)){
						
						try {
							$response = $api->payment->fetch($razorpay_payment_id)->capture(array('amount'=>$payment['amount'])); 
							
							$api->payment->fetch($razorpay_payment_id)->edit(array('notes'=> array('description'=> $description)));
							
						}catch (\Exception $e){
							
							Order_item::whereIn('order_master_id', $MasterData)->delete();
							Order_master::whereIn('id', $MasterData)->delete();
						
							$res['msgType'] = 'error';
							$res['msg'] = array('oneError' => array(__('Payment failed')));
							return response()->json($res);
						}            
					}
				}
			
			//Mollie
			}elseif($payment_method_id == 6){
	
				if($gtext['isenable_mollie'] == 1){

					$priceString = number_format($totalAmount, 2);
					$price = str_replace(",","", $priceString);
					$amount = (string) $price;
					// $amount = strval($price);

					$mollie_currency = $gtext['mollie_currency'];
						
					$mollie_api_key = $gtext['mollie_api_key'];
					Mollie::api()->setApiKey($mollie_api_key); // your mollie test api key

					$makePayment = [
						"amount" => [
							"currency" => $mollie_currency, //'EUR', // Type of currency you want to send
							"value" => $amount, //'30.00' You must send the correct number of decimals, thus we enforce the use of strings
						],
						"description" => $description, 
						"redirectUrl" => route('frontend.thank') // after the payment completion where you to redirect
					];
					
					$payment = Mollie::api()->payments->create($makePayment);
				
					$payment = Mollie::api()->payments->get($payment->id);
					
					$intent = $payment->getCheckoutUrl();
				}
				
			}else{
				$intent = '';
			}
			
			if($payment_method_id != 4){

				Session::forget('shopping_cart');
				
				if($gtext['ismail'] == 1){
					self::orderNotify($MasterData);
				}
			}
			
			$res['msgType'] = 'success';
			$res['msg'] = __('Your order is successfully.');
			$res['intent'] = $intent;
			return response()->json($res);
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Oops! Your order is failed. Please try again.');
			return response()->json($res);
		}
    }
		
    public function PayPalPaymentSuccess(Request $request){
		$gtext = gtext();
		
		$order_master_ids = Session::get('order_master_ids');

        Session::forget('order_master_ids');
		
		$accessToken = $this->PayPalClient->generateAccessToken();
		$OrderId = $request['token'];

        if (empty($request['PayerID']) || empty($request['token'])) {
			
			Order_item::whereIn('order_master_id', $order_master_ids)->delete();
			Order_master::whereIn('id', $order_master_ids)->delete();

            \Session::put('pt_payment_error', __('Payment failed'));
            return Redirect::route('frontend.checkout');
        }
		
		$response = $this->PayPalClient->capturePaymentOrder($accessToken, $OrderId);
		$resArr = json_decode($response->getBody(), true); 

        // Handle the response as needed
        if ($response->getStatusCode() === 201) {
			if (isset($resArr['status']) && $resArr['status'] == 'COMPLETED') {
				
				// $TransactionID = $resArr['purchase_units'][0]['payments']['captures'][0]['id'];
				
				Session::forget('shopping_cart');
				
				 if($gtext['ismail'] == 1){
					self::orderNotify($order_master_ids);
				}
				
				return Redirect::route('frontend.thank');
			}
        } else {
			Order_item::whereIn('order_master_id', $order_master_ids)->delete();
			Order_master::whereIn('id', $order_master_ids)->delete();
			
			\Session::put('pt_payment_error', __('Payment failed'));
			return Redirect::route('frontend.checkout');
        }
    }
	
    public function PayPalPaymentCancel(){
		
		$order_master_ids = Session::get('order_master_ids');

        Session::forget('order_master_ids');
		
		Order_item::whereIn('order_master_id', $order_master_ids)->delete();
		Order_master::whereIn('id', $order_master_ids)->delete();
		
		\Session::put('pt_payment_error', __('You have canceled the transaction'));
		return Redirect::route('frontend.checkout');
    }
	
    //Order Notify
    public function orderNotify($MasterData) {
		$gtext = gtext();
		
 		$datalist = DB::table('order_masters as a')
			->join('order_items as b', 'a.id', '=', 'b.order_master_id')
			->join('users as c', 'a.seller_id', '=', 'c.id')
			->join('payment_method as d', 'a.payment_method_id', '=', 'd.id')
			->join('payment_status as e', 'a.payment_status_id', '=', 'e.id')
			->join('order_status as f', 'a.order_status_id', '=', 'f.id')
			->join('products as g', 'b.product_id', '=', 'g.id')
			->select(
				'a.id', 
				'a.customer_id', 
				'a.seller_id', 
				'a.payment_status_id', 
				'a.order_status_id', 
				'a.order_no', 
				'a.created_at', 
				'a.shipping_title', 
				'a.shipping_fee',
				'g.title', 
				'b.quantity', 
				'b.price', 
				'b.total_price', 
				'b.tax', 
				'b.discount',
				'b.variation_color',
				'b.variation_size',
				'a.email as customer_email', 
				'a.name as customer_name', 
				'a.phone as customer_phone', 
				'a.country', 
				'a.state', 
				'a.zip_code', 
				'a.city', 
				'a.address as customer_address',  
				'c.shop_name',  
				'c.shop_url',  
				'c.email as seller_email',  
				'd.method_name', 
				'e.pstatus_name', 
				'f.ostatus_name')
			->whereIn('a.id', $MasterData)
			->orderBy('a.seller_id', 'ASC')
			->get();

		$index = 0;
		$mdata = array();
		$orderDataArr = array();
		$tempSellerId = ''; 
		$SellerCount = 0;
		$totalAmount = 0;
		$totalTax = 0;
		$totalDiscount = 0;
		
		$item_list = '';
		foreach($datalist as $row){
			if($index == 0){
				$mdata['payment_status_id'] = $row->payment_status_id;
				$mdata['order_status_id'] = $row->order_status_id;
				$mdata['customer_name'] = $row->customer_name;
				$mdata['customer_email'] = $row->customer_email;
				$mdata['customer_address'] = $row->customer_address;
				$mdata['city'] = $row->city;
				$mdata['state'] = $row->state;
				$mdata['zip_code'] = $row->zip_code;
				$mdata['country'] = $row->country;
				$mdata['customer_phone'] = $row->customer_phone;
				$mdata['order_no'] = 'order_no888';
				$mdata['created_at'] = $row->created_at;
				$mdata['method_name'] = $row->method_name;
				$mdata['pstatus_name'] = $row->pstatus_name;
				$mdata['ostatus_name'] = $row->ostatus_name;
				$mdata['shipping_title'] = $row->shipping_title;
				$mdata['shipping_fee'] = $row->shipping_fee;
			}
			
			$totalAmount +=$row->total_price;
			$totalTax +=$row->tax;
			$totalDiscount +=$row->discount;
			
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
			
			if($tempSellerId != $row->seller_id){

				$orderDataArr[$row->seller_id]['id'] = $row->id;
				$orderDataArr[$row->seller_id]['order_no'] = $row->order_no;
				$orderDataArr[$row->seller_id]['seller_id'] = $row->seller_id;
				$orderDataArr[$row->seller_id]['seller_email'] = $row->seller_email;
				$orderDataArr[$row->seller_id]['shop_name'] = $row->shop_name;
				
				$item_list .= '<tr>
								<td colspan="3" style="width:100%;text-align:left;border:1px solid #ddd;background-color:#f7f7f7;font-weight:bold;">'.__('Sold By').': <a href="'.route('frontend.stores', [$row->seller_id, str_slug($row->shop_url)]).'"> '.$row->shop_name.'</a>, '.__('Order#').': <a href="'.route('frontend.order-invoice', [$row->id, $row->order_no]).'"> '.$row->order_no.'</a></td>
							</tr>';

				$tempSellerId=$row->seller_id; 
				$SellerCount++;		
			}
			
			$item_list .= '<tr>
							<td style="width:70%;text-align:left;border:1px solid #ddd;">'.$row->title.'<br>'.$size.'</td>
							<td style="width:15%;text-align:center;border:1px solid #ddd;">'.$price.' x '.$row->quantity.'</td>
							<td style="width:15%;text-align:right;border:1px solid #ddd;">'.$total_price.'</td>
						</tr>';
			
			$index++;
		}

		$shipping_fee = $mdata['shipping_fee'] * $SellerCount;
		
		$total_amount_shipping_fee = $totalAmount + $shipping_fee + $totalTax;
		
		if($gtext['currency_position'] == 'left'){
			$shippingFee = $gtext['currency_icon'].NumberFormat($mdata['shipping_fee']);
			$shipping_fee = $gtext['currency_icon'].NumberFormat($shipping_fee);
			$tax = $gtext['currency_icon'].NumberFormat($totalTax);
			$discount = $gtext['currency_icon'].NumberFormat($totalDiscount);
			$subtotal = $gtext['currency_icon'].NumberFormat($totalAmount);
			$total_amount = $gtext['currency_icon'].NumberFormat($total_amount_shipping_fee);
			
		}else{
			$shippingFee = NumberFormat($mdata['shipping_fee']).$gtext['currency_icon'];
			$shipping_fee = NumberFormat($shipping_fee).$gtext['currency_icon'];
			$tax = NumberFormat($totalTax).$gtext['currency_icon'];
			$discount = NumberFormat($totalDiscount).$gtext['currency_icon'];
			$subtotal = NumberFormat($totalAmount).$gtext['currency_icon'];
			$total_amount = NumberFormat($total_amount_shipping_fee).$gtext['currency_icon'];
		}
		
		if($mdata['payment_status_id'] == 1){
			$pstatus = '#26c56d'; //Completed = 1
		}elseif($mdata['payment_status_id'] == 2){
			$pstatus = '#fe9e42'; //Pending = 2
		}elseif($mdata['payment_status_id'] == 3){
			$pstatus = '#f25961'; //Canceled = 3
		}elseif($mdata['payment_status_id'] == 4){
			$pstatus = '#f25961'; //Incompleted 4
		}
		
		if($mdata['order_status_id'] == 1){
			$ostatus = '#fe9e42'; //Awaiting processing = 1
		}elseif($mdata['order_status_id'] == 2){
			$ostatus = '#fe9e42'; //Processing = 2
		}elseif($mdata['order_status_id'] == 3){
			$ostatus = '#fe9e42'; //Ready for pickup = 3
		}elseif($mdata['order_status_id'] == 4){
			$ostatus = '#26c56d'; //Completed 4
		}elseif($mdata['order_status_id'] == 5){
			$ostatus = '#f25961'; //Canceled 5
		}

		$base_url = url('/');

		$InvoiceDownloads = '';
		$orderNos = '';
		$invoice_index = 1;
		$f = 0;
		foreach($orderDataArr as $row){
			if($f++){
				$orderNos .= ', ';
			}
			$orderNos .= $row['order_no'];
			
			$InvoiceDownloads .= '<a href="'.route('frontend.order-invoice', [$row['id'], $row['order_no']]).'" style="background:'.$gtext['theme_color'].';display:block;text-align:center;padding:7px 15px;margin:0 10px 10px 0;border-radius:3px;text-decoration:none;color:#fff;float:left;">'.__('Invoice').' ('.$row['order_no'].')</a>';
			$invoice_index++;
		}
		
		if($gtext['ismail'] == 1){
			try {

				require 'vendor/autoload.php';
				$mail = new PHPMailer(true);
				$mail->CharSet = "UTF-8";

				if($gtext['mailer'] == 'smtp'){
					$mail->SMTPDebug = 0; //0 = off (for production use), 1 = client messages, 2 = client and server messages
					$mail->isSMTP();
					$mail->Host       = $gtext['smtp_host'];
					$mail->SMTPAuth   = true;
					$mail->Username   = $gtext['smtp_username'];
					$mail->Password   = $gtext['smtp_password'];
					$mail->SMTPSecure = $gtext['smtp_security'];
					$mail->Port       = $gtext['smtp_port'];
				}

				//Get mail
				$mail->setFrom($gtext['from_mail'], $gtext['from_name']);
				$mail->addAddress($mdata['customer_email'], $mdata['customer_name']);
				foreach($orderDataArr as $row){
				$mail->addAddress($row['seller_email'], $row['shop_name']);
				// $mail->addCC($row['seller_email'], $row['shop_name']);
				}
				$mail->isHTML(true);
				$mail->CharSet = "utf-8";
				$mail->Subject = $orderNos.' - '. __('Your order is successfully.');
				
				$mail->Body = '<table style="background-color:#edf2f7;color:#111111;padding:40px 0px;line-height:24px;font-size:14px;" border="0" cellpadding="0" cellspacing="0" width="100%">	
								<tr>
									<td>
										<table style="background-color:#fff;max-width:1000px;margin:0 auto;padding:30px;" border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr><td style="font-size:40px;border-bottom:1px solid #ddd;padding-bottom:25px;font-weight:bold;text-align:center;">'.$gtext['company'].'</td></tr>
											<tr><td style="font-size:25px;font-weight:bold;padding:30px 0px 5px 0px;">'.__('Hi').' '.$mdata['customer_name'].'</td></tr>
											<tr><td>'.__('We have received your order and will contact you as soon as your package is shipped. You can find your purchase information below.').'</td></tr>
											<tr>
												<td style="padding-top:30px;padding-bottom:20px;">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td style="vertical-align: top;">
																<table border="0" cellpadding="3" cellspacing="0" width="100%">
																	<tr><td style="font-size:16px;font-weight:bold;">'.__('BILL TO').':</td></tr>
																	<tr><td><strong>'.$mdata['customer_name'].'</strong></td></tr>
																	<tr><td>'.$mdata['customer_address'].'</td></tr>
																	<tr><td>'.$mdata['city'].', '.$mdata['state'].', '.$mdata['zip_code'].', '.$mdata['country'].'</td></tr>
																	<tr><td>'.$mdata['customer_email'].'</td></tr>
																	<tr><td>'.$mdata['customer_phone'].'</td></tr>
																</table>
																<table style="padding:30px 0px;" border="0" cellpadding="3" cellspacing="0" width="100%">
																	<tr><td style="font-size:16px;font-weight:bold;">'.__('BILL FROM').':</td></tr>
																	<tr><td><strong>'.$gtext['company'].'</strong></td></tr>
																	<tr><td>'.$gtext['invoice_address'].'</td></tr>
																	<tr><td>'.$gtext['invoice_email'].'</td></tr>
																	<tr><td>'.$gtext['invoice_phone'].'</td></tr>
																</table>
															</td>
															<td style="vertical-align: top;">
																<table style="text-align:right;" border="0" cellpadding="3" cellspacing="0" width="100%">
																	<tr><td><strong>'.__('Order Date').'</strong>: '.date('d-m-Y', strtotime($mdata['created_at'])).'</td></tr>
																	<tr><td><strong>'.__('Payment Method').'</strong>: '.$mdata['method_name'].'</td></tr>
																	<tr><td><strong>'.__('Payment Status').'</strong>: <span style="color:'.$pstatus.'">'.$mdata['pstatus_name'].'</span></td></tr>
																	<tr><td><strong>'.__('Order Status').'</strong>: <span style="color:'.$ostatus.'">'.$mdata['ostatus_name'].'</span></td></tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td>
													<table style="border-collapse:collapse;" border="0" cellpadding="5" cellspacing="0" width="100%">
														<tr>
															<th style="width:70%;text-align:left;border:1px solid #ddd;">'.__('Product').'</th>
															<th style="width:15%;text-align:center;border:1px solid #ddd;">'.__('Price').'</th>
															<th style="width:15%;text-align:right;border:1px solid #ddd;">'.__('Total').'</th>
														</tr>
														'.$item_list.'
													</table>
												</td>
											</tr>
											<tr>
												<td style="padding-top:5px;padding-bottom:20px;">
													<table style="font-weight:bold;" border="0" cellpadding="5" cellspacing="0" width="100%">
														<tr>
															<td style="width:85%;text-align:left;">'.$mdata['shipping_title'].': '.$shippingFee.' <span style="float:right">'.__('Shipping Fee').':</span></td>
															<td style="width:15%;text-align:right;">'.$shipping_fee.'</td>
														</tr>
														<tr>
															<td style="width:85%;text-align:right;">'.__('Tax').':</td>
															<td style="width:15%;text-align:right;">'.$tax.'</td>
														</tr>
														<tr>
															<td style="width:85%;text-align:right;">'.__('Subtotal').':</td>
															<td style="width:15%;text-align:right;">'.$subtotal.'</td>
														</tr>
														<tr>
															<td style="width:85%;text-align:right;">'.__('Total').':</td>
															<td style="width:15%;text-align:right;">'.$total_amount.'</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr><td style="padding-top:30px;padding-bottom:50px;">'.$InvoiceDownloads.'</td></tr>
											<tr><td style="padding-top:10px;border-top:1px solid #ddd;text-align:center;">'.__('Thank you for purchasing our products.').'</td></tr>
											<tr><td style="padding-top:5px;text-align:center;">'.__('If you have any questions about this invoice, please contact us').'</td></tr>
											<tr><td style="padding-top:5px;text-align:center;"><a href="'.$base_url.'">'.$base_url.'</a></td></tr>
										</table>
									</td>
								</tr>
							</table>';

				$mail->send();
				
				return 1;
			} catch (Exception $e) {
				return 0;
			}
		}
	}	
}
