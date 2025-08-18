<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
protected $fillable = [
'user_id','package_id','billing_cycle','price','currency','status',
'starts_at','expires_at','canceled_at','last_paid_at','next_due_at',
'payment_method','payment_txn_ref','meta',
];

protected $casts = [
'starts_at'   => 'datetime',
'expires_at'  => 'datetime',
'canceled_at' => 'datetime',
'last_paid_at'=> 'datetime',
'next_due_at' => 'datetime',
'meta'        => 'array',
];

public function user()    { return $this->belongsTo(User::class); }
public function package() { return $this->belongsTo(Package::class); }

public function isActive(): bool { return $this->status === 'active' && (is_null($this->expires_at) || $this->expires_at->isFuture()); }
}
