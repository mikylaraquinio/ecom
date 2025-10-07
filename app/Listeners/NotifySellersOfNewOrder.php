<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\NewIncomingOrderNotification;

class NotifySellersOfNewOrder
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order();

        // Collect unique seller user_ids from items
        $sellerIds = $order->orderItems()
            ->with('product:id,user_id')
            ->get()
            ->pluck('product.user_id')
            ->filter()
            ->unique();

        // Notify each seller
        User::whereIn('id', $sellerIds)->get()
            ->each(fn(User $seller) => $seller->notify(new NewIncomingOrderNotification($order)));
    }
}