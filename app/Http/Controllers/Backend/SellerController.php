<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Package;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\Media_option;
use App\Models\Bank_information;
use App\Models\Withdrawal;
use App\Models\Withdrawal_image;
use App\Models\Product;
use App\Models\Pro_image;
use App\Models\Related_product;
use App\Models\Review;
use App\Models\Order_item;
use App\Models\Order_master;
use App\Services\WaaSService;
class SellerController extends Controller
{

    protected $waasService;

    public function __construct(WaaSService $waasService)
    {
        $this->waasService = $waasService;
    }

    public function LoadSellerRegister()
    {
        $groups=Group::where('status','active')->get();
     //   dd($groups);
        // Kenya by default (change if multi-country)
        $countryId = (int) \App\Models\Country::where('country_name','Kenya')->value('id');

        $countyLevelId = (int) DB::table('geo_levels')
            ->whereIn('name', ['county','County'])
            ->value('id');

        $counties = DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $countyLevelId)
            ->orderBy('name')
            ->get(['id','name']);

        // Use your top-level product categories as "Store Category"
        $storeCategories = DB::table('pro_categories')
            ->where('is_publish',1)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id','name']);
        return view('frontend.seller-register',compact('groups','counties','storeCategories','countryId'));
    }

//    public function SellerRegister(Request $request)
//    {
//
//		$gtext = gtext();
//
//		$secretkey = $gtext['secretkey'];
//		$recaptcha = $gtext['is_recaptcha'];
//		if($recaptcha == 1){
//			$request->validate([
//				'g-recaptcha-response' => 'required',
//				'name' => 'required',
//				'email' => 'required|email|unique:users',
//				'password' => 'required|confirmed|min:6',
//				'shop_name' => 'required',
////				'shop_url' => 'required',
//				'shop_phone' => 'required',
//                'classification' => 'required|in:individual,company',             // NEW
//                'document_number' => 'required|string|max:191',                  // NEW
//                'group_option' => 'nullable|in:group',                           // NEW
//                'group_id' => 'required_if:group_option,group|exists:groups,id', // NEW
//			]);
//
//			$captcha = $request->input('g-recaptcha-response');
//
//			$ip = $_SERVER['REMOTE_ADDR'];
//			$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip'.$ip;
//			$response = file_get_contents($url);
//			$responseKeys = json_decode($response, true);
//			if($responseKeys["success"] == false) {
//				return redirect("seller/register")->withFail(__('The recaptcha field is required'));
//			}
//		}else{
//			$request->validate([
//				'name' => 'required',
//				'email' => 'required|email|unique:users',
//				'password' => 'required|confirmed|min:6',
//				'shop_name' => 'required',
////				'shop_url' => 'required',
//				'shop_phone' => 'required',
//                'classification' => 'required|in:individual,company',             // NEW
//                'document_number' => 'required|string|max:191',                  // NEW
//                'group_option' => 'nullable|in:group',                           // NEW
//                'group_id' => 'required_if:group_option,group|exists:groups,id', // NEW
//            ]);
//		}
//
//		$SellerSettings = gSellerSettings();
//		if($SellerSettings['seller_auto_active'] == 1){
//			$status_id = 1;
//		}else{
//			$status_id = 2;
//		}
//
//		$data = array(
//			'name' => $request->input('name'),
//			'email' => $request->input('email'),
//			'password' => Hash::make($request->input('password')),
//			'bactive' => base64_encode($request->input('password')),
//			'shop_name' => $request->input('shop_name'),
//			'shop_url' => $request->input('shop_name'),
//			'phone' => $request->input('shop_phone'),
//			'status_id' => $status_id,
//			'role_id' => 3,
//            'classification'  => $request->input('classification'),   // <-- new
//            'document_number' => $request->input('document_number'),  // <-- new
//		);
//
//        if ($request->filled('group_option') && $request->input('group_option') === 'group' && $request->filled('group_id')) {
//            $data['group_id'] = (int) $request->input('group_id');
//        }
//
//		$response = User::create($data);
//
//		if($response){
//            //call propel here-------
//            $data = [
//                'name' => $request->input('shop_name'),
//                'mobile_no' =>$request->input('shop_phone'),
//                'request_id' => $request->input('email'),
//            ];
//
//           // $response = WaaSService::createWallet($data);
//            $this->waasService->addBeneficiary($data,$response->id);
//            // already has an active subscription?
//            if ($response->currentSubscription()->exists()) return;
//
//            $free = Package::where('name', 'Free')->first(); // or by id
//            if (!$free) return;
//
//            $now = now();
//            UserSubscription::create([
//                'user_id'      => $response->id,
//                'package_id'   => $free->id,
//                'billing_cycle'=> 'monthly',
//                'price'        => 0,
//                'currency'     => 'KES',
//                'status'       => 'active',
//                'starts_at'    => $now,
//                'expires_at'   => null,        // keep free open-ended, or set $now->copy()->addMonths(1)
//                'next_due_at'  => null,
//            ]);
//
//			if($gtext['is_mailchimp'] == 1){
//				$name = $request->input('name');
//				$email_address = $request->input('email');
//
//				$HTTP_Status = self::MailChimpSubscriber($name, $email_address);
//				if($HTTP_Status == 200){
//					$SubscriberCount = Subscriber::where('email_address', '=', $email_address)->count();
//					if($SubscriberCount == 0){
//						$data = array(
//							'email_address' => $email_address,
//							'first_name' => $name,
//							'last_name' => $name,
//							'status' => 'subscribed'
//						);
//						Subscriber::create($data);
//					}
//				}
//			}
//
//			if($status_id == 1){
//				return redirect()->back()->withSuccess(__('Thanks! You have register successfully. Please login.'));
//			}else{
//				return redirect()->back()->withSuccess(__('Thanks! You have register successfully. Your account is pending for review.'));
//			}
//
//		}else{
//			return redirect()->back()->withFail(__('Oops! You are failed registration. Please try again.'));
//		}
//    }
    public function SellerRegister(Request $request)
    {
        $gtext       = gtext();
        $recaptchaOn = (int) $gtext['is_recaptcha'] === 1;
        $saveMode    = $request->input('save_mode', 'submit'); // 'draft' | 'submit'
        $isDraft     = $saveMode === 'draft';

        // If you want captcha only on final submission:
        if ($recaptchaOn && !$isDraft) {
            $request->validate(['g-recaptcha-response' => 'required']);
            $captcha   = $request->input('g-recaptcha-response');
            $secretkey = $gtext['secretkey'] ?? '';
            $ip        = $request->ip();
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                .urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip='.$ip;
            $resp = @file_get_contents($url);
            $ok   = $resp ? json_decode($resp, true) : ['success' => false];
            if (empty($ok['success'])) {
                return back()->withFail(__('The recaptcha field is required'))->withInput();
            }
        }

        // Build validation rules dynamically
        $rulesBase = [
            'name'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'email'       => $isDraft ? 'nullable|email' : 'required|email',
            'password'    => $isDraft ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
            'shop_name'   => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',
            'shop_phone'  => $isDraft ? 'nullable|string|max:20' : 'required|string|max:20',
            'geo_unit_id' => $isDraft ? 'nullable|integer|exists:geo_units,id' : 'required|integer|exists:geo_units,id',
            'address_line'=> $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',

            'classification'  => $isDraft ? 'nullable|in:individual,company' : 'required|in:individual,company',
            'group_option'    => 'nullable|in:group',
            'group_id'        => 'nullable|integer|exists:groups,id',

            // Step 2 (only enforce on submit)
            'document_number'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'document_file'          => $isDraft ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096' : 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'business_license_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'kra_pin'                => 'nullable|string|max:20',
            'brand_auth_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'contact_person_name'    => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'contact_person_phone'   => $isDraft ? 'nullable|regex:/^\+2547\d{8}$/' : 'required|regex:/^\+2547\d{8}$/',

            // Step 3
            'bank_name'      => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'bank_branch'    => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'account_name'   => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'account_number' => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'swift_code'     => 'nullable|string|max:50',
            'mobile_money'   => 'nullable|regex:/^\+2547\d{8}$/',

            // Step 4
            'store_category_id' => $isDraft ? 'nullable|integer|exists:pro_categories,id' : 'required|integer|exists:pro_categories,id',
            'store_description' => $isDraft ? 'nullable|string' : 'required|string',
            'shipping_methods'  => 'nullable|array',
            'shipping_methods.*'=> 'in:local_pickup,within_county,nationwide',
            'store_logo'        => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'store_banner'      => 'nullable|image|mimes:jpg,jpeg,png|max:8192',
        ];

        // Email uniqueness rules:
        $existingUser = $request->filled('email')
            ? User::where('email', $request->input('email'))->first()
            : null;
        if (!$existingUser) {
            $rulesBase['email'] = ($isDraft ? 'nullable' : 'required').'|email|unique:users,email';
        } else {
            // allow the same email to resume draft
            $rulesBase['email'] = ($isDraft ? 'nullable' : 'required').'|email';
        }

        // When group option is chosen, group_id required
        if ($request->input('group_option') === 'group') {
            $rulesBase['group_id'] = 'required|integer|exists:groups,id';
        }

        $validated = $request->validate($rulesBase);

        // Seller auto active toggle (from your settings)
        $sellerSettings = gSellerSettings();
        $autoActive = (int)($sellerSettings['seller_auto_active'] ?? 0) === 1;

        DB::beginTransaction();
        try {
            // CREATE or UPDATE user (draft resume supported)
            if (!$existingUser) {
                // brand new user (even in draft we’ll create for resume)
                $user = new User();
                $user->email = $validated['email'] ?? null;
                $user->password = $request->filled('password') ? Hash::make($request->input('password')) : Hash::make(Str::random(12));
                $user->bactive  = $request->filled('password') ? base64_encode($request->input('password')) : null;
                $user->role_id  = 3; // seller
            } else {
                $user = $existingUser;
                // if they changed password in a later attempt
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                    $user->bactive  = base64_encode($request->input('password'));
                }
                // ensure role is seller
                if ((int)$user->role_id !== 3) $user->role_id = 3;
            }

            // core info
            if ($request->filled('name'))       $user->name       = $request->input('name');
            if ($request->filled('shop_name'))  $user->shop_name  = $request->input('shop_name');
            if ($request->filled('shop_phone')) $user->phone      = $request->input('shop_phone');
            if ($request->filled('address_line')) $user->address  = $request->input('address_line');
            if ($request->filled('geo_unit_id')) $user->geo_unit_id = (int)$request->input('geo_unit_id');

            // shop_url (unique slug)
            if ($request->filled('shop_name')) {
                $user->shop_url = $this->uniqueShopSlug($request->input('shop_name'), $user->id ?? null);
            }

            // business meta
            if ($request->filled('classification'))  $user->classification  = $request->input('classification');
            if ($request->filled('document_number')) $user->document_number = $request->input('document_number');
            if ($request->filled('group_option') && $request->input('group_option') === 'group' && $request->filled('group_id')) {
                $user->group_id = (int)$request->input('group_id');
            } else {
                // keep previous or null-out on draft if they removed
                if ($isDraft && !$request->filled('group_id')) $user->group_id = null;
            }

            // status & kyc state
            if ($isDraft) {
                $user->status_id  = 2; // pending/inactive until submission
                $user->kyc_status = 'not_submitted';
            } else {
                $user->status_id  = $autoActive ? 1 : 2;
                $user->kyc_status = 'pending';
                $user->kyc_submitted_at = now();
            }

            $user->save();

            // --- Documents table (1:1) ---
            $docs = SellerDocument::firstOrNew(['user_id' => $user->id]);
            if ($request->filled('document_number')) $docs->document_number = $request->input('document_number');
            if ($request->filled('kra_pin'))         $docs->kra_pin         = $request->input('kra_pin');

            if ($request->hasFile('document_file')) {
                $docs->document_file_path = $request->file('document_file')->store('seller_docs', 'public');
            }
            if ($request->hasFile('business_license_file')) {
                $docs->business_license_file_path = $request->file('business_license_file')->store('seller_docs', 'public');
            }
            if ($request->hasFile('brand_auth_file')) {
                $docs->brand_auth_file_path = $request->file('brand_auth_file')->store('seller_docs', 'public');
            }
            $docs->user_id = $user->id;
            $docs->save();

            // --- Settlement table (1:1) ---
            $settle = SellerSettlement::firstOrNew(['user_id' => $user->id]);
            foreach (['bank_name','bank_branch','account_name','account_number','swift_code','mobile_money'] as $f) {
                if ($request->filled($f)) $settle->{$f} = $request->input($f);
                elseif ($isDraft && !$request->filled($f)) ; // keep old on draft if absent
            }
            $settle->user_id = $user->id;
            $settle->save();

            // --- Store table (1:1) ---
            $store = SellerStore::firstOrNew(['user_id' => $user->id]);
            if ($request->filled('store_category_id')) $store->store_category_id = (int)$request->input('store_category_id');
            if ($request->filled('store_description')) $store->store_description = $request->input('store_description');

            // shipping methods -> json
            if ($request->has('shipping_methods')) {
                $store->shipping_methods = array_values(array_unique(array_filter((array)$request->input('shipping_methods'))));
            } elseif ($isDraft) {
                // keep previous on draft if not sent
            } else {
                $store->shipping_methods = []; // submit with none selected
            }

            if ($request->hasFile('store_logo')) {
                $store->store_logo_path = $request->file('store_logo')->store('seller_stores', 'public');
            }
            if ($request->hasFile('store_banner')) {
                $store->store_banner_path = $request->file('store_banner')->store('seller_stores', 'public');
            }
            $store->user_id = $user->id;
            $store->save();

            // Optional: store progress step (if you posted it)
            if ($request->filled('progress_step')) {
                // If you added this field to users, you can persist:
                // $user->progress_step = (int)$request->input('progress_step');
                // $user->save();
            }

            // Create wallet + Free subscription only on final submission
            if (!$isDraft) {
                // ---- WAAS / BENEFICIARY ----
                if (isset($this->waasService)) {
                    $payload = [
                        'name'       => $user->shop_name ?: $user->name,
                        'mobile_no'  => $user->phone,
                        'request_id' => $user->email,
                    ];
                    try {
                        $this->waasService->addBeneficiary($payload, $user->id);
                    } catch (\Throwable $e) {
                        // log and continue — don’t break onboarding
                        \Log::warning('waasService addBeneficiary failed: '.$e->getMessage());
                    }
                }

                // ---- Free package ----
                if (!$user->currentSubscription()->exists()) {
                    $free = Package::where('name', 'Free')->first();
                    if ($free) {
                        UserSubscription::create([
                            'user_id'       => $user->id,
                            'package_id'    => $free->id,
                            'billing_cycle' => 'monthly',
                            'price'         => 0,
                            'currency'      => 'KES',
                            'status'        => 'active',
                            'starts_at'     => now(),
                            'expires_at'    => null,
                            'next_due_at'   => null,
                        ]);
                    }
                }

                // ---- Mailchimp (optional) ----
                if ((int)$gtext['is_mailchimp'] === 1 && $user->email) {
                    try {
                        $HTTP_Status = self::MailChimpSubscriber($user->name, $user->email);
                        if ($HTTP_Status == 200) {
                            $exists = Subscriber::where('email_address', $user->email)->exists();
                            if (!$exists) {
                                Subscriber::create([
                                    'email_address' => $user->email,
                                    'first_name'    => $user->name,
                                    'last_name'     => $user->name,
                                    'status'        => 'subscribed'
                                ]);
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('MailChimp subscribe failed: '.$e->getMessage());
                    }
                }
            }

            DB::commit();

            if ($isDraft) {
                return back()->withSuccess(__('Draft saved. You can resume later from your account.'));
            }

            if ((int)$user->status_id === 1) {
                return back()->withSuccess(__('Thanks! You have registered successfully. Please login.'));
            }
            return back()->withSuccess(__('Thanks! Registration submitted. Your account is pending review.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('SellerRegister failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withFail(__('Oops! Registration could not be completed. Please try again.'))->withInput();
        }
    }

    /** Generate a unique shop_url from a name. */
    private function uniqueShopSlug(string $name = null, $excludeUserId = null): string
    {
        $base = Str::slug($name ?? '') ?: 'shop';
        $slug = $base;
        $i = 2;
        while (
        DB::table('users')
            ->where('shop_url', $slug)
            ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
	//MailChimp Subscriber
    public function MailChimpSubscriber($name, $email){
		$gtext = gtext();

		$apiKey = $gtext['mailchimp_api_key'];
		$listId = $gtext['audience_id'];

        //Create mailchimp API url
        $memberId = md5(strtolower($email));
        $dataCenter = substr($apiKey, strpos($apiKey, '-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

        //Member info
        $data = array(
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields'  => [
                'FNAME'     => $name,
                'LNAME'     => $name
            ]
        );

        $jsonString = json_encode($data);

        // send a HTTP POST request with curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

		return $httpCode;
    }

	//has shop url Slug
    public function hasShopSlug(Request $request){
		$res = array();

		$slug = str_slug($request->shop_url);
        $count = User::where('shop_url', $slug) ->count();
		if($count == 0){
			$res['slug'] = $slug;
			$res['count'] = 0;
		}else{
			$res['slug'] = $slug;
			$res['count'] = 1;
		}

		return response()->json($res);
	}

	//Sellers page load
    public function getSellersPageLoad(){
		$statuslist = DB::table('user_status')->orderBy('id', 'asc')->get();
		$countrylist = DB::table('countries')->where('is_publish', '=', 1)->orderBy('country_name', 'asc')->get();
		$media_datalist = Media_option::orderBy('id','desc')->paginate(28);

		$AllCount = User::where('role_id', '=', 3)->count();
		$ActiveCount = User::where('status_id', '=', 1)->where('role_id', '=', 3)->count();
		$InactiveCount = User::where('status_id', '=', 2)->where('role_id', '=', 3)->count();
//
//		$datalist = DB::table('users')
        $datalist = User::with([
            'currentSubscription' => function ($q) {
                $q->select('id','user_id','package_id','billing_cycle','price','currency','status','starts_at','expires_at');
            },
            'currentSubscription.package:id,name'
        ])
			->join('user_roles', 'users.role_id', '=', 'user_roles.id')
			->join('user_status', 'users.status_id', '=', 'user_status.id')
			->select('users.*', 'user_roles.role', 'user_status.status')
			->where('users.role_id', 3)
            ->where('users.status_id', '!=', 3)
			->orderBy('users.id','desc')
			->paginate(20);

        return view('backend.sellers', compact('AllCount', 'ActiveCount', 'InactiveCount', 'statuslist', 'countrylist', 'media_datalist', 'datalist'));
    }

	//Get data for Sellers Pagination
	public function getSellersTableData(Request $request){

		$status = $request->status;
		$search = $request->search;

		if($request->ajax()){

			if($search != ''){

				$datalist = DB::table('users')
					->join('user_roles', 'users.role_id', '=', 'user_roles.id')
					->join('user_status', 'users.status_id', '=', 'user_status.id')
					->select('users.*', 'user_roles.role', 'user_status.status')
					->where(function ($query) use ($search){
						$query->where('name', 'like', '%'.$search.'%')
							->orWhere('email', 'like', '%'.$search.'%')
							->orWhere('phone', 'like', '%'.$search.'%')
							->orWhere('shop_name', 'like', '%'.$search.'%')
							->orWhere('shop_url', 'like', '%'.$search.'%')
							->orWhere('address', 'like', '%'.$search.'%')
							->orWhere('city', 'like', '%'.$search.'%')
							->orWhere('state', 'like', '%'.$search.'%');
					})
					->where(function ($query) use ($status){
						$query->whereRaw("users.status_id = '".$status."' OR '".$status."' = '0'");
					})
					->where(function ($query) use ($status){
						$query->whereRaw("users.role_id = 3");
					})
					->orderBy('users.id','desc')
					->paginate(20);
			}else{

			$datalist = DB::table('users')
				->join('user_roles', 'users.role_id', '=', 'user_roles.id')
				->join('user_status', 'users.status_id', '=', 'user_status.id')
				->select('users.*', 'user_roles.role', 'user_status.status')
				->where(function ($query) use ($status){
					$query->whereRaw("users.status_id = '".$status."' OR '".$status."' = '0'");
				})
				->where(function ($query) use ($status){
					$query->whereRaw("users.role_id = 3");
				})
				->orderBy('users.id','desc')
				->paginate(20);
			}

			return view('backend.partials.sellers_table', compact('datalist'))->render();
		}
	}

	//Save data for Sellers
    public function saveSellersData(Request $request){
		$res = array();

		$id = $request->input('RecordId');
		$name = $request->input('name');
		$email = $request->input('email');
		$password = $request->input('password');
		$shop_name = $request->input('shop_name');
		$shop_url = str_slug($request->input('shop_url'));
		$phone = $request->input('phone');
		$address = $request->input('address');
		$city = $request->input('city');
		$state = $request->input('state');
		$zip_code = $request->input('zip_code');
		$country_id = $request->input('country_id');
		$status_id = $request->input('status_id');
		$photo = $request->input('photo');

		$validator_array = array(
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => $request->input('password'),
			'shop_name' => $request->input('shop_name'),
			'shop_url' => $request->input('shop_url'),
			'phone' => $request->input('phone'),
			'address' => $request->input('address'),
			'city' => $request->input('city'),
			'state' => $request->input('state'),
			'zip_code' => $request->input('zip_code'),
			'country_id' => $request->input('country_id'),
		);
		$rId = $id == '' ? '' : ','.$id;
		$validator = Validator::make($validator_array, [
			'name' => 'required|max:191',
			'email' => 'required|max:191|unique:users,email' . $rId,
			'password' => 'required|max:191',
			'shop_name' => 'required',
			'shop_url' => 'required',
			'phone' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'zip_code' => 'required',
			'country_id' => 'required',
		]);

		$errors = $validator->errors();

		if($errors->has('name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('name');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('email')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('email');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('password')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('password');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('shop_name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('shop_name');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('shop_url')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('shop_url');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('phone')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('phone');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('address')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('address');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('city')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('city');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('state')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('state');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('zip_code')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('zip_code');
			$res['id'] = '';
			return response()->json($res);
		}

		if($errors->has('country_id')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('country_id');
			$res['id'] = '';
			return response()->json($res);
		}

		$data = array(
			'name' => $name,
			'email' => $email,
			'password' => Hash::make($password),
			'shop_name' => $shop_name,
			'shop_url' => $shop_url,
			'phone' => $phone,
			'address' => $address,
			'city' => $city,
			'state' => $state,
			'zip_code' => $zip_code,
			'country_id' => $country_id,
			'status_id' => $status_id,
			'photo' => $photo,
			'role_id' => 3,
			'bactive' => base64_encode($password)
		);

		if($id ==''){
			$response = User::create($data)->id;
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('New Data Added Successfully');
				$res['id'] = $response;
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
				$res['id'] = '';
			}
		}else{
			$response = User::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Updated Successfully');
				$res['id'] = $id;
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
				$res['id'] = '';
			}
		}

		return response()->json($res);
    }

	//Save data for Bank Information
    public function saveBankInformationData(Request $request){
		$res = array();

		$id = $request->input('bank_information_id');
		$seller_id = $request->input('seller_id');
		$bank_name = $request->input('bank_name');
		$bank_code = $request->input('bank_code');
		$account_number = $request->input('account_number');
		$account_holder = $request->input('account_holder');
		$paypal_id = $request->input('paypal_id');
		$description = $request->input('description');

		$data = array(
			'seller_id' => $seller_id,
			'bank_name' => $bank_name,
			'bank_code' => $bank_code,
			'account_number' => $account_number,
			'account_holder' => $account_holder,
			'paypal_id' => $paypal_id,
			'description' => $description
		);

		if($id ==''){
			$response = Bank_information::create($data)->id;
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('New Data Added Successfully');
				$res['id'] = $response;
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
				$res['id'] = '';
			}
		}else{
			$response = Bank_information::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Updated Successfully');
				$res['id'] = $id;
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
				$res['id'] = '';
			}
		}

		return response()->json($res);
    }

	//Get data for Sellers by id
//    public function getSellerById(Request $request){
//		$gtext = gtext();
//		$lan = glan();
//
//		$datalist = array(
//			'seller_data' => '',
//			'bank_information' => '',
//			'CurrentBalance' => 0,
//			'OrderBalance' => 0,
//			'WithdrawalBalance' => 0,
//			'TotalProducts' => 0
//		);
//
//		$id = $request->id;
//
//		$data = DB::table('users')->where('id', $id)->first();
//		$data->bactive = base64_decode($data->bactive);
//		$data->created_at = date('d F, Y', strtotime($data->created_at));
//
//		$bankInfoData = DB::table('bank_informations')->where('seller_id', $id)->first();
//
//		$datalist['seller_data'] = $data;
//		$datalist['bank_information'] = $bankInfoData;
//
//		$sql = "SELECT (IFNULL(SUM(b.total_price), 0) + IFNULL(SUM(b.tax), 0)) AS OrderBalance
//		FROM order_masters a
//		INNER JOIN order_items b ON a.id = b.order_master_id
//		WHERE a.payment_status_id = 1
//		AND a.order_status_id = 4
//		AND a.seller_id = '".$id."';";
//		$aRow = DB::select($sql);
//		$OrderBalance = $aRow[0]->OrderBalance;
//
//		$sql1 = "SELECT (IFNULL(SUM(amount), 0) + IFNULL(SUM(fee_amount), 0)) AS WithdrawalBalance
//		FROM withdrawals
//		WHERE seller_id = '".$id."'
//		AND status_id = 3;";
//		$aRow1 = DB::select($sql1);
//		$WithdrawalBalance = $aRow1[0]->WithdrawalBalance;
//		$OrderWithdrawalBalance = ($OrderBalance - $WithdrawalBalance);
//
//		if($gtext['currency_position'] == 'left'){
//			$datalist['CurrentBalance'] = $gtext['currency_icon'].NumberFormat($OrderWithdrawalBalance);
//			$datalist['OrderBalance'] = $gtext['currency_icon'].NumberFormat($OrderBalance);
//			$datalist['WithdrawalBalance'] = $gtext['currency_icon'].NumberFormat($WithdrawalBalance);
//		}else{
//			$datalist['CurrentBalance'] = NumberFormat($OrderWithdrawalBalance).$gtext['currency_icon'];
//			$datalist['OrderBalance'] = NumberFormat($OrderBalance).$gtext['currency_icon'];
//			$datalist['WithdrawalBalance'] = NumberFormat($WithdrawalBalance).$gtext['currency_icon'];
//		}
//		$sql2 = "SELECT COUNT(id) AS TotalProducts
//		FROM products
//		WHERE user_id = '".$id."'
//		AND is_publish = 1
//		AND lan = '".$lan."';";
//		$aRow2 = DB::select($sql2);
//		$datalist['TotalProducts'] = $aRow2[0]->TotalProducts;
//
//		return response()->json($datalist);
//	}
    public function getSellerById(Request $request)
    {
        $gtext = gtext();
        $lan = glan();

        $datalist = [
            'seller_data'        => '',
            'bank_information'   => '',
            'CurrentBalance'     => 0,
            'OrderBalance'       => 0,
            'WithdrawalBalance'  => 0,
            'TotalProducts'      => 0,
            'package'            => [ // new block
                'name'          => null,
                'billing_cycle' => null,
                'price'         => null,
                'currency'      => null,
                'status'        => null,
                'starts_at'     => null,
                'expires_at'    => null,
            ],
        ];

        $id = $request->id;

        // ---- Seller core ----
        $data = DB::table('users')->where('id', $id)->first();
        if (!$data) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $data->bactive     = $data->bactive ? base64_decode($data->bactive) : $data->bactive;
        $data->created_at  = date('d F, Y', strtotime($data->created_at));

        $bankInfoData = DB::table('bank_informations')->where('seller_id', $id)->first();

        $datalist['seller_data']      = $data;
        $datalist['bank_information'] = $bankInfoData;

        // ---- Balances ----
        $sql = "
        SELECT (IFNULL(SUM(b.total_price), 0) + IFNULL(SUM(b.tax), 0)) AS OrderBalance
        FROM order_masters a
        INNER JOIN order_items b ON a.id = b.order_master_id
        WHERE a.payment_status_id = 1
          AND a.order_status_id  = 4
          AND a.seller_id        = ?
    ";
        $aRow = DB::select($sql, [$id]);
        $OrderBalance = (float)($aRow[0]->OrderBalance ?? 0);

        $sql1 = "
        SELECT (IFNULL(SUM(amount), 0) + IFNULL(SUM(fee_amount), 0)) AS WithdrawalBalance
        FROM withdrawals
        WHERE seller_id = ?
          AND status_id = 3
    ";
        $aRow1 = DB::select($sql1, [$id]);
        $WithdrawalBalance = (float)($aRow1[0]->WithdrawalBalance ?? 0);

        $OrderWithdrawalBalance = $OrderBalance - $WithdrawalBalance;

        if ($gtext['currency_position'] == 'left') {
            $datalist['CurrentBalance']    = $gtext['currency_icon'] . NumberFormat($OrderWithdrawalBalance);
            $datalist['OrderBalance']      = $gtext['currency_icon'] . NumberFormat($OrderBalance);
            $datalist['WithdrawalBalance'] = $gtext['currency_icon'] . NumberFormat($WithdrawalBalance);
        } else {
            $datalist['CurrentBalance']    = NumberFormat($OrderWithdrawalBalance) . $gtext['currency_icon'];
            $datalist['OrderBalance']      = NumberFormat($OrderBalance) . $gtext['currency_icon'];
            $datalist['WithdrawalBalance'] = NumberFormat($WithdrawalBalance) . $gtext['currency_icon'];
        }

        $sql2 = "
        SELECT COUNT(id) AS TotalProducts
        FROM products
        WHERE user_id   = ?
          AND is_publish = 1
          AND lan        = ?
    ";
        $aRow2 = DB::select($sql2, [$id, $lan]);
        $datalist['TotalProducts'] = (int)($aRow2[0]->TotalProducts ?? 0);

        // ---- Subscription / Package (NEW) ----
        // latest active subscription for this user
        $sub = DB::select("
        SELECT s.id, s.package_id, s.billing_cycle, s.price, s.currency, s.status,
               s.starts_at, s.expires_at, p.name AS package_name
        FROM user_subscriptions s
        JOIN packages p ON p.id = s.package_id
        WHERE s.user_id = ?
          AND s.status = 'active'
        ORDER BY s.starts_at DESC
        LIMIT 1
    ", [$id]);

        if (!empty($sub)) {
            $s = $sub[0];

            // format price using the subscription’s currency (not the site currency)
            $formattedPrice = NumberFormat((float)$s->price);
            $priceOut = $s->currency === 'KES'
                ? ($gtext['currency_position'] == 'left'
                    ? $gtext['currency_icon'] . $formattedPrice
                    : $formattedPrice . $gtext['currency_icon'])
                : ($s->currency . ' ' . $formattedPrice);

            $datalist['package'] = [
                'name'          => $s->package_name,
                'billing_cycle' => $s->billing_cycle,
                'price'         => $priceOut,
                'currency'      => $s->currency,
                'status'        => $s->status,
                'starts_at'     => $s->starts_at,
                'expires_at'    => $s->expires_at,
            ];
        } else {
            // Optional: fallback to most recent (any status) if you want
            // or leave as nulls to signal “no active subscription”.
        }
        $wallets = DB::table('wallets')
            ->where('user_id', $id)
            ->orderByDesc('is_system_wallet')   // system/primary first if you want
            ->orderBy('id')
            ->get();

        $formatMoney = function($amount, $currency) use ($gtext) {
            $amount = NumberFormat((float)$amount);
            return $gtext['currency_position'] == 'left'
                ? ($currency ?: $gtext['currency_icon']).$amount
                : $amount.($currency ?: $gtext['currency_icon']);
        };

        $payloadWallets = [];
        foreach ($wallets as $w) {
            $payloadWallets[] = [
                'id'             => $w->id,
                'wallet_name'    => $w->wallet_name,
                'account_number' => $w->account_number,
                'wallet_type'    => $w->wallet_type,
                'balance'        => $formatMoney($w->balance, $w->currency),
                'raw_balance'    => (float)$w->balance,
                'currency'       => $w->currency,
                'wallet_limit'   => isset($w->wallet_limit) ? $formatMoney($w->wallet_limit, $w->currency) : null,
                'is_system_wallet' => (int)$w->is_system_wallet,
            ];
        }

        $datalist['wallets'] = $payloadWallets;

        return response()->json($datalist);
    }


	//Delete data for Sellers
	public function deleteSeller(Request $request){

		$res = array();

		$id = $request->id;

		if($id != ''){
            $response = User::where('id', $id)->update(['status_id' => 3]);

            if ($response) {
                $res['msgType'] = 'success';
                $res['msg'] = __('User deactivated successfully');
            } else {
                $res['msgType'] = 'error';
                $res['msg'] = __('Failed to deactivate user');
            }
            return response()->json($res);

			$aRows = Product::where('user_id', $id)->get();
			$idsArray = array();
			foreach($aRows as $key => $row){
				$idsArray[$key] = $row->id;
			}

			$withdrawalsRows = Withdrawal::where('seller_id', $id)->get();
			$withdrawalIdsArray = array();
			foreach($withdrawalsRows as $key => $row){
				$withdrawalIdsArray[$key] = $row->id;
			}

			Order_item::where('seller_id', $id)->delete();
			Order_master::where('seller_id', $id)->delete();

			Withdrawal_image::whereIn('withdrawal_id', $withdrawalIdsArray)->delete();
			Withdrawal::where('seller_id', $id)->delete();

			Review::whereIn('item_id', $idsArray)->delete();
			Related_product::whereIn('product_id', $idsArray)->delete();
			Pro_image::whereIn('product_id', $idsArray)->delete();
			Product::where('user_id', $id)->delete();

			Bank_information::where('seller_id', $id)->delete();
			$response = User::where('id', $id)->delete();
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Removed Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data remove failed');
			}
		}

		return response()->json($res);
	}

	//Bulk Action for Sellers
	public function bulkActionSellers(Request $request){

		$res = array();

		$idsStr = $request->ids;
		$idsArray = explode(',', $idsStr);

		$BulkAction = $request->BulkAction;

		if($BulkAction == 'active'){
			$response = User::whereIn('id', $idsArray)->update(['status_id' => 1]);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}

		}elseif($BulkAction == 'inactive'){

			$response = User::whereIn('id', $idsArray)->update(['status_id' => 2]);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}

		}elseif($BulkAction == 'delete'){

			$aRows = Product::whereIn('user_id', $idsArray)->get();
			$itemIdsArray = array();
			foreach($aRows as $key => $row){
				$itemIdsArray[$key] = $row->id;
			}

			$withdrawalsRows = Withdrawal::whereIn('seller_id', $idsArray)->get();
			$withdrawalIdsArray = array();
			foreach($withdrawalsRows as $key => $row){
				$withdrawalIdsArray[$key] = $row->id;
			}

			Order_item::whereIn('seller_id', $idsArray)->delete();
			Order_master::whereIn('seller_id', $idsArray)->delete();

			Withdrawal_image::whereIn('withdrawal_id', $withdrawalIdsArray)->delete();
			Withdrawal::whereIn('seller_id', $idsArray)->delete();

			Review::whereIn('item_id', $itemIdsArray)->delete();
			Related_product::whereIn('product_id', $itemIdsArray)->delete();
			Pro_image::whereIn('product_id', $itemIdsArray)->delete();

			Product::whereIn('user_id', $idsArray)->delete();

			Bank_information::whereIn('seller_id', $idsArray)->delete();
			$response = User::whereIn('id', $idsArray)->delete();
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Removed Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data remove failed');
			}
		}

		return response()->json($res);
	}
}
