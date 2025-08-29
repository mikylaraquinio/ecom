<div class="container px-4 px-lg-5 mt-5">
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
        @forelse($products as $product)
            @php
                $imageUrl = $product->image
                    ? asset('storage/' . $product->image)
                    : asset('assets/products.jpg'); // Default fallback image
            @endphp

            <div class="col d-flex position-relative" style="overflow: visible;">
                <!-- Top-right Action Icons -->
                <div class="position-absolute top-0 end-0 p-2 z-3" style="z-index: 10;">
                    @auth
                        <button class="btn btn-sm btn-outline-danger border-0 p-1 wishlist-btn"
                            data-product-id="{{ $product->id }}">
                            <i class="{{ auth()->user()->wishlist->contains($product->id) ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    @endauth

                    <button class="btn btn-sm btn-outline-secondary border-0 p-1" data-bs-toggle="modal"
                        data-bs-target="#productModal{{ $product->id }}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <!-- Clickable Product Card -->
                <a href="#" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}" class="text-decoration-none w-100">
                    <div class="shadow-sm rounded bg-white product-box p-0 overflow-hidden d-flex flex-column" style="cursor: pointer; transition: 0.3s; border-radius: 12px; position: relative; z-index: 1;">

                        <!-- Product Image -->
                        <div class="position-relative overflow-hidden" style="height: 180px;">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                            <span class="badge bg-success position-absolute top-0 start-0 m-2 shadow-sm">New</span>
                        </div>

                        <!-- Product Content -->
                        <div class="p-2 px-3 d-flex flex-column flex-grow-1">
                            <h6 class="fw-semibold text-truncate mb-1" style="font-size: 14px; color: #222;">
                                {{ $product->name }}
                            </h6>

                            <div class="text-danger fw-bold mb-1" style="font-size: 15px;">
                                ₱{{ number_format($product->price, 2) }}
                            </div>

                            <div class="d-flex justify-content-between align-items-center text-muted mb-2" style="font-size: 12px;">
                                <span><i class="fas fa-star text-warning"></i> 4.8</span>
                                <span>Sold 234</span>
                            </div>

                            <!-- Add to Cart -->
                            <button class="btn btn-sm btn-outline-success w-100 mt-auto add-to-cart"
                                data-product-id="{{ $product->id }}">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Product Modal -->
            <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1"
                aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="productModalLabel{{ $product->id }}" style="color: #222;">
                                {{ $product->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <img class="img-fluid rounded w-100" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                </div>
                                <div class="col-md-7">
                                    <h6 class="fw-bold" style="color: #222;">{{ $product->name }}</h6>
                                    <p style="color: #444;">{{ $product->description ?? 'No description available.' }}</p>
                                    <p class="fw-bold" style="color: #2d6a4f;">Price:
                                        ₱{{ number_format($product->price, 2) }}</p>
                                    <p class="mb-1">
                                        <strong>Unit:</strong> {{ ucfirst($product->unit ?? 'N/A') }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Min Order:</strong> {{ $product->min_order_qty ?? 1 }} {{ $product->unit ?? 'unit(s)' }}
                                    </p>
                                    <p style="color: #666;">
                                        Category: {{ $product->category->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success add-to-cart-modal"
                                data-product-id="{{ $product->id }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No products found. <a href="{{ route('shop') }}">Continue Shopping</a></p>
        @endforelse
    </div>
</div>

<style>
    .product-box {
        border: 1px solid #e1e1e1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease-in-out;
        min-height: 100%;
    }

    .product-box:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #aaa;
    }

    .product-box img {
        object-fit: cover;
    }

    .btn-outline-success {
        font-size: 13px;
        padding: 6px 12px;
    }

    .wishlist-btn:hover i,
    .btn-outline-secondary:hover i {
        color: #e3342f;
    }

    .badge.bg-success {
        background-color: #28a745 !important;
        font-size: 10px;
        padding: 4px 6px;
        border-radius: 4px;
    }

    .product-box h6 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .modal-lg {
        max-width: 800px;
    }
</style>