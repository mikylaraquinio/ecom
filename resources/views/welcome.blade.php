<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <div class="container">
        <form action="{{ url('/search') }}" method="GET" class="d-flex justify-content-center align-items-center flex-wrap gap-2">
            <input
                type="text"
                name="query"
                class="form-control"
                placeholder="🔍 Search fresh produce, categories, or farmers..."
                style="max-width: 500px; border: 2px solid #a2c96f; border-radius: 10px; padding: 10px;"
                required
            >
            <button type="submit" class="btn btn-success px-4" style="background-color: #71b127; border-radius: 10px;">
                Search
            </button>
        </form>
    </div>

    <!-- Quick Access Cards (Square & Consistent) -->
<div class="container mt-4">
    <div class="d-flex overflow-auto gap-3 pb-2" style="scroll-snap-type: x mandatory;">
        <!-- FarmLive -->
        <div style="width: 100px; height: 100px; scroll-snap-align: start;">
            <div class="d-flex flex-column align-items-center justify-content-center h-100 border rounded shadow-sm bg-white text-center p-2">
                <img src="{{ asset('assets/FLive.png') }}" alt="FarmLive" style="width: 36px; height: 36px;">
                <div class="mt-2 small fw-semibold">FarmLive</div>
            </div>
        </div>

        <!-- FarMart -->
        <div style="width: 100px; height: 100px; scroll-snap-align: start;">
            <div class="d-flex flex-column align-items-center justify-content-center h-100 border rounded shadow-sm bg-white text-center p-2">
                <img src="{{ asset('assets/FLive.png') }}" alt="FarMart" style="width: 36px; height: 36px;">
                <div class="mt-2 small fw-semibold">FarMart</div>
            </div>
        </div>

        <!-- FarmCommunity -->
        <div style="width: 100px; height: 100px; scroll-snap-align: start;">
            <div class="d-flex flex-column align-items-center justify-content-center h-100 border rounded shadow-sm bg-white text-center p-2">
                <img src="{{ asset('assets/FLive.png') }}" alt="FarmLive" style="width: 36px; height: 36px;">
                <div class="mt-2 small fw-semibold">Farm Community</div>
            </div>
        </div>

        <!-- FarmPrograms -->
        <div style="width: 100px; height: 100px; scroll-snap-align: start;">
            <div class="d-flex flex-column align-items-center justify-content-center h-100 border rounded shadow-sm bg-white text-center p-2">
                <img src="{{ asset('assets/FLive.png') }}" alt="FarMart" style="width: 36px; height: 36px;">
                <div class="mt-2 small fw-semibold">Farm Programs</div>
            </div>
        </div>
    </div>
</div>




    
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
                            <a href="{{ url('/shop?category=204') }}" class="btn btn-light view-btn">View All</a>
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
                            <a href="{{ url('/shop?category=205') }}" class="btn btn-light view-btn">View All</a>
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
                            <a href="{{ url('/shop?category=206') }}" class="btn btn-light view-btn">View All</a>
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
                            <a href="{{ url('/shop?category=207') }}" class="btn btn-light view-btn">View All</a>
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