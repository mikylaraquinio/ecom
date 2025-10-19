<x-guest-layout>
    <div class="register-container">
        <div class="register-card">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo">
            <h2>Email Verification Required</h2>
            <p class="subtitle">
                A verification link has been sent to your email address.  
                Please check your inbox and click the link to activate your account.
            </p>

            @if (session('status') == 'verification-link-sent')
                <p class="text-green-600 mb-3">A new verification link has been sent to your email!</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="farm-btn w-full">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-red-500 underline">Logout</button>
            </form>
        </div>
    </div>
</x-guest-layout>
