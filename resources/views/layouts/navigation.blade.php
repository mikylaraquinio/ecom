<header class="header fixed-top">
  <div class="header-inner">
    <div class="container">
      @if (Route::has('login'))
      @php
        // Helper to mark icons active based on route name or path
        $activeIcon = function (...$patterns) {
          foreach ($patterns as $p) {
            if (request()->routeIs($p) || request()->is($p)) return 'active';
          }
          return '';
        };
      @endphp

      <nav class="navbar navbar-light bg-white border-bottom navbar-no-hamburger navbar-compact">
        <!-- Top row -->
        <div class="container align-items-center py-1">
          <!-- Left: Brand -->
          <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/welcome') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo" style="height:80px; max-height:60px; transition: all 0.2s ease;">
          </a>

          <!-- Center: Desktop search (shown on lg+) -->
          <form action="{{ url('/search') }}" method="GET" role="search"
                class="d-none d-lg-flex flex-grow-1 justify-content-center">
            <label for="navbarSearch" class="visually-hidden">Search</label>
            <div class="input-group search-pill">
              <span class="input-group-text" id="search-addon">
                <i class="bi bi-search"></i>
              </span>
              <input id="navbarSearch" type="text" name="query" class="form-control"
                     placeholder="Search fresh produce, categories, or farmers…"
                     aria-label="Search" aria-describedby="search-addon" required>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>

          <!-- Right: Icons (always visible; no hamburger) -->
          <div class="d-flex align-items-center gap-1">
            <!-- Mobile search trigger -->
            <button class="btn icon-link d-lg-none"
                    type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch"
                    aria-controls="mobileSearch" aria-expanded="false" aria-label="Toggle search"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Search">
              <i class="bi bi-search"></i>
            </button>

            @auth
              <!-- Home -->
              <a class="icon-link {{ $activeIcon('welcome', '/') }}"
                 href="{{ url('/welcome') }}"
                 @if($activeIcon('welcome','/')) aria-current="page" @endif
                 data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home" aria-label="Home">
                <i class="bi bi-house"></i>
              </a>

              <!-- Shop -->
              <a class="icon-link {{ $activeIcon('shop','shop*') }}"
                 href="{{ route('shop') }}"
                 @if($activeIcon('shop','shop*')) aria-current="page" @endif
                 data-bs-toggle="tooltip" data-bs-placement="bottom" title="Shop" aria-label="Shop">
                <i class="bi bi-bag"></i>
              </a>

              <!-- Cart -->
              <a class="icon-link position-relative {{ $activeIcon('cart','cart*') }}"
                 href="{{ route('cart') }}"
                 @if($activeIcon('cart','cart*')) aria-current="page" @endif
                 data-bs-toggle="tooltip" data-bs-placement="bottom" title="Cart" aria-label="Cart">
                <i class="bi bi-cart"></i>
                @if ($cartCount > 0)
                  <span class="badge rounded-pill bg-danger badge-dot">{{ $cartCount }}</span>
                @endif
              </a>

              @php
                $user = auth()->user();
                $notifications = $user ? $user->notifications()->latest()->limit(10)->get() : collect();
                $unreadCount  = $user ? $user->unreadNotifications()->count() : 0;
              @endphp

              <!-- Notifications (highlight when in notifications pages) -->
              <div class="dropdown">
                <a class="icon-link dropdown-toggle position-relative {{ $activeIcon('notifications.*') }}"
                   id="notifDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                   aria-expanded="false"
                   @if($activeIcon('notifications.*')) aria-current="page" @endif
                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications" aria-label="Notifications">
                  <i class="bi bi-bell"></i>
                  @if($unreadCount > 0)
                    <span class="badge rounded-pill bg-danger badge-dot">{{ $unreadCount }}</span>
                  @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end notifications-menu shadow-sm p-0"
                     aria-labelledby="notifDropdown">
                  <div class="menu-header d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="fw-semibold small text-muted">Notifications</span>
                    @if($unreadCount > 0)
                      <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn">Mark all</button>
                    @endif
                  </div>

                  <div class="menu-body">
                    @forelse($notifications as $n)
                      @php
                        $data    = $n->data ?? [];
                        $title   = $data['title'] ?? 'New notification';
                        $message = $data['message'] ?? ($data['body'] ?? '');
                        $url = match ($data['type'] ?? null) {
                            'new_order' => route('myshop'),
                            'order_status' => isset($data['order_id'])
                                ? route('user_profile', ['order' => $data['order_id']])
                                : route('user_profile'),
                            default => $data['url'] ?? '#',
                        };
                        $isUnread = is_null($n->read_at);
                      @endphp

                      <a href="{{ $url }}"
                         class="notif-item d-flex align-items-start {{ $isUnread ? 'is-unread' : '' }}"
                         data-id="{{ $n->id }}">
                        <span class="dot"></span>
                        <div class="content flex-grow-1">
                          <div class="title">{{ $title }}</div>
                          @if($message)
                            <div class="msg text-muted">{{ $message }}</div>
                          @endif
                          <div class="time text-muted small">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                      </a>
                    @empty
                      <div class="p-4 text-center text-muted small">No notifications yet</div>
                    @endforelse

                    <div class="menu-footer d-flex justify-content-between align-items-center px-3 py-2">
                      <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm">View all</a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User / Profile (highlight when on profile pages) -->
              <div class="dropdown">
                <a href="#" class="icon-link dropdown-toggle d-flex align-items-center {{ $activeIcon('user_profile*','profile*') }}"
                   id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                   @if($activeIcon('user_profile*','profile*')) aria-current="page" @endif
                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="Account" aria-label="Account">
                  <i class="bi bi-person"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li><a class="dropdown-item" href="{{ route('user_profile') }}">Profile</a></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button class="dropdown-item" type="submit">Log Out</button>
                    </form>
                  </li>
                </ul>
              </div>
            @else
              <a class="icon-link {{ $activeIcon('welcome','/') }}"
                 href="{{ url('/welcome') }}"
                 @if($activeIcon('welcome','/')) aria-current="page" @endif
                 data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home" aria-label="Home">
                <i class="bi bi-house"></i>
              </a>
              <a class="icon-link {{ $activeIcon('login','login*') }}"
                 href="{{ route('login') }}"
                 @if($activeIcon('login','login*')) aria-current="page" @endif
                 data-bs-toggle="tooltip" data-bs-placement="bottom" title="Log in" aria-label="Log in">
                <i class="bi bi-box-arrow-in-right"></i>
              </a>
              @if (Route::has('register'))
                <a class="icon-link {{ $activeIcon('register','register*') }}"
                   href="{{ route('register') }}"
                   @if($activeIcon('register','register*')) aria-current="page" @endif
                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="Register" aria-label="Register">
                  <i class="bi bi-person-plus"></i>
                </a>
              @endif
            @endauth
          </div>
        </div>

        <!-- Mobile search (second row) -->
        <div class="container">
          <div class="collapse d-lg-none w-100 mt-1" id="mobileSearch">
            <form action="{{ url('/search') }}" method="GET" role="search" class="w-100">
              <label for="navbarSearchMobile" class="visually-hidden">Search</label>
              <div class="input-group search-pill">
                <span class="input-group-text" id="search-addon-mobile">
                  <i class="bi bi-search"></i>
                </span>
                <input id="navbarSearchMobile" type="text" name="query" class="form-control"
                       placeholder="Search fresh produce, categories, or farmers…"
                       aria-label="Search" aria-describedby="search-addon-mobile" required>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </nav>
      @endif
    </div>
  </div>

  <!-- Styles -->
  <style>
    .navbar-no-hamburger { border-bottom: 1px solid rgba(0,0,0,.06); }
    .navbar-compact .nav-link { padding: .25rem .4rem; font-size: .95rem; line-height: 1.2; }

    /* Icon buttons */
    .icon-link {
      min-width: 36px; height: 36px;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 8px;
      color: #5b6b79; text-decoration: none;
      transition: background .2s ease, color .2s ease, transform .06s ease, box-shadow .2s ease;
      position: relative;
    }
    .icon-link:hover, .icon-link:focus { background: #f5f7f8; color: #3b4956; }
    .icon-link:active { transform: translateY(1px); }

    /* Active state: subtle highlight + indicator bar */
    .icon-link.active {
      background: #f0f6ec;       /* soft green tint */
      color: #2f6a2f;            /* brand-ish green */
      box-shadow: inset 0 0 0 1px rgba(104,177,58,.35);
    }
    .icon-link.active::after {
      content: ""; position: absolute; left: 20%; right: 20%; bottom: -6px;
      height: 2px; background: #68b13a; border-radius: 2px;
    }

    /* Badges */
    .badge-dot {
      position: absolute; top: 0; right: 0;
      transform: translate(35%,-35%);
      font-size: .6rem; line-height: 1; padding: .2rem .3rem;
    }

    /* Search (compact) */
    .search-pill {
      width: 100%; max-width: 680px;
      border-radius: 9999px; overflow: hidden;
      border: 1px solid #dfe6e1; background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,.04);
    }
    .search-pill .input-group-text { background: transparent; border: 0; color: #7a8a97; padding: .35rem .5rem; }
    .search-pill .form-control { border: 0; padding: .45rem .75rem; min-height: 0; }
    .search-pill .form-control::placeholder { color: #90a0ad; }
    .search-pill .btn { border: 0; padding: .45rem .75rem; border-radius: 0; }
    .search-pill:focus-within { box-shadow: 0 0 0 3px rgba(104,177,58,.14), 0 4px 12px rgba(0,0,0,.05); border-color: #68b13a; }

    /* Notifications dropdown */
    .notifications-menu { width: min(420px, 90vw); border: 1px solid rgba(0,0,0,.06); }
    .notifications-menu .notif-item { padding: .6rem .9rem; text-decoration: none; border-top: 1px solid rgba(0,0,0,.04); }
    .notifications-menu .notif-item:first-child { border-top: 0; }
    .notifications-menu .notif-item.is-unread { background: #f8fbf6; }
    .notifications-menu .notif-item .dot {
      width: .5rem; height: .5rem; border-radius: 50%; background: #68b13a;
      display: inline-block; margin-right: .6rem; margin-top: .45rem; flex: 0 0 .5rem;
    }
    .notifications-menu .title { font-weight: 600; }
    .notifications-menu .msg { font-size: .9rem; }
  </style>

  <!-- Scripts -->
  <script>
    // Enable Bootstrap tooltips (show page name on hover)
    document.addEventListener('DOMContentLoaded', function () {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el, { boundary: 'window' }));
    });

    // Autofocus mobile search when expanded
    document.addEventListener('shown.bs.collapse', (e) => {
      if (e.target.id === 'mobileSearch') {
        const input = document.getElementById('navbarSearchMobile');
        input && setTimeout(() => input.focus(), 50);
      }
    });

    // Notifications: mark single as read (fire-and-forget) + Mark all
    document.addEventListener('DOMContentLoaded', function () {
      const token = document.querySelector('meta[name="csrf-token"]')?.content;

      document.querySelectorAll('.notifications-menu .notif-item').forEach(el => {
        el.addEventListener('click', () => {
          const id = el.dataset.id;
          if (!id || !token) return;
          fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            keepalive: true
          }).catch(() => {});
        });
      });

      const markAllBtn = document.getElementById('markAllReadBtn');
      if (markAllBtn && token) {
        markAllBtn.addEventListener('click', async (e) => {
          e.preventDefault();
          const r = await fetch('{{ route("notifications.readAll") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
          });
          if (r.ok) location.reload();
        });
      }

      // Optional Echo realtime badge update (kept if you use Echo)
      if (window.Echo && "{{ auth()->id() }}") {
        window.Echo.private('users.{{ auth()->id() }}')
          .notification((data) => {
            const bell = document.querySelector('#notifDropdown .badge');
            if (bell) {
              bell.textContent = (parseInt(bell.textContent || '0', 10) + 1);
            } else {
              const badge = document.createElement('span');
              badge.className = 'badge rounded-pill bg-danger badge-dot';
              badge.textContent = '1';
              document.getElementById('notifDropdown').appendChild(badge);
            }
            const menu = document.querySelector('#notifDropdown + .dropdown-menu .menu-body');
            if (menu) {
              const a = document.createElement('a');
              a.href = data.url || '#';
              a.className = 'notif-item d-flex align-items-start is-unread';
              a.innerHTML = `
                <span class="dot"></span>
                <div class="content flex-grow-1">
                  <div class="title">${data.title || 'Notification'}</div>
                  ${data.message ? `<div class="msg text-muted">${data.message}</div>` : ''}
                  <div class="time text-muted small">just now</div>
                </div>`;
              menu.prepend(a);
            }
          });
      }
    });
  </script>

  <!-- Ensure Bootstrap Icons + Bootstrap JS are included in your base layout -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
</header>
