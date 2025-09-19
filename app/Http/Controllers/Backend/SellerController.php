<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Package;
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
use App\Services\WaaSService;

use Illuminate\Http\Request;
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

    /** Normalize Kenyan phone to +2547XXXXXXXX */
    private function normalizeKEPhone(?string $raw): ?string {
        if (!$raw) return null;
        $s = trim($raw);
        // keep leading + for +254, drop other non-digits
        $s = preg_replace('/(?!^\+)[^\d]/', '', $s);

        // Already +2547XXXXXXXX
        if (preg_match('/^\+2547\d{8}$/', $s)) return $s;

        // 2547XXXXXXXX
        if (preg_match('/^2547\d{8}$/', $s)) return '+'.$s;

        // 07XXXXXXXX -> +2547XXXXXXXX
        if (preg_match('/^07\d{8}$/', $s)) return '+254'.substr($s,1);

        // 7XXXXXXXXX (9 digits) -> +2547XXXXXXXX
        if (preg_match('/^7\d{8}$/', $s)) return '+254'.$s;

        return $s; // fallback (shouldn’t happen if validated, but safe)
    }

    public function SellerRegister(Request $request)
    {
        $gtext       = gtext();
        $recaptchaOn = (int) $gtext['is_recaptcha'] === 1;
        $saveMode    = $request->input('save_mode', 'submit'); // 'draft' | 'submit'
        $isDraft     = $saveMode === 'draft';

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

        // Accept +2547XXXXXXXX OR 07XXXXXXXX OR 7XXXXXXXX (with optional spaces/dashes)
//        $kePhoneRegex = 'regex:/^(?:\+254|0)?\s?7\d(?:[\s-]?\d){7}$/';

        // In SellerRegister() — build rules using the constant correctly:
        $rulesBase = [
            'name'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'email'       => $isDraft ? 'nullable|email' : 'required|email',
            'password'    => $isDraft ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
            'shop_name'   => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',

            // ✅ Use "regex:{$pattern}" (string) — NOT the raw pattern
            'shop_phone'  => [$isDraft ? 'nullable' : 'required', 'regex:'.self::KE_PHONE_PCRE],

            'geo_unit_id' => $isDraft ? 'nullable|integer|exists:geo_units,id' : 'required|integer|exists:geo_units,id',
            'address_line'=> $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'classification'  => $isDraft ? 'nullable|in:individual,company' : 'required|in:individual,company',
            'group_option'    => 'nullable|in:group',
            'group_id'        => 'nullable|integer|exists:groups,id',

            // Step 2
            'document_number'        => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'document_file'          => $isDraft ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096' : 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'business_license_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'kra_pin'                => 'nullable|string|max:20',
            'brand_auth_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'contact_person_name'    => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'contact_person_phone'   => [$isDraft ? 'nullable' : 'required', 'regex:'.self::KE_PHONE_PCRE],

            // Step 3
            'bank_name'      => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'bank_branch'    => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'account_name'   => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'account_number' => $isDraft ? 'nullable|string|max:191' : 'required|string|max:191',
            'swift_code'     => 'nullable|string|max:50',
            'mobile_money'   => ['nullable', 'regex:'.self::KE_PHONE_PCRE],

            // Step 4
            'store_category_id' => $isDraft ? 'nullable|integer|exists:pro_categories,id' : 'required|integer|exists:pro_categories,id',
            'store_description' => $isDraft ? 'nullable|string' : 'required|string',
            'shipping_methods'  => 'nullable|array',
            'shipping_methods.*'=> 'in:local_pickup,within_county,nationwide',
            'store_logo'        => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'store_banner'      => 'nullable|image|mimes:jpg,jpeg,png|max:8192',
        ];

        // Email uniqueness logic
        $existingUser = $request->filled('email')
            ? User::where('email', $request->input('email'))->first()
            : null;
        if (!$existingUser) {
            $rulesBase['email'] = ($isDraft ? 'nullable' : 'required').'|email|unique:users,email';
        } else {
            $rulesBase['email'] = ($isDraft ? 'nullable' : 'required').'|email';
        }

        if ($request->input('group_option') === 'group') {
            $rulesBase['group_id'] = 'required|integer|exists:groups,id';
        }

        $validated = $request->validate($rulesBase);

        // --- Normalize phones to E.164 (+2547XXXXXXXX) ---
        $request->merge([
            'shop_phone'           => $this->normalizeKEPhone($request->input('shop_phone')),
            'contact_person_phone' => $this->normalizeKEPhone($request->input('contact_person_phone')),
            'mobile_money'         => $this->normalizeKEPhone($request->input('mobile_money')),
        ]);

        // Seller auto-active toggle
        $sellerSettings = gSellerSettings();
        $autoActive = (int)($sellerSettings['seller_auto_active'] ?? 0) === 1;

        DB::beginTransaction();
        try {
            // CREATE or UPDATE user for draft resume
            if (!$existingUser) {
                $user = new User();
                $user->email = $validated['email'] ?? null;
                $user->password = $request->filled('password') ? Hash::make($request->input('password')) : Hash::make(Str::random(12));
                $user->bactive  = $request->filled('password') ? base64_encode($request->input('password')) : null;
                $user->role_id  = 3; // seller
            } else {
                $user = $existingUser;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                    $user->bactive  = base64_encode($request->input('password'));
                }
                if ((int)$user->role_id !== 3) $user->role_id = 3;
            }

            // Core info
            if ($request->filled('name'))         $user->name        = $request->input('name');
            if ($request->filled('shop_name'))    $user->shop_name   = $request->input('shop_name');
            if ($request->filled('shop_phone'))   $user->phone       = $request->input('shop_phone');
            if ($request->filled('address_line')) $user->address     = $request->input('address_line');
            if ($request->filled('geo_unit_id'))  $user->geo_unit_id = (int)$request->input('geo_unit_id');

            if ($request->filled('shop_name')) {
                $user->shop_url = $this->uniqueShopSlug($request->input('shop_name'), $user->id ?? null);
            }

            // Meta
            if ($request->filled('classification'))  $user->classification  = $request->input('classification');
            if ($request->filled('document_number')) $user->document_number = $request->input('document_number');
            if ($request->input('group_option') === 'group' && $request->filled('group_id')) {
                $user->group_id = (int)$request->input('group_id');
            } else {
                if ($isDraft && !$request->filled('group_id')) $user->group_id = null;
            }

            // Status & KYC
            if ($isDraft) {
                $user->status_id  = 2;
                $user->kyc_status = 'not_submitted';
            } else {
                $user->status_id  = $autoActive ? 1 : 2;
                $user->kyc_status = 'pending';
                $user->kyc_submitted_at = now();
            }

            $user->save();

            // Documents
            $docs = SellerDocument::firstOrNew(['user_id' => $user->id]);
            if ($request->filled('document_number')) $docs->document_number = $request->input('document_number');
            if ($request->filled('kra_pin'))         $docs->kra_pin         = $request->input('kra_pin');

            if ($request->hasFile('document_file'))         $docs->document_file_path         = $request->file('document_file')->store('seller_docs', 'public');
            if ($request->hasFile('business_license_file')) $docs->business_license_file_path = $request->file('business_license_file')->store('seller_docs', 'public');
            if ($request->hasFile('brand_auth_file'))       $docs->brand_auth_file_path       = $request->file('brand_auth_file')->store('seller_docs', 'public');

            $docs->user_id = $user->id;
            $docs->save();

            // Settlement
            $settle = SellerSettlement::firstOrNew(['user_id' => $user->id]);
            foreach (['bank_name','bank_branch','account_name','account_number','swift_code','mobile_money'] as $f) {
                if ($request->filled($f)) $settle->{$f} = $request->input($f);
            }
            $settle->user_id = $user->id;
            $settle->save();

            // Store
            $store = SellerStore::firstOrNew(['user_id' => $user->id]);
            if ($request->filled('store_category_id')) $store->store_category_id = (int)$request->input('store_category_id');
            if ($request->filled('store_description')) $store->store_description = $request->input('store_description');
            if ($request->has('shipping_methods')) {
                $store->shipping_methods = array_values(array_unique(array_filter((array)$request->input('shipping_methods'))));
            } else {
                $store->shipping_methods = $isDraft ? ($store->shipping_methods ?? []) : [];
            }
            if ($request->hasFile('store_logo'))   $store->store_logo_path   = $request->file('store_logo')->store('seller_stores', 'public');
            if ($request->hasFile('store_banner')) $store->store_banner_path = $request->file('store_banner')->store('seller_stores', 'public');
            $store->user_id = $user->id;
            $store->save();

            // Post-submit hooks
            if (!$isDraft) {
                if (isset($this->waasService)) {
                    $payload = [
                        'name'       => $user->shop_name ?: $user->name,
                        'mobile_no'  => $user->phone,
                        'request_id' => $user->email,
                    ];
                    try { $this->waasService->addBeneficiary($payload, $user->id); }
                    catch (\Throwable $e) { \Log::warning('waasService addBeneficiary failed: '.$e->getMessage()); }
                }

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

    /** Generate a unique shop_url from a name. */
//    private function uniqueShopSlug(string $name = null, $excludeUserId = null): string
//    {
//        $base = Str::slug($name ?? '') ?: 'shop';
//        $slug = $base;
//        $i = 2;
//        while (
//        DB::table('users')
//            ->where('shop_url', $slug)
//            ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
//            ->exists()
//        ) {
//            $slug = $base . '-' . $i++;
//        }
//        return $slug;
//    }

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
}
