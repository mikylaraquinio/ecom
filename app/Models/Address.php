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
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }

    // 👇 Add accessor for full address
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->floor_unit_number,
            $this->barangay,
            $this->city,
            $this->province,
        ]);

        return implode(', ', $parts);
    }
}
