<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FarmSmart</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Styles / Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  @php
    // Hide navbar/footer/widget on chat routes (e.g., route('chat') / route('chat.*'))
    // If you also pass ?embed=1 to chat, this still works fine.
    $isChatView = \Illuminate\Support\Facades\Route::is('chat') || \Illuminate\Support\Facades\Route::is('chat.*');
  @endphp

  <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

      {{-- Navbar (hidden on chat view) --}}
      @unless($isChatView)
        @include('layouts.navigation')
      @endunless

      <!-- Page Content -->
      <main style="{{ $isChatView ? '' : 'padding-top: 80px;' }}">
        {{ $slot }}

        {{-- Floating chat widget (don’t render it on the chat page itself) --}}
        @unless($isChatView)
          @include('partials.chat-widget')
        @endunless
      </main>

      {{-- Footer (hidden on chat view) --}}
      @unless($isChatView)
        <footer class="footer text-white">
          <div class="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
              <span>&copy; 2025 <strong>FarmSmart</strong>. All Rights Reserved.</span>
            </div>
            <div>
              <a href="#" target="_blank" class="text-white text-decoration-none me-3">BUSINFO-T3</a>
              <a href="#" target="_blank" class="text-white text-decoration-none me-3">Privacy Policy</a>
              <a href="#" target="_blank" class="text-white text-decoration-none">Contact Us</a>
            </div>
          </div>
        </footer>
      @endunless

    </div>
  </body>
</html>
