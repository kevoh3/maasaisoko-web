<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    /**
     * Fields that can be mass-assigned.
     */
    protected $fillable = [
        'name',
        'registration_number',
        'type',
        'industry',
        'contact_person',
        'phone',
        'email',
        'address',
        'county',
        'sub_county',
        'kra_pin',
        'business_permit_number',
        'certificate_of_incorporation',
        'tax_compliance_certificate',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'website',
        'social_media',
        'status',
        'verified_by',
        'verified_at',
        'verified_status',
        'verified_notes',
        'approved_by',
        'approved_at',
    ];

    /**
     * Cast attributes to correct types.
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Admin/staff who verified the group.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Admin/staff who approved the group.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Users (sellers/members) who belong to this group.
     * Assumes `users` table has a `group_id` column.
     */
    public function members()
    {
        return $this->hasMany(User::class, 'group_id');
    }

    /**
     * Scope for active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for verified groups.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_status', 'verified');
    }
}
