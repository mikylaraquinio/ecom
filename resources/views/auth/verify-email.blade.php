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
      min-height: 100vh;
      padding: 2rem;
    }

    .register-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
      padding: 3rem 2.5rem;
      width: 100%;
      max-width: 440px;
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .register-card img {
      height: 70px;
      margin-bottom: 1.25rem;
    }

    .register-card h2 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #305f1c;
      margin-bottom: 0.75rem;
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
      text-decoration: none;
      display: inline-block;
      transition: all 0.25s ease;
      box-shadow: 0 2px 8px rgba(113, 177, 39, 0.25);
    }

    .farm-btn:hover {
      background: #5a9216;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(90, 146, 22, 0.25);
    }

    .success-text {
      color: #16a34a;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 1rem;
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

      @if (session('status') == 'verification-link-sent')
        <p class="success-text">✅ A new verification link has been sent to your email!</p>
      @endif

      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="farm-btn">Resend Verification Email</button>
      </form>
    </div>
  </div>
</x-guest-layout>
