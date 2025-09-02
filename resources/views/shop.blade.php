<x-app-layout>
    <section class="header-banner" style="position: relative; text-align: center;">
        <div style="position: relative; width: 100%; height: 300px; overflow: hidden;">
            <img src="{{ asset('assets/shop-bg2.jpg') }}" alt="Shop Banner"
                style="width: 100%; height: 100%; object-fit: cover; filter: brightness(50%);">

            <div
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4);">
            </div>
        </div>

        <div class="banner-text"
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">

            <p style="font-family: 'Pacifico', cursive; color: #ffcc00; font-size: 1.5rem; margin-bottom: 10px;">
                "Supporting farmers by providing a fair marketplace for their products"
            </p>

            <h1
                style="font-family: 'Fredoka One', sans-serif; color: white; font-size: 3rem; font-weight: bold; text-shadow: 2px 2px 8px rgba(0,0,0,0.6);">
                SHOP PRODUCTS
            </h1>

            <div style="width: 120px; height: 5px; background-color: #ffcc00; margin: 10px auto; border-radius: 10px;">
            </div>
        </div>
    </section>

    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Fredoka+One&display=swap" rel="stylesheet">

    <!-- Search Bar + Filters -->
    <section class="py-4 bg-light">
        <div class="container px-4 px-lg-5">
            <div class="row align-items-center">
                <!-- Search Input -->
                <div class="col-md-6">
                    <form id="searchForm" method="GET" action="{{ route('shop') }}" class="d-flex">
                        <input type="text" name="search" id="searchBox" class="form-control"
                            placeholder="Search products..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>
                </div>

                <!-- Filters Dropdown -->
                <div class="col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button"
                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filters
                        </button>
                        <div class="dropdown-menu p-3 w-100">
                            <form id="filterForm" method="GET" action="{{ route('shop') }}">
                                <!-- Category Filter -->
                                <div class="mb-2">
                                    <label for="categorySelect" class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Price Range -->
                                <div class="mb-2">
                                    <label class="form-label">Price Range</label>
                                    <div class="d-flex">
                                        <input type="number" name="min_price" class="form-control me-2"
                                            placeholder="Min" value="{{ request('min_price') }}">
                                        <input type="number" name="max_price" class="form-control" placeholder="Max"
                                            value="{{ request('max_price') }}">
                                    </div>
                                </div>

                                <!-- Stock Availability -->
                                <div class="mb-2">
                                    <label class="form-label">Stock Availability</label>
                                    <select name="stock" class="form-select">
                                        <option value="">All</option>
                                        <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In
                                            Stock</option>
                                        <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                    </select>
                                </div>

                                <!-- Sort By -->
                                <div class="mb-2">
                                    <label class="form-label">Sort By</label>
                                    <select name="sort_by" class="form-select">
                                        <option value="">Default</option>
                                        <option value="low_to_high" {{ request('sort_by') == 'low_to_high' ? 'selected' : '' }}>Price: Low to High</option>
                                        <option value="high_to_low" {{ request('sort_by') == 'high_to_low' ? 'selected' : '' }}>Price: High to Low</option>
                                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>
                                            Newest First</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-2">Apply Filters</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Display -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div id="product-list">
                @include('partials.product-list') <!-- Products loaded via AJAX -->
            </div>
        </div>
    </section>

    <!-- Keep all existing scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchBox = document.getElementById("searchBox");
            const filterForm = document.getElementById("filterForm");
            const loadingScreen = document.getElementById("loading-screen");
            const shopRoute = "{{ route('shop') }}";

            function showLoading() {
                if (loadingScreen) loadingScreen.classList.remove("d-none");
            }

            function hideLoading() {
                if (loadingScreen) loadingScreen.classList.add("d-none");
            }

            function applyFilters() {
                let formData = new FormData(filterForm);
                let queryString = new URLSearchParams(formData).toString();
                let searchQuery = searchBox.value.trim();

                if (searchQuery) queryString += `&search=${encodeURIComponent(searchQuery)}`;

                showLoading();
                fetch(shopRoute + "?" + queryString, {
                    method: "GET",
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("product-list").innerHTML = data.trim() === ""
                            ? "<p>No products found.</p>"
                            : data;
                        window.history.pushState({}, '', shopRoute + "?" + queryString);

                        attachAddToCartListeners();
                    })
                    .catch(error => console.error("Error fetching filtered products:", error))
                    .finally(() => hideLoading());
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
                            })
                            .catch(error => console.error("Error:", error));
                    });
                });
            }

            function attachWishlistListeners() {
                document.querySelectorAll(".wishlist-btn").forEach(button => {
                    button.addEventListener("click", function (event) {
                        event.stopPropagation(); // Prevent triggering modal if inside a card

                        const productId = this.dataset.productId;
                        const icon = this.querySelector("i");
                        const isWishlisted = icon.classList.contains("fas");

                        const message = isWishlisted
                            ? "Remove this product from your wishlist?"
                            : "Add this product to your wishlist?";

                        Swal.fire({
                            title: message,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#d33',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/wishlist/toggle/${productId}`, {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                        "Accept": "application/json"
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === "added") {
                                            icon.classList.remove("far");
                                            icon.classList.add("fas");
                                            Swal.fire({
                                                title: "Added!",
                                                text: "Product added to wishlist.",
                                                icon: "success",
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            icon.classList.remove("fas");
                                            icon.classList.add("far");
                                            Swal.fire({
                                                title: "Removed!",
                                                text: "Product removed from wishlist.",
                                                icon: "info",
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        }
                                    });
                            }
                        });
                    });
                });
            }

            attachWishlistListeners();
            attachAddToCartListeners();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</x-app-layout>