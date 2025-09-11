<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pro_category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'subheader_image',
        'description',
        'layout',
        'lan',
        'parent_id',
        'is_subheader',
        'is_publish',
        'og_title',
        'og_image',
        'og_description',
        'og_keywords',
    ];
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // convenience accessors
    public function scopePublished($q) { return $q->where('is_publish', 1); }
    public function scopeLang($q, $lan) { return $lan ? $q->where('lan', $lan) : $q; }
}
