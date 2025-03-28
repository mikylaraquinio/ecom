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
                        <p>Discover an extensive collection of agricultural products, from bestsellers to unique finds,
                            curated to enhance your farming experience and inspire growth.</p>
                    </div>
                    <div class="col-md-6 d-flex justify-content-center">
                        <img src="{{ asset('assets/farmer1.jpg') }}" class="img-fluid small-img">
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Featured Categories Section -->
    <section id="featured-categories" class="py-5">
        <div class="container text-center">
            <h1 class="p-title mb-4">FEATURED CATEGORIES</h1>
            <div class="row">
                <!-- Grains & Cereals -->
                <div class="col-md-3">
                    <div class="category-card">
                        <img src="{{ asset('assets/Cereal&Grains.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Grains & Cereals</h5>
                            <p class="category-text">Quality grains and cereals for consumption and farming.</p>
                            <a href="{{ url('/shop?category=1') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Vegetables -->
                <div class="col-md-3">
                    <div class="category-card">
                        <img src="{{ asset('assets/vegetables.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Vegetables</h5>
                            <p class="category-text">Fresh and organic vegetables for healthy meals.</p>
                            <a href="{{ url('/shop?category=2') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Fruits -->
                <div class="col-md-3">
                    <div class="category-card">
                        <img src="{{ asset('assets/Fruits.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Fruits</h5>
                            <p class="category-text">Sweet and nutritious fruits for all seasons.</p>
                            <a href="{{ url('/shop?category=3') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Herbs & Spices -->
                <div class="col-md-3">
                    <div class="category-card">
                        <img src="{{ asset('assets/herbs&Spices.jpg') }}" class="img-fluid category-img">
                        <div class="overlay">
                            <h5 class="category-title">Herbs & Spices</h5>
                            <p class="category-text">Aromatic herbs and spices for culinary and medicinal use.</p>
                            <a href="{{ url('/shop?category=4') }}" class="btn btn-light view-btn">View All</a>
                        </div>
                    </div>
                </div>
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

    
</x-app-layout>