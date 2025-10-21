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
        {{-- LEFT COLUMN: Dynamic Categories --}}
        <div class="col-lg-3">
          <div class="card section-shadow border-0 rounded-4 overflow-hidden h-100">
            <div class="card-header bg-success text-white fw-semibold py-3">
              Shop by Categories
            </div>

            <div class="list-group list-group-flush small py-2">
              @php
                // Group categories by keyword
                $livestock = $mainCategories->filter(fn($cat) => in_array(strtolower($cat->name), [
                  'cattle / cow',
                  'pig / hog',
                  'goat',
                  'chicken',
                  'duck',
                  'sheep',
                  'carabao / water buffalo',
                  'rabbit',
                  'fish & aquatic'
                ]));

                $produce = $mainCategories->filter(fn($cat) => in_array(strtolower($cat->name), [
                  'fruits',
                  'vegetables',
                  'root crops',
                  'grains & rice',
                  'legumes',
                  'herbs & spices',
                  'processed produce',
                  'feeds & farm inputs',
                  'farm equipment & tools',
                  'farm services'
                ]));
              @endphp

              {{-- 🐄 LIVESTOCK SECTION --}}
              @if ($livestock->count())
                <div class="px-3 pt-2 text-muted text-uppercase small fw-semibold border-bottom pb-1">
                  Livestock
                </div>
                @foreach ($livestock as $main)
                  @auth
                    <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                      data-bs-toggle="collapse" data-bs-target="#cat-{{ $main->id }}" aria-expanded="false"
                      aria-controls="cat-{{ $main->id }}">
                      <span>{{ $main->name }}</span>
                      <i class="bi bi-caret-down-fill small"></i>
                    </button>
                  @else
                    <button type="button"
                      class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-muted"
                      onclick="window.location.href='{{ route('login') }}'">
                      <span>{{ $main->name }}</span>
                      <i class="bi bi-lock-fill small"></i>
                    </button>
                  @endauth

                  <div class="collapse ps-3" id="cat-{{ $main->id }}">
                    @foreach ($main->subcategories as $sub)
                      @auth
                        <a href="{{ route('shop', ['category' => \Illuminate\Support\Str::slug($sub->name)]) }}"
                          class="list-group-item list-group-item-action border-0 small">
                          {{ $sub->name }}
                        </a>
                      @else
                        <button type="button" class="list-group-item list-group-item-action border-0 small text-muted"
                          onclick="window.location.href='{{ route('login') }}'">
                          {{ $sub->name }}
                        </button>
                      @endauth

                      @if ($sub->subcategories->count())
                        <div class="ps-3">
                          @foreach ($sub->subcategories as $child)
                            @auth
                              <a href="{{ route('shop', ['category' => \Illuminate\Support\Str::slug($child->name)]) }}"
                                class="list-group-item list-group-item-action border-0 small text-muted">
                                — {{ $child->name }}
                              </a>
                            @else
                              <button type="button" class="list-group-item list-group-item-action border-0 small text-muted"
                                onclick="window.location.href='{{ route('login') }}'">
                                — {{ $child->name }}
                              </button>
                            @endauth
                          @endforeach
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endforeach
              @endif

              {{-- 🌾 FARM PRODUCE SECTION --}}
              @if ($produce->count())
                <div class="px-3 pt-3 mt-2 text-muted text-uppercase small fw-semibold border-bottom pb-1">
                  Farm Produce
                </div>
                @foreach ($produce as $main)
                  @auth
                    <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                      data-bs-toggle="collapse" data-bs-target="#cat-{{ $main->id }}" aria-expanded="false"
                      aria-controls="cat-{{ $main->id }}">
                      <span>{{ $main->name }}</span>
                      <i class="bi bi-caret-down-fill small"></i>
                    </button>
                  @else
                    <button type="button"
                      class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-muted"
                      onclick="window.location.href='{{ route('login') }}'">
                      <span>{{ $main->name }}</span>
                      <i class="bi bi-lock-fill small"></i>
                    </button>
                  @endauth

                  <div class="collapse ps-3" id="cat-{{ $main->id }}">
                    @foreach ($main->subcategories as $sub)
                      @auth
                        <a href="{{ route('shop', ['category_slug' => \Illuminate\Support\Str::slug($sub->name)]) }}"
                          class="list-group-item list-group-item-action border-0 small">
                          {{ $sub->name }}
                        </a>
                      @else
                        <button type="button" class="list-group-item list-group-item-action border-0 small text-muted"
                          onclick="window.location.href='{{ route('login') }}'">
                          {{ $sub->name }}
                        </button>
                      @endauth

                      @if ($sub->subcategories->count())
                        <div class="ps-3">
                          @foreach ($sub->subcategories as $child)
                            @auth
                              <a href="{{ route('shop', ['category' => \Illuminate\Support\Str::slug($child->name)]) }}"
                                class="list-group-item list-group-item-action border-0 small text-muted">
                                — {{ $child->name }}
                              </a>
                            @else
                              <button type="button" class="list-group-item list-group-item-action border-0 small text-muted"
                                onclick="window.location.href='{{ route('login') }}'">
                                — {{ $child->name }}
                              </button>
                            @endauth
                          @endforeach
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>


        {{-- RIGHT COLUMN: Carousel + Products --}}
        <div class="col-lg-9 d-flex flex-column gap-4">

          {{-- === CAROUSEL === --}}
          <div id="mainCarousel" class="carousel slide section-shadow rounded-4 overflow-hidden" data-bs-ride="carousel"
            data-bs-interval="4000">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="{{ asset('assets/11.11.png') }}" class="d-block w-100 banner-img"
                  alt="Provincial Livestock Fair">
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
      body {
        background: #f4f6f5;
      }

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

      .banner-img:hover {
        transform: scale(1.02);
      }

      @media (max-width: 768px) {
        #home-layout .col-lg-3 {
          display: none;
        }

        .banner-img {
          aspect-ratio: 16/7;
        }
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

</x-app-layout>