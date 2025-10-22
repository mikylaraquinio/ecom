<x-guest-layout>
  <div class="min-h-screen flex items-center justify-center bg-[#f6f8f7] px-4">
    <div class="w-full max-w-md bg-white shadow-md rounded-2xl p-6 text-center">

      <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo"
           class="mx-auto mb-4 w-28">

      <h2 class="text-2xl font-bold text-green-700 mb-2">Verify Your Email</h2>

      <p class="text-gray-600 text-sm mb-5 leading-relaxed">
        We’ve sent a verification link to your registered email address.<br>
        Please check your inbox and click the link to activate your account.
      </p>

      @if (session('status') == 'verification-link-sent')
        <div class="text-green-600 text-sm font-medium mb-3">
          ✅ A new verification link has been sent to your email!
        </div>
      @endif

      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-medium transition">
          Resend Verification Email
        </button>
      </form>

      <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm text-red-500 hover:text-red-600 underline">
          Logout
        </button>
      </form>

    </div>
  </div>
</x-guest-layout>
