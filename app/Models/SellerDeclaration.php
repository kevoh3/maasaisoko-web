<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerDeclaration extends Model
{
    protected $fillable = [
        'user_id','signature_path','declaration_date'
    ];
}
