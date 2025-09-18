<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <div class="container mt-3 mt-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <form action="{{ url('/search') }}" method="GET" class="search-form">
                    <div class="input-group search-group">
                        <input
                            type="text"
                            name="query"
                            class="form-control"
                            placeholder="🔍 Search fresh produce, categories, or farmers..."
                            aria-label="Search"
                            required
                        >
                        <button type="submit" class="btn btn-success" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Events / Ads (compact card) --}}
<section id="events-ads" class="mt-4">
  <div class="container px-3 px-lg-4"> {{-- slightly tighter container --}}
    <div class="outer-card border rounded-3 shadow-sm p-2 p-md-3">
      <div class="row g-2 align-items-stretch">

        {{-- LEFT: carousel --}}
        <div class="col-lg-9">
          <div class="hero-wrapper rounded-3 overflow-hidden">
            <div id="livestockEventsCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
              <div class="carousel-indicators">
                <button type="button" data-bs-target="#livestockEventsCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#livestockEventsCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#livestockEventsCarousel" data-bs-slide-to="2"></button>
              </div>

              <div class="carousel-inner h-100">
                <div class="carousel-item active">
                  <a href="#">
                    <img src="{{ asset('assets/crops.jpg') }}" class="d-block w-100 event-poster" alt="Provincial Livestock Fair">
                    <div class="carousel-caption text-start compact-caption">
                      <h6 class="mb-1 fw-semibold">Provincial Livestock Fair</h6>
                      <p class="mb-0 small">Dagupan • Oct 12–14</p>
                    </div>
                  </a>
                </div>

                <div class="carousel-item">
                  <a href="#">
                    <img src="{{ asset('assets/farmer.jpg') }}" class="d-block w-100 event-poster" alt="Free Vet Outreach">
                    <div class="carousel-caption text-start compact-caption">
                      <h6 class="mb-1 fw-semibold">Free Vet Outreach</h6>
                      <p class="mb-0 small">Urdaneta • Sept 28</p>
                    </div>
                  </a>
                </div>

                <div class="carousel-item">
                  <a href="#">
                    <img src="{{ asset('assets/shop-bg.jpg') }}" class="d-block w-100 event-poster" alt="Cattle Auction Day">
                    <div class="carousel-caption text-start compact-caption">
                      <h6 class="mb-1 fw-semibold">Cattle Auction Day</h6>
                      <p class="mb-0 small">Mangaldan • Oct 20</p>
                    </div>
                  </a>
                </div>
              </div>

              <button class="carousel-control-prev" type="button" data-bs-target="#livestockEventsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#livestockEventsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          </div>
        </div>

        {{-- RIGHT: stacked buttons (same height as carousel) --}}
        <div class="col-lg-3">
          <div class="right-rail d-flex flex-column gap-2">
            <button type="button"
                    class="rail-btn flex-fill py-3 px-3 border rounded-3 bg-white shadow-sm text-center group-filter"
                    data-group="livestock">
              <div class="fw-semibold">Livestocks</div>
              <div class="text-muted small">Cattle, poultry, etc.</div>
            </button>

            <button type="button"
                    class="rail-btn flex-fill py-3 px-3 border rounded-3 bg-white shadow-sm text-center group-filter"
                    data-group="produce">
              <div class="fw-semibold">Produce</div>
              <div class="text-muted small">Fruits, vegetables, grains…</div>
            </button>
          </div>

          <div class="d-flex align-items-center gap-2 mt-2" id="activeFilter" style="display:none;">
            <span class="badge bg-success" id="activeFilterLabel"></span>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- compact styles --}}
  <style>
    /* compact overall height (adjust these two to taste) */
    #events-ads { --hero-h: 300px; }                 /* desktop height (was ~380px) */
     @media (max-width: 991.98px) {             /* below lg */
      #events-ads { --hero-h: auto; }
      #events-ads .hero-wrapper { height: auto; }
      #events-ads .right-rail {
        height: auto;
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
        margin-top: .5rem;
      }
      #events-ads .rail-btn {
        padding: .9rem .8rem;
      }
      #events-ads .rail-btn .fs-5 { font-size: 1rem; }
    }

    /* outer card smaller radius/padding handled via classes */
    #events-ads .hero-wrapper { height: var(--hero-h); }
    #events-ads .right-rail { height: var(--hero-h); }
    #events-ads .rail-btn { min-height: 0; }

    /* image & caption compact */
    #events-ads .event-poster { height: 100%; object-fit: cover; }
    #events-ads .compact-caption {
      left: 0; right: 0; bottom: 0;
      padding: 14px 12px 10px;                        /* smaller padding */
      background: linear-gradient(to top, rgba(0,0,0,.55), rgba(0,0,0,0));
      text-shadow: 0 1px 2px rgba(0,0,0,.5);
      border-bottom-left-radius: .5rem;
      border-bottom-right-radius: .5rem;
    }
    #events-ads .compact-caption h6 { font-size: .95rem; }
    #events-ads .compact-caption p { font-size: .75rem; }

    /* smaller gaps on mobile */
    @media (max-width: 576px) {
      #events-ads .compact-caption h6 { font-size: .9rem; }
      #events-ads .compact-caption p { font-size: .72rem; }
    }

    /* active outline */
    #events-ads .group-filter.active { outline: 3px solid #71b127; outline-offset: 2px; }
  </style>
</section>


    
    <!-- Featured Categories Section 
    <section id="featured-categories" class="py-5">
        <div class="container text-center">
            <h1 class="p-title mb-4">FEATURED CATEGORIES</h1>
            <div class="row">-->
                <!-- Grains & Cereals 
                <div class="ol-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="category-card">
                        <img src="{{ asset('assets/Cereal&Grains.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Grains & Cereals</h5>
                            <p class="category-text">Quality grains and cereals for consumption and farming.</p>
                            <a href="{{ url('/shop?category=204') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>-->

                <!-- Vegetables 
                <div class="ol-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="category-card">
                        <img src="{{ asset('assets/vegetables.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Vegetables</h5>
                            <p class="category-text">Fresh and organic vegetables for healthy meals.</p>
                            <a href="{{ url('/shop?category=205') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>-->

                <!-- Fruits 
                <div class="ol-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="category-card">
                        <img src="{{ asset('assets/Fruits.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Fruits</h5>
                            <p class="category-text">Sweet and nutritious fruits for all seasons.</p>
                            <a href="{{ url('/shop?category=206') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>-->

                <!-- Herbs & Spices 
                <div class="ol-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="category-card">
                        <img src="{{ asset('assets/herbs&Spices.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Herbs & Spices</h5>
                            <p class="category-text">Aromatic herbs and spices for culinary and medicinal use.</p>
                            <a href="{{ url('/shop?category=207') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>-->

    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="p-title mb-0">Latest Products</h2>
                <a href="{{ route('shop') }}" class="btn btn-outline-success btn-sm">Browse All</a>
            </div>

            <div id="product-list" class="min-vh-25 d-flex align-items-center justify-content-center">
                <div class="text-muted">Loading products…</div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const productList = document.getElementById("product-list");
            const shopRoute = "{{ route('shop') }}";

            // Initial load of products (same markup as Shop)
            loadProducts(shopRoute);

            function loadProducts(url) {
                fetch(url, {
                    method: "GET",
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(response => response.text())
                .then(html => {
                    productList.innerHTML = html.trim() || "<p>No products found.</p>";
                    attachInteractions();
                    wirePagination(); // handle pagination links inside the partial
                })
                .catch(err => {
                    console.error(err);
                    productList.innerHTML = "<p class='text-danger'>Failed to load products.</p>";
                });
            }

            function attachInteractions() {
                // Add to cart buttons (matches your Shop script)
                document.querySelectorAll(".add-to-cart-modal").forEach(button => {
                    button.addEventListener("click", function () {
                        const productId = this.dataset.productId;
                        fetch(`/cart/add/${productId}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ quantity: 1 })
                        })
                        .then(r => r.json())
                        .then(data => {
                            alert(data.success ? "Product added to cart!" : (data.message || "Could not add to cart."));
                            // If you're using Bootstrap modals per card, you can hide them here like in Shop.
                        })
                        .catch(console.error);
                    });
                });

                // Wishlist buttons (matches your Shop script)
                document.querySelectorAll(".wishlist-btn").forEach(button => {
                    button.addEventListener("click", function (event) {
                        event.stopPropagation();
                        const productId = this.dataset.productId;
                        const icon = this.querySelector("i");

                        fetch(`/wishlist/toggle/${productId}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                "Accept": "application/json"
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === "added") {
                                icon.classList.remove("far");
                                icon.classList.add("fas");
                            } else {
                                icon.classList.remove("fas");
                                icon.classList.add("far");
                            }
                        })
                        .catch(console.error);
                    });
                });
            }

            function wirePagination() {
                // Intercept pagination links that are inside the partial
                document.querySelectorAll("#product-list .pagination a").forEach(link => {
                    link.addEventListener("click", function (e) {
                        e.preventDefault();
                        const url = this.getAttribute("href");
                        if (!url) return;
                        loadProducts(url);
                        // Optional: scroll back to top of the product section on page change
                        productList.scrollIntoView({ behavior: "smooth", block: "start" });
                    });
                });
            }
            const filterButtons = document.querySelectorAll('.group-filter');
            const activeFilter = document.getElementById('activeFilter');
            const activeFilterLabel = document.getElementById('activeFilterLabel');
            const clearFilterBtn = document.getElementById('clearFilter');

            function setActiveGroup(group) {
            filterButtons.forEach(b => b.classList.toggle('active', b.dataset.group === group));
            if (group) {
                activeFilter.style.display = '';
                activeFilterLabel.textContent = group.charAt(0).toUpperCase() + group.slice(1);
            } else {
                activeFilter.style.display = 'none';
            }
            }

            function applyGroupFilter(group) {
            setActiveGroup(group);
            const url = shopRoute + "?group=" + encodeURIComponent(group);
            loadProducts(url);
            window.history.pushState({}, '', '?group=' + encodeURIComponent(group));
            }

            filterButtons.forEach(btn => {
            btn.addEventListener('click', () => applyGroupFilter(btn.dataset.group));
            });

            if (clearFilterBtn) {
            clearFilterBtn.addEventListener('click', () => {
                setActiveGroup(null);
                loadProducts(shopRoute);
                window.history.pushState({}, '', window.location.pathname);
            });
            }

            /* Optional: if URL already has ?group=... on load, honor it */
            const params = new URLSearchParams(window.location.search);
            if (params.get('group')) {
            setActiveGroup(params.get('group'));
            loadProducts(shopRoute + "?group=" + encodeURIComponent(params.get('group')));
            } else {
            loadProducts(shopRoute); // your existing initial load
            }
        });
    </script>

    
</x-app-layout>