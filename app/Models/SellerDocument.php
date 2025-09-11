<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerDocument extends Model
{
    protected $fillable = [
        'user_id','document_number','kra_pin',
        'document_file_path','business_license_file_path','brand_auth_file_path'
    ];
}
