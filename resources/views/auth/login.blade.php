@php($title = 'Login | FarmSmart')

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f5f8f3;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 120px);
            padding: 2rem;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card img {
            height: 65px;
            margin-bottom: 1rem;
        }

        .login-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2f4f1c;
            margin-bottom: 0.5rem;
        }

        .login-card p.subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .input-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f9fafb;
            font-size: 0.95rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group input:focus {
            border-color: #71b127;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(113, 177, 39, 0.1);
        }

        .forgot-password {
            text-align: right;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .forgot-password a {
            color: #4C7737;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: #5b8d43;
            text-decoration: underline;
        }

        /* Main login button */
        .btn-login {
            background: linear-gradient(90deg, #71b127, #9feb47);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.1s ease;
        }

        .btn-login:hover {
            background: #5a9216;
            transform: translateY(-1px);
        }

        /* Google button */
        .btn-google {
            margin-top: 1rem;
            width: 100%;
            background: #ffffff;
            color: #374151;
            font-weight: 500;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-google img {
            width: 18px;
            height: 18px;
        }

        .btn-google:hover {
            background: #f3f4f6;
        }

        /* Divider line */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.8rem 0;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
            margin: 0 0.75rem;
        }

        /* Signup link */
        .signup-link {
            font-size: 0.95rem;
        }

        .signup-link a {
            color: #4C7737;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-card">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo">
            <h2>Log In</h2>
            <p class="subtitle">
                Empowering livestock farmers with tools, data, and market connections — your success starts here.
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>

                <!-- LOGIN BUTTON -->
                <button type="submit" class="btn-login">Log In</button>

                <div class="divider">or</div>

                <a href="{{ route('google.redirect') }}" class="btn-google">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google icon">
                    Login with Google
                </a>


                <p class="signup-link mt-4">
                    Don’t have an account?
                    <a href="{{ route('register') }}">Sign up</a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
