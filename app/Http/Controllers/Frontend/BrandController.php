<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Tp_option;
use App\Models\Country;

class BrandController extends Controller
{
    // ===== Helpers (same idea as category page) =====
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

    // MySQL 8+ recursive CTE
    private function geoDescendantIdsIncludingSelf(int $geoId): array
    {
        if (!$geoId) return [];
        $rows = DB::select("
            WITH RECURSIVE d AS (
              SELECT id,parent_id FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id,g.parent_id FROM geo_units g JOIN d ON g.parent_id = d.id
            ) SELECT id FROM d
        ", [$geoId]);
        return array_map(fn($r) => (int)$r->id, $rows);
    }

    private function geoAncestorNamesPath(int $geoId): ?string
    {
        if (!$geoId) return null;
        $rows = DB::select("
            WITH RECURSIVE a AS (
              SELECT id,parent_id,name FROM geo_units WHERE id = ?
              UNION ALL
              SELECT g.id,g.parent_id,g.name FROM geo_units g JOIN a ON a.parent_id = g.id
            ) SELECT name FROM a
        ", [$geoId]);
        if (!$rows) return null;
        // rows come child->...->root, so reverse
        $names = array_reverse(array_map(fn($r) => $r->name, $rows));
        return implode(' / ', $names);
    }

    // Counts per county scoped to BRAND (includes all descendants of each county)
    private function countiesWithCountsForBrand(int $countryId, int $brandId): \Illuminate\Support\Collection
    {
        $counties = $this->countiesForCountry($countryId);

        foreach ($counties as $c) {
            $geoIds = $this->geoDescendantIdsIncludingSelf((int)$c->id);

            $c->product_count = DB::table('products')
                ->join('users','products.user_id','=','users.id')
                ->where('products.is_publish',1)
                ->where('users.status_id',1)
                ->where('products.brand_id', $brandId)
                ->where(function($q) use ($geoIds){
                    $q->whereIn('products.geo_unit_id', $geoIds)
                        ->orWhere(function($q2) use ($geoIds){
                            $q2->whereNull('products.geo_unit_id')
                                ->whereIn('users.geo_unit_id', $geoIds);
                        });
                })
                ->count();
        }

        return $counties;
    }

    // ===== Pages =====
    public function getBrandPage($id, $title)
    {
        $params = ['brand_id' => (int)$id];

        $mdata = Brand::where('id',$id)->where('is_publish',1)->first();
        $metadata = $mdata ?: (object)[
            'id' => '', 'name' => '', 'slug' => '', 'thumbnail' => '', 'is_publish' => ''
        ];

        $sData = Tp_option::where('option_name','page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';

        $num = in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12;

        // Location inputs
        $selectedGeo  = (int) request('geo', 0);
        $selectedPath = $this->geoAncestorNamesPath($selectedGeo);

        // Counties list (Kenya by default; change if multi-country)
        $countryId = (int) (request('country_id') ?? Country::where('country_name','Kenya')->value('id'));
        $counties  = $this->countiesWithCountsForBrand($countryId, (int)$id);

        // Base query
        $q = DB::table('products')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
            ->where('products.is_publish', 1)
            ->where('users.status_id', 1)
            ->where('products.brand_id', $id);

        // Optional geo filter (product.geo or seller.geo)
        if ($selectedGeo) {
            $desc = $this->geoDescendantIdsIncludingSelf($selectedGeo);
            if (!empty($desc)) {
                $q->where(function($w) use ($desc){
                    $w->whereIn('products.geo_unit_id', $desc)
                        ->orWhere(function($w2) use ($desc){
                            $w2->whereNull('products.geo_unit_id')
                                ->whereIn('users.geo_unit_id', $desc);
                        });
                });
            }
        }

        $datalist = $q->orderBy('products.id','desc')->paginate($num);

        for ($i=0; $i<count($datalist); $i++) {
            $Reviews = getReviews($datalist[$i]->id);
            $datalist[$i]->TotalReview      = $Reviews[0]->TotalReview;
            $datalist[$i]->TotalRating      = $Reviews[0]->TotalRating;
            $datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        return view('frontend.brand', compact(
            'params','metadata','brand_variation','datalist',
            'counties','selectedGeo','selectedPath'
        ));
    }

    // Ajax grid (respects price + geo)
    public function getBrandGrid(Request $request)
    {
        $brand_id = (int)$request->brand_id;
        $min_price = $request->min_price === '' ? 0 : (float)$request->min_price;
        $max_price = $request->max_price !== '' ? (float)$request->max_price : null;
        $selectedGeo = (int) $request->geo;

        $sData = Tp_option::where('option_name','page_variation')->first();
        $dataObj = $sData ? json_decode($sData['option_value']) : null;
        $brand_variation = $dataObj->brand_variation ?? 'left_sidebar';

        $num = $request->num ?: (in_array($brand_variation, ['left_sidebar','right_sidebar']) ? 9 : 12);

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

        if ($request->ajax()) {
            $q = DB::table('products')
                ->join('users', 'products.user_id', '=', 'users.id')
                ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
                ->where('products.is_publish', 1)
                ->where('users.status_id', 1)
                ->where('products.brand_id', $brand_id);

            if ($max_price !== null) {
                $q->whereBetween('products.sale_price', [$min_price, $max_price]);
            }

            if ($selectedGeo) {
                $desc = $this->geoDescendantIdsIncludingSelf($selectedGeo);
                if (!empty($desc)) {
                    $q->where(function($w) use ($desc){
                        $w->whereIn('products.geo_unit_id', $desc)
                            ->orWhere(function($w2) use ($desc){
                                $w2->whereNull('products.geo_unit_id')
                                    ->whereIn('users.geo_unit_id', $desc);
                            });
                    });
                }
            }

            $datalist = $q->orderBy('products.'.$field_name, $order_name)
                ->paginate($num);

            for ($i=0; $i<count($datalist); $i++) {
                $Reviews = getReviews($datalist[$i]->id);
                $datalist[$i]->TotalReview      = $Reviews[0]->TotalReview;
                $datalist[$i]->TotalRating      = $Reviews[0]->TotalRating;
                $datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
            }

            return view('frontend.partials.brand-grid', compact('brand_variation', 'datalist'))->render();
        }
    }
}
