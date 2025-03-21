<header class="header fixed-top">
    <div class="header-inner">
        <div class="container">
            @if (Route::has('login')) 
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <a class="navbar-brand" href="#">FarmSmart</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        @auth
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item"><a class="nav-link active" href="{{ url('/welcome') }}">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('shop') }}">Shop</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('cart') }}">Cart</a></li>

                                <!-- User Dropdown -->
                                <li class="nav-item dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-user"></i> {{ Auth::user()->name }}
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
                            </ul>
                        @else
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item"><a class="nav-link active" href="{{ url('/welcome') }}">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log in</a></li>
                                @if (Route::has('register'))
                                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                                @endif
                            </ul>
                        @endauth
                    </div>
                </nav>
            @endif
        </div>
    </div>
</header>
