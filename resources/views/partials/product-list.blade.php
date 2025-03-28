<div class="container px-4 px-lg-5 mt-5">
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
        @forelse($products as $product)
            @php
                $imageUrl = $product->image 
                    ? asset('storage/' . $product->image) 
                    : asset('assets/products.jpg'); // Default fallback image
            @endphp

            <div class="col d-flex">
                <a href="#" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}" class="text-decoration-none w-100">

                    <!-- Product Card -->
                    <div class="shadow-sm rounded bg-white p-3 text-center product-box d-flex flex-column justify-content-between"
                        style="cursor: pointer; width: 100%; min-height: 280px; transition: 0.3s; border-radius: 12px;">

                        <!-- Product Image -->
                        <div class="position-relative">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                class="w-100 rounded-top" style="height: 150px; object-fit: cover;">

                            <!-- Wishlist & Quick View Icons -->
                            <div class="position-absolute top-0 end-0 p-2">
                                <button class="btn btn-sm btn-outline-danger border-0 p-1"><i class="fas fa-heart"></i></button>
                                <button class="btn btn-sm btn-outline-secondary border-0 p-1"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <h6 class="fw-bolder text-truncate mt-2" style="color: #222; font-size: 14px;">
                            {{ $product->name }}
                        </h6>
                        <p class="mb-0 fw-bold" style="color: #2d6a4f; font-size: 14px;">
                            ₱{{ number_format($product->price, 2) }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-2">
                            <button class="btn btn-sm btn-outline-success w-100 add-to-cart" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
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
                                    <p class="fw-bold" style="color: #2d6a4f;">Price: ₱{{ number_format($product->price, 2) }}</p>
                                    <p style="color: #666;">
                                        Category: {{ $product->category->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success add-to-cart-modal" data-product-id="{{ $product->id }}">
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
        border: 2px solid #A7D7A8;
        min-height: 280px;
        transition: 0.3s ease-in-out;
    }

    .product-box:hover {
        background-color: #D4EDDA;
        border-color: #2D6A4F;
        transform: scale(1.05);
        box-shadow: 0px 4px 10px rgba(45, 106, 79, 0.4);
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
