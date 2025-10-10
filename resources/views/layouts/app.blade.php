<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FarmSmart</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/fs_icon.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Bootstrap / Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  @php
    $isChatView = \Illuminate\Support\Facades\Route::is('chat') || \Illuminate\Support\Facades\Route::is('chat.*');
  @endphp

  <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 d-flex flex-column">

      {{-- ===== NAVBAR ===== --}}
      @unless($isChatView)
        @include('layouts.navigation')
      @endunless

      {{-- ===== MAIN CONTENT ===== --}}
      <main class="page-main flex-grow-1 {{ $isChatView ? '' : 'has-navbar' }}">
        {{ $slot }}

        {{-- Floating Chat Widget --}}
        @unless($isChatView)
          @include('partials.chat-widget')
        @endunless
      </main>

      {{-- ===== FOOTER ===== --}}
      @unless($isChatView)
      <footer class="footer mt-auto text-white">
        <div class="container py-5">
          <div class="row gy-4">
            <!-- About -->
            <div class="col-md-3">
              <h5 class="text-uppercase fw-bold mb-3">FarmSmart</h5>
              <p class="small text-light">
                Empowering farmers by connecting them directly to local consumers. 
                FarmSmart provides a fair, transparent, and sustainable marketplace for agricultural products.
              </p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-3">
              <h6 class="fw-semibold mb-3 text-uppercase">Quick Links</h6>
              <ul class="list-unstyled small">
                <li><a href="{{ route('shop') }}" class="footer-link">Shop Products</a></li>
                <li><a href="#" class="footer-link">Categories</a></li>
                <li><a href="#" class="footer-link">About Us</a></li>
                <li><a href="#" class="footer-link">Contact</a></li>
              </ul>
            </div>

            <!-- Support -->
            <div class="col-md-3">
              <h6 class="fw-semibold mb-3 text-uppercase">Customer Support</h6>
              <ul class="list-unstyled small">
                <li><a href="#" class="footer-link">FAQs</a></li>
                <li><a href="#" class="footer-link">Terms & Conditions</a></li>
                <li><a href="#" class="footer-link">Privacy Policy</a></li>
                <li><a href="#" class="footer-link">Return Policy</a></li>
              </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-3">
              <h6 class="fw-semibold mb-3 text-uppercase">Follow Us</h6>
              <div class="d-flex gap-3">
                <a href="#" class="footer-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="footer-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="footer-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="footer-icon"><i class="fab fa-youtube"></i></a>
              </div>
              <p class="small mt-3">Stay connected for latest product updates!</p>
            </div>
          </div>

          <hr class="border-light my-4">

          <div class="text-center small">
            &copy; {{ date('Y') }} <strong>FarmSmart</strong>. All Rights Reserved. | Built for Sustainable Agriculture 🌱
          </div>
        </div>
      </footer>
      @endunless
    </div>

    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      .min-h-screen {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      main.page-main {
        flex: 1 0 auto;
      }

      /* Navbar adjustments */
      nav.navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      }
      .page-main.has-navbar {
        padding-top: var(--navbar-height, 100px);
      }

      :root {
        --navbar-height: 100px;
      }

      @media (max-width: 991px) {
        :root {
          --navbar-height: 120px;
        }
      }

      /* ===== FOOTER STYLING ===== */
      .footer {
        background: linear-gradient(180deg, #5a9216, #71b127);
        color: #fff;
        font-size: 0.9rem;
      }

      .footer h5, .footer h6 {
        color: #fff;
        font-weight: 600;
      }

      .footer p, .footer ul li {
        color: rgba(255,255,255,0.9);
      }

      .footer .footer-link {
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        transition: color 0.3s ease;
      }

      .footer .footer-link:hover {
        color: #d4f4a1;
        text-decoration: underline;
      }

      .footer .footer-icon {
        background: #ffffff22;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease, transform 0.3s ease;
      }

      .footer .footer-icon:hover {
        background: #ffffff44;
        transform: scale(1.1);
      }

      hr {
        opacity: 0.2;
      }

      @media (max-width: 768px) {
        .footer {
          text-align: center;
        }
        .footer .footer-icon {
          margin: 0 auto;
        }
      }

      /* Dark Mode */
      .dark .footer {
        background: #355a1b;
      }
    </style>
  </body>
</html>
