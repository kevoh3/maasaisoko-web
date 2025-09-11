<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerSettlement extends Model
{
    protected $fillable = [
        'user_id','bank_name','bank_branch','account_name','account_number','swift_code','mobile_money'
    ];
}
