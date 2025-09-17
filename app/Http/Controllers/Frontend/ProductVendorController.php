<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tp_option;
use App\Models\User;
use App\Models\Country;

class ProductVendorController extends Controller
{
    /* ================= Helpers (mirror Brand/County patterns) ================= */

    private function countyLevelId(): int
    {
        return (int) (DB::table('geo_levels')->whereIn('name', ['county','County'])->value('id') ?? 1);
    }

    private function countiesForCountry(int $countryId)
    {
        return DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $this->countyLevelId())
            ->orderBy('name')
            ->get(['id','name','slug','code']);
    }

    /** MySQL 8+ recursive CTE: geo + descendants (including self) */
    private function geoDescendantIdsIncludingSelf(int $geoId): array
    {
        if ($geoId <= 0) return [];
        $rows = DB::select("
            WITH RECURSIVE d AS (
              SELECT id, parent_id FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id, g.parent_id
              FROM geo_units g
              JOIN d ON g.parent_id = d.id
            )
            SELECT id FROM d
        ", [$geoId]);

        return array_map(fn($r) => (int)$r->id, $rows);
    }

    /** Human path for a geo (e.g., "Nairobi / Westlands / Kitisuru") */
    private function geoAncestorNamesPath(int $geoId): string
    {
        if ($geoId <= 0) return '';
        $rows = DB::select("
            WITH RECURSIVE a AS (
              SELECT id, parent_id, name FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id, g.parent_id, g.name
              FROM geo_units g
              JOIN a ON a.parent_id = g.id
            )
            SELECT name FROM a
        ", [$geoId]);

        if (!$rows) return '';
        $names = array_reverse(array_map(fn($r) => $r->name, $rows));
        return implode(' / ', $names);
    }

    /**
     * Counties with product counts for a specific VENDOR.
     * (Counts strictly by products.geo_unit_id within each county's descendant set.)
     */
    private function countiesWithCountsForVendor(int $countryId, int $vendorId)
    {
        $counties = $this->countiesForCountry($countryId);

        foreach ($counties as $c) {
            $geoIds = $this->geoDescendantIdsIncludingSelf((int)$c->id);

            $c->product_count = DB::table('products')
                ->join('users','products.user_id','=','users.id')
                ->where('products.is_publish',1)
                ->where('users.status_id',1)
                ->where('products.user_id', $vendorId)
                ->whereIn('products.geo_unit_id', $geoIds) // strict product geo
                ->count();
        }

        return $counties;
    }

    /* ================= Pages ================= */

    // GET /seller/{id}/{title}   -> name('frontend.vendor')
    public function getProductByVenderdPage($id, $title)
    {
        $vendorId     = (int) $id;
        $selectedGeo  = (int) request('geo', 0);            // for sidebar/filter state
        $selectedPath = $this->geoAncestorNamesPath($selectedGeo);

        // Layout variation (same as Brand/County)
        $sData = Tp_option::where('option_name', 'page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';
        $perPage = in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12;

        // Vendor info for header/meta
        $vendor = User::select('id','shop_name','shop_url','status_id')->find($vendorId);

        // Counties list (Kenya by default; change if multi-country)
        $countryId = (int) (request('country_id') ?? Country::where('country_name','Kenya')->value('id'));
        $counties  = $this->countiesWithCountsForVendor($countryId, $vendorId);

        // Optional geo filter (strictly by products.geo_unit_id)
        $desc = $selectedGeo ? $this->geoDescendantIdsIncludingSelf($selectedGeo) : [];

        $q = DB::table('products')
            ->join('users','products.user_id','=','users.id')
            ->select('products.*','users.shop_name','users.id as seller_id','users.shop_url')
            ->where('products.is_publish',1)
            ->where('users.status_id',1)
            ->where('products.user_id',$vendorId);

        if (!empty($desc)) {
            $q->whereIn('products.geo_unit_id', $desc);
        }

        $datalist = $q->orderBy('products.id','desc')
            ->paginate($perPage);

        // Attach review stats (to match other pages)
        foreach ($datalist as $p) {
            $Reviews = getReviews($p->id);
            $p->TotalReview      = $Reviews[0]->TotalReview;
            $p->TotalRating      = $Reviews[0]->TotalRating;
            $p->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        $metadata = [
            'name' => $vendor->shop_name ?? $title,
            'thumbnail' => '', // set if you have a shop image
        ];
        $params = ['vendor_id' => $vendorId];

        return view('frontend.vendor', compact(
            'params','metadata','brand_variation','datalist','vendor',
            'counties','selectedGeo','selectedPath'
        ));
    }

    // GET /frontend/getVendorGrid   -> name('frontend.getVendorGrid')
    public function getProductByVenderdGrid(Request $request)
    {
        if (!$request->ajax()) abort(404);

        $vendorId  = (int) $request->vendor_id;
        $selectedGeo = (int) $request->geo;  // allow county filter via AJAX too

        $min_price = $request->min_price === '' ? 0 : (float)$request->min_price;
        $max_price = $request->max_price !== '' ? (float)$request->max_price : null;

        $sData = Tp_option::where('option_name','page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';
        $num = $request->num ?: (in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12);

        // sort mapping
        $field_name = 'id'; $order_name = 'desc';
        if ($request->sortby) {
            $map = [
                'date_asc'  => ['created_at','asc'],
                'date_desc' => ['created_at','desc'],
                'name_asc'  => ['title','asc'],
                'name_desc' => ['title','desc'],
            ];
            [$field_name, $order_name] = $map[$request->sortby] ?? ['id','desc'];
        }

        $q = DB::table('products')
            ->join('users','products.user_id','=','users.id')
            ->select('products.*','users.shop_name','users.id as seller_id','users.shop_url')
            ->where('products.is_publish',1)
            ->where('users.status_id',1)
            ->where('products.user_id',$vendorId);

        // optional price
        if ($max_price !== null) {
            $q->whereBetween('products.sale_price', [$min_price, $max_price]);
        }

        // optional geo filter (strict product geo)
        if ($selectedGeo) {
            $desc = $this->geoDescendantIdsIncludingSelf($selectedGeo);
            if (!empty($desc)) {
                $q->whereIn('products.geo_unit_id', $desc);
            }
        }

        $datalist = $q->orderBy('products.'.$field_name, $order_name)
            ->paginate($num);

        foreach ($datalist as $p) {
            $Reviews = getReviews($p->id);
            $p->TotalReview      = $Reviews[0]->TotalReview;
            $p->TotalRating      = $Reviews[0]->TotalRating;
            $p->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        return view('frontend.partials.vendor-grid', compact('brand_variation','datalist'))->render();
    }
}
