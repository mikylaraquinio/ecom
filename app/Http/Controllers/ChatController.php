<?php

// app/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;

class ChatController extends Controller
{
    public function index($receiverId = null)
    {
        $userId = Auth::id();

        // ✅ Fetch all users you have chatted with
        $conversations = User::whereHas('messagesSent', function ($q) use ($userId) {
            $q->where('receiver_id', $userId);
        })
            ->orWhereHas('messagesReceived', function ($q) use ($userId) {
                $q->where('sender_id', $userId);
            })
            ->get();

        $messages = collect();
        $receiver = null;

        if ($receiverId) {
            if ((int) $receiverId === $userId) {
                return redirect()->route('chat')
                    ->with('error', 'You cannot chat with yourself.');
            }

            $receiver = User::findOrFail($receiverId);

            $messages = Message::where(function ($q) use ($receiverId, $userId) {
                $q->where('sender_id', $userId)
                    ->where('receiver_id', $receiverId);
            })
                ->orWhere(function ($q) use ($receiverId, $userId) {
                    $q->where('sender_id', $receiverId)
                        ->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('chat', [
            'conversations' => $conversations,
            'messages' => $messages,
            'receiver' => $receiver,
            'receiverId' => $receiverId,
        ]);
    }



    public function store(Request $request, $receiverId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        return back();
    }

    public function conversations()
{
    $userId = Auth::id();

    $conversations = User::where(function ($q) use ($userId) {
            $q->whereHas('messagesSent', fn($qq)=> $qq->where('receiver_id', $userId))
              ->orWhereHas('messagesReceived', fn($qq)=> $qq->where('sender_id', $userId));
        })
        ->where('id', '!=', $userId)
        ->orderBy('name')
        ->get(['id','name','profile_picture']);

    return response()->json([
        'data' => $conversations->map(fn($u)=>[
            'id'   => $u->id,
            'name' => $u->name,
            'avatar' => $u->profile_picture ? asset('storage/'.$u->profile_picture) : asset('assets/default.png'),
        ])
    ]);
}

public function messages($receiverId)
{
    $userId = Auth::id();

    $receiver = User::findOrFail($receiverId);

    $messages = Message::where(function ($q) use ($receiverId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($receiverId, $userId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
        })
        ->orderBy('created_at')
        ->get(['id','sender_id','receiver_id','message','created_at']);

    return response()->json([
        'receiver' => ['id'=>$receiver->id, 'name'=>$receiver->name],
        'data' => $messages->map(fn($m)=>[
            'id'=>$m->id,
            'mine'=>$m->sender_id === $userId,
            'text'=>$m->message,
            'time'=>$m->created_at->diffForHumans(),
        ])
    ]);
}


}
