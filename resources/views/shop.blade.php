<x-app-layout>
    {{-- ===========================
    HEADER BANNER
    ============================ --}}
    <section class="header-banner position-relative text-center">
        <div style="height: 300px; overflow: hidden;">
            <img src="{{ asset('assets/shop-bg2.jpg') }}" alt="Shop Banner" class="w-100 h-100 object-fit-cover"
                style="filter: brightness(55%);">
        </div>
        <div class="banner-overlay"></div>
        <div class="banner-text position-absolute top-50 start-50 translate-middle text-center">
            <p class="fw-semibold" style="color:#ffcc00; font-family:'Pacifico', cursive; font-size:1.4rem;">
                "Supporting farmers by providing a fair marketplace for their products"
            </p>
            <h1 class="fw-bold text-white"
                style="font-family:'Fredoka One', sans-serif; font-size:3rem; text-shadow:2px 2px 8px rgba(0,0,0,0.5);">
                SHOP PRODUCTS
            </h1>
            <div class="mx-auto"
                style="width:120px; height:5px; background-color:#ffcc00; border-radius:10px; margin-top:10px;"></div>
        </div>
    </section>

    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Fredoka+One&display=swap" rel="stylesheet">

    {{-- ===========================
    SEARCH + FILTERS
    ============================ --}}
    <section class="py-4 bg-light border-bottom">
        <div class="container px-4 px-lg-5">
            <div class="row g-3 align-items-center">
                {{-- Search --}}
                <div class="col-md-6">
                    <form id="searchForm" method="GET" action="{{ route('shop') }}" class="d-flex">
                        <input type="text" name="search" id="searchBox" class="form-control rounded-pill"
                            placeholder="Search products..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success rounded-pill ms-2 px-4">Search</button>
                    </form>
                </div>

                {{-- Filters --}}
                <div class="col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-success w-100 rounded-pill dropdown-toggle" type="button"
                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filters
                        </button>
                        <div class="dropdown-menu p-3 w-100 shadow border-0">
                            <form id="filterForm" method="GET" action="{{ route('shop') }}">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Price Range</label>
                                    <div class="d-flex">
                                        <input type="number" name="min_price" class="form-control me-2"
                                            placeholder="Min" value="{{ request('min_price') }}">
                                        <input type="number" name="max_price" class="form-control" placeholder="Max"
                                            value="{{ request('max_price') }}">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Sort By</label>
                                    <select name="sort_by" class="form-select">
                                        <option value="">Default</option>
                                        <option value="low_to_high" {{ request('sort_by') == 'low_to_high' ? 'selected' : '' }}>Price: Low to High</option>
                                        <option value="high_to_low" {{ request('sort_by') == 'high_to_low' ? 'selected' : '' }}>Price: High to Low</option>
                                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>
                                            Newest First</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-pill mt-2">Apply
                                    Filters</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===========================
    PRODUCT GRID
    ============================ --}}
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div id="product-list" class="row g-4">
                @include('partials.product-list')
            </div>
        </div>
    </section>

    {{-- ===========================
    RECOMMENDED PRODUCTS
    ============================ --}}
    <section class="py-5" style="background-color:#f9f7f2;">
        <div class="container px-4 px-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-success">🌿 Explore Our Recommendations</h4>
                <a href="{{ route('shop') }}" class="btn btn-outline-success btn-sm rounded-pill">View All</a>
            </div>
            <div id="recommended-products" class="row g-4">
                @include('partials.product-list')
            </div>
        </div>
    </section>

    {{-- ===========================
    WISHLIST ALERT (top-right floating message)
    ============================ --}}
    <div id="wishlistAlert" class="alert alert-success text-center"
        style="display:none; position:fixed; top:20px; right:20px; z-index:9999; border-radius:10px; min-width:220px;">
    </div>



    {{-- ===========================
    CUSTOM STYLES
    ============================ --}}
    <style>
        body {
            background: #f8f9f7;
        }

        /* Banner overlay */
        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        /* Product Grid (Shopee-style) */
        #product-list .product-card,
        #recommended-products .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all .25s ease-in-out;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-card .card-body {
            padding: 10px 12px;
            text-align: center;
        }

        .product-card .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            min-height: 40px;
        }

        .product-card .product-price {
            color: #71b127;
            font-weight: bold;
            font-size: 1rem;
        }

        .product-card .btn {
            font-size: 0.85rem;
            border-radius: 50px;
        }

        @media (max-width: 768px) {
            .product-card img {
                height: 180px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ===========================
    SCRIPTS (unchanged)
    ============================ --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchBox = document.getElementById("searchBox");
            const filterForm = document.getElementById("filterForm");
            const shopRoute = "{{ route('shop') }}";

            function applyFilters() {
                let formData = new FormData(filterForm);
                let queryString = new URLSearchParams(formData).toString();
                let searchQuery = searchBox.value.trim();
                if (searchQuery) queryString += `&search=${encodeURIComponent(searchQuery)}`;

                fetch(shopRoute + "?" + queryString, {
                    method: "GET",
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("product-list").innerHTML = data.trim() || "<p>No products found.</p>";
                        window.history.pushState({}, '', shopRoute + "?" + queryString);
                        attachAddToCartListeners();
                    })
                    .catch(error => console.error("Error fetching filtered products:", error));
            }

            searchBox.addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    applyFilters();
                }
            });

            filterForm.addEventListener("change", applyFilters);

            function attachAddToCartListeners() {
                document.querySelectorAll(".add-to-cart-modal").forEach(button => {
                    button.addEventListener("click", function () {
                        let productId = this.dataset.productId;
                        fetch(`/cart/add/${productId}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ quantity: 1 })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Product added to cart!");
                                    let modal = document.getElementById(`productModal${productId}`);
                                    let modalInstance = bootstrap.Modal.getInstance(modal);
                                    if (modalInstance) modalInstance.hide();
                                } else {
                                    alert(data.message);
                                }
                            });
                    });
                });
            }

            attachAddToCartListeners();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const token = document.querySelector('meta[name="csrf-token"]').content;

            document.body.addEventListener('click', async function (e) {
                const btn = e.target.closest('.wishlist-btn');
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                const productId = btn.dataset.productId;
                const icon = btn.querySelector('i');

                try {
                    const res = await fetch(`/wishlist/toggle/${productId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (res.status === 401 || data.status === 'unauthenticated') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Login Required',
                            text: 'Please log in to add items to your wishlist 💚',
                            confirmButtonColor: '#71b127'
                        });
                        return;
                    }

                    if (data.status === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: 'Item added to wishlist ❤️',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else if (data.status === 'removed') {
                        icon.classList.remove('fas');
                        icon.classList.add('far');

                        // Remove card only if on wishlist page
                        if (window.location.pathname.includes('/wishlist')) {
                            const productCard = btn.closest('.product-card');
                            if (productCard) productCard.remove();
                        }

                        Swal.fire({
                            icon: 'info',
                            title: 'Removed',
                            text: 'Item removed from wishlist 💔',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }

                } catch (err) {
                    console.error('Wishlist toggle error:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong while updating your wishlist.',
                    });
                }
            });
        });
    </script>

</x-app-layout>