<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>


    {{-- Two-column quick filters --}}
    <section class="container mt-4">
        <div class="row g-3">
            <div class="col-6">
            <button type="button"
                    class="w-100 p-4 border rounded-3 bg-white shadow-sm text-center group-filter"
                    data-group="produce" style="min-height:110px;">
                <div class="fw-bold fs-5">Produce</div>
                <div class="text-muted small">Fruits, vegetables, grains…</div>
            </button>
            </div>
            <div class="col-6">
            <button type="button"
                    class="w-100 p-4 border rounded-3 bg-white shadow-sm text-center group-filter"
                    data-group="livestock" style="min-height:110px;">
                <div class="fw-bold fs-5">Livestocks</div>
                <div class="text-muted small">Cattle, poultry, etc.</div>
            </button>
            </div>
        </div>

        <!-- Active-filter chip -->
        <div class="d-flex align-items-center gap-2 mt-2" id="activeFilter" style="display:none;">
            <span class="badge bg-success" id="activeFilterLabel"></span>
            <button class="btn btn-link p-0 ms-2" id="clearFilter">Clear</button>
        </div>
    </section>

    <style>
    /* tiny highlight when a filter is active */
    .group-filter.active { outline: 3px solid #71b127; outline-offset: 2px; }
    </style>

    <div class="container mt-3 mt-md-4">
        <form action="{{ url('/search') }}" method="GET" class="d-flex justify-content-center align-items-center flex-wrap gap-2">
            <input
                type="text"
                name="query"
                class="form-control"
                placeholder="🔍 Search fresh produce, categories, or farmers..."
                style="max-width: 500px; border: 2px solid #a2c96f; border-radius: 10px; padding: 10px;"
                required
            >
            <button type="submit" class="btn btn-success px-4" style="background-color: #71b127; border-radius: 10px;">
                Search
            </button>
        </form>
    </div>


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
    
    <section id="start_now py-5"
        style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(/assets/products.jpg) no-repeat right center;">
        <div class="container text-white py-5">
            <div class="row py-5">
                <div class="col-lg-6">
                    <h1 class="font-weight-bold py-5" style="color: black;">Discover new products, shop our bestsellers today!</h1>
                </div>
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