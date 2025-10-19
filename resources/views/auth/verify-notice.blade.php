<x-guest-layout>
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
        .farm-btn {
            background: linear-gradient(90deg, #71b127, #9feb47);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.1s ease;
        }
        .farm-btn:hover {
            background: #5a9216;
            transform: translateY(-1px);
        }
    </style>

    <div class="register-container">
        <div class="register-card">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo">
            <h2>Verify Your Email</h2>
            <p class="subtitle">
                A verification link has been sent to your email address.  
                Please check your inbox to complete your registration.
            </p>

            @if (session('status'))
                <p class="text-green-600 mb-2">{{ session('status') }}</p>
            @endif

            <a href="{{ route('login') }}" class="farm-btn mt-3 block text-center">
                Go to Login
            </a>
        </div>
    </div>
</x-guest-layout>
