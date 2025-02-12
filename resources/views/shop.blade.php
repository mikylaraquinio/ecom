<x-app-layout>

    <!-- Category Filter Dropdown -->
    <section class="py-5 bg-light">
        <div class="container px-4 px-lg-5 mt-5">
            <h3 class="text-center mb-4">Filter by Category</h3>
            <div class="row justify-content-center">
                <form method="GET" action="{{ route('shop') }}" class="col-md-6">
                    <div class="input-group">
                        <select name="category" class="form-select" aria-label="Product Category">
                            <option value="">Select a Category</option>
                            <option value="Fresh Produce" {{ request('category') == 'Fresh Produce' ? 'selected' : '' }}>Fresh Produce</option>
                            <option value="Dairy Products" {{ request('category') == 'Dairy Products' ? 'selected' : '' }}>Dairy Products</option>
                            <option value="Grains and Pulses" {{ request('category') == 'Grains and Pulses' ? 'selected' : '' }}>Grains and Pulses</option>
                            <!-- Add other categories here -->
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Products Display -->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                @if($products->isEmpty())
                    <div class="col-12 text-center">
                        <h4>No products available</h4>
                    </div>
                @else
                    @foreach($products as $product)
                        <div class="col mb-5">
                            <div class="card h-100">
                                <!-- Product image-->
                                <img class="card-img-top" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" />
                                <!-- Product details-->
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <!-- Product name-->
                                        <h5 class="fw-bolder">{{ $product->name }}</h5>
                                        <!-- Product price-->
                                        ${{ number_format($product->price, 2) }}
                                    </div>
                                </div>
                                <!-- Product actions-->
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View Details</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
