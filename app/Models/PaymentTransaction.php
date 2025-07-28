<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';
    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'status',
        'transaction_code',
        'description',
        'channel',
        'reference_number',
        'transaction_date',
    ];
}
