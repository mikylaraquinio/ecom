<div class="container px-4 px-lg-5 mt-5">
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4 justify-content-center">
        @foreach($products as $product)
            @php
                $imageUrl = $product->image
                    ? asset('storage/' . $product->image)
                    : asset('assets/products.jpg'); // Default fallback image
            @endphp

            <div class="col d-flex justify-content-center">
                <a href="#" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}"
                    class="text-decoration-none">
                    
                    <!-- Single Product Box -->
                    <div class="p-2 shadow-sm rounded bg-white text-center product-box"
                        style="cursor: pointer; width: 200px; min-height: 250px; display: flex; flex-direction: column; justify-content: space-between; transition: 0.3s;">

                        <!-- Product Image -->
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                            style="object-fit: cover; height: 60%; width: 100%; border-radius: 5px;">

                        <!-- Product Details -->
                        <h6 class="fw-bolder text-truncate mt-2" style="color: #222; font-size: 14px;">
                            {{ $product->name }}
                        </h6>
                        <p class="mb-0 fw-bold" style="color: #2d6a4f; font-size: 13px;">
                            ${{ number_format($product->price, 2) }}
                        </p>
                    </div>
                </a>
            </div>

            <!-- Product Modal -->
            <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1"
                aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg"> <!-- Increased modal size for better spacing -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="productModalLabel{{ $product->id }}" style="color: #222;">
                                {{ $product->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- Image on Left -->
                                <div class="col-md-5">
                                    <img class="img-fluid rounded w-100" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                </div>
                                <!-- Texts on Right -->
                                <div class="col-md-7">
                                    <h6 class="fw-bold" style="color: #222;">{{ $product->name }}</h6>
                                    <p style="color: #444;">{{ $product->description }}</p>
                                    <p class="fw-bold" style="color: #2d6a4f;">Price: ${{ number_format($product->price, 2) }}</p>
                                    <p style="color: #666;">
                                        Category: {{ $product->category->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <!-- Add to Cart Button -->
                            <button type="button" class="btn btn-primary add-to-cart-modal"
                                data-product-id="{{ $product->id }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .product-box {
        transition: 0.3s ease-in-out;
        border: 2px solid #A7D7A8;
        min-height: 250px; /* Ensuring uniform product box height */
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

    /* Optional: Active Selection */
    .product-box.active {
        background-color: #A7D7A8;
        border-color: #1B5E20;
    }

    .modal-lg {
        max-width: 800px;
    }
</style>
