<?php

// app/Notifications/NewOrderForSeller.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
// (Optional) email support:
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderForSeller extends Notification
{
    use Queueable;

    public $order;
    public $itemsForSeller; // Collection of OrderItem models belonging to this seller

    public function __construct($order, $itemsForSeller)
    {
        $this->order = $order;
        $this->itemsForSeller = $itemsForSeller;
    }

    // Store in DB (and optionally send via mail)
    public function via($notifiable)
    {
        // return ['database', 'mail']; // enable mail if you want email as well
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $buyer = $this->order->user;

        $itemsLines = $this->itemsForSeller->map(function ($item) {
            return [
                'product_id'  => $item->product_id,
                'product_name'=> optional($item->product)->name,
                'quantity'    => (int) $item->quantity,
                'price'       => (float) $item->price,
            ];
        })->values();

        // 👇 keys your navbar already uses
        $title   = 'New order';
        $message = 'Order #'.$this->order->id.' from '.$buyer->name.' ('
                . $itemsLines->pluck('product_name')->join(', ')
                . ')';
        $url     = url('myshop'.$this->order->id); // adjust to your route

        return [
            'type'               => 'new_order',
            'order_id'           => $this->order->id,
            'buyer_id'           => $buyer->id,
            'buyer_name'         => $buyer->name,
            'items'              => $itemsLines,
            'total_amount'       => (float) $this->order->total_amount,
            'shipping_fee'       => (float) $this->order->shipping_fee,
            'fulfillment_method' => $this->order->fulfillment_method,

            // 💡 navbar-friendly fields
            'title'   => 'New order',
            'message' => 'Order #'.$this->order->id.' from '.$this->order->user->name,
            'url'     => route('myshop'),
        ];
    }
}
