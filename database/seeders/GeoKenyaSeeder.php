<?php
namespace Database\Seeders;

use App\Models\Country;
use App\Models\GeoLevel;
use App\Models\GeoUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GeoKenyaSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Ensure Kenya exists in countries
        $kenya = Country::firstOrCreate(
            ['country_name' => 'Kenya'],
            ['is_publish' => 1]
        );

        // 2) Define Kenya’s level names & order
        // position: 1 = county, 2 = constituency, 3 = ward
        $levels = [
            ['name' => 'County',        'slug' => Str::slug('county'),        'position' => 1],
            ['name' => 'Constituency',  'slug' => Str::slug('constituency'),  'position' => 2],
            ['name' => 'Ward',          'slug' => Str::slug('ward'),          'position' => 3],
        ];

        $levelIds = [];
        foreach ($levels as $L) {
            $lvl = GeoLevel::firstOrCreate(
                ['country_id' => $kenya->id, 'slug' => $L['slug']],
                ['name' => $L['name'], 'position' => $L['position'], 'is_publish' => 1]
            );
            $levelIds[$L['position']] = $lvl->id;
        }

        // 3) Load JSON
        $path = database_path('seeders/data/kenya_admin.json');
        $data = json_decode(file_get_contents($path), true) ?? [];

        // 4) Insert hierarchy
        foreach ($data as $county) {
            $countyUnit = GeoUnit::firstOrCreate(
                [
                    'country_id' => $kenya->id,
                    'level_id'   => $levelIds[1],
                    'name'       => trim($county['county_name']),
                ],
                [
                    'slug'       => Str::slug($county['county_name']),
                    'code'       => (string)($county['county_code'] ?? null),
                    'is_publish' => 1,
                ]
            );

            foreach ($county['constituencies'] as $const) {
                $constUnit = GeoUnit::firstOrCreate(
                    [
                        'country_id' => $kenya->id,
                        'level_id'   => $levelIds[2],
                        'parent_id'  => $countyUnit->id,
                        'name'       => trim($const['constituency_name']),
                    ],
                    [
                        'slug'       => Str::slug($const['constituency_name']),
                        'is_publish' => 1,
                    ]
                );

                foreach ($const['wards'] as $wardName) {
                    GeoUnit::firstOrCreate(
                        [
                            'country_id' => $kenya->id,
                            'level_id'   => $levelIds[3],
                            'parent_id'  => $constUnit->id,
                            'name'       => trim($wardName),
                        ],
                        [
                            'slug'       => Str::slug($wardName),
                            'is_publish' => 1,
                        ]
                    );
                }
            }
        }
    }
}
