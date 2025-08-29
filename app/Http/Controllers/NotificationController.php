<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

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
}
