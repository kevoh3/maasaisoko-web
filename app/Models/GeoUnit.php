<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GeoUnit extends Model
{
    protected $fillable = ['country_id','level_id','parent_id','code','name','slug','is_publish'];

    public function country() { return $this->belongsTo(Country::class); }
    public function level()   { return $this->belongsTo(GeoLevel::class, 'level_id'); }

    public function parent()  { return $this->belongsTo(GeoUnit::class, 'parent_id'); }
    public function children(){ return $this->hasMany(GeoUnit::class, 'parent_id'); }



    /** Scope by level name (e.g., 'county', 'constituency', 'ward') */
    public function scopeAtLevel($query, string $levelName)
    {
        return $query->whereHas('level', fn($q) => $q->where('slug', Str::slug($levelName)));
    }
}
