<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'mobile_number',
        'notes',
        'floor_unit_number',
        'province',
        'city',
        'barangay',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
