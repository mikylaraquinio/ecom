<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $audience = 'buyer') {} // 'buyer' or 'seller'

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        $title   = $this->audience === 'buyer' ? 'Order Placed Successfully' : 'New order received';
        $message = $this->audience === 'buyer'
            ? "Thanks! Your order {$orderNumber} has been placed."
            : ($this->order->user->name.' placed order '.$orderNumber.'.');

        $url = $this->audience === 'buyer'
            ? url('/user_profile?order='.$this->order->id)
            : url('/myshop?order='.$this->order->id);

        // Useful extras for display
        $items = $this->order->orderItems->map(function ($it) {
            return [
                'product_name' => $it->product->name ?? 'Item',
                'quantity'     => $it->quantity,
                'price'        => $it->price,
            ];
        })->values();

        return [
            'title'     => $title,
            'message'   => $message,
            'url'       => $url,
            'order_id'  => $this->order->id,
            'order_no'  => $orderNumber,
            'type'      => 'order_placed',
            'audience'  => $this->audience,
            'items'     => $items,
            'total'     => $this->order->total_amount,
            'payment'   => $this->order->payment_method,
            'fulfillment' => $this->order->fulfillment_method,
        ];
    }
}
