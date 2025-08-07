<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            display: flex;
            flex-direction: row;
            min-height: calc(100vh - 112px);
            margin-top: 72px;
            width: 100%;
        }

        .login-left {
            flex: 1;
            background-color: #d7f2c9; /* Pastel green */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            text-align: center;
        }

        .welcome-content {
            color: #2f4f1c;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center horizontally */
            justify-content: center;
            text-align: center;
        }

        .welcome-content img {
            max-height: 70px;
            margin-bottom: 1.5rem;
            display: block;
        }

        .welcome-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .welcome-content p {
            font-size: 1.2rem;
            max-width: 450px;
            line-height: 1.6;
            margin: 0 auto;
        }

        .login-box {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 3rem;
            max-width: 500px;
            border-radius: 0;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.05);
        }

        .login-box h2 {
            font-size: 2rem;
            color: #4C7737;
            margin-bottom: 0.5rem;
        }

        .login-box p {
            font-size: 1rem;
            color: #6c757d;
        }

        .input-group {
            margin-top: 1.5rem;
        }

        .input-group label {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .forgot-password {
            text-align: right;
            margin-top: 0.75rem;
            font-size: 0.95rem;
        }

        .farm-btn {
            background: linear-gradient(to right, #71b127, #9feb47);
            color: white;
            font-weight: bold;
            border: none;
            padding: 0.85rem;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            margin-top: 2rem;
        }

        .farm-btn:hover {
            background: #5d9f22;
        }

        .signup-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 1rem;
        }

        .signup-text a {
            color: #4C7737;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
                min-height: auto;
            }

            .login-left,
            .login-box {
                flex: none;
                width: 100%;
                border-radius: 0;
            }

            .login-box {
                padding: 2rem;
                box-shadow: none;
            }

            .welcome-content h1 {
                font-size: 2rem;
            }

            .welcome-content p {
                font-size: 1.1rem;
            }
        }
    </style>

    <div class="login-wrapper">
        <!-- Left side content -->
        <div class="login-left">
            <div class="welcome-content">
                <img src="assets/logo.png" alt="FarmSmart Logo">
                <h1>Welcome to FarmSmart</h1>
                <p>Empowering farmers with tools and knowledge to cultivate success. Join the smart farming movement today.</p>
            </div>
        </div>

        <!-- Right side login card -->
        <div class="login-box">
            <div class="text-center mb-4">
                <h2>Welcome Back</h2>
                <p>Log in to access fresh opportunities.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Forgot your password?</a>
                </div>

                <button type="submit" class="farm-btn">Log in</button>

                <div class="signup-text">
                    Don't have an account? <a href="{{ route('register') }}">Sign up here</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
