<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;

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
use Illuminate\Validation\Rule; // <-- add this
class GroupsController extends Controller
{

    protected $waasService;

    public function __construct(WaaSService $waasService)
    {
        $this->waasService = $waasService;
    }

    public function LoadSellerRegister()
    {
        return view('frontend.seller-register');
    }
    public function index(Request $request)
    {
        $search         = trim((string) $request->get('search', ''));
        $status         = $request->get('status');           // pending|active|suspended|inactive
        $verifiedStatus = $request->get('verified_status');  // pending|verified|rejected
        $statuslist = DB::table('user_status')->orderBy('id', 'asc')->get();
        $countrylist = DB::table('countries')->where('is_publish', '=', 1)->orderBy('country_name', 'asc')->get();
        $media_datalist = Media_option::orderBy('id','desc')->paginate(28);

        // Counts for quick filters
        $Counts = [
            'all'       => Group::count(),
            'active'    => Group::where('status', 'active')->count(),
            'pending'   => Group::where('status', 'pending')->count(),
            'suspended' => Group::where('status', 'suspended')->count(),
            'inactive'  => Group::where('status', 'inactive')->count(),
        ];

        $groups = Group::with(['verifier:id,name', 'approver:id,name'])
            ->withCount('members') // uses Group::members() -> hasMany(User::class, 'group_id')
            ->when($status, fn($q)        => $q->where('status', $status))
            ->when($verifiedStatus, fn($q)=> $q->where('verified_status', $verifiedStatus))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('kra_pin', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('backend.groups.index', compact('groups', 'Counts', 'search', 'status', 'verifiedStatus','statuslist','media_datalist','countrylist'));
    }
    /**
     * Create/Update a Group (used by GroupEntry_formId via AJAX).
     * Expects optional "id" for updates; creates a new record when id is empty.
     * Returns JSON: { msgType: 'success'|'error', msg: string, id?: int }
     */
    public function saveGroupData(Request $request)
    {
        $id = $request->input('id');

        // Validate
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:191'],
            'registration_number'   => ['nullable', 'string', 'max:191', Rule::unique('groups', 'registration_number')->ignore($id)],
            'type'                  => ['nullable', 'string', 'max:50'],
            'industry'              => ['nullable', 'string', 'max:100'],

            'contact_person'        => ['nullable', 'string', 'max:191'],
            'phone'                 => ['required', 'string', 'max:50'],
            'email'                 => ['nullable', 'email', 'max:191', Rule::unique('groups', 'email')->ignore($id)],
            'address'               => ['nullable', 'string', 'max:255'],
            'county'                => ['nullable', 'string', 'max:100'],
            'sub_county'            => ['nullable', 'string', 'max:100'],

            'kra_pin'               => ['nullable', 'string', 'max:20', Rule::unique('groups', 'kra_pin')->ignore($id)],
            'business_permit_number'=> ['nullable', 'string', 'max:100'],
            'certificate_of_incorporation' => ['nullable', 'string', 'max:100'],
            'tax_compliance_certificate'   => ['nullable', 'string', 'max:100'],

            'bank_name'             => ['nullable', 'string', 'max:191'],
            'bank_branch'           => ['nullable', 'string', 'max:191'],
            'bank_account_number'   => ['nullable', 'string', 'max:191'],

            'website'               => ['nullable', 'string', 'max:255'],
            'social_media'          => ['nullable', 'string', 'max:255'],

            // Status/verification
            'status'                => ['required', Rule::in(['pending','active','suspended','inactive'])],
            'verified_status'       => ['nullable', Rule::in(['pending','verified','rejected'])],
            'verified_notes'        => ['nullable', 'string', 'max:1000'],

            // Media picker stores a path string; we persist it in "photo"
            'logo'                  => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $group = DB::transaction(function () use ($request, $id) {
                $group = $id ? Group::findOrFail($id) : new Group();

                // Map fields
                $group->name                         = $request->input('name');
                $group->registration_number          = $request->input('registration_number');
                $group->type                         = $request->input('type');
                $group->industry                     = $request->input('industry');

                $group->contact_person               = $request->input('contact_person');
                $group->phone                        = $request->input('phone');
                $group->email                        = $request->input('email');
                $group->address                      = $request->input('address');
                $group->county                       = $request->input('county');
                $group->sub_county                   = $request->input('sub_county');

                $group->kra_pin                      = $request->input('kra_pin');
                $group->business_permit_number       = $request->input('business_permit_number');
                $group->certificate_of_incorporation = $request->input('certificate_of_incorporation');
                $group->tax_compliance_certificate   = $request->input('tax_compliance_certificate');

                $group->bank_name                    = $request->input('bank_name');
                $group->bank_branch                  = $request->input('bank_branch');
                $group->bank_account_number          = $request->input('bank_account_number');

                $group->website                      = $request->input('website');
                $group->social_media                 = $request->input('social_media');

                // Persist logo path into "photo" (your blades check $row->logo ?? $row->photo)
                if ($request->filled('logo')) {
                    $group->photo = $request->input('logo');
                }

                $group->status                       = $request->input('status') ?? 'pending';
                $group->verified_status              = $request->input('verified_status') ?: 'pending';
                $group->verified_notes               = $request->input('verified_notes');

                // Auto-stamp verification metadata when moving to "verified"
                if ($group->isDirty('verified_status') && $group->verified_status === 'verified') {
                    $group->verified_by  = Auth::id();
                    $group->verified_at  = now();
                }

                $group->save();

                return $group;
            });

            return response()->json([
                'msgType' => 'success',
                'msg'     => $id ? __('Group updated successfully.') : __('Group created successfully.'),
                'id'      => $group->id,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'msgType' => 'error',
                'msg'     => __('Something went wrong while saving the group.'),
            ], 422);
        }
    }

    /**
     * Load a group by id for editing (used by onLoadEditData).
     * Returns JSON: { group: {...} }
     */
    public function getGroupById(Request $request)
    {
        $request->validate(['id' => ['required','integer','min:1']]);

        $group = Group::withCount('members')->findOrFail($request->id);

        return response()->json([
            'group' => $group,
        ]);
    }

    /**
     * Delete a group (guarded: cannot delete if it still has members).
     */
    public function deleteGroup(Request $request)
    {
        $request->validate(['id' => ['required','integer','min:1']]);

        $group = Group::withCount('members')->findOrFail($request->id);

        if ($group->members_count > 0) {
            return response()->json([
                'msgType' => 'error',
                'msg'     => __('Cannot delete a group that still has members.'),
            ], 422);
        }

        $group->delete();

        return response()->json([
            'msgType' => 'success',
            'msg'     => __('Group deleted successfully.'),
        ]);
    }

    /**
     * Bulk actions: active/inactive/delete (same UX as sellers).
     * Expects ids[]=... and BulkAction in {active,inactive,delete}
     */
    public function bulkActionGroups(Request $request)
    {
        $request->validate([
            'ids'        => ['required'],
            'BulkAction' => ['required', Rule::in(['active','inactive','delete'])],
        ]);

        $ids = is_array($request->ids) ? $request->ids : explode(',', (string) $request->ids);
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return response()->json(['msgType' => 'error', 'msg' => __('No valid records selected.')], 422);
        }

        if ($request->BulkAction === 'delete') {
            // Guard: only delete groups with zero members
            $deletableIds = Group::withCount('members')
                ->whereIn('id', $ids)
                ->get()
                ->filter(fn($g) => $g->members_count == 0)
                ->pluck('id')
                ->all();

            Group::whereIn('id', $deletableIds)->delete();

            $skipped = count($ids) - count($deletableIds);
            $msg = __('Deleted :n groups. :s skipped due to existing members.', ['n' => count($deletableIds), 's' => $skipped]);

            return response()->json(['msgType' => 'success', 'msg' => $msg]);
        }

        // Status updates
        $newStatus = $request->BulkAction === 'active' ? 'active' : 'inactive';
        Group::whereIn('id', $ids)->update(['status' => $newStatus]);

        return response()->json([
            'msgType' => 'success',
            'msg'     => __('Updated :n groups to :status.', ['n' => count($ids), 'status' => $newStatus]),
        ]);
    }

    public function SellerRegister(Request $request)
    {
        $gtext = gtext();

        $secretkey = $gtext['secretkey'];
        $recaptcha = $gtext['is_recaptcha'];
        if($recaptcha == 1){
            $request->validate([
                'g-recaptcha-response' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
                'shop_name' => 'required',
//				'shop_url' => 'required',
                'shop_phone' => 'required',
            ]);

            $captcha = $request->input('g-recaptcha-response');

            $ip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretkey).'&response='.urlencode($captcha).'&remoteip'.$ip;
            $response = file_get_contents($url);
            $responseKeys = json_decode($response, true);
            if($responseKeys["success"] == false) {
                return redirect("seller/register")->withFail(__('The recaptcha field is required'));
            }
        }else{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
                'shop_name' => 'required',
//				'shop_url' => 'required',
                'shop_phone' => 'required',
            ]);
        }

        $SellerSettings = gSellerSettings();
        if($SellerSettings['seller_auto_active'] == 1){
            $status_id = 1;
        }else{
            $status_id = 2;
        }

        $data = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'bactive' => base64_encode($request->input('password')),
            'shop_name' => $request->input('shop_name'),
            'shop_url' => $request->input('shop_name'),
            'phone' => $request->input('shop_phone'),
            'status_id' => $status_id,
            'role_id' => 3
        );

        $response = User::create($data);

        if($response){
            //call propel here-------
            $data = [
                'name' => $request->input('shop_name'),
                'mobile_no' =>$request->input('shop_phone'),
                'request_id' => $request->input('email'),
            ];

            // $response = WaaSService::createWallet($data);
            $this->waasService->addBeneficiary($data,$response->id);

            if($gtext['is_mailchimp'] == 1){
                $name = $request->input('name');
                $email_address = $request->input('email');

                $HTTP_Status = self::MailChimpSubscriber($name, $email_address);
                if($HTTP_Status == 200){
                    $SubscriberCount = Subscriber::where('email_address', '=', $email_address)->count();
                    if($SubscriberCount == 0){
                        $data = array(
                            'email_address' => $email_address,
                            'first_name' => $name,
                            'last_name' => $name,
                            'status' => 'subscribed'
                        );
                        Subscriber::create($data);
                    }
                }
            }

            if($status_id == 1){
                return redirect()->back()->withSuccess(__('Thanks! You have register successfully. Please login.'));
            }else{
                return redirect()->back()->withSuccess(__('Thanks! You have register successfully. Your account is pending for review.'));
            }

        }else{
            return redirect()->back()->withFail(__('Oops! You are failed registration. Please try again.'));
        }
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
    public function getGroupssPageLoad(){
        $statuslist = DB::table('user_status')->orderBy('id', 'asc')->get();
        $countrylist = DB::table('countries')->where('is_publish', '=', 1)->orderBy('country_name', 'asc')->get();
        $media_datalist = Media_option::orderBy('id','desc')->paginate(28);

        $AllCount = User::where('role_id', '=', 3)->count();
        $ActiveCount = User::where('status_id', '=', 1)->where('role_id', '=', 3)->count();
        $InactiveCount = User::where('status_id', '=', 2)->where('role_id', '=', 3)->count();

        $datalist = DB::table('users')
            ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->join('user_status', 'users.status_id', '=', 'user_status.id')
            ->select('users.*', 'user_roles.role', 'user_status.status')
            ->where('users.role_id', 3)
            ->orderBy('users.id','desc')
            ->paginate(20);

        return view('backend.groups.index', compact('AllCount', 'ActiveCount', 'InactiveCount', 'statuslist', 'countrylist', 'media_datalist', 'datalist'));
    }

    //Get data for Sellers Pagination
    public function getGroupsTableData(Request $request)
    {
        $status         = $request->status;            // '0' = all, else: pending|active|suspended|inactive
        $verifiedStatus = $request->verified_status;   // '', else: pending|verified|rejected
        $search         = trim((string) $request->search);

        if (!$request->ajax()) {
            abort(404);
        }

        $groups = \App\Models\Group::with(['verifier:id,name', 'approver:id,name'])
            ->withCount('members') // provides members_count for the blade
            ->when($status && $status !== '0', fn($q) => $q->where('status', $status))
            ->when($verifiedStatus, fn($q) => $q->where('verified_status', $verifiedStatus))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('kra_pin', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // IMPORTANT: returns the partial you shared and passes $groups (not $datalist)
        return view('backend.groupd.partials.groups_table', compact('groups'))->render();
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
    public function getSellerById(Request $request){
        $gtext = gtext();
        $lan = glan();

        $datalist = array(
            'seller_data' => '',
            'bank_information' => '',
            'CurrentBalance' => 0,
            'OrderBalance' => 0,
            'WithdrawalBalance' => 0,
            'TotalProducts' => 0
        );

        $id = $request->id;

        $data = DB::table('users')->where('id', $id)->first();
        $data->bactive = base64_decode($data->bactive);
        $data->created_at = date('d F, Y', strtotime($data->created_at));

        $bankInfoData = DB::table('bank_informations')->where('seller_id', $id)->first();

        $datalist['seller_data'] = $data;
        $datalist['bank_information'] = $bankInfoData;

        $sql = "SELECT (IFNULL(SUM(b.total_price), 0) + IFNULL(SUM(b.tax), 0)) AS OrderBalance
		FROM order_masters a
		INNER JOIN order_items b ON a.id = b.order_master_id
		WHERE a.payment_status_id = 1
		AND a.order_status_id = 4
		AND a.seller_id = '".$id."';";
        $aRow = DB::select($sql);
        $OrderBalance = $aRow[0]->OrderBalance;

        $sql1 = "SELECT (IFNULL(SUM(amount), 0) + IFNULL(SUM(fee_amount), 0)) AS WithdrawalBalance
		FROM withdrawals
		WHERE seller_id = '".$id."'
		AND status_id = 3;";
        $aRow1 = DB::select($sql1);
        $WithdrawalBalance = $aRow1[0]->WithdrawalBalance;
        $OrderWithdrawalBalance = ($OrderBalance - $WithdrawalBalance);

        if($gtext['currency_position'] == 'left'){
            $datalist['CurrentBalance'] = $gtext['currency_icon'].NumberFormat($OrderWithdrawalBalance);
            $datalist['OrderBalance'] = $gtext['currency_icon'].NumberFormat($OrderBalance);
            $datalist['WithdrawalBalance'] = $gtext['currency_icon'].NumberFormat($WithdrawalBalance);
        }else{
            $datalist['CurrentBalance'] = NumberFormat($OrderWithdrawalBalance).$gtext['currency_icon'];
            $datalist['OrderBalance'] = NumberFormat($OrderBalance).$gtext['currency_icon'];
            $datalist['WithdrawalBalance'] = NumberFormat($WithdrawalBalance).$gtext['currency_icon'];
        }

        $sql2 = "SELECT COUNT(id) AS TotalProducts
		FROM products
		WHERE user_id = '".$id."'
		AND is_publish = 1
		AND lan = '".$lan."';";
        $aRow2 = DB::select($sql2);
        $datalist['TotalProducts'] = $aRow2[0]->TotalProducts;

        return response()->json($datalist);
    }

    //Delete data for Sellers
    public function deleteSeller(Request $request){

        $res = array();

        $id = $request->id;

        if($id != ''){

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
