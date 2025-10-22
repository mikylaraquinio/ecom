<div class="container px-4 px-lg-5 mt-5">
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
    @forelse($products as $product)
      @php
        $imageUrl = $product->image ? asset('storage/' . $product->image) : asset('assets/products.jpg');
      @endphp

      <div class="col d-flex position-relative" style="overflow: visible;">
        <!-- Top-right Action Icons -->
        <div class="position-absolute top-0 end-0 p-2 z-3" style="z-index: 10;">
          @auth
            <button class="btn btn-sm btn-outline-danger border-0 p-1 wishlist-btn" data-product-id="{{ $product->id }}"
              title="Toggle Wishlist">
              <i class="{{ auth()->user()->wishlist->contains($product->id) ? 'fas' : 'far' }} fa-heart"></i>
            </button>
          @endauth

          <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-secondary border-0 p-1"
            title="View Product">
            <i class="fas fa-eye"></i>
          </a>
        </div>

        <!-- Clickable Product Card -->
        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none w-100">
          <div class="shadow-sm rounded bg-white product-box p-0 overflow-hidden d-flex flex-column"
            style="cursor:pointer; transition:.3s; border-radius:12px; position:relative; z-index:1;">

            <!-- Product Image -->
            <div class="position-relative overflow-hidden" style="height: 180px;">
              <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
              <span class="badge bg-success position-absolute top-0 start-0 m-2 shadow-sm">New</span>
            </div>

            <!-- Product Content -->
            <div class="p-2 px-3 d-flex flex-column flex-grow-1">
              <h6 class="fw-semibold text-truncate mb-1" style="font-size:14px; color:#222;">
                {{ $product->name }}
              </h6>

              <div class="text-danger fw-bold mb-1" style="font-size:15px;">
                ₱{{ number_format($product->price, 2) }}
              </div>

              <div class="d-flex justify-content-between align-items-center text-muted mb-2" style="font-size:12px;">
                <span>
                  <i class="fas fa-star text-warning"></i>
                  {{ $product->avg_rating ? number_format($product->avg_rating, 1) : '—' }}
                  {{-- optional: show count --}}
                  {{-- <span class="ms-1">({{ $product->ratings_count }})</span> --}}
                </span>
                <span>Sold {{ number_format((int) ($product->total_sold ?? 0)) }}</span>
              </div>

              <!-- Quick Add to Cart -->

            </div>
          </div>
        </a>
      </div>
    @empty
      <p class="text-center text-muted">
        No products found. <a href="{{ route('shop') }}">Continue Shopping</a>
      </p>
    @endforelse
  </div>
</div>

<style>
  .product-box {
    border: 1px solid #e1e1e1;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    transition: all .3s ease-in-out;
    min-height: 100%;
  }

  .product-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
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
</style>