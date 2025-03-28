<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address_id', 'payment_method', 'total_amount', 'status', 'shipping_address_id'];

    // Define constants for statuses
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DELIVERED = 'delivered';

    // Order Items relationship
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // User relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Products relationship (many-to-many)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'price') // Assuming you want to track quantity and price in the pivot table
                    ->withTimestamps();
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
}
