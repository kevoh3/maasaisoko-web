<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
protected $fillable = [
'name',
'items',
'base_monthly_price',
'quarterly_discount',
'bi_annual_discount',
'annual_discount',
'quarterly_total',
'quarterly_monthly_rate',
'bi_annual_total',
'bi_annual_monthly_rate',
'annual_total',
'annual_monthly_rate',
];

protected static function boot()
{
parent::boot();

static::saving(function ($package) {
// Quarterly calculations
$quarterlyRate = $package->base_monthly_price * (1 - ($package->quarterly_discount / 100));
$package->quarterly_monthly_rate = $quarterlyRate;
$package->quarterly_total = $quarterlyRate * 3;

// Bi-Annual calculations
$biAnnualRate = $package->base_monthly_price * (1 - ($package->bi_annual_discount / 100));
$package->bi_annual_monthly_rate = $biAnnualRate;
$package->bi_annual_total = $biAnnualRate * 6;

// Annual calculations
$annualRate = $package->base_monthly_price * (1 - ($package->annual_discount / 100));
$package->annual_monthly_rate = $annualRate;
$package->annual_total = $annualRate * 12;
});
}
}
