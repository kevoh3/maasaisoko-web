<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';
    protected $fillable = [
        'user_id',
        'wallet_name',
        'account_number',
        'wallet_type',
        'balance',
        'currency',
        'notification_number',
        'client_id',
        'client_secret',
        'request_id',
        'wallet_limit',
        'is_system_wallet',
    ];

    protected $casts = [
        'balance' => 'float',
        'wallet_limit' => 'float',
        'is_system_wallet' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
