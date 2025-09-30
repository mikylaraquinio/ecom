<?php

// app/Notifications/OrderStatusChangedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $orderNumber,
        public string $newStatus,  // e.g. 'accepted', 'denied', 'shipped'
        public string $url
    ) {}

    public function via($notifiable)
    {
        return ['database']; // add 'broadcast' if using Echo
    }

    public function toDatabase($notifiable)
    {
        $pretty = ucfirst($this->newStatus);
        return [
            'title'   => "Order {$pretty}",
            'message' => "Your order {$this->orderNumber} is now {$this->newStatus}.",
            'url'     => $this->url,
            'order_id'=> $this->orderId,
            'type'    => "order_{$this->newStatus}",
        ];
    }
}
