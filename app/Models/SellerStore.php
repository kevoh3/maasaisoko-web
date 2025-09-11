<?php
// app/Models/SellerStore.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerStore extends Model
{
    protected $fillable = [
        'user_id','store_category_id','store_logo_path','store_banner_path','store_description','shipping_methods'
    ];

    protected $casts = [
        'shipping_methods' => 'array',
    ];
}
