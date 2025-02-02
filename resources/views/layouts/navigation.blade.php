<header class="header">
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
                                <li class="nav-item"><a class="nav-link" href="#">Categories</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Cart</a></li>
                            </ul>
                            <!-- User Dropdown -->
                            <div class="hidden sm:flex sm:items-center">
                                <x-dropdown align="right">
                                    <x-slot name="trigger">
                                        <button class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
                                            {{ Auth::user()->name }}
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                Log Out
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>

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
