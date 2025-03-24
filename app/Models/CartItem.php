<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
    ];

    // Define the relationship to the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
