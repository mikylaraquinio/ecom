<x-app-layout>

    <!-- Category Filter Dropdown -->
    <section class="py-5 bg-light">
        <div class="container px-4 px-lg-5 mt-5">
            <h3 class="text-center mb-4">Filter by Category</h3>
            <div class="row justify-content-center">
                <form method="GET" action="{{ route('shop') }}" class="col-md-6">
                    <div class="input-group">
                        <select name="category" class="form-select" id="categorySelect">
                            <option value="">Select a Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('categorySelect').addEventListener('change', function () {
            this.form.submit(); // Auto-submit form when category is selected
        });
    </script>

    <!-- Products Display -->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                @foreach($products as $product)
                    @php
                        $imageUrl = $product->image 
                            ? asset('storage/' . $product->image) 
                            : asset('assets/products.jpg'); // Default fallback image
                    @endphp

                    <div class="col mb-5">
                        <div class="product-box border p-3 shadow-sm rounded bg-white">
                            <div class="card h-100">
                                <!-- Product Image -->
                                <img class="card-img-top" src="{{ $imageUrl }}" alt="{{ $product->name }}">

                                <!-- Product Details -->
                                <div class="card-body p-4 text-center">
                                    <h5 class="fw-bolder">{{ $product->name }}</h5>
                                    <p>${{ number_format($product->price, 2) }}</p>
                                    <p>Category: {{ $product->category->name }}</p>
                                </div>

                                <!-- View Details Button -->
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                    <button type="button" class="btn btn-outline-dark mt-auto" data-bs-toggle="modal"
                                        data-bs-target="#productModal{{ $product->id }}">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                                <!-- Product Modal -->
                                <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1"
                                    aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="productModalLabel{{ $product->id }}">Product Details -
                                                    {{ $product->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Image on Left -->
                                                    <div class="col-md-4">
                                                        <img class="img-fluid rounded" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                                    </div>
                                                    <!-- Texts on Right -->
                                                    <div class="col-md-8">
                                                        <h6>Product Name: {{ $product->name }}</h6>
                                                        <p>Description: {{ $product->description }}</p>
                                                        <p>Price: ${{ number_format($product->price, 2) }}</p>
                                                        <p>Category: {{ $product->category->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-end gap-2">
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn1 btn btn-primary">Add to Cart</button>
                                            </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                @endforeach
            </div>
        </div>
    </section>

</x-app-layout>
