@php($title = 'Register | FarmSmart')

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f5f8f3;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 120px);
            padding: 2rem;
        }

        .register-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 480px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-card img {
            height: 65px;
            margin-bottom: 1rem;
        }

        .register-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2f4f1c;
            margin-bottom: 0.5rem;
        }

        .register-card p.subtitle {
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

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f9fafb;
            font-size: 0.95rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
            resize: none;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #71b127;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(113, 177, 39, 0.1);
        }

        /* Register button */
        .farm-btn {
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

        .farm-btn:hover {
            background: #5a9216;
            transform: translateY(-1px);
        }

        /* Divider */
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

        /* Google register button */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background-color: #fff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            color: #3c4043;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Roboto', Arial, sans-serif;
            height: 45px;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .btn-google img {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            margin-top: -1px;
        }

        .btn-google:hover {
            background-color: #f7f8f8;
            border-color: #c6c6c6;
        }

        .btn-google:active {
            background-color: #e8e8e8;
            border-color: #a8a8a8;
        }

        /* Login link */
        .signup-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        .signup-text a {
            color: #4C7737;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>

    <div class="register-container">
        <div class="register-card">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo">
            <h2>Create Account</h2>
            <p class="subtitle">
                Join the FarmSmart community and grow your farm with powerful tools and connections.
            </p>

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
                    <textarea id="address" name="address" rows="2" placeholder="e.g. Brgy. Sampaguita, San Juan, Batangas" required>{{ old('address') }}</textarea>
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

                <div class="divider">or</div>

                <!-- Register with Google -->
                <button type="button" class="btn-google" onclick="window.location.href='{{ route('google.redirect') }}'">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google icon">
                    Register with Google
                </button>


                <div class="signup-text">
                    Already have an account? <a href="{{ route('login') }}">Log in</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
