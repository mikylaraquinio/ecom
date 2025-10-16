{{-- resources/views/chat-embed.blade.php --}}
@php
  $conversations = $conversations ?? collect();
  $messages = $messages ?? collect();
  $receiver = $receiver ?? null;
  $listOnly = request()->boolean('list');
@endphp

<x-app-layout>
  {{-- Kill the app chrome on this page + make the content full-bleed --}}
  <style>
    /* Hide common layout chrome from app.blade */
    header.bg-white.shadow,
    nav.navbar,
    .sidebar,
    footer,
    .footer,
    .app-footer {
      display: none !important;
    }

    /* Remove any leftover paddings/margins the layout may add */
    main,
    .container,
    .container-fluid {
      padding: 0 !important;
      margin: 0 !important;
    }

    /* Chat layout */
    #embedChat {
      height: 100vh;
      max-height: 100%;
      overflow: hidden;
      background: #fff;
    }

    #embedChat .hover-bg:hover {
      background: #f8f9fa;
    }

    #embedChat .side {
      width: 260px;
      max-width: 42%;
    }

    #embedChat .main {
      flex: 1;
      background: #f5f5f5;
      min-width: 0;
    }

    #embedChat .head {
      height: 44px;
      display: flex;
      align-items: center;
      padding: 0 .75rem;
      background: #fff;
      border-bottom: 1px solid #eee;
    }

    #embedChat .msgs {
      flex: 1;
      overflow: auto;
      padding: 1rem;
    }

    #embedChat .bubble {
      max-width: 70%;
      padding: .5rem .75rem;
      border-radius: 12px;
    }

    #embedChat .bubble.me {
      background: #198754;
      color: #fff;
    }

    #embedChat .bubble.them {
      background: #6c757d;
      color: #fff;
    }

    #embedChat .inputbar {
      padding: .5rem;
      border-top: 1px solid #eee;
      background: #fff;
    }

    @media (max-width:575.98px) {
      #embedChat .side {
        width: 50%;
      }
    }

    /* Optional: list-only mode (if you use ?list=1) */
    @if($listOnly)
      #embedChat .main {
        display: none !important;
      }

      #embedChat .side {
        width: 100% !important;
        max-width: none !important;
      }

    @endif
  </style>

  <div id="embedChat" class="container-fluid">
    <div class="d-flex h-100">
      {{-- LEFT: sidebar --}}
      <aside class="side bg-white d-flex flex-column border-end">
        <div class="p-2 d-flex align-items-center gap-2 border-bottom">
          <div class="input-group input-group-sm flex-grow-1">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input id="chatSearch" class="form-control" placeholder="Search name">
          </div>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
              {{-- BS5 --}} data-toggle="dropdown"> {{-- BS4 fallback --}}
              All
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item active" href="#" data-filter="all">All</a></li>
              <li><a class="dropdown-item" href="#" data-filter="unread">Unread</a></li>
              <li><a class="dropdown-item" href="#" data-filter="archived">Archived</a></li>
            </ul>
          </div>
        </div>

        <div class="flex-grow-1 overflow-auto" id="conversationList">
          @forelse($conversations as $conv)
            <a href="{{ route('chat', $conv->id) }}?embed=1"
              class="d-block px-3 py-2 text-decoration-none text-dark hover-bg conv-item">
              <div class="d-flex align-items-center gap-2">
                <img
                  src="{{ $conv->profile_picture ? asset('storage/' . $conv->profile_picture) : asset('assets/default.png') }}"
                  class="rounded-circle" width="32" height="32" alt="profile">
                <div class="flex-grow-1 text-truncate conv-name">{{ $conv->name }}</div>
              </div>
            </a>
          @empty
            <div class="text-muted small px-3 pt-3">No Conversation Found</div>
          @endforelse
        </div>
      </aside>

      {{-- RIGHT: main panel --}}
      <main class="main d-flex flex-column">
        @if($receiver)
          <div class="head d-flex align-items-center justify-content-between mb-2">
            <!-- Receiver Name on the left -->
            <strong class="text-success fs-6">{{ $receiver->name }}</strong>

            <!-- 🧭 Report Button on the far right -->
            <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" data-bs-toggle="modal"
              data-bs-target="#reportModal" onclick="setReportTarget({{ $receiver->id }}, 'User')"
              title="Report this user">
              <i class="fas fa-flag"></i>
            </button>
          </div>

          <div class="msgs" id="messagesPane">
            @forelse($messages as $msg)
              <div
                class="d-flex {{ $msg->sender_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                <div class="bubble {{ $msg->sender_id == Auth::id() ? 'me' : 'them' }}">
                  <div>{{ $msg->message }}</div>
                  <div class="small opacity-75 mt-1">{{ $msg->created_at->diffForHumans() }}</div>
                </div>
              </div>
            @empty
              <div class="text-muted">No messages yet.</div>
            @endforelse
          </div>

          <form action="{{ route('chat.send', $receiver->id) }}" method="POST" class="inputbar d-flex gap-2">
            @csrf
            <input type="text" name="message" class="form-control" placeholder="Type a message…" required>
            <button class="btn btn-success btn-sm">Send</button>
          </form>
        @else

          {{-- Welcome state --}}
          <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center p-4 bg-light">
            <svg width="180" height="120" viewBox="0 0 300 200" class="mb-3">
              <rect x="35" y="50" width="230" height="120" rx="10" fill="#e9ecef" />
              <rect x="60" y="80" width="120" height="12" rx="6" fill="#adb5bd" />
              <rect x="60" y="100" width="90" height="12" rx="6" fill="#ced4da" />
              <rect x="200" y="95" width="48" height="26" rx="6" fill="#ff6b6b" />
              <circle cx="212" cy="108" r="3" fill="#fff" />
              <circle cx="224" cy="108" r="3" fill="#fff" />
              <circle cx="236" cy="108" r="3" fill="#fff" />
            </svg>
            <h6 class="fw-bold mb-1">Welcome to Farm Chat</h6>
            <div class="text-muted">Start responding to your buyers now!</div>
          </div>
        @endif
      </main>
    </div>
  </div>

  {{-- Small helper for the sidebar search; works with or without Bootstrap JS --}}
  <script>
    (function () {
      const q = document.getElementById('chatSearch');
      const items = Array.from(document.querySelectorAll('#embedChat .conv-item'));
      if (!q) return;
      q.addEventListener('input', () => {
        const v = q.value.toLowerCase().trim();
        items.forEach(a => {
          const name = a.querySelector('.conv-name')?.textContent.toLowerCase() || '';
          a.style.display = name.includes(v) ? '' : 'none';
        });
      });
    })();
  </script>
</x-app-layout>