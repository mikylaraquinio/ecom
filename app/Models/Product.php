<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'description', 'price', 'image', 'category_id', 'stock'];
    protected $primaryKey = 'id'; // Optional, only if you have a custom primary key

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalSoldAttribute()
    {
        return $this->orderItems()->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->sum('order_items.quantity');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

