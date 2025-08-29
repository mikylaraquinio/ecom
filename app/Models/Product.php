<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'image', 'image_path', 'category_id', 'user_id', 'unit',
    'min_order_qty',];

    // Optional if you have a custom primary key
    protected $primaryKey = 'id';

    // OrderItems relationship (One-to-Many)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Total Sold Attribute (custom accessor to calculate total sold)
    public function getTotalSoldAttribute()
    {
        return $this->orderItems()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->sum('order_items.quantity');
    }

    // Category relationship (One-to-Many, belongs to Category)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // User relationship (One-to-Many, belongs to User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Orders relationship (Many-to-Many via pivot table)
    // Correct One-to-Many relation
    public function OrderItem()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    // In Product.php
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id'); // or 'seller_id'
    }


}
