<x-app-layout>
    <div class="container py-4">
        <div class="chat-container d-flex border rounded shadow-sm" style="height: 85vh; overflow: hidden;">

            <!-- Sidebar: Conversations -->
            <div class="conversations border-end bg-white" style="width: 250px; overflow-y:auto;">
                <h6 class="p-3 border-bottom">Conversations</h6>
                @forelse($conversations as $conv)
                    <a href="{{ route('chat', $conv->id) }}" class="d-block p-2 text-decoration-none 
                                  {{ isset($receiver) && $receiver->id === $conv->id ? 'bg-light fw-bold' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <img src="{{ $conv->profile_picture ? asset('storage/' . $conv->profile_picture) : asset('assets/default.png') }}"
                                    class="rounded-circle" width="32" height="32" alt="profile">
                            </div>
                            <div class="text-truncate">{{ $conv->name }}</div>
                        </div>
                    </a>
                @empty
                    <p class="p-3 text-muted">No conversations yet</p>
                @endforelse
            </div>

            <!-- Chat Area -->
            <div class="chat-box d-flex flex-column flex-grow-1 bg-light">
                <!-- Chat Header -->
                <div class="p-3 border-bottom bg-white">
                    <h5 class="mb-0">
                        @if($receiver)
                            Chat with <span class="text-success">{{ $receiver->name }}</span>
                        @else
                            Chat
                        @endif
                    </h5>
                </div>

                <!-- Messages -->
                <div class="messages flex-grow-1 p-3 overflow-auto">
                    @if($receiver)
                        @forelse($messages as $msg)
                            <div
                                class="d-flex {{ $msg->sender_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div class="p-2 rounded 
                                            {{ $msg->sender_id == Auth::id() ? 'bg-success text-white' : 'bg-secondary text-white' }}"
                                    style="max-width: 70%;">
                                    {{ $msg->message }}
                                    <div class="small text-muted">{{ $msg->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No messages yet. Start the conversation 👋</div>
                        @endforelse
                    @else
                        <div class="text-muted">Select a user to start chatting.</div>
                    @endif
                </div>

                <!-- Chat Input -->
                @if ($receiver)
                    <form action="{{ route('chat.send', $receiver->id) }}" method="POST"
                        class="chat-input d-flex border-top bg-white p-2">
                        @csrf
                        <input type="text" name="message" class="form-control me-2" placeholder="Type a message..."
                            required>
                        <button class="btn btn-success">Send</button>
                    </form>
                @else
                    <div class="p-3 text-muted border-top bg-light">
                        Select a user to start chatting.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>