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
        'fees_and_charges',
        'running_balance',
        'party_b_name',
        'party_b_account_number',
        'party_b_platform'
    ];
}
