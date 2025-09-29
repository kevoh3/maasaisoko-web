<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Package;
use App\Models\SellerDeclaration;
use App\Models\UserSubscription;
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
use App\Models\Country;
use App\Models\SellerDocument;     // << add these 3 if you have dedicated tables
use App\Models\SellerSettlement;   //    (otherwise adjust to your schema)
use App\Models\SellerStore;
use App\Services\SmsService;
use App\Services\WaaSService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    protected $waasService;

    public function __construct(WaaSService $waasService)
    {
        $this->waasService = $waasService;
    }
    private const KE_PHONE_PCRE = '/^(?:\+254|0)?\s?7\d(?:[\s-]?\d){7}$/';

    public function LoadSellerRegister()
    {
        $groups = Group::where('status','active')->get();

        $countryId = (int) \App\Models\Country::where('country_name','Kenya')->value('id');

        $countyLevelId = (int) DB::table('geo_levels')
            ->whereIn('name', ['county','County'])
            ->value('id');

        $counties = DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $countyLevelId)
            ->orderBy('name')
            ->get(['id','name']);

        $storeCategories = DB::table('pro_categories')
            ->where('is_publish',1)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id','name']);

        return view('frontend.seller-register',compact('groups','counties','storeCategories','countryId'));
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



    // ---- MailChimp ----
    public function MailChimpSubscriber($name, $email){
        $gtext = gtext();

        $apiKey = $gtext['mailchimp_api_key'];
        $listId = $gtext['audience_id'];

        $memberId   = md5(strtolower($email));
        $dataCenter = substr($apiKey, strpos($apiKey, '-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

        $data = [
            'email_address' => $email,
            'status'        => 'subscribed',
            'merge_fields'  => ['FNAME'=>$name, 'LNAME'=>$name]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode;
    }

    /** AJAX: normalize/claim a shop slug */
    public function hasShopSlug(Request $request){
        $res = [];
        $slug = str_slug($request->shop_url);
        $count = User::where('shop_url', $slug)->count();
        $res['slug']  = $slug;
        $res['count'] = $count;
        return response()->json($res);
    }

    // ---------------- Backend (list/manage sellers) ----------------

    public function getSellersPageLoad(){
        $statuslist      = DB::table('user_status')->orderBy('id', 'asc')->get();
        $countrylist     = DB::table('countries')->where('is_publish', 1)->orderBy('country_name', 'asc')->get();
        $media_datalist  = Media_option::orderBy('id','desc')->paginate(28);

        $AllCount      = User::where('role_id', 3)->count();
        $ActiveCount   = User::where('status_id', 1)->where('role_id', 3)->count();
        $InactiveCount = User::where('status_id', 2)->where('role_id', 3)->count();

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

    public function getSellersTableData(Request $request){
        $status = $request->status;
        $search = $request->search;

        if($request->ajax()){
            if($search != ''){
                $datalist = DB::table('users')
                    ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
                    ->join('user_status', 'users.status_id', '=', 'user_status.id')
                    ->select('users.*', 'user_roles.role', 'user_status.status')
                    ->where(function ($q) use ($search){
                        $q->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('shop_name', 'like', '%'.$search.'%')
                            ->orWhere('shop_url', 'like', '%'.$search.'%')
                            ->orWhere('address', 'like', '%'.$search.'%')
                            ->orWhere('city', 'like', '%'.$search.'%')
                            ->orWhere('state', 'like', '%'.$search.'%');
                    })
                    ->where(function ($q) use ($status){
                        $q->whereRaw("users.status_id = '".$status."' OR '".$status."' = '0'");
                    })
                    ->whereRaw("users.role_id = 3")
                    ->orderBy('users.id','desc')
                    ->paginate(20);
            }else{
                $datalist = DB::table('users')
                    ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
                    ->join('user_status', 'users.status_id', '=', 'user_status.id')
                    ->select('users.*', 'user_roles.role', 'user_status.status')
                    ->where(function ($q) use ($status){
                        $q->whereRaw("users.status_id = '".$status."' OR '".$status."' = '0'");
                    })
                    ->whereRaw("users.role_id = 3")
                    ->orderBy('users.id','desc')
                    ->paginate(20);
            }

            return view('backend.partials.sellers_table', compact('datalist'))->render();
        }
    }

    public function saveSellersData(Request $request){
        $res = [];

        $id         = $request->input('RecordId');
        $name       = $request->input('name');
        $email      = $request->input('email');
        $password   = $request->input('password');
        $shop_name  = $request->input('shop_name');
        $shop_url   = str_slug($request->input('shop_url'));
        $phone      = $request->input('phone');
        $address    = $request->input('address');
        $city       = $request->input('city');
        $state      = $request->input('state');
        $zip_code   = $request->input('zip_code');
        $country_id = $request->input('country_id');
        $status_id  = $request->input('status_id');
        $photo      = $request->input('photo');

        $validator = Validator::make([
            'name'       => $name,
            'email'      => $email,
            'password'   => $password,
            'shop_name'  => $shop_name,
            'shop_url'   => $shop_url,
            'phone'      => $phone,
            'address'    => $address,
            'city'       => $city,
            'state'      => $state,
            'zip_code'   => $zip_code,
            'country_id' => $country_id,
        ], [
            'name'       => 'required|max:191',
            'email'      => 'required|max:191|unique:users,email' . ($id ? ','.$id : ''),
            'password'   => 'required|max:191',
            'shop_name'  => 'required',
            'shop_url'   => 'required',
            'phone'      => 'required',
            'address'    => 'required',
            'city'       => 'required',
            'state'      => 'required',
            'zip_code'   => 'required',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            $field = array_key_first($validator->errors()->messages());
            return response()->json([
                'msgType' => 'error',
                'msg'     => $validator->errors()->first($field),
                'id'      => ''
            ]);
        }

        $data = [
            'name'       => $name,
            'email'      => $email,
            'password'   => Hash::make($password),
            'shop_name'  => $shop_name,
            'shop_url'   => $shop_url,
            'phone'      => $phone,
            'address'    => $address,
            'city'       => $city,
            'state'      => $state,
            'zip_code'   => $zip_code,
            'country_id' => $country_id,
            'status_id'  => $status_id,
            'photo'      => $photo,
            'role_id'    => 3,
            'bactive'    => base64_encode($password)
        ];

        if(!$id){
            $newId = User::create($data)->id;
            return response()->json([
                'msgType' => $newId ? 'success' : 'error',
                'msg'     => $newId ? __('New Data Added Successfully') : __('Data insert failed'),
                'id'      => $newId ?: ''
            ]);
        } else {
            $ok = User::where('id', $id)->update($data);
            return response()->json([
                'msgType' => $ok ? 'success' : 'error',
                'msg'     => $ok ? __('Data Updated Successfully') : __('Data update failed'),
                'id'      => $id
            ]);
        }
    }

    public function getSellerById(Request $request)
    {
        $gtext = gtext();
        $lan   = glan();

        $datalist = [
            'seller_data'        => '',
            'bank_information'   => '',
            'CurrentBalance'     => 0,
            'OrderBalance'       => 0,
            'WithdrawalBalance'  => 0,
            'TotalProducts'      => 0,
            'package'            => [
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
        $data = DB::table('users')->where('id', $id)->first();
        if (!$data) return response()->json(['message' => 'Seller not found'], 404);

        $data->bactive    = $data->bactive ? base64_decode($data->bactive) : $data->bactive;
        $data->created_at = date('d F, Y', strtotime($data->created_at));
        $bankInfoData     = DB::table('bank_informations')->where('seller_id', $id)->first();

        $datalist['seller_data']      = $data;
        $datalist['bank_information'] = $bankInfoData;

        // Balances
        $OrderBalance = (float) (DB::select("
            SELECT (IFNULL(SUM(b.total_price), 0) + IFNULL(SUM(b.tax), 0)) AS OrderBalance
            FROM order_masters a
            INNER JOIN order_items b ON a.id = b.order_master_id
            WHERE a.payment_status_id = 1
              AND a.order_status_id  = 4
              AND a.seller_id        = ?
        ", [$id])[0]->OrderBalance ?? 0);

        $WithdrawalBalance = (float) (DB::select("
            SELECT (IFNULL(SUM(amount), 0) + IFNULL(SUM(fee_amount), 0)) AS WithdrawalBalance
            FROM withdrawals
            WHERE seller_id = ?
              AND status_id = 3
        ", [$id])[0]->WithdrawalBalance ?? 0);

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

        $datalist['TotalProducts'] = (int) (DB::select("
            SELECT COUNT(id) AS TotalProducts
            FROM products
            WHERE user_id   = ?
              AND is_publish = 1
              AND lan        = ?
        ", [$id, $lan])[0]->TotalProducts ?? 0);

        // Subscription / Package (active)
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
        }

        // Wallets payload (if you keep wallets in DB)
        $wallets = DB::table('wallets')
            ->where('user_id', $id)
            ->orderByDesc('is_system_wallet')
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
                'id'               => $w->id,
                'wallet_name'      => $w->wallet_name,
                'account_number'   => $w->account_number,
                'wallet_type'      => $w->wallet_type,
                'balance'          => $formatMoney($w->balance, $w->currency),
                'raw_balance'      => (float)$w->balance,
                'currency'         => $w->currency,
                'wallet_limit'     => isset($w->wallet_limit) ? $formatMoney($w->wallet_limit, $w->currency) : null,
                'is_system_wallet' => (int)$w->is_system_wallet,
            ];
        }
        $datalist['wallets'] = $payloadWallets;

        return response()->json($datalist);
    }

    public function deleteSeller(Request $request){
        $res = [];
        $id = $request->id;

        if($id != ''){
            // Soft deactivate
            $response = User::where('id', $id)->update(['status_id' => 3]);
            if ($response) {
                return response()->json(['msgType' => 'success', 'msg' => __('User deactivated successfully')]);
            }
            return response()->json(['msgType' => 'error', 'msg' => __('Failed to deactivate user')]);
        }
        return response()->json(['msgType' => 'error', 'msg' => __('Invalid request')]);
    }

    public function bulkActionSellers(Request $request){
        $res = [];

        $idsStr    = $request->ids;
        $idsArray  = explode(',', $idsStr);
        $BulkAction= $request->BulkAction;

        if($BulkAction == 'active'){
            $ok = User::whereIn('id', $idsArray)->update(['status_id' => 1]);
            return response()->json(['msgType' => $ok ? 'success' : 'error', 'msg' => $ok ? __('Data Updated Successfully') : __('Data update failed')]);
        }elseif($BulkAction == 'inactive'){
            $ok = User::whereIn('id', $idsArray)->update(['status_id' => 2]);
            return response()->json(['msgType' => $ok ? 'success' : 'error', 'msg' => $ok ? __('Data Updated Successfully') : __('Data update failed')]);
        }elseif($BulkAction == 'delete'){
            // Hard delete path if you still need it:
            $aRows = Product::whereIn('user_id', $idsArray)->get();
            $itemIdsArray = $aRows->pluck('id')->all();

            $withdrawalsRows = Withdrawal::whereIn('seller_id', $idsArray)->get();
            $withdrawalIdsArray = $withdrawalsRows->pluck('id')->all();

            Order_item::whereIn('seller_id', $idsArray)->delete();
            Order_master::whereIn('seller_id', $idsArray)->delete();

            Withdrawal_image::whereIn('withdrawal_id', $withdrawalIdsArray)->delete();
            Withdrawal::whereIn('seller_id', $idsArray)->delete();

            Review::whereIn('item_id', $itemIdsArray)->delete();
            Related_product::whereIn('product_id', $itemIdsArray)->delete();
            Pro_image::whereIn('product_id', $itemIdsArray)->delete();
            Product::whereIn('user_id', $idsArray)->delete();
            Bank_information::whereIn('seller_id', $idsArray)->delete();

            $ok = User::whereIn('id', $idsArray)->delete();
            return response()->json(['msgType' => $ok ? 'success' : 'error', 'msg' => $ok ? __('Data Removed Successfully') : __('Data remove failed')]);
        }

        return response()->json(['msgType' => 'error', 'msg' => __('No action performed')]);
    }



    /** Normalize Kenyan phone to +2547XXXXXXXX */
    private function normalizeKEPhone(?string $raw): ?string {
        if (!$raw) return null;
        $s = trim($raw);
        // keep leading + for +254, drop other non-digits
        $s = preg_replace('/(?!^\+)[^\d]/', '', $s);

        if (preg_match('/^\+2547\d{8}$/', $s)) return $s;       // +2547XXXXXXXX
        if (preg_match('/^2547\d{8}$/', $s))  return '+'.$s;    // 2547XXXXXXXX
        if (preg_match('/^07\d{8}$/', $s))    return '+254'.substr($s,1); // 07XXXXXXXX
        if (preg_match('/^7\d{8}$/', $s))     return '+254'.$s; // 7XXXXXXXXX
        return $s;
    }

    /** Step-aware register (draft OR submit) with OTP enforcement on Step 1 when submitting */

    /** Step-aware register (draft OR submit) with OTP enforcement on Step 1 when submitting) */

//    public function SellerRegister(Request $request)
//    {
//        $gtext       = gtext();
//        $recaptchaOn = (int) $gtext['is_recaptcha'] === 1;
//
//        $saveMode = $request->input('save_mode', 'submit'); // 'draft' | 'submit'
//        $isDraft  = $saveMode === 'draft';
//        $step     = max(1, min(5, (int) $request->input('current_step', 1))); // 🔹 allow step 5 (review)
//
//        // reCAPTCHA only on final submit (step 5, not draft)
//        if ($recaptchaOn && !$isDraft && $step === 5) {
//            $request->validate(['g-recaptcha-response' => 'required']);
//            $captcha   = $request->input('g-recaptcha-response');
//            $secretkey = $gtext['secretkey'] ?? '';
//            $ip        = $request->ip();
//            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
//                .urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip='.$ip;
//            $resp = @file_get_contents($url);
//            $ok   = $resp ? json_decode($resp, true) : ['success' => false];
//            if (empty($ok['success'])) {
//                return back()->withFail(__('The recaptcha field is required'))->withInput();
//            }
//        }
//
//        // Per-step validation rules (unchanged idea; tweak for your current fields/steps)
//        $rules = match ($step) {
//            1 => [
//                'username'                     => $isDraft ? 'nullable|regex:/^[A-Za-z0-9._-]{4,}$/' : 'required|regex:/^[A-Za-z0-9._-]{4,}$/',
//                'password'                     => $isDraft ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
//                'shop_phone'                   => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',
//                'seller_type'                  => $isDraft ? 'nullable|in:sole_proprietor,partnership,company' : 'required|in:sole_proprietor,partnership,company',
//                'email'                        => 'nullable|email',
//                'group_id'                     => 'nullable|integer|exists:groups,id',
//
//                'contact_person_name'          => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'contact_person_designation'   => $isDraft ? 'nullable|in:proprietor,director,manager,agent' : 'required|in:proprietor,director,manager,agent',
//                'contact_person_phone'         => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',
//
//                'address_street'               => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'address_city'                 => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'geo_unit_id'                  => $isDraft ? 'nullable|integer|exists:geo_units,id' : 'required|integer|exists:geo_units,id',
//            ],
//            2 => [
//                // Keep your doc rules small; upload only what’s present
//                'doc_sole_brs'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//                'doc_sole_id'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//                'doc_sole_kra'       => 'nullable|file|mimes:pdf|max:4096',
//                'doc_sole_sbp'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//                // add partnership/company variants as needed
//            ],
//            3 => [
//                'account_name'       => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'bank_name'          => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'bank_branch'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'account_number'     => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'account_type'       => $isDraft ? 'nullable|in:current,savings' : 'required|in:current,savings',
//                'swift_code'         => 'nullable|string|max:50',
//                'mobile_money'       => 'nullable|string|max:30',
//                'mobile_money_paybill'=> 'nullable|string|max:50',
//            ],
//            4 => [
//                'shop_name'          => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',
//                'store_category_id'  => $isDraft ? 'nullable|integer|exists:pro_categories,id' : 'required|integer|exists:pro_categories,id',
//                'store_description'  => $isDraft ? 'nullable|string' : 'required|string|min:10',
//                'store_logo'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//                'store_banner'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,txt,doc,docx|max:8192',
//                'store_city'         => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
//                'store_county'       => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
//                'store_sub_county'   => 'nullable|string|max:120',
//                'store_ward'         => 'nullable|string|max:120',
//                'store_coords'       => 'nullable|string|max:60',
//            ],
//            5 => [
//                // Final confirm; usually no new fields beyond signature/recaptcha
//                'signature_data'     => $isDraft ? 'nullable|string' : 'nullable|string', // optional
//            ],
//            default => [],
//        };
//
//        // Step 1: unique email/username and OTP enforcement (non-draft)
//        if ($step === 1) {
//            // email uniqueness if provided
//            if ($request->filled('email')) {
//                $rules['email'] .= '|unique:users,email';
//            }
//            // username uniqueness
//            if ($request->filled('username')) {
//                $rules['username'] .= '|unique:users,name';
//            }
//
//            if (!$isDraft) {
//                if (!session('seller_otp_verified')) {
//                    return back()->withFail(__('Please verify your phone (OTP) before continuing.'))->withInput();
//                }
//            }
//        }
//
//        $validated = $request->validate($rules);
//
//        // Normalize phones (non-fatal)
//        $request->merge([
//            'shop_phone'           => $this->normalizeKEPhone($request->input('shop_phone')),
//            'contact_person_phone' => $this->normalizeKEPhone($request->input('contact_person_phone')),
//            'mobile_money'         => $this->normalizeKEPhone($request->input('mobile_money')),
//        ]);
//
//        // Auto-activate setting
//        $sellerSettings = gSellerSettings();
//        $autoActive = (int)($sellerSettings['seller_auto_active'] ?? 0) === 1;
//
//        DB::beginTransaction();
//        try {
//            // Find/create the user by email OR username (for drafts without email yet, we can key by username)
//            $user = null;
//            if ($request->filled('email')) {
//                $user = \App\Models\User::where('email', $request->input('email'))->first();
//            }
//            if (!$user && $request->filled('username')) {
//                $user = \App\Models\User::where('name', $request->input('username'))->first();
//            }
//            if (!$user) {
//                $user = new \App\Models\User();
//                $user->role_id  = 3; // seller
//            }
//
//            // Set core fields if present
//            if ($request->filled('email'))       $user->email   = $request->input('email');
//            if ($request->filled('username'))    $user->name    = $request->input('username');
//            if ($request->filled('password')) {
//                $user->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
//                $user->bactive  = base64_encode($request->input('password')); // if you still need this
//            }
//
//            // Step-specific fills (persist progressively)
//            if ($step >= 1) {
//                if ($request->filled('shop_phone'))          $user->phone       = $request->input('shop_phone');
//                if ($request->filled('address_street') || $request->filled('address_city')) {
//                    $user->address = trim(($request->input('address_street','')).' '.$request->input('address_city',''));
//                }
//                if ($request->filled('geo_unit_id'))         $user->geo_unit_id = (int)$request->input('geo_unit_id');
//                if ($request->filled('group_id'))            $user->group_id    = (int)$request->input('group_id');
//
//                // You can map seller_type → classification if you want to keep using that column
//                // e.g. individual/company mapping:
//                if ($request->filled('seller_type')) {
//                    $user->classification = $request->input('seller_type') === 'company' ? 'company' : 'individual';
//                }
//            }
//
//            if ($step >= 4) {
//                if ($request->filled('shop_name')) {
//                    $user->shop_name = $request->input('shop_name');
//                    // ensure unique slug
//                    $user->shop_url  = $this->uniqueShopSlug($request->input('shop_name'), $user->id ?? null);
//                }
//            }
//
//            // Status & KYC
//            if ($isDraft) {
//                $user->status_id  = 2;
//                $user->kyc_status = 'not_submitted';
//            } else {
//                if ($step === 5) { // final confirm
//                    $user->status_id  = $autoActive ? 1 : 2;
//                    $user->kyc_status = 'pending';
//                    $user->kyc_submitted_at = now();
//                } else {
//                    $user->status_id  = 2; // still pending/incomplete
//                }
//            }
//
//            // 🔹 Track progress
//            $user->onboarding_step = max((int)($user->onboarding_step ?? 1), $step);
//
//            $user->save();
//
//            // Step 2: documents (save whatever is present)
//            if ($step >= 2) {
//                $docs = \App\Models\SellerDocument::firstOrNew(['user_id' => $user->id]);
//                // Map any text fields as needed…
//                // Save any uploaded files present in this request:
//                foreach (['doc_sole_brs','doc_sole_id','doc_sole_kra','doc_sole_sbp'] as $f) {
//                    if ($request->hasFile($f)) {
//                        $path = $request->file($f)->store('seller_docs', 'public');
//                        // You can store paths in separate columns or a JSON column; sample:
//                        $col = $f.'_path';
//                        $docs->{$col} = $path;
//                    }
//                }
//                $docs->user_id = $user->id;
//                $docs->save();
//            }
//
//            // Step 3: settlement
//            if ($step >= 3) {
//                $settle = \App\Models\SellerSettlement::firstOrNew(['user_id' => $user->id]);
//                foreach (['account_name','bank_name','bank_branch','account_number','swift_code','mobile_money','mobile_money_paybill','account_type'] as $f) {
//                    if ($request->filled($f)) $settle->{$f} = $request->input($f);
//                }
//                $settle->user_id = $user->id;
//                $settle->save();
//            }
//
//            // Step 4: store
//            if ($step >= 4) {
//                $store = \App\Models\SellerStore::firstOrNew(['user_id' => $user->id]);
//                if ($request->filled('store_category_id')) $store->store_category_id = (int)$request->input('store_category_id');
//                if ($request->filled('store_description')) $store->store_description = $request->input('store_description');
//                if ($request->hasFile('store_logo'))   $store->store_logo_path   = $request->file('store_logo')->store('seller_stores', 'public');
//                if ($request->hasFile('store_banner')) $store->store_banner_path = $request->file('store_banner')->store('seller_stores', 'public');
//
//                // optional physical split
//                foreach (['store_city','store_county','store_sub_county','store_ward','store_coords'] as $f) {
//                    if ($request->filled($f)) $store->{$f} = $request->input($f);
//                }
//
//                $store->user_id = $user->id;
//                $store->save();
//            }
//
//            // Step 5: capture signature image if you want (optional)
//            if ($step === 5 && $request->filled('signature_data')) {
//                // decode and store if needed
//                // Storage::disk('public')->put('seller_signatures/'.$user->id.'.png', base64_decode(preg_replace('#^data:image/\w+;base64,#i','',$request->input('signature_data'))));
//            }
//
//            DB::commit();
//
//            // Redirect flow
//            if ($isDraft) {
//                return back()
//                    ->withSuccess(__('Draft saved. You can resume later from your account.'))
//                    ->withInput(['current_step' => $step]); // stay on same step
//            }
//
//            if ($step < 5) {
//                return back()
//                    ->withSuccess(__('Saved. Continue to next step.'))
//                    ->withInput(['current_step' => $step + 1]);
//            }
//
//            // Final
//            if ((int)$user->status_id === 1) {
//                return back()->withSuccess(__('Thanks! You have registered successfully. Please login.'));
//            }
//            return back()->withSuccess(__('Thanks! Registration submitted. Your account is pending review.'));
//        } catch (\Throwable $e) {
//            DB::rollBack();
//            \Log::error('SellerRegister failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            return back()->withFail(__('Oops! Registration could not be completed. Please try again.'))
//                ->withInput(['current_step' => $step]);
//        }
//    }
//    public function SellerRegister(Request $request)
//    {
//        $gtext       = gtext();
//        $recaptchaOn = (int) $gtext['is_recaptcha'] === 1;
//
//        $saveMode = $request->input('save_mode', 'submit'); // 'draft' | 'submit'
//        $isDraft  = $saveMode === 'draft';
//        $step     = max(1, min(5, (int) $request->input('current_step', 1)));
//
//        // reCAPTCHA only on final submit (step 5, not draft)
//        if ($recaptchaOn && !$isDraft && $step === 5) {
//            $request->validate(['g-recaptcha-response' => 'required']);
//            $captcha   = $request->input('g-recaptcha-response');
//            $secretkey = $gtext['secretkey'] ?? '';
//            $ip        = $request->ip();
//            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
//                .urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip='.$ip;
//            $resp = @file_get_contents($url);
//            $ok   = $resp ? json_decode($resp, true) : ['success' => false];
//            if (empty($ok['success'])) {
//                return back()->withFail(__('The recaptcha field is required'))->withInput();
//            }
//        }
//
//        // Validation (per step)
//        $rules = match ($step) {
//            1 => [
//                'username'                     => $isDraft ? 'nullable|regex:/^[A-Za-z0-9._-]{4,}$/' : 'required|regex:/^[A-Za-z0-9._-]{4,}$/',
//                'password'                     => $isDraft ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
//                'shop_phone'                   => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',
//                'seller_type'                  => $isDraft ? 'nullable|in:sole_proprietor,partnership,company' : 'required|in:sole_proprietor,partnership,company',
//                'email'                        => 'nullable|email',
//                'group_id'                     => 'nullable|integer|exists:groups,id',
//
//                'contact_person_name'          => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'contact_person_designation'   => $isDraft ? 'nullable|in:proprietor,director,manager,agent' : 'required|in:proprietor,director,manager,agent',
//                'contact_person_phone'         => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',
//                'contact_person_email'         => 'nullable|email',
//
//                'address_building'             => 'nullable|string|max:191',
//                'address_street'               => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'address_city'                 => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'postal_box'                   => 'nullable|string|max:191',
//                'postal_code'                  => 'nullable|string|max:191',
//
//                'geo_unit_id'                  => $isDraft ? 'nullable|integer|exists:geo_units,id' : 'required|integer|exists:geo_units,id',
//                'geo_path'                     => 'nullable|string|max:191',
//                'personal_kra_pin'             => 'nullable|string|max:50',
//            ],
//            2 => [
//                // Upload only what's present
//                'doc_sole_brs'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//                'doc_sole_id'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//                'doc_sole_kra'             => 'nullable|file|mimes:pdf|max:4096',
//                'doc_sole_sbp'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
//
//                // If you later add partnership/company file inputs, add rules here.
//            ],
//            3 => [
//                'account_name'             => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'bank_name'                => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'bank_branch'              => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'account_number'           => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
//                'account_type'             => $isDraft ? 'nullable|in:current,savings' : 'required|in:current,savings',
//                'swift_code'               => 'nullable|string|max:50',
//                'mobile_money'             => 'nullable|string|max:30',
//                'mobile_money_paybill'     => 'nullable|string|max:50',
//            ],
//            4 => [
//                'shop_name'                => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',
//                'store_category_id'        => $isDraft ? 'nullable|integer|exists:pro_categories,id' : 'required|integer|exists:pro_categories,id',
//                'store_description'        => $isDraft ? 'nullable|string' : 'required|string|min:10',
//                'store_logo'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//                'store_banner'             => 'nullable|file|mimes:jpg,jpeg,png,pdf,txt,doc,docx|max:8192',
//                'store_city'               => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
//                'store_county'             => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
//                'store_sub_county'         => 'nullable|string|max:120',
//                'store_ward'               => 'nullable|string|max:120',
//                'store_coords'             => 'nullable|string|max:60',
//            ],
//            5 => [
//                'signature_data'           => 'nullable|string',
//                'declaration_date'         => 'nullable|date',
//            ],
//            default => [],
//        };
//
//        // Step 1: unique email/username and OTP enforcement (non-draft)
//        if ($step === 1) {
//            if ($request->filled('email')) {
//                $rules['email'] .= '|unique:users,email';
//            }
//            if ($request->filled('username')) {
//                $rules['username'] .= '|unique:users,name';
//            }
//            if (!$isDraft && !session('seller_otp_verified')) {
//                return back()->withFail(__('Please verify your phone (OTP) before continuing.'))->withInput();
//            }
//        }
//
//        $validated = $request->validate($rules);
//
//        // Normalize phones (non-fatal)
//        $request->merge([
//            'shop_phone'           => $this->normalizeKEPhone($request->input('shop_phone')),
//            'contact_person_phone' => $this->normalizeKEPhone($request->input('contact_person_phone')),
//            'mobile_money'         => $this->normalizeKEPhone($request->input('mobile_money')),
//        ]);
//
//        // Auto-activate setting
//        $sellerSettings = gSellerSettings();
//        $autoActive = (int)($sellerSettings['seller_auto_active'] ?? 0) === 1;
//
//        DB::beginTransaction();
//        try {
//            // Find/create the user by email OR username
//            $user = null;
//            if ($request->filled('email')) {
//                $user = User::where('email', $request->input('email'))->first();
//            }
//            if (!$user && $request->filled('username')) {
//                $user = User::where('name', $request->input('username'))->first();
//            }
//            if (!$user) {
//                $user = new User();
//                $user->role_id  = 3; // seller
//            }
//
//            // Core fields
//            if ($request->filled('email'))       $user->email   = $request->input('email');
//            if ($request->filled('username'))    $user->name    = $request->input('username');
//            if ($request->filled('password')) {
//                $user->password = Hash::make($request->input('password'));
//                $user->bactive  = base64_encode($request->input('password')); // legacy
//            }
//
//            // Step 1 persistence (including new columns)
//            if ($step >= 1) {
//                if ($request->filled('shop_phone'))          $user->phone       = $request->input('shop_phone');
//                if ($request->filled('address_street') || $request->filled('address_city')) {
//                    $user->address = trim(($request->input('address_street','')).' '.$request->input('address_city',''));
//                }
//                if ($request->filled('address_city'))        $user->city        = $request->input('address_city'); // keep existing column in sync
//                if ($request->filled('geo_unit_id'))         $user->geo_unit_id = (int)$request->input('geo_unit_id');
//                if ($request->filled('geo_path'))            $user->geo_path    = $request->input('geo_path');
//                if ($request->filled('group_id'))            $user->group_id    = (int)$request->input('group_id');
//
//                // NEW: dedicated contact & postal/address fields
//                foreach ([
//                             'contact_person_name',
//                             'contact_person_designation',
//                             'contact_person_phone',
//                             'contact_person_email',
//                             'address_building',
//                             'address_street',
//                             'postal_box',
//                             'postal_code',
//                         ] as $f) {
//                    if ($request->filled($f)) $user->{$f} = $request->input($f);
//                }
//                // also mirror postal_code to legacy zip_code if provided
//                if ($request->filled('postal_code')) $user->zip_code = $request->input('postal_code');
//
//                // classification mapping
//                if ($request->filled('seller_type')) {
//                    $user->classification = match ($request->input('seller_type')) {
//                        'company'     => 'company',
//                        'partnership' => 'partnership',
//                        default       => 'individual',
//                    };
//                }
//            }
//
//            // Step 4: store name & slug
//            if ($step >= 4 && $request->filled('shop_name')) {
//                $user->shop_name = $request->input('shop_name');
//                $user->shop_url  = $this->uniqueShopSlug($request->input('shop_name'), $user->id ?? null);
//            }
//
//            // Status & KYC
//            if ($isDraft) {
//                $user->status_id  = 2;
//                $user->kyc_status = 'not_submitted';
//            } else {
//                if ($step === 5) {
//                    $user->status_id  = $autoActive ? 1 : 2;
//                    $user->kyc_status = 'pending';
//                    $user->kyc_submitted_at = now();
//                } else {
//                    $user->status_id  = 2;
//                }
//            }
//
//            // Progress
//            $user->onboarding_step = max((int)($user->onboarding_step ?? 1), $step);
//            $user->save();
//
//            // Step 2: documents (map to your columns)
//            if ($step >= 2) {
//                $docs = SellerDocument::firstOrNew(['user_id' => $user->id]);
//
//                // Persist textual bits if available
//                if ($request->filled('personal_kra_pin')) {
//                    $docs->kra_pin = $request->input('personal_kra_pin');
//                }
//                if ($request->filled('contact_person_id')) {
//                    $docs->document_number = $request->input('contact_person_id');
//                }
//
//                // Files mapping (sole proprietor block)
//                // - BRS (business registration) → business_license_file_path (closest fit)
//                if ($request->hasFile('doc_sole_brs')) {
//                    $docs->business_license_file_path = $request->file('doc_sole_brs')->store('seller_docs', 'public');
//                }
//                // - ID/Passport (single field in form) → additional_id_files (json array)
//                if ($request->hasFile('doc_sole_id')) {
//                    $path = $request->file('doc_sole_id')->store('seller_docs', 'public');
//                    $arr  = (array) json_decode($docs->additional_id_files ?? '[]', true);
//                    $arr[] = $path;
//                    $docs->additional_id_files = json_encode(array_values(array_unique($arr)));
//                }
//                // - KRA PIN certificate (pdf) → kra_cert_file_path
//                if ($request->hasFile('doc_sole_kra')) {
//                    $docs->kra_cert_file_path = $request->file('doc_sole_kra')->store('seller_docs', 'public');
//                }
//                // - Single Business Permit (SBP) → document_file_path (or keep separate; we reuse this)
//                if ($request->hasFile('doc_sole_sbp')) {
//                    $docs->document_file_path = $request->file('doc_sole_sbp')->store('seller_docs', 'public');
//                }
//
//                // (If you later add partnership/company inputs, extend here similarly.)
//
//                $docs->user_id = $user->id;
//                $docs->save();
//            }
//
//            // Step 3: settlement
//            if ($step >= 3) {
//                $settle = SellerSettlement::firstOrNew(['user_id' => $user->id]);
//                foreach ([
//                             'account_name','bank_name','bank_branch','account_number',
//                             'swift_code','mobile_money','mobile_money_paybill','account_type'
//                         ] as $f) {
//                    if ($request->filled($f)) $settle->{$f} = $request->input($f);
//                }
//                $settle->user_id = $user->id;
//                $settle->save();
//            }
//
//            // Step 4: store
//            if ($step >= 4) {
//                $store = SellerStore::firstOrNew(['user_id' => $user->id]);
//
//                if ($request->filled('store_category_id')) $store->store_category_id = (int)$request->input('store_category_id');
//                if ($request->filled('store_description')) $store->store_description = $request->input('store_description');
//
//                if ($request->hasFile('store_logo')) {
//                    $store->store_logo_path = $request->file('store_logo')->store('seller_stores', 'public');
//                }
//                if ($request->hasFile('store_banner')) {
//                    $store->store_banner_path = $request->file('store_banner')->store('seller_stores', 'public');
//                }
//
//                // new physical split
//                foreach (['store_city','store_county','store_sub_county','store_ward','store_coords'] as $f) {
//                    if ($request->filled($f)) $store->{$f} = $request->input($f);
//                }
//
//                $store->user_id = $user->id;
//                $store->save();
//            }
//
//            // Step 5: signature + declaration date
//            if ($step === 5) {
//                $decl = SellerDeclaration::firstOrNew(['user_id' => $user->id]);
//
//                // Save date if provided
//                if ($request->filled('declaration_date')) {
//                    $decl->declaration_date = $request->date('declaration_date');
//                }
//
//                // Save signature image if provided
//                if ($request->filled('signature_data')) {
//                    $b64 = $request->input('signature_data');
//                    $png = preg_replace('#^data:image/\w+;base64,#i','', $b64);
//                    if ($png) {
//                        $pngData = base64_decode($png, true);
//                        if ($pngData !== false) {
//                            $path = 'seller_signatures/'.$user->id.'_'.Str::random(6).'.png';
//                            Storage::disk('public')->put($path, $pngData);
//                            $decl->signature_path = $path;
//                        }
//                    }
//                }
//
//                $decl->user_id = $user->id;
//                $decl->save();
//            }
//
//            DB::commit();
//
//            // Redirect flow
//            if ($isDraft) {
//                return back()
//                    ->withSuccess(__('Draft saved. You can resume later from your account.'))
//                    ->withInput(['current_step' => $step]); // stay on same step
//            }
//
//            if ($step < 5) {
//                return back()
//                    ->withSuccess(__('Saved. Continue to next step.'))
//                    ->withInput(['current_step' => $step + 1]);
//            }
//
//            // Final
//            if ((int)$user->status_id === 1) {
//                return back()->withSuccess(__('Thanks! You have registered successfully. Please login.'));
//            }
//            return back()->withSuccess(__('Thanks! Registration submitted. Your account is pending review.'));
//        } catch (\Throwable $e) {
//            DB::rollBack();
//            \Log::error('SellerRegister failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            return back()->withFail(__('Oops! Registration could not be completed. Please try again.'))
//                ->withInput(['current_step' => $step]);
//        }
//    }
    public function SellerRegister(Request $request)
    {
        $gtext       = gtext();
        $recaptchaOn = (int) $gtext['is_recaptcha'] === 1;

        $saveMode = $request->input('save_mode', 'submit'); // 'draft' | 'submit'
        $isDraft  = $saveMode === 'draft';
        $step     = max(1, min(5, (int) $request->input('current_step', 1))); // allow 1..5

        /**
         * Build a "safe old input" array to flash back to the form.
         * - Excludes passwords, tokens, OTP code, signature image, and file fields (browsers won’t prefill them anyway).
         * - You can still append/override keys via $extra.
         */
        $fileFields = [
            // Sole
            'doc_sole_brs','doc_sole_id','doc_sole_kra','doc_sole_sbp',
            // Partnership
            'doc_partner_brs','doc_partner_ids','doc_partner_kra','doc_partner_sbp','doc_partner_agreement',
            // Company
            'doc_company_certificate','doc_company_kra','doc_company_sbp','doc_company_ids','doc_company_board_resolution',
            // Store
            'store_logo','store_banner',
        ];
        $secretFields = [
            '_token','password','password_confirmation','g-recaptcha-response','signature_data','otp_code'
        ];
        $oldSafe = function(array $extra = []) use ($request, $fileFields, $secretFields) {
            return array_merge(
                $request->except(array_merge($fileFields, $secretFields)),
                $extra
            );
        };

        // reCAPTCHA only on final submit (step 5, not draft)
        if ($recaptchaOn && !$isDraft && $step === 5) {
            $request->validate(['g-recaptcha-response' => 'required']);
            $captcha   = $request->input('g-recaptcha-response');
            $secretkey = $gtext['secretkey'] ?? '';
            $ip        = $request->ip();
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                .urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip='.$ip;
            $resp = @file_get_contents($url);
            $ok   = $resp ? json_decode($resp, true) : ['success' => false];
            if (empty($ok['success'])) {
                return back()
                    ->withFail(__('The recaptcha field is required'))
                    ->withInput($oldSafe(['current_step' => $step]));
            }
        }

        // Validation rules per step
        $rules = match ($step) {
            1 => [
                'username'                   => $isDraft ? 'nullable|regex:/^[A-Za-z0-9._-]{4,}$/' : 'required|regex:/^[A-Za-z0-9._-]{4,}$/',
                'password'                   => $isDraft ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
                'shop_phone'                 => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',
                'seller_type'                => $isDraft ? 'nullable|in:sole_proprietor,partnership,company' : 'required|in:sole_proprietor,partnership,company',
                'email'                      => 'nullable|email',
                'group_id'                   => 'nullable|integer|exists:groups,id',

                'contact_person_name'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'contact_person_designation' => $isDraft ? 'nullable|in:proprietor,director,manager,agent' : 'required|in:proprietor,director,manager,agent',
                'contact_person_phone'       => $isDraft ? 'nullable|string|max:30' : 'required|string|max:30',

                'address_street'             => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'address_city'               => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'geo_unit_id'                => $isDraft ? 'nullable|integer|exists:geo_units,id' : 'required|integer|exists:geo_units,id',
            ],
            2 => [
                // Only validate files that are present
                'doc_sole_brs'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_sole_id'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_sole_kra'               => 'nullable|file|mimes:pdf|max:4096',
                'doc_sole_sbp'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

                'doc_partner_brs'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_partner_ids.*'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_partner_kra'            => 'nullable|file|mimes:pdf|max:4096',
                'doc_partner_sbp'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_partner_agreement'      => 'nullable|file|mimes:pdf|max:4096',

                'doc_company_certificate'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_company_kra'            => 'nullable|file|mimes:pdf|max:4096',
                'doc_company_sbp'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_company_ids.*'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_company_board_resolution'=> 'nullable|file|mimes:pdf|max:4096',
            ],
            3 => [
                'account_name'               => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'bank_name'                  => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'bank_branch'                => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'account_number'             => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
                'account_type'               => $isDraft ? 'nullable|in:current,savings' : 'required|in:current,savings',
                'swift_code'                 => 'nullable|string|max:50',
                'mobile_money'               => 'nullable|string|max:30',
                'mobile_money_paybill'       => 'nullable|string|max:50',
            ],
            4 => [
                'shop_name'                  => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',
                'store_category_id'          => $isDraft ? 'nullable|integer|exists:pro_categories,id' : 'required|integer|exists:pro_categories,id',
                'store_description'          => $isDraft ? 'nullable|string' : 'required|string|min:10',
                'store_logo'                 => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
                'store_banner'               => 'nullable|file|mimes:jpg,jpeg,png,pdf,txt,doc,docx|max:8192',
                'store_city'                 => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
                'store_county'               => $isDraft ? 'nullable|string|max:120' : 'required|string|max:120',
                'store_sub_county'           => 'nullable|string|max:120',
                'store_ward'                 => 'nullable|string|max:120',
                'store_coords'               => 'nullable|string|max:60',
            ],
            5 => [
                'signature_data'             => 'nullable|string', // optional
            ],
            default => [],
        };

        // Extra uniqueness/OTP checks for step 1
        if ($step === 1) {
            if ($request->filled('email')) {
                $rules['email'] .= '|unique:users,email';
            }
            if ($request->filled('username')) {
                $rules['username'] .= '|unique:users,name';
            }
            if (!$isDraft && !session('seller_otp_verified')) {
                return back()
                    ->withFail(__('Please verify your phone (OTP) before continuing.'))
                    ->withInput($oldSafe(['current_step' => $step]));
            }
        }

        $validated = $request->validate($rules);

        // Normalize phones (non-fatal)
        $request->merge([
            'shop_phone'           => $this->normalizeKEPhone($request->input('shop_phone')),
            'contact_person_phone' => $this->normalizeKEPhone($request->input('contact_person_phone')),
            'mobile_money'         => $this->normalizeKEPhone($request->input('mobile_money')),
        ]);

        // Auto-activate setting
        $sellerSettings = gSellerSettings();
        $autoActive = (int)($sellerSettings['seller_auto_active'] ?? 0) === 1;

        DB::beginTransaction();
        try {
            // Find/create the user by email OR username
            $user = null;
            if ($request->filled('email')) {
                $user = \App\Models\User::where('email', $request->input('email'))->first();
            }
            if (!$user && $request->filled('username')) {
                $user = \App\Models\User::where('name', $request->input('username'))->first();
            }
            if (!$user) {
                $user = new \App\Models\User();
                $user->role_id  = 3; // seller
            }

            // Set core fields if present
            if ($request->filled('email'))    $user->email = $request->input('email');
            if ($request->filled('username')) $user->name  = $request->input('username');
            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
                $user->bactive  = base64_encode($request->input('password'));
            }

            // Step-specific fills (progressively)
            if ($step >= 1) {
                if ($request->filled('shop_phone')) $user->phone = $request->input('shop_phone');

                if ($request->filled('address_street') || $request->filled('address_city')) {
                    $user->address = trim(($request->input('address_street','')).' '.$request->input('address_city',''));
                }

                if ($request->filled('geo_unit_id')) $user->geo_unit_id = (int)$request->input('geo_unit_id');
                if ($request->filled('group_id'))    $user->group_id    = (int)$request->input('group_id');

                // Map seller_type → classification for users table
                if ($request->filled('seller_type')) {
                    $user->classification = match ($request->input('seller_type')) {
                        'company'      => 'company',
                        'partnership'  => 'partnership',
                        default        => 'individual',
                    };
                }
            }

            if ($step >= 4) {
                if ($request->filled('shop_name')) {
                    $user->shop_name = $request->input('shop_name');
                    // ensure unique slug
                    $user->shop_url  = $this->uniqueShopSlug($request->input('shop_name'), $user->id ?? null);
                }
            }

            // Status & KYC
            if ($isDraft) {
                $user->status_id  = 2;
                $user->kyc_status = 'not_submitted';
            } else {
                if ($step === 5) { // final confirm
                    $user->status_id  = $autoActive ? 1 : 2;
                    $user->kyc_status = 'pending';
                    $user->kyc_submitted_at = now();
                } else {
                    $user->status_id  = 2; // still in-progress
                }
            }

            // Track progress
            $user->onboarding_step = max((int)($user->onboarding_step ?? 1), $step);

            $user->save();

            // Step 2: documents
            if ($step >= 2) {
                $docs = \App\Models\SellerDocument::firstOrNew(['user_id' => $user->id]);

                // (Optional) If you collect document_number / kra_pin on Step 1 you can set them here
                if ($request->filled('personal_kra_pin')) {
                    $docs->kra_pin = $request->input('personal_kra_pin');
                }

                // Save ANY uploaded files present
                $map = [
                    'doc_sole_brs' => 'document_file_path',
                    'doc_sole_id'  => 'brand_auth_file_path', // or your preferred columns
                    'doc_sole_kra' => 'business_license_file_path',
                    'doc_sole_sbp' => 'business_license_file_path',

                    'doc_partner_brs'       => 'document_file_path',
                    'doc_partner_kra'       => 'business_license_file_path',
                    'doc_partner_sbp'       => 'business_license_file_path',
                    'doc_partner_agreement' => 'brand_auth_file_path',

                    'doc_company_certificate'      => 'document_file_path',
                    'doc_company_kra'              => 'business_license_file_path',
                    'doc_company_sbp'              => 'business_license_file_path',
                    'doc_company_board_resolution' => 'brand_auth_file_path',
                ];

                foreach ($map as $input => $column) {
                    if ($request->hasFile($input)) {
                        $docs->{$column} = $request->file($input)->store('seller_docs', 'public');
                    }
                }

                // Handle multi-file arrays if you later want (e.g., doc_company_ids[], doc_partner_ids[])
                // You could merge them to a single PDF or store JSON in a new column if needed.

                $docs->user_id = $user->id;
                $docs->save();
            }

            // Step 3: settlement
            if ($step >= 3) {
                $settle = \App\Models\SellerSettlement::firstOrNew(['user_id' => $user->id]);
                foreach ([
                             'account_name','bank_name','bank_branch','account_number',
                             'swift_code','mobile_money','mobile_money_paybill','account_type'
                         ] as $f) {
                    if ($request->filled($f)) $settle->{$f} = $request->input($f);
                }
                $settle->user_id = $user->id;
                $settle->save();
            }

            // Step 4: store
            if ($step >= 4) {
                $store = \App\Models\SellerStore::firstOrNew(['user_id' => $user->id]);
                if ($request->filled('store_category_id')) $store->store_category_id = (int)$request->input('store_category_id');
                if ($request->filled('store_description')) $store->store_description = $request->input('store_description');

                if ($request->hasFile('store_logo')) {
                    $store->store_logo_path = $request->file('store_logo')->store('seller_stores', 'public');
                }
                if ($request->hasFile('store_banner')) {
                    $store->store_banner_path = $request->file('store_banner')->store('seller_stores', 'public');
                }

                foreach (['store_city','store_county','store_sub_county','store_ward','store_coords'] as $f) {
                    if ($request->filled($f)) $store->{$f} = $request->input($f);
                }

                $store->user_id = $user->id;
                $store->save();
            }

            // Step 5: optional signature
            if ($step === 5 && $request->filled('signature_data')) {
                // Storage::disk('public')->put(
                //     'seller_signatures/'.$user->id.'.png',
                //     base64_decode(preg_replace('#^data:image/\w+;base64,#i','',$request->input('signature_data')))
                // );
            }

            DB::commit();

            // Redirect flow (+ flash safe inputs so Step 2 knows seller_type, etc.)
            if ($isDraft) {
                return back()
                    ->withSuccess(__('Draft saved. You can resume later from your account.'))
                    ->withInput($oldSafe(['current_step' => $step]));
            }

            if ($step < 5) {
                return back()
                    ->withSuccess(__('Saved. Continue to next step.'))
                    ->withInput($oldSafe(['current_step' => $step + 1]));
            }

            // Final
            if ((int)$user->status_id === 1) {
                return back()->withSuccess(__('Thanks! You have registered successfully. Please login.'));
            }

            return back()->withSuccess(__('Thanks! Registration submitted. Your account is pending review.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('SellerRegister failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()
                ->withFail(__('Oops! Registration could not be completed. Please try again.'))
                ->withInput($oldSafe(['current_step' => $step]));
        }
    }

    /** OTP: send (returns {status:'ok'} on success) */
    public function sendOtp(Request $req)
    {
        $raw   = $req->input('phone');
        $phone = $this->normalizeKEPhone($raw);
        if (!$phone) {
            return response()->json(['status'=>'error','message'=>'Phone is required'], 422);
        }

        try {
            $code = random_int(100000, 999999);
            SmsService::otpVerification($phone, $code);

            session([
                'seller_otp_code' => (string)$code,
                'seller_otp_exp'  => now()->addMinutes(10),
                'seller_otp_verified' => false,
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            \Log::error('sendOtp failed: '.$e->getMessage());
            return response()->json(['status'=>'error','message'=>'Failed to send code'], 500);
        }
    }

    /** OTP: verify (returns {status:'ok'} when code matches & not expired) */
    public function verifyOtp(Request $req)
    {
        $code = (string) $req->input('code');
        $ok = session('seller_otp_code') === $code && now()->lt(session('seller_otp_exp'));

        if ($ok) {
            session(['seller_otp_verified' => true]);
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status'=>'error','message'=>'invalid'], 422);
    }


}
