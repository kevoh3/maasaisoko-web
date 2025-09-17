<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tp_option;
use App\Models\Country;

class ProductGeoController extends Controller
{
    /** ===== Helpers (mirroring BrandController style) ===== */

    private function countyLevelId(): int
    {
        return (int) (DB::table('geo_levels')->whereIn('name', ['county', 'County'])->value('id') ?? 1);
    }

    private function countiesForCountry(int $countryId)
    {
        return DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $this->countyLevelId())
            ->orderBy('name')
            ->get(['id','name','slug','code']);
    }

    /** MySQL 8+ recursive CTE: selected geo + descendants (including self) */
    private function geoDescendantIdsIncludingSelf(int $geoId): array
    {
        if ($geoId <= 0) return [];
        $rows = DB::select("
            WITH RECURSIVE d AS (
              SELECT id,parent_id FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id,g.parent_id FROM geo_units g JOIN d ON g.parent_id = d.id
            ) SELECT id FROM d
        ", [$geoId]);
        return array_map(fn($r) => (int)$r->id, $rows);
    }

    /** Human-readable path (e.g., "Nairobi / Westlands / Kitisuru") */
    private function geoPath(int $geoId): string
    {
        if ($geoId <= 0) return '';
        $rows = DB::select("
            WITH RECURSIVE a AS (
              SELECT id,parent_id,name FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id,g.parent_id,g.name FROM geo_units g JOIN a ON a.parent_id = g.id
            ) SELECT name FROM a
        ", [$geoId]);

        if (!$rows) return '';
        $names = array_reverse(array_map(fn($r) => $r->name, $rows));
        return implode(' / ', $names);
    }

    /**
     * Counties with product counts (no brand filter; strictly products.geo_unit_id).
     * This mirrors Brand's "countiesWithCountsForBrand" but without brand_id.
     */
    private function countiesWithCounts(int $countryId)
    {
        $counties = $this->countiesForCountry($countryId);

        foreach ($counties as $c) {
            $geoIds = $this->geoDescendantIdsIncludingSelf((int)$c->id);

            $c->product_count = DB::table('products')
                ->join('users', 'products.user_id', '=', 'users.id')
                ->where('products.is_publish', 1)
                ->where('users.status_id', 1)
                ->whereIn('products.geo_unit_id', $geoIds) // strictly product geo
                ->count();
        }

        return $counties;
    }

    /** ===== Pages ===== */

    // Route: GET /county/{id}/{title}  -> name('frontend.county')
    public function getProductByGeoPage($id, $title)
    {
        $geoId        = (int) $id;
        $selectedGeo  = $geoId;
        $selectedPath = $this->geoPath($selectedGeo);

        // Match brand layout choice so view behaves the same
        $sData = Tp_option::where('option_name', 'page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';

        // Counties list (Kenya by default; change if multi-country)
        $countryId = (int) (request('country_id') ?? Country::where('country_name', 'Kenya')->value('id'));
        $counties  = $this->countiesWithCounts($countryId);

        $perPage = in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12;

        $desc = $this->geoDescendantIdsIncludingSelf($geoId);

        $datalist = DB::table('products')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
            ->where('products.is_publish', 1)
            ->where('users.status_id', 1)
            ->when(!empty($desc), fn($q) => $q->whereIn('products.geo_unit_id', $desc))
            ->orderBy('products.id', 'desc')
            ->paginate($perPage);

        foreach ($datalist as $p) {
            $Reviews = getReviews($p->id);
            $p->TotalReview      = $Reviews[0]->TotalReview;
            $p->TotalRating      = $Reviews[0]->TotalRating;
            $p->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        return view('frontend.county', compact(
            'datalist', 'selectedGeo', 'selectedPath', 'title',
            'brand_variation', 'counties'
        ));
    }

    // Route: GET /frontend/getCountyGrid -> name('frontend.getCountyGrid')
    public function getProductByGeoGrid(Request $request)
    {
        if (!$request->ajax()) abort(404);

        $geoId      = (int) $request->get('geo', 0);
        $min_price  = $request->min_price === '' ? 0 : (float) $request->min_price;
        $max_price  = $request->max_price !== '' ? (float) $request->max_price : null;

        $sData = Tp_option::where('option_name','page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';
        $num = $request->num ?: (in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12);

        // sorting
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

        $desc = $geoId ? $this->geoDescendantIdsIncludingSelf($geoId) : [];

        $q = DB::table('products')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
            ->where('products.is_publish', 1)
            ->where('users.status_id', 1);

        if (!empty($desc)) {
            $q->whereIn('products.geo_unit_id', $desc);
        }

        if ($max_price !== null) {
            $q->whereBetween('products.sale_price', [$min_price, $max_price]);
        }

        $datalist = $q->orderBy('products.'.$field_name, $order_name)
            ->paginate($num);

        foreach ($datalist as $p) {
            $Reviews = getReviews($p->id);
            $p->TotalReview      = $Reviews[0]->TotalReview;
            $p->TotalRating      = $Reviews[0]->TotalRating;
            $p->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        return view('frontend.partials.county-grid', compact('brand_variation', 'datalist'))->render();
    }
}
