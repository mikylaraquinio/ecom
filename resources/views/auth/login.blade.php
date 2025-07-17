<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="login-wrapper farm-bg">
        <div class="login-box farm-style">
            <div class="d-flex flex-column align-items-center mb-4">
                <img src="assets/logo.png" alt="FarmSmart Logo" class="mb-2" style="height: 50px">
                <h2 class="text-center" style="color: #4C7737;">Welcome Back</h2>
                <p class="text-muted text-center" style="font-size: 0.9rem;">Log in to access fresh opportunities.</p>
            </div>


            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. you@example.com" value="{{ old('email') }}" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="forgot-password text-end mb-3">
                    <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 0.9rem;">Forgot your password?</a>
                </div>

                <button type="submit" class="farm-btn">Log in</button>

                <div class="signup-text mt-3">
                    Don't have an account? <a href="{{ route('register') }}">Sign up here</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
