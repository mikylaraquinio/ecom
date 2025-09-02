<x-app-layout>
    <div class="container my-4">

        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                @if(optional($product->category)->parent)
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop') }}?category={{ $product->category->parent->id }}">
                            {{ $product->category->parent->name }}
                        </a>
                    </li>
                @endif
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop') }}?category={{ $product->category->id }}">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4 align-items-start">
            {{-- LEFT: Gallery --}}
            <div class="col-12 col-lg-5">
                <div class="product-gallery d-flex flex-column align-items-center">
                    <div class="main-image border rounded p-2 bg-white w-100">
                        <img id="mainImg" src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid">
                    </div>

                    <div class="thumbs d-flex gap-2 mt-3 flex-wrap w-100">
                        @foreach($gallery as $img)
                            <button type="button" class="thumb btn p-0 border-0">
                                <img src="{{ $img }}" class="thumb-img rounded" alt="thumb">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Info --}}
            <div class="col-12 col-lg-7">
                <h2 class="fw-semibold h4 mb-2">{{ $product->name }}</h2>

                <div class="d-flex align-items-center gap-3 small text-muted mb-3">
                    <div class="text-warning">
                    <i class="fa fa-star"></i>
                    <span>{{ $avgRating ?? '—' }}</span>
                </div>
                <a href="#ratings" class="text-decoration-none">{{ number_format($storeStats['ratings_count']) }} Ratings</a>
                    <span class="text-muted">|</span>
                    <span>Sold {{ $product->total_sold ?? '—' }}</span>
                </div>

                <div class="price-box rounded p-3 mb-3">
                    <div class="h1 m-0 fw-bold text-danger">
                        ₱{{ number_format($product->price, 2) }}
                    </div>
                </div>

                <dl class="row small mb-3">
                    <dt class="col-4 col-md-3 text-muted">Shipping</dt>
                    <dd class="col-8 col-md-9">Pangasinan only · Calculated at checkout</dd>

                    <dt class="col-4 col-md-3 text-muted">Unit</dt>
                    <dd class="col-8 col-md-9">{{ ucfirst($product->unit ?? 'N/A') }}</dd>

                    <dt class="col-4 col-md-3 text-muted">Min Order</dt>
                    <dd class="col-8 col-md-9">{{ $product->min_order_qty ?? 1 }} {{ $product->unit ?? 'unit(s)' }}</dd>

                    <dt class="col-4 col-md-3 text-muted">Stock</dt>
                    <dd class="col-8 col-md-9">{{ $product->stock }}</dd>
                </dl>

                {{-- Quantity --}}
                <div class="d-flex align-items-center mb-4">
                    <span class="me-3 text-muted">Quantity</span>
                    <div class="input-group" style="max-width: 180px;">
                        <button class="btn btn-outline-secondary" type="button" id="decQty">−</button>
                        <input
                            type="number"
                            class="form-control text-center"
                            id="qtyInput"
                            min="{{ (int)($product->min_order_qty ?? 1) }}"
                            max="{{ (int)($product->stock ?? 9999) }}"
                            value="{{ (int)($product->min_order_qty ?? 1) }}"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="incQty">+</button>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-wrap gap-2">
                    <form id="addToCartForm" method="POST" action="{{ route('cart.add', $product->id) }}">
                        @csrf
                        <input type="hidden" name="quantity" id="qtyField" value="{{ (int)($product->min_order_qty ?? 1) }}">
                        <button type="submit" class="btn btn-outline-danger btn-lg px-4">
                            <i class="fas fa-cart-plus me-1"></i> Add To Cart
                        </button>
                    </form>

                    <form method="GET" action="{{ route('checkout.show') }}"
                        onsubmit="document.getElementById('buyNowQty').value = document.getElementById('qtyInput').value;">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" id="buyNowQty" name="quantity" value="{{ (int)($product->min_order_qty ?? 1) }}">
                        <button class="btn btn-danger btn-lg px-4">Buy Now</button>
                    </form>


                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                            @csrf
                            <button class="btn btn-outline-secondary btn-lg" type="submit">
                                <i class="{{ auth()->user()->wishlist->contains($product->id) ? 'fas' : 'far' }} fa-heart me-1"></i>
                                Favorite
                            </button>
                        </form>
                    @endauth
                </div>

                <div class="mt-4 small text-muted">
                    Category: {{ $product->category->name ?? 'Uncategorized' }}
                </div>
            </div>
        </div>

        {{-- Seller store card --}}
        <div class="mt-5 p-3 border rounded bg-white">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img
                        src="{{ $seller?->profile_picture ? asset('storage/'.$seller->profile_picture) : asset('assets/default.png') }}"
                        alt="{{ $seller?->username ?? 'Seller' }}"
                        class="rounded-circle"
                        style="width:64px;height:64px;object-fit:cover;"
                    >
                    <div>
                        <div class="fw-semibold">{{ $seller?->username ?? $seller?->name ?? 'Seller' }}</div>
                        <div class="small text-muted">
                            Joined {{ optional($storeStats['member_since'])->diffForHumans() ?? '—' }}
                        </div>
                        <div class="small text-muted">
                            {{ $storeStats['products_count'] }} products
                            @if($storeStats['followers_count'])
                                · {{ number_format($storeStats['followers_count']) }} followers
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('chat', ['receiverId' => $seller->id]) }}" class="btn btn-outline-success">
                        <i class="fa-regular fa-comments me-1"></i> Chat Now
                    </a>
                    {{-- View Shop filters by seller (see controller note) --}}
                    <a href="{{ route('shop') }}?seller={{ $seller->id }}" class="btn btn-outline-secondary">
                        <i class="fa-regular fa-store me-1"></i> View Shop
                    </a>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-4 mt-3 small">
                <div><span class="text-muted">Ratings:</span> {{ number_format($storeStats['ratings_count']) }}</div>
                @if($storeStats['response_rate']) <div><span class="text-muted">Response Rate:</span> {{ $storeStats['response_rate'] }}</div> @endif
                @if($storeStats['response_time']) <div><span class="text-muted">Response Time:</span> {{ $storeStats['response_time'] }}</div> @endif
            </div>
        </div>

        {{-- Product Specs --}}
        <div class="mt-4 p-3 border rounded bg-white">
            <h5 class="fw-semibold mb-3">Product Specifications</h5>
            <dl class="row mb-0 small">
                <dt class="col-4 col-md-3 text-muted">Category</dt>
                <dd class="col-8 col-md-9">
                    @if($product->category?->parent)
                        {{ $product->category->parent->name }} ›
                    @endif
                    {{ $product->category->name ?? 'Uncategorized' }}
                </dd>

                <dt class="col-4 col-md-3 text-muted">Unit</dt>
                <dd class="col-8 col-md-9">{{ ucfirst($product->unit ?? 'N/A') }}</dd>

                <dt class="col-4 col-md-3 text-muted">Min Order</dt>
                <dd class="col-8 col-md-9">{{ $product->min_order_qty ?? 1 }} {{ $product->unit ?? 'unit(s)' }}</dd>

                <dt class="col-4 col-md-3 text-muted">Ships From</dt>
                <dd class="col-8 col-md-9">
                    {{ $seller->barangay ?? '' }} {{ $seller->city ?? '' }} {{ $seller->province ?? '' }}
                </dd>
            </dl>
        </div>

        {{-- Description --}}
        <div class="mt-4 p-3 border rounded bg-white">
            <h5 class="fw-semibold mb-3">Product Description</h5>
            <div class="small">{!! nl2br(e($product->description)) !!}</div>
        </div>
    </div>

    <style>
        .product-gallery .main-image { max-height: 520px; display:flex; align-items:center; justify-content:center; }
        .product-gallery .main-image img { max-height: 500px; width:100%; object-fit:contain; }
        .thumbs .thumb-img { width:70px; height:70px; object-fit:cover; border:1px solid #e5e5e5; border-radius:8px; }
        .thumbs .thumb.active .thumb-img, .thumbs .thumb-img:hover { border-color:#d0011b; }
        .price-box { background:#fff5f6; border:1px solid #ffd9de; }
        @media (max-width: 991.98px) { .product-gallery .main-image { max-height: 380px; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qtyInput = document.getElementById('qtyInput');
            const qtyField = document.getElementById('qtyField');
            const min = parseInt(qtyInput.min || 1, 10);
            const max = parseInt(qtyInput.max || 9999, 10);
            function sync() { qtyField.value = qtyInput.value; }
            document.getElementById('decQty').addEventListener('click', () => { qtyInput.value = Math.max(min, +qtyInput.value - 1); sync(); });
            document.getElementById('incQty').addEventListener('click', () => { qtyInput.value = Math.min(max, +qtyInput.value + 1); sync(); });
            qtyInput.addEventListener('change', () => { let v = +qtyInput.value || min; qtyInput.value = Math.max(min, Math.min(max, v)); sync(); });

            // thumbs
            document.querySelectorAll('.thumbs .thumb').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('mainImg').src = btn.querySelector('img').src;
                    document.querySelectorAll('.thumbs .thumb').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                });
            });
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('addToCartForm');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault(); // stop full-page POST
    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: new FormData(form)
    })
    .then(r => r.json())
    .then(data => {
      // Use your own toast/SweetAlert here
      alert(data.message || 'Added to cart!');
    })
    .catch(() => alert('Something went wrong adding to cart.'));
  });
});
</script>

</x-app-layout>
