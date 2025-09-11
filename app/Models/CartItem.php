<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'meta',         // snapshot of product/seller/etc.
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'meta' => 'array',
    ];

    /* Relationships */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* Accessors */
    public function getLineSubtotalAttribute(): float
    {
        return (float)($this->quantity * $this->unit_price);
    }
}
