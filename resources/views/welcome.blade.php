<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Welcome') }}
    </h2>
  </x-slot>

  {{-- =========================
       TWO-COLUMN LAYOUT: Categories | (Carousel + Products)
       ========================= --}}
  <section id="home-layout" class="mt-4 mb-5">
    <div class="container px-3 px-lg-4">
      <div class="row g-4">

        {{-- LEFT COLUMN: Categories --}}
        <div class="col-lg-3">
          <div class="card section-shadow border-0 rounded-4 overflow-hidden h-100">
            <div class="card-header bg-success text-white fw-semibold py-3">
              <i class="bi bi-list me-2"></i> Shop by Categories
            </div>
            <div class="list-group list-group-flush small py-2">
              {{-- LIVESTOCK --}}
              <a href="{{ route('shop', ['group'=>'livestock','category'=>'cattle']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-cow"></i> Cattle (Baka)
              </a>
              <a href="{{ route('shop', ['group'=>'livestock','category'=>'carabao']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-shield-check"></i> Carabao (Kalabaw)
              </a>
              <a href="{{ route('shop', ['group'=>'livestock','category'=>'goat']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-emoji-sunglasses"></i> Goat (Kambing)
              </a>
              <a href="{{ route('shop', ['group'=>'livestock','category'=>'swine']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-piggy-bank"></i> Swine (Baboy)
              </a>

              <div class="px-3 pt-3 text-muted text-uppercase small fw-semibold">Produce</div>
              <a href="{{ route('shop', ['group'=>'produce','category'=>'rice']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-basket"></i> Rice (Bigas/Palay)
              </a>
              <a href="{{ route('shop', ['group'=>'produce','category'=>'mango']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-brightness-alt-high"></i> Mango (Carabao Mango)
              </a>
              <a href="{{ route('shop', ['group'=>'produce','category'=>'vegetables']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 category-link">
                <i class="bi bi-flower1"></i> Vegetables
              </a>
            </div>
          </div>
        </div>

        {{-- RIGHT COLUMN: Carousel + Products --}}
        <div class="col-lg-9 d-flex flex-column gap-4">

          {{-- === CAROUSEL === --}}
          <div id="mainCarousel" class="carousel slide section-shadow rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="{{ asset('assets/shop-bg.jpg') }}" class="d-block w-100 banner-img" alt="Provincial Livestock Fair">
              </div>
              <div class="carousel-item">
                <img src="{{ asset('assets/farmer.jpg') }}" class="d-block w-100 banner-img" alt="Free Vet Outreach">
              </div>
              <div class="carousel-item">
                <img src="{{ asset('assets/crops.jpg') }}" class="d-block w-100 banner-img" alt="Cattle Auction Day">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>

          {{-- === PRODUCTS (CARD SECTION) === --}}
          <section id="products-section" class="section-shadow border rounded-4 bg-white p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-semibold mb-0">🛒 Latest Products</h5>
              <a href="{{ route('shop') }}" class="btn btn-outline-success btn-sm">Browse All</a>
            </div>

            <div id="product-list" class="min-vh-25 d-flex align-items-center justify-content-center">
              <div class="text-muted">Loading products…</div>
            </div>
          </section>

        </div>
      </div>
    </div>

    {{-- === STYLES === --}}
    <style>
      body { background: #f4f6f5; }

      /* Card and section style */
      .section-shadow {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        transition: box-shadow .3s ease, transform .3s ease;
      }
      .section-shadow:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
        transform: translateY(-2px);
      }

      /* Sidebar */
      #home-layout .list-group-item {
        border: 0;
        padding: .7rem .95rem;
        transition: background .2s;
      }
      #home-layout .list-group-item:hover {
        background: #f5f8f6;
      }

      /* Carousel */
      .banner-img {
        aspect-ratio: 16/5;
        object-fit: cover;
        transition: transform .4s ease;
      }
      .banner-img:hover { transform: scale(1.02); }

      @media (max-width: 768px) {
        #home-layout .col-lg-3 { display: none; }
        .banner-img { aspect-ratio: 16/7; }
      }
    </style>
  </section>

  {{-- === SCRIPT: Dynamic Loader === --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const productList = document.getElementById("product-list");
      const shopRoute = "{{ route('shop') }}";

      function loadProducts(url) {
        fetch(url, {
          headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.text())
        .then(html => {
          productList.innerHTML = html.trim() || "<div class='text-center text-muted py-4'>No products found.</div>";
        })
        .catch(() => {
          productList.innerHTML = "<div class='text-center text-danger py-4'>Failed to load products.</div>";
        });
      }

      loadProducts(shopRoute);
    });
  </script>

  <div class="chat-container" style="max-width:600px;margin:auto;padding:20px;">
    <div id="chatBox" style="border:1px solid #ccc;padding:15px;height:300px;overflow-y:auto;border-radius:10px;background:white;">
        <div><strong>FarmSmart AI:</strong> Hello! How can I help your farm today? 🌱</div>
    </div>

    <div style="display:flex;gap:10px;margin-top:10px;">
        <input type="text" id="userMessage" placeholder="Ask about livestock, crops, etc..." style="flex:1;padding:10px;border-radius:8px;border:1px solid #bbb;">
        <button onclick="sendMessage()" style="background:#71b127;color:white;border:none;padding:10px 20px;border-radius:8px;">Send</button>
    </div>
</div>

<script>
async function sendMessage() {
    const input = document.getElementById('userMessage');
    const chatBox = document.getElementById('chatBox');
    const message = input.value.trim();
    if (!message) return;

    chatBox.innerHTML += `<div><strong>You:</strong> ${message}</div>`;
    input.value = '';

    const response = await fetch("{{ route('chat') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    });

    const data = await response.json();
    chatBox.innerHTML += `<div><strong>FarmSmart AI:</strong> ${data.reply}</div>`;
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>

</x-app-layout>
