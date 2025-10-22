<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Please enter the 6-digit code sent to your email.
    </div>

    @if (session('message'))
        <div class="text-green-600">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.code.submit') }}">
        @csrf
        <input type="text" name="code" maxlength="6" required 
               class="border rounded px-3 py-2 w-full text-center text-lg tracking-widest" 
               placeholder="Enter code">

        <button class="mt-3 bg-green-600 text-white px-4 py-2 rounded">Verify</button>
    </form>
</x-guest-layout>
