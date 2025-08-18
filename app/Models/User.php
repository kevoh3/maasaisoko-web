<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'shop_name',
        'shop_url',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country_id',
        'photo',
        'bactive',
        'bkey',
        'status_id',
        'role_id',
        'group_id',
        'classification','document_number',

        // NEW FIELDS
        'kyc_status','kyc_submitted_at','kyc_verified_at','kyc_rejected_at','kyc_notes',
        'registration_fee_paid','registration_fee_amount','registration_fee_currency',
        'registration_fee_paid_at','registration_fee_txn_ref','registration_fee_method',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // Scopes (handy for querying sellers)
    public function scopeSellers($q)        { return $q->where('role_id', 3); }
    public function scopeKycVerified($q)    { return $q->where('kyc_status', 'verified'); }
    public function scopeRegFeePaid($q)     { return $q->where('registration_fee_paid', true); }

    // Convenience helpers
    public function isSeller(): bool             { return (int)$this->role_id === 3; }
    public function isKycVerified(): bool        { return $this->kyc_status === 'verified'; }
    public function hasPaidRegistration(): bool  { return (bool)$this->registration_fee_paid; }
    public function subscriptions() {
        return $this->hasMany(\App\Models\UserSubscription::class);
    }

    public function currentSubscription() {
        return $this->hasOne(\App\Models\UserSubscription::class)
            ->where('status', 'active')
            ->latest('starts_at');
    }

    public function currentPackage(): ?\App\Models\Package {
        $sub = $this->currentSubscription()->first();
        return $sub?->package;
    }

    public function currentPackageItemsLimit(): int {
        return $this->currentPackage()?->items ?? 0;
    }
}
