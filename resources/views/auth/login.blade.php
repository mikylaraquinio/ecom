<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="login-container">
        <div class="form-box login">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h1>Login</h1>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="forgot-link">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    @endif
                </div>

                <div class="remember-me">
                    <label for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

</x-guest-layout>
