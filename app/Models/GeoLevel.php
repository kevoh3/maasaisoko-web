<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoLevel extends Model
{
    protected $fillable = ['country_id','name','slug','position','is_publish'];

    public function country()  { return $this->belongsTo(Country::class); }
    public function units()    { return $this->hasMany(GeoUnit::class, 'level_id'); }
}
