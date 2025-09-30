<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $orderNumber,
        public string $buyerName,
        public string $url // deep link to order page
    ) {}

    public function via($notifiable)
    {
        return ['database']; // add 'broadcast' if you want real-time
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => 'New order received',
            'message' => "{$this->buyerName} placed order {$this->orderNumber}.",
            'url'     => $this->url,
            'order_id'=> $this->orderId,
            'type'    => 'order_placed',
        ];
    }
}