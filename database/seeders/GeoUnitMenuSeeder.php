<?php

namespace Database\Seeders;

use App\Models\GeoUnit;
use App\Models\Menu_child;
use App\Models\Pro_category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class GeoUnitMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
//            $geoUnits = GeoUnit::where('country_id', 117)
//                ->where('level_id', 1)
//                ->where('is_publish', 1)
//                ->orderByRaw('CAST(code AS UNSIGNED)')
//                ->get();
//
//            $sort = 0;
//            foreach ($geoUnits as $d) {
//                Menu_child::firstOrCreate(
//                    [
//                        'menu_id'        => 120,
//                        'menu_parent_id' => 1018,
//                        'menu_type'      => 'geo_unit',
//                        'item_id'        => $d->id,
//                    ],
//                    [
//                        'mega_menu_id'   => null,
//                        'item_label'     => $d->name,
//                        'custom_url'     => $d->slug,
//                        'target_window'  => '_self',
//                        'css_class'      => '',
//                        'lan'            => 'en',
//                        'sort_order'     => $sort++,
//                    ]
//                );
//            }
//            $sellers = User::where('role_id', 3)
//                ->where('status_id', 1)
////                ->where('is_publish', 1)
//                ->orderByRaw('CAST(id AS UNSIGNED)')
//                ->get();
//
//            $sellersort = 0;
//            foreach ($sellers as $d) {
//                Menu_child::firstOrCreate(
//                    [
//                        'menu_id'        => 120,
//                        'menu_parent_id' => 1019,
//                        'menu_type'      => 'shop',
//                        'item_id'        => $d->id,
//                    ],
//                    [
//                        'mega_menu_id'   => null,
//                        'item_label'     => $d->shop_name,
//                        'custom_url'     => $d->shop_name,
//                        'target_window'  => '_self',
//                        'css_class'      => '',
//                        'lan'            => 'en',
//                        'sort_order'     => $sellersort++,
//                    ]
//                );
//            }
            //////
            ///
              $categories = Pro_category::where('is_publish',1)
                  ->whereNull('parent_id')
                  ->orderBy('name')
                            ->get();
                        $sort = 0;
                        foreach ($categories as $d) {
                            Menu_child::firstOrCreate(
                                [
                                    'menu_id'        => 120,
                                    'menu_parent_id' => 1020,
                                    'menu_type'      => 'product_category',
                                    'item_id'        => $d->id,
                                    'thumbnail'        => $d->thumbnail,
                                ],
                                [
                                    'mega_menu_id'   => null,
                                    'item_label'     => $d->name,
                                    'custom_url'     => $d->slug,
                                    'target_window'  => '_self',
                                    'css_class'      => '',
                                    'lan'            => 'en',
                                    'sort_order'     => $sort++,
                                ]
                            );
                        }

        });

    }
}
