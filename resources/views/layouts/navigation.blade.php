<header class="header fixed-top shadow">
    <div class="header-inner">
        <div class="container">
            @if (Route::has('login')) 
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <a class="navbar-brand" href="#">
                        <img src="assets/logo.png" alt="FarmSmart Logo" style="height: 50px;">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item"><a class="nav-link" href="{{ url('/welcome') }}">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('shop') }}">Shop</a></li>
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('cart') }}">
                                    <i class="fa fa-shopping-cart"></i> Cart
                                    @if ($cartCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <!--Notification-->
                            @php
                                $user = auth()->user();
                                $notifications = $user
                                    ? $user->notifications()->latest()->limit(10)->get()
                                    : collect();
                                $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
                            @endphp

                            <li class="nav-item dropdown">
                                <button class="btn nav-link position-relative px-2"
                                        id="notifDropdown"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        title="Notifications">
                                    <i class="fa-regular fa-bell"></i>
                                    @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                    @endif
                                </button>

                                <div class="dropdown-menu dropdown-menu-end p-0"
                                    aria-labelledby="notifDropdown"
                                    style="width: 320px; max-height: 380px; overflow:auto;">
                                    @forelse($notifications as $n)
                                    @php
                                        $data = $n->data ?? [];
                                        $title = $data['title'] ?? 'Notification';
                                        $message = $data['message'] ?? ($data['body'] ?? '');
                                        $url = $data['url'] ?? '#';
                                    @endphp

                                    <a href="{{ $url }}"
                                        class="dropdown-item d-flex gap-2 align-items-start notif-item {{ $n->read_at ? '' : 'bg-light' }}"
                                        data-id="{{ $n->id }}">
                                        <span class="mt-1">
                                        <i class="fa-solid fa-circle small {{ $n->read_at ? 'text-secondary' : 'text-success' }}"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                        <div class="small fw-semibold">{{ $title }}</div>
                                        @if($message)
                                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($message, 90) }}</div>
                                        @endif
                                        <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="p-4 text-center text-muted small">
                                        No notifications yet
                                    </div>
                                    @endforelse

                                    <div class="dropdown-divider m-0"></div>
                                    <div class="d-flex justify-content-between align-items-center p-2">
                                    <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm">View all</a>
                                    @if($unreadCount > 0)
                                        <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn">Mark all as read</button>
                                    @endif
                                    </div>
                                </div>
                            </li>

                            

                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-user"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('user_profile') }}">Profile</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="dropdown-item" type="submit">Log Out</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ url('/welcome') }}">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
                            @if (Route::has('register'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                            @endif
                        @endauth
                    </ul>
                </nav>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Mark single notification as read when clicked, then navigate
        document.querySelectorAll('.notif-item').forEach(item => {
            item.addEventListener('click', function (e) {
            const id = this.dataset.id;
            const href = this.getAttribute('href') || '#';

            // Fire-and-forget mark-read so navigation feels instant
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                keepalive: true
            });

            // Let the browser navigate
            });
        });

        // Mark all as read
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function () {
            fetch(`/notifications/read-all`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            })
            .then(() => window.location.reload());
            });
        }
        });
        </script>
</header>
