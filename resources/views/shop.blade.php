<x-app-layout>
    <!-- Search Bar -->
    <section class="py-4 bg-light">
        <div class="container px-4 px-lg-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('shop') }}" method="GET">
                        <label for="searchBox" class="form-label">Search</label>
                        <input type="text" name="search" id="searchBox" class="form-control filter-option"
                            placeholder="Search products..." value="{{ request('search') }}">
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters + Products Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <!-- Sidebar Filters (Left Side) -->
                <div class="col-md-3">
                    <h5 class="mb-3">Filter Products</h5>
                    <form id="filterForm" method="GET" action="{{ route('shop') }}">
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="categorySelect" class="form-label">Category</label>
                            <select name="category" class="form-select filter-option" id="categorySelect">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="minPrice" class="form-label">Min Price</label>
                                    <input type="number" name="min_price" id="minPrice"
                                        class="form-control filter-option" placeholder="Min Price"
                                        value="{{ request('min_price') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="maxPrice" class="form-label">Max Price</label>
                                    <input type="number" name="max_price" id="maxPrice"
                                        class="form-control filter-option" placeholder="Max Price"
                                        value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Stock Availability -->
                        <div class="mb-3">
                            <label class="form-label">Stock Availability</label>
                            <select name="stock" class="form-select filter-option">
                                <option value="">All</option>
                                <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock
                                </option>
                                <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>
                                    Out of Stock</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort_by" class="form-select filter-option">
                                <option value="">Default</option>
                                <option value="low_to_high" {{ request('sort_by') == 'low_to_high' ? 'selected' : '' }}>
                                    Price: Low to High</option>
                                <option value="high_to_low" {{ request('sort_by') == 'high_to_low' ? 'selected' : '' }}>
                                    Price: High to Low</option>
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First
                                </option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Products Display (Right Side) -->
                <div class="col-md-9">
                    <div id="product-list">
                        @include('shop.partials.product_list') <!-- Products loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function applyFilters() {
                let form = document.getElementById('filterForm');
                let formData = new FormData(form);
                let queryString = new URLSearchParams(formData).toString();
                let shopRoute = "{{ route('shop') }}";

                // ✅ Allow search to work even when other filters are empty
                let searchQuery = document.getElementById("searchBox").value.trim();
                if (searchQuery) {
                    queryString += `&search=${encodeURIComponent(searchQuery)}`;
                }

                // 🛑 Prevent sending a request only if NO filters & NO search input
                if (!queryString.replace(/=&/g, '&').replace(/=$/, '')) {
                    console.log("No filters or search query applied, skipping fetch.");
                    return;
                }

                fetch(shopRoute + "?" + queryString, {
                    method: "GET",
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('product-list').innerHTML = data.trim() === "" ? "<p>No products found.</p>" : data;
                        window.history.pushState({}, '', shopRoute + "?" + queryString);
                    })
                    .catch(error => console.error("Error fetching filtered products:", error));
            }

            // 🔄 Trigger applyFilters when search is used
            document.getElementById("searchBox").addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    applyFilters();
                }
            });

            // ✅ AUTOCOMPLETE SEARCH FUNCTIONALITY  
            let searchBox = document.getElementById("searchBox");
            let suggestionBox = document.createElement("div");
            let selectedIndex = -1;
            let suggestions = [];

            suggestionBox.setAttribute("id", "searchSuggestions");
            Object.assign(suggestionBox.style, {
                position: "absolute",
                left: "0",
                background: "#fff",
                border: "1px solid #ddd",
                maxHeight: "200px",
                overflowY: "auto",
                display: "none",
                zIndex: "1000",
                width: searchBox.offsetWidth + "px"
            });
            searchBox.parentNode.style.position = "relative";
            searchBox.parentNode.appendChild(suggestionBox);

            searchBox.addEventListener("input", function () {
                let query = this.value.trim().toLowerCase();
                if (query.length < 2) {
                    suggestionBox.style.display = "none";
                    return;
                }

                fetch(`/autocomplete?search=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions = extractMatchingWords(data, query);
                        selectedIndex = -1;
                        suggestionBox.innerHTML = suggestions.length > 0
                            ? suggestions.map((word, index) =>
                                `<div class="suggestion-item" data-word="${word}" data-index="${index}" 
                        style="padding: 5px; cursor: pointer;">${word}</div>`
                            ).join("")
                            : "";
                        suggestionBox.style.display = suggestions.length > 0 ? "block" : "none";
                    })
                    .catch(error => console.error("Error fetching autocomplete suggestions:", error));
            });

            suggestionBox.addEventListener("click", function (event) {
                if (event.target.classList.contains("suggestion-item")) {
                    searchBox.value = event.target.getAttribute("data-word");
                    suggestionBox.style.display = "none";
                    searchBox.focus();
                    applyFilters(); // Apply filters when an autocomplete option is selected
                }
            });

            searchBox.addEventListener("keydown", function (event) {
                let items = document.querySelectorAll(".suggestion-item");
                if (suggestions.length === 0) return;

                if (event.key === "ArrowDown") {
                    event.preventDefault();
                    selectedIndex = (selectedIndex + 1) % suggestions.length;
                    updateHighlightedSuggestion();
                } else if (event.key === "ArrowUp") {
                    event.preventDefault();
                    selectedIndex = selectedIndex === 0 ? -1 : (selectedIndex - 1 + suggestions.length) % suggestions.length;
                    updateHighlightedSuggestion();
                } else if (event.key === "Enter") {
                    event.preventDefault();
                    if (selectedIndex !== -1) {
                        searchBox.value = suggestions[selectedIndex];
                        suggestionBox.style.display = "none";
                    }
                    applyFilters(); // Apply filters when pressing Enter
                } else if (event.key === "Escape") {
                    suggestionBox.style.display = "none";
                    searchBox.focus();
                }
            });

            function updateHighlightedSuggestion() {
                let items = document.querySelectorAll(".suggestion-item");
                items.forEach((item, index) => {
                    item.style.background = index === selectedIndex ? "#007bff" : "#fff";
                    item.style.color = index === selectedIndex ? "#fff" : "#000";
                });
            }

            document.addEventListener("click", function (event) {
                if (!searchBox.contains(event.target) && !suggestionBox.contains(event.target)) {
                    suggestionBox.style.display = "none";
                }
            });

            function extractMatchingWords(products, query) {
                let words = new Set();
                let lowerQuery = query.toLowerCase();

                products.forEach(product => {
                    let productWords = product.name.toLowerCase().split(" ");
                    productWords.forEach(word => {
                        if (word.startsWith(lowerQuery)) {
                            words.add(word);
                        }
                    });
                });

                return Array.from(words);
            }

            searchBox.addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    if (selectedIndex !== -1 && suggestions[selectedIndex]) {
                        searchBox.value = suggestions[selectedIndex];
                        suggestionBox.style.display = "none";
                    }
                    let query = this.value.trim();
                    if (query) {
                        applyFilters();
                    }
                }
            });

            document.querySelectorAll('.filter-option').forEach(item => {
                item.addEventListener('input', applyFilters);
            });

            // ✅ ADD TO CART FUNCTIONALITY
            document.addEventListener("click", function (event) {
                if (event.target.classList.contains("add-to-cart-modal")) {
                    let productId = event.target.getAttribute("data-product-id");
                    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

                    let button = event.target;
                    button.disabled = true; // Disable button while processing
                    button.innerText = "Adding...";

                    fetch(`/cart/add/${productId}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({ quantity: 1 })
                    })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.success ? "Product added to cart!" : "Failed to add product.");
                            if (data.success) {
                                let modal = bootstrap.Modal.getInstance(document.getElementById(`productModal${productId}`));
                                modal.hide();
                            }
                        })
                        .catch(error => console.error("Error adding product to cart:", error))
                        .finally(() => {
                            button.disabled = false; // Re-enable button
                            button.innerText = "Add to Cart";
                        });
                }
            });
        });
    </script>
</x-app-layout>