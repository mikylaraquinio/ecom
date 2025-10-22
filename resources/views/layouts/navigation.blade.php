@php
use Illuminate\Support\Facades\Route;
@endphp

<header class="header fixed-top">
  <div class="header-inner bg-white border-bottom shadow-sm">
      @if (Route::has('login'))
      @php
        $activeIcon = function (...$patterns) {
          foreach ($patterns as $p) {
            if (request()->routeIs($p) || request()->is($p)) return 'active';
          }
          return '';
        };

        $user = auth()->user();
        $notifications = $user ? $user->notifications()->latest()->limit(10)->get() : collect();
        $unreadCount  = $user ? $user->unreadNotifications()->count() : 0;
        $avatar = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('assets/default-avatar.png');
      @endphp

      <nav class="navbar navbar-light bg-white navbar-no-hamburger navbar-compact">
        <div class="container align-items-center py-2">
          <!-- Left: Logo -->
          <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/welcome') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo" style="height:65px;">
          </a>

          <!-- Right: Navigation -->
          <div class="d-flex align-items-center gap-3">

            <!-- Mobile search toggle -->
            <button class="btn icon-link d-lg-none" data-bs-toggle="collapse" data-bs-target="#mobileSearch" title="Search">
              <i class="bi bi-search"></i>
            </button>

            @auth
              <!-- Home -->
              <a class="nav-link-item {{ $activeIcon('welcome') }}" href="{{ url('/welcome') }}">
                <span class="d-none d-lg-inline">Home</span>
                <i class="bi bi-house d-inline d-lg-none"></i>
              </a>

              <!-- Shop -->
              <a class="nav-link-item {{ $activeIcon('shop*') }}" href="{{ route('shop') }}">
                <span class="d-none d-lg-inline">Shop</span>
                <i class="bi bi-bag d-inline d-lg-none"></i>
              </a>

              <!-- Cart -->
              <a class="nav-link-item position-relative {{ $activeIcon('cart*') }}" href="{{ route('cart') }}">
                <span class="d-none d-lg-inline">Cart</span>
                <i class="bi bi-cart d-inline d-lg-none"></i>
                @if ($cartCount > 0)
                  <span class="badge rounded-pill bg-danger badge-dot">{{ $cartCount }}</span>
                @endif
              </a>

              <!-- Chat (icon only) -->
              <div class="dropdown">
                <a class="icon-link dropdown-toggle" id="chatDropdown" data-bs-toggle="dropdown" data-bs-display="static"
 title="Chat">
                  <i class="bi bi-chat-dots"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end chat-dropdown shadow-sm p-0" aria-labelledby="chatDropdown">
                  <iframe src="{{ route('chat') }}?embed=1" class="chat-iframe" title="Chat"></iframe>
                </div>
              </div>

              <!-- Notifications (icon only) -->
              <div class="dropdown">
                <a class="icon-link dropdown-toggle position-relative {{ $activeIcon('notifications.*') }}"
                   id="notifDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static"
 title="Notifications">
                  <i class="bi bi-bell"></i>
                  @if($unreadCount > 0)
                    <span class="badge rounded-pill bg-danger badge-dot">{{ $unreadCount }}</span>
                  @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end notifications-menu shadow-sm p-0" aria-labelledby="notifDropdown">
                  <div class="menu-header d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="fw-semibold small text-muted">Notifications</span>
                    @if($unreadCount > 0)
                      <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn">Mark all</button>
                    @endif
                  </div>
                  <div class="menu-body">
                    @forelse($notifications as $n)
                      @php
                        $data = $n->data ?? [];
                        $title = $data['title'] ?? 'New notification';
                        $message = $data['message'] ?? '';
                        $url = $data['url'] ?? '#';
                        $isUnread = is_null($n->read_at);
                      @endphp
                      <a href="{{ $url }}" class="notif-item d-flex align-items-start {{ $isUnread ? 'is-unread' : '' }}" data-id="{{ $n->id }}">
                        <span class="dot"></span>
                        <div class="content flex-grow-1">
                          <div class="title">{{ $title }}</div>
                          @if($message)<div class="msg text-muted">{{ $message }}</div>@endif
                          <div class="time text-muted small">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                      </a>
                    @empty
                      <div class="p-4 text-center text-muted small">No notifications yet</div>
                    @endforelse
                  </div>
                </div>
              </div>

              <!-- Profile (icon only) -->
              <div class="dropdown">
                <a href="#" class="icon-link dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" data-bs-display="static"
 title="Account">
                  <i class="bi bi-person-circle fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end user-dropdown shadow-sm" aria-labelledby="userDropdown">
                  <li class="px-3 py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                      <img src="{{ $avatar }}" class="rounded-circle" width="36" height="36">
                      <div>
                        <strong>{{ $user->name }}</strong><br>
                        <small class="text-muted">{{ $user->email }}</small>
                      </div>
                    </div>
                  </li>
                  <li><a class="dropdown-item" href="{{ route('user_profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                      <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                    </form>
                  </li>
                </ul>
              </div>

            @else
              <a class="nav-link-item {{ $activeIcon('welcome') }}" href="{{ url('/welcome') }}">
                <span class="d-none d-lg-inline">Home</span>
                <i class="bi bi-house d-inline d-lg-none"></i>
              </a>
              <a class="nav-link-item {{ $activeIcon('login') }}" href="{{ route('login') }}">
                <span class="d-none d-lg-inline">Login</span>
                <i class="bi bi-box-arrow-in-right d-inline d-lg-none"></i>
              </a>
              <a class="nav-link-item {{ $activeIcon('register') }}" href="{{ route('register') }}">
                <span class="d-none d-lg-inline">Register</span>
                <i class="bi bi-person-plus d-inline d-lg-none"></i>
              </a>
            @endauth
          </div>
        </div>
      </nav>
      @endif
  </div>

  <!-- Styles -->
  <style>
    .nav-link-item {
      color: #4a4a4a;
      text-decoration: none;
      font-weight: 500;
      transition: color .2s, background .2s;
      padding: .4rem .6rem;
      border-radius: .4rem;
      position: relative;
    }
    .nav-link-item:hover { background: #f8f9f8; color: #2f6a2f; }
    .nav-link-item.active { font-weight: 700; color: #2f6a2f; }

    .icon-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 36px; height: 36px;
      color: #5b6b79;
      text-decoration: none;
      border-radius: 8px;
      transition: background .2s, color .2s;
    }
    .icon-link:hover { background: #f5f7f8; color: #2f6a2f; }
    .icon-link.active { background: #f0f6ec; color: #2f6a2f; box-shadow: inset 0 0 0 1px rgba(104,177,58,.35); }

    .badge-dot { font-size: .6rem; padding: .2rem .3rem; position: absolute; top: -2px; right: -4px; }

    .search-pill { border-radius: 999px; overflow: hidden; border: 1px solid #dfe6e1; }
    .search-pill:focus-within { border-color: #68b13a; box-shadow: 0 0 0 3px rgba(104,177,58,.14); }

    .notifications-menu, .chat-dropdown, .user-dropdown { border-radius: 1rem; overflow: hidden; }
    .chat-dropdown { width: min(600px, 90vw); height: min(70vh, 700px); }
    .chat-iframe { border: none; width: 100%; height: 100%; }

    .search-pill {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 520px; /* smaller and balanced */
    border-radius: 50px;
    overflow: hidden;
    border: 1px solid #dfe6e1;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .search-pill .input-group-text {
    background: transparent;
    border: none;
    color: #7a8a97;
    padding: 0 0.75rem;
  }

  .search-pill .form-control {
    border: none;
    box-shadow: none;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
  }

  .search-pill .form-control:focus {
    outline: none;
    box-shadow: none;
  }

  .search-pill .btn {
    background: #2f6a2f;
    border: none;
    border-radius: 0;
    padding: 0.6rem 0.9rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
  }

  .search-pill .btn:hover {
    background: #398f39;
  }

  .search-pill:focus-within {
    border-color: #68b13a;
    box-shadow: 0 0 0 3px rgba(104,177,58,.14);
  }
  nav.navbar {
  z-index: 3050 !important;
}

  </style>
</header>
