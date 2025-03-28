<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'farm_address',
        'government_id',
        'farm_registration_certificate',
        'mobile_payment_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


