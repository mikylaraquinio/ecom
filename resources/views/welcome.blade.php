<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <section id="banner">
        <div class="banner">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="l-title">Harvest awaits at the Cooperative</h3>
                        <p>Discover an extensive collection of agricultural products, from bestsellers to unique finds, curated to enhance your farming experience and inspire growth.</p>
                    </div>
                    <div class="col-md-6 d-flex justify-content-center">
                        <img src="{{ asset('assets/farmer1.jpg') }}" class="img-fluid small-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="products">
        <div class="container text-center">
            <h1 class="p-title">TOP PRODUCTS</h1>
            <div class="row">
                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light mb-2 p-3">
                        <div class="card-body">
                            <img src="{{ asset('assets/farmer.jpg') }}" class="img-fluid">
                            <h5 class="mt-3">Rice</h5>
                            <p class="text-muted">5 Kilos</p>
                            <h6>$34.5</h6>
                            <button class="btn1 mt-2 w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light mb-2 p-3">
                        <div class="card-body">
                            <img src="{{ asset('assets/farmer.jpg') }}" class="img-fluid">
                            <h5 class="mt-3">Corn</h5>
                            <p class="text-muted">3 Kilos</p>
                            <h6>$20.0</h6>
                            <button class="btn1 mt-2 w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light mb-2 p-3">
                        <div class="card-body">
                            <img src="{{ asset('assets/farmer.jpg') }}" class="img-fluid">
                            <h5 class="mt-3">Wheat</h5>
                            <p class="text-muted">2 Kilos</p>
                            <h6>$15.0</h6>
                            <button class="btn1 mt-2 w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 text-center">
                    <div class="card border-0 bg-light mb-2 p-3">
                        <div class="card-body">
                            <img src="{{ asset('assets/tre.jpg') }}" class="img-fluid">
                            <h5 class="mt-3">Barley</h5>
                            <p class="text-muted">4 Kilos</p>
                            <h6>$25.5</h6>
                            <button class="btn1 mt-2 w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="start_now py-5" style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(/assets/products.jpg) no-repeat right center;">
        <div class="container text-white py-5">
            <div class="row py-5">
                <div class="col-lg-6">
                    <h1 class="font-weight-bold py-5">Discover new products, shop our bestsellers today!</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories Section -->
    <section id="featured-categories" class="py-5">
        <div class="container text-center">
            <h1 class="p-title mb-4">FEATURED CATEGORIES</h1>
            <div class="row">
                <!-- Crops -->
                <div class="col-md-4">
                    <div class="category-card">
                        <img src="{{ asset('assets/crops.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Crops</h5>
                            <p class="category-text">High-quality crops for farming and consumption.</p>
                            <button class="btn btn-light view-btn">View All</button>
                        </div>
                    </div>
                </div>

                <!-- Livestock & Poultry -->
                <div class="col-md-4">
                    <div class="category-card">
                        <img src="{{ asset('assets/livestock.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Livestock & Poultry</h5>
                            <p class="category-text">Healthy farm animals and poultry products.</p>
                            <button class="btn btn-light view-btn">View All</button>
                        </div>
                    </div>
                </div>

                <!-- Ornamental & Medicinal Plants -->
                <div class="col-md-4">
                    <div class="category-card">
                        <img src="{{ asset('assets/ornamental.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Ornamental & Medicinal Plants</h5>
                            <p class="category-text">A variety of plants for decoration and healing.</p>
                            <button class="btn btn-light view-btn">View All</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
