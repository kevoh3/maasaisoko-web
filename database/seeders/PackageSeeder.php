<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Standard',
                'items' => 15,
                'base_monthly_price' => 250,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 40,
                'annual_discount' => 60,
            ],
            [
                'name' => 'Bronze',
                'items' => 30,
                'base_monthly_price' => 700,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 28.57,
                'annual_discount' => 42.86,
            ],
            [
                'name' => 'Silver',
                'items' => 45,
                'base_monthly_price' => 800,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 25,
                'annual_discount' => 37.5,
            ],
            [
                'name' => 'Gold',
                'items' => 60,
                'base_monthly_price' => 900,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 22.22,
                'annual_discount' => 33.33,
            ],
            [
                'name' => 'Diamond',
                'items' => 75,
                'base_monthly_price' => 1000,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 20,
                'annual_discount' => 30,
            ],
            [
                'name' => 'Platinum',
                'items' => 100,
                'base_monthly_price' => 1100,
                'quarterly_discount' => 0,
                'bi_annual_discount' => 18.18,
                'annual_discount' => 27.27,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
