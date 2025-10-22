<?php

// app/Models/Seller.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'pickup_address',
        'pickup_full_name',
        'pickup_phone',
        'pickup_region_group',
        'pickup_province',
        'pickup_city',
        'pickup_barangay',
        'pickup_postal',
        'pickup_detail',
        'business_type',
        'tax_id',
        'gov_id_path',
        'rsbsa_path',
        'mayors_permit_path',
        'status',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    // Handy accessor if you want a single-line address
    public function getPickupAddressLineAttribute()
    {
        return collect([
            $this->pickup_detail,
            $this->pickup_barangay,
            $this->pickup_city,
            $this->pickup_province,
            $this->pickup_region_group,
            $this->pickup_postal
        ])->filter()->implode(', ');
    }
}

