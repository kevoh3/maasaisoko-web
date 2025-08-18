<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class FreePackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::updateOrCreate(
            ['name' => 'Free'],
            [
                'items'               => 5, // e.g. 5 items allowed
                'base_monthly_price'  => 0,
                'quarterly_discount'  => 0,
                'bi_annual_discount'  => 0,
                'annual_discount'     => 0,
                'quarterly_total'     => 0,
                'quarterly_monthly_rate' => 0,
                'bi_annual_total'     => 0,
                'bi_annual_monthly_rate' => 0,
                'annual_total'        => 0,
                'annual_monthly_rate' => 0,
            ]
        );
    }
}
