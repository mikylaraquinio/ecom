<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        /* Reuse your login page style */
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
            background-color: #d7f2c9;
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
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .welcome-content img {
            max-height: 70px;
            margin-bottom: 1.5rem;
        }

        .welcome-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .welcome-content p {
            font-size: 1.2rem;
            max-width: 450px;
            line-height: 1.6;
        }

        .login-box {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 3rem;
            max-width: 600px;
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

        .input-group input, .input-group textarea {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
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

        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
                min-height: auto;
            }

            .login-left, .login-box {
                flex: none;
                width: 100%;
            }

            .login-box {
                padding: 2rem;
                box-shadow: none;
            }
        }
    </style>

    <div class="login-wrapper">
        <!-- Left section -->
        <div class="login-left">
            <div class="welcome-content">
                <img src="assets/logo.png" alt="FarmSmart Logo">
                <h1>Grow with FarmSmart</h1>
                <p>Connect with farmers, buy fresh produce, and thrive in agriculture.</p>
            </div>
        </div>

        <!-- Right form section -->
        <div class="login-box">
            <div class="text-center mb-4">
                <h2>Create Account</h2>
                <p>Sign up and become part of our farming community.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g. Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. juan@email.com" value="{{ old('email') }}" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Phone -->
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="09XXXXXXXXX" value="{{ old('phone') }}" required pattern="^09\d{9}$">
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Address -->
                <div class="input-group">
                    <label for="address">Complete Address</label>
                    <textarea id="address" name="address" rows="2" placeholder="e.g. Brgy. Sampaguita, San Juan, Batangas" required style="resize: none;">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <button type="submit" class="farm-btn">Register</button>

                <div class="signup-text">
                    Already have an account? <a href="{{ route('login') }}">Log in here</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
