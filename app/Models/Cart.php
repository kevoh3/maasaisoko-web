<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',       // 'open','converted','abandoned'...
        'currency',
        'ip_address',
        'user_agent',
        // optional totals if you decide to persist them:
        'subtotal',
        'tax',
        'discount',
        'total',
        'meta',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'meta' => 'array',
    ];

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /* Scopes */
    public function scopeOpen($q)
    {
        return $q->where('status', 'open');
    }

    /* Helpers (computed totals on the fly if you prefer not to store) */
    public function getItemsCountAttribute(): int
    {
        return (int)$this->items->sum('quantity');
    }

    public function getComputedSubtotalAttribute(): float
    {
        return (float)$this->items->sum(fn($i) => $i->quantity * $i->unit_price);
    }
}
