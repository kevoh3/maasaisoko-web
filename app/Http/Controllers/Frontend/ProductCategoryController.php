<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pro_category;
use App\Models\Tp_option;

class ProductCategoryController extends Controller
{
    /**
     * Category page (with county dropdown filter).
     */
    public function getProductCategoryPage($id, $title)
    {
        $params = ['category_id' => $id];

        $mdata = Pro_category::where('id', $id)->where('is_publish', 1)->first();
        $metadata = $mdata ?: (object)[
            'id' => '', 'name' => '', 'slug' => '', 'thumbnail' => '',
            'subheader_image' => '', 'description' => '', 'lan' => '',
            'parent_id' => '', 'is_subheader' => '', 'is_publish' => '',
            'og_title' => '', 'og_image' => '', 'og_description' => '', 'og_keywords' => ''
        ];

        $sData = Tp_option::where('option_name', 'page_variation')->first();
        $category_variation = $sData ? json_decode($sData['option_value'])->category_variation : 'left_sidebar';
        $num = in_array($category_variation, ['left_sidebar', 'right_sidebar']) ? 9 : 12;

        // include category descendants
        $catIds = $mdata ? $this->descendantIds((int) $mdata->id) : [(int) $id];

        // counties for dropdown (defaults to Kenya)
        $countryId   = (int) (request('country_id') ?? Country::where('country_name', 'Kenya')->value('id'));
        $counties    = $this->countiesWithCounts($countryId, $catIds);

        $selectedGeo  = (int) request('geo', 0);
        $selectedPath = $selectedGeo ? $this->geoPath($selectedGeo) : '';

        $datalist = DB::table('products')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
            ->where('products.is_publish', 1)
            ->where('users.status_id', 1)
            ->whereIn('products.cat_id', $catIds)
            ->when($selectedGeo > 0, function ($q) use ($selectedGeo) {
                $geoIds = $this->geoDescendantIdsIncludingSelf($selectedGeo);
                $q->where(function ($w) use ($geoIds) {
                    $w->whereIn('products.geo_unit_id', $geoIds)
                        ->orWhere(function ($w2) use ($geoIds) {
                            // fallback: use seller location if product location is null
                            $w2->whereNull('products.geo_unit_id')
                                ->whereIn('users.geo_unit_id', $geoIds);
                        });
                });
            })
            ->orderBy('products.id', 'desc')
            ->paginate($num);

        for ($i = 0; $i < count($datalist); $i++) {
            $Reviews = getReviews($datalist[$i]->id);
            $datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
            $datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
            $datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
        }

        return view('frontend.product-category', compact(
            'params', 'metadata', 'category_variation', 'datalist', 'counties', 'selectedGeo','selectedPath'
        ));
    }

    /**
     * AJAX grid (respects county filter + category descendants).
     */
    public function getProductCategoryGrid(Request $request)
    {
        $id        = (int) $request->cat_id;
        $min_price = $request->min_price === '' ? 0 : (float) $request->min_price;
        $max_price = $request->max_price;
        $selectedGeo = (int) $request->get('geo', 0);

        $sData = Tp_option::where('option_name', 'page_variation')->first();
        $category_variation = $sData ? json_decode($sData['option_value'])->category_variation : 'left_sidebar';

        $num = $request->num ?: (in_array($category_variation, ['left_sidebar', 'right_sidebar']) ? 9 : 12);

        $field_name = 'id';
        $order_name = 'desc';
        if ($request->sortby) {
            if ($request->sortby === 'date_asc')        { $field_name = 'created_at'; $order_name = 'asc'; }
            elseif ($request->sortby === 'date_desc')   { $field_name = 'created_at'; $order_name = 'desc'; }
            elseif ($request->sortby === 'name_asc')    { $field_name = 'title';      $order_name = 'asc'; }
            elseif ($request->sortby === 'name_desc')   { $field_name = 'title';      $order_name = 'desc'; }
        }

        if ($request->ajax()) {
            // include category descendants for grid too
            $catIds = $this->descendantIds($id);

            $base = DB::table('products')
                ->join('users', 'products.user_id', '=', 'users.id')
                ->select('products.*', 'users.shop_name', 'users.id as seller_id', 'users.shop_url')
                ->where('products.is_publish', 1)
                ->where('users.status_id', 1)
                ->whereIn('products.cat_id', $catIds)
                ->when($selectedGeo > 0, function ($q) use ($selectedGeo) {
                    $geoIds = $this->geoDescendantIdsIncludingSelf($selectedGeo);
                    $q->where(function ($w) use ($geoIds) {
                        $w->whereIn('products.geo_unit_id', $geoIds)
                            ->orWhere(function ($w2) use ($geoIds) {
                                $w2->whereNull('products.geo_unit_id')
                                    ->whereIn('users.geo_unit_id', $geoIds);
                            });
                    });
                });

            if ($max_price !== null && $max_price !== '') {
                $base->whereBetween('products.sale_price', [$min_price, (float) $max_price]);
            }

            $datalist = $base->orderBy("products.$field_name", $order_name)->paginate($num);

            for ($i = 0; $i < count($datalist); $i++) {
                $Reviews = getReviews($datalist[$i]->id);
                $datalist[$i]->TotalReview = $Reviews[0]->TotalReview;
                $datalist[$i]->TotalRating = $Reviews[0]->TotalRating;
                $datalist[$i]->ReviewPercentage = number_format($Reviews[0]->ReviewPercentage);
            }

            return view('frontend.partials.product-category-grid', compact('category_variation', 'datalist'))->render();
        }
    }

    /**
     * Collect descendant category IDs (BFS).
     */
    private function descendantIds(int $rootId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];

        while (!empty($queue)) {
            $pid = array_shift($queue);
            $children = Pro_category::where('parent_id', $pid)->pluck('id')->all();
            foreach ($children as $cid) {
                if (!in_array($cid, $ids, true)) {
                    $ids[] = $cid;
                    $queue[] = $cid;
                }
            }
        }
        return $ids;
    }

    /**
     * Resolve the level id for "county".
     */
    private function countyLevelId(): int
    {
        return (int) (DB::table('geo_levels')->whereIn('name', ['county', 'County'])->value('id') ?? 1);
        // adjust fallback "1" as needed
    }

    /**
     * Counties list for a country.
     */
    private function countiesForCountry(int $countryId)
    {
        return DB::table('geo_units')
            ->where('country_id', $countryId)
            ->where('level_id', $this->countyLevelId())
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'code']);
    }

    /**
     * MySQL 8+ recursive CTE to get a geo unit and all descendants.
     * (If you're on MySQL 5.7, replace with a PHP recursion.)
     */
    private function geoDescendantIdsIncludingSelf(int $geoId): array
    {
        $rows = DB::select("
            WITH RECURSIVE d AS (
                SELECT id FROM geo_units WHERE id = ?
                UNION ALL
                SELECT g.id FROM geo_units g JOIN d ON g.parent_id = d.id
            ) SELECT id FROM d
        ", [$geoId]);

        return array_map(fn($r) => (int) $r->id, $rows);
    }

    /**
     * Counties with product counts for the current category tree.
     */
    private function countiesWithCounts(int $countryId, array $categoryIds)
    {
        $counties = $this->countiesForCountry($countryId);

        foreach ($counties as $c) {
            $desc = $this->geoDescendantIdsIncludingSelf($c->id);

            $c->product_count = DB::table('products')
                ->join('users', 'products.user_id', '=', 'users.id')
                ->where('products.is_publish', 1)
                ->where('users.status_id', 1)
                ->whereIn('products.cat_id', $categoryIds)
                ->where(function ($q) use ($desc) {
                    $q->whereIn('products.geo_unit_id', $desc)
                        ->orWhere(function ($q2) use ($desc) {
                            $q2->whereNull('products.geo_unit_id')
                                ->whereIn('users.geo_unit_id', $desc);
                        });
                })
                ->count();
        }

        return $counties;
    }
    /**
     * JSON: direct children of a geo unit (optionally with product counts for a category tree).
     * GET /geo/children?parent_id=...&cat_id=...
     */
    public function geoChildren(Request $request)
    {
        $parentId = (int) $request->query('parent_id');
        $catId    = (int) $request->query('cat_id');
        $withCounts = $catId > 0;

        $children = DB::table('geo_units')
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get(['id','name']);

        if (!$withCounts) {
            return response()->json($children);
        }

        // compute counts per child for the current category tree
        $categoryIds = $this->descendantIds($catId);

        foreach ($children as $c) {
            $desc = $this->geoDescendantIdsIncludingSelf((int)$c->id);
            $c->count = DB::table('products')
                ->join('users','products.user_id','=','users.id')
                ->where('products.is_publish',1)
                ->where('users.status_id',1)
                ->whereIn('products.cat_id', $categoryIds)
                ->where(function ($q) use ($desc) {
                    $q->whereIn('products.geo_unit_id', $desc)
                        ->orWhere(function ($q2) use ($desc) {
                            $q2->whereNull('products.geo_unit_id')
                                ->whereIn('users.geo_unit_id', $desc);
                        });
                })
                ->count();
        }

        return response()->json($children);
    }

    /** Human-readable path for the selected geo (e.g., "Nairobi / Starehe / Hospital"). */
    private function geoPath(int $geoId): string
    {
        if ($geoId <= 0) return '';
        $path = [];
        $current = DB::table('geo_units')->where('id', $geoId)->first(['id','name','parent_id']);
        while ($current) {
            $path[] = $current->name;
            $current = $current->parent_id
                ? DB::table('geo_units')->where('id', $current->parent_id)->first(['id','name','parent_id'])
                : null;
        }
        return implode(' / ', array_reverse($path));
    }
}
