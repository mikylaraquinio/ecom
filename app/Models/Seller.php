<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_name',
        'farm_address',
        'gov_id',
        'farm_certificate',
        'mobile_money',
        'terms',
    ];
}

