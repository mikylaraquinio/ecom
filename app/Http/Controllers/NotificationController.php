<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\OrderStatusUpdated;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function read(Request $request, DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id !== $request->user()->id, 403);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        return response()->json(['ok' => true]);
    }

    public function accept(Order $order) {
    $order->status = 'accepted';
    $order->save();

    $order->user->notify(new OrderStatusUpdated($order)); // buyer
    return back()->with('success', 'Order accepted.');
    }

    // Deny
    public function deny(Order $order) {
        $order->status = 'denied';
        $order->save();

        $order->user->notify(new OrderStatusUpdated($order));
        return back()->with('success', 'Order denied.');
    }

    // Mark shipped
    public function ship(Request $req, Order $order) {
        $order->status = 'shipped';
        // if you store tracking fields, set them here
        // $order->tracking_no = $req->tracking_no;
        // $order->courier = $req->courier;
        $order->save();

        $extra = '';
        if ($req->filled('courier') || $req->filled('tracking_no')) {
            $extra = trim('('.$req->courier.' '.$req->tracking_no.')');
        }
        $order->user->notify(new OrderStatusUpdated($order, $extra));

        return back()->with('success', 'Order marked as shipped.');
    }

    // Complete
    public function complete(Order $order) {
        $order->status = 'completed';
        $order->save();

        $order->user->notify(new OrderStatusUpdated($order));
        return back()->with('success', 'Order completed.');
    }
}
