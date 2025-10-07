<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
// use Illuminate\Contracts\Queue\ShouldQueue; // optional, then implements ShouldQueue

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public $order) {}

    public function via($notifiable)
    {
        return ['database']; // add 'mail' if you want email too
    }

    public function toDatabase($notifiable)
    {
        $status = (string) $this->order->status; // accepted|denied|shipped|completed
        $titles = [
            'accepted'  => 'Order accepted',
            'denied'    => 'Order denied',
            'shipped'   => 'Order shipped',
            'completed' => 'Order completed',
        ];

        $title = $titles[$status] ?? 'Order update';
        $message = "Order #{$this->order->id} is now {$status}.";

        return [
            'type'       => 'order_status',
            'order_id'   => $this->order->id,
            'status'     => $status,
            // 👇 these keys match your navbar dropdown
            'title'      => $title,
            'message'    => $message,
            'url'        => route('user_profile'), // ← change to your buyer orders page/route
        ];
    }
}
