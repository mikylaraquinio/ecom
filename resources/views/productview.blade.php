<x-app-layout>
  <div class="container my-4">

    {{-- =================== BREADCRUMBS =================== --}}
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

    {{-- =================== MAIN CONTENT =================== --}}
    <div class="row g-4 align-items-start">

      {{-- ========== LEFT: GALLERY (Shopee-like) ========== --}}
      <div class="col-12 col-lg-5">
        <div class="product-gallery d-flex flex-column align-items-center">
          <div class="main-image border rounded p-2 bg-white w-100 position-relative shadow-xs">
            <img id="mainImg" src="{{ image_url($mainImage) }}" alt="{{ $product->name }}" class="img-fluid">

            {{-- Prev / Next controls --}}
            @if(count($gallery) > 1)
              <button type="button" class="img-nav img-nav-prev" aria-label="Previous image">
                <i class="fa-solid fa-chevron-left"></i>
              </button>
              <button type="button" class="img-nav img-nav-next" aria-label="Next image">
                <i class="fa-solid fa-chevron-right"></i>
              </button>
            @endif
          </div>

          {{-- Scrollable thumbs rail --}}
          <div class="thumbs w-100 mt-3">
            <div class="thumbs-rail d-flex gap-2 flex-nowrap overflow-auto pe-1">
              @foreach($gallery as $i => $img)
                <button type="button" class="thumb btn p-0 border-0 {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                  <img src="{{ image_url($img) }}" class="thumb-img rounded" alt="thumb {{ $i + 1 }}">
                </button>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- ========== RIGHT: INFO ========== --}}
      <div class="col-12 col-lg-7">
        <div class="pdp-card border rounded-3 bg-white shadow-xs">

          {{-- Title + Rating + Sold --}}
          <div class="d-flex flex-column gap-2">
            <h1 class="pdp-title h4 mb-0">{{ $product->name }}</h1>

            <div class="d-flex align-items-center flex-wrap gap-3 small text-muted">
              <div class="text-warning d-inline-flex align-items-center gap-1">
                <i class="fa fa-star"></i>
                <span>{{ $avgRating ?? '—' }}</span>
              </div>
              <a href="#ratings" class="text-decoration-none">{{ number_format($storeStats['ratings_count']) }}
                Ratings</a>
              <span class="text-muted">•</span>
              <span>Sold {{ number_format((int) ($product->total_sold ?? 0)) }}</span>
              <span class="text-muted">•</span>

              {{-- Visible stock badge --}}
              <span class="badge stock-badge {{ ($product->stock ?? 0) > 0 ? 'stock--in' : 'stock--out' }}">
                @if(($product->stock ?? 0) > 0)
                  <i class="fa-solid fa-check me-1"></i> In stock
                @else
                  <i class="fa-solid fa-triangle-exclamation me-1"></i> Out of stock
                @endif
              </span>
            </div>
          </div>

          {{-- Price Panel --}}
          <div class="price-box rounded-3 p-3 my-3 border position-relative">
            <div class="d-flex align-items-center justify-content-between">
              <div class="h1 m-0 fw-bold text-danger">
                ₱{{ number_format($product->price, 2) }}
              </div>

              <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light border d-none d-md-inline-flex align-items-center gap-1">
                  <i class="fa-regular fa-share-from-square"></i> Share
                </button>

                <!-- 🧭 Mini Report Button -->
                <button class="btn btn-sm btn-outline-danger d-none d-md-inline-flex align-items-center gap-1"
                  data-bs-toggle="modal" data-bs-target="#reportModal"
                  onclick="setReportTarget({{ $product->id }}, 'Product')" title="Report Product">
                  <i class="fas fa-flag"></i> Report
                </button>
              </div>
            </div>

            <div class="small text-muted mt-1">
              VAT included where applicable
            </div>
          </div>


          {{-- Quick facts --}}
          <dl class="row small mb-3">
            <dt class="col-4 col-md-3 text-muted">Shipping</dt>
            <dd class="col-8 col-md-9">Pangasinan only · <span class="text-muted">Calculated at checkout</span></dd>

            <dt class="col-4 col-md-3 text-muted">Unit</dt>
            <dd class="col-8 col-md-9">{{ ucfirst($product->unit ?? 'N/A') }}</dd>

            <dt class="col-4 col-md-3 text-muted">Min Order</dt>
            <dd class="col-8 col-md-9">{{ $product->min_order_qty ?? 1 }} {{ $product->unit ?? 'unit(s)' }}</dd>

            <dt class="col-4 col-md-3 text-muted">Stock</dt>
            <dd class="col-8 col-md-9">{{ (int) $product->stock }}</dd>
          </dl>

          {{-- Quantity + Actions --}}
          @if(($product->stock ?? 0) > 0)
            <div class="d-flex align-items-center mb-4">
              <span class="me-3 text-muted">Quantity</span>
              <div class="qty-group qty-sm">
                <button class="qty-btn" type="button" id="decQty" aria-label="Decrease">−</button>
                <input type="number" class="qty-input" id="qtyInput" min="{{ (int) ($product->min_order_qty ?? 1) }}"
                  max="{{ (int) ($product->stock ?? 9999) }}" value="{{ (int) ($product->min_order_qty ?? 1) }}"
                  inputmode="numeric">
                <button class="qty-btn" type="button" id="incQty" aria-label="Increase">+</button>
              </div>
            </div>
          @endif


          <div class="d-flex flex-wrap gap-2">
            @php
              $user = auth()->user(); // ✅ define the user
              $isOwner = $user && $product->user_id === $user->id;
            @endphp

            @if ($isOwner)
              {{-- 🚫 Owner cannot buy their own product --}}
              <button class="btn btn-secondary btn-lg px-4 w-100" disabled>
                <i class="fa-solid fa-ban me-1"></i> You cannot buy your own product
              </button>

            @elseif (($product->stock ?? 0) > 0)
              {{-- ✅ Add to Cart --}}
              <form id="addToCartForm" method="POST" action="{{ route('cart.add', $product->id) }}">
                @csrf
                <input type="hidden" name="quantity" id="qtyField" value="{{ (int) ($product->min_order_qty ?? 1) }}">
                <button type="submit" class="btn btn-outline-danger btn-lg px-4">
                  <i class="fas fa-cart-plus me-1"></i> Add To Cart
                </button>
              </form>

              {{-- ✅ Buy Now --}}
              <form method="POST" action="{{ route('checkout.prepare') }}"
                onsubmit="document.getElementById('buyNowQty').value = document.getElementById('qtyInput').value;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" id="buyNowQty" name="quantity" value="{{ (int) ($product->min_order_qty ?? 1) }}">
                <button class="btn btn-danger btn-lg px-4">Buy Now</button>
              </form>

            @else
              {{-- ❌ SOLD OUT --}}
              <button class="btn btn-secondary btn-lg px-4" disabled>
                <i class="fa-solid fa-ban me-1"></i> Sold Out
              </button>
            @endif


            {{-- Wishlist --}}
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


          {{-- Trust bar --}}
          <div class="trust-bar d-flex flex-wrap gap-3 mt-4 border-top pt-3 small">
            <div class="d-flex align-items-center gap-2">
              <i class="fa-regular fa-shield-check text-success"></i><span>Buyer Protection</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="fa-regular fa-truck-fast text-primary"></i><span>Fast fulfillment</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="fa-regular fa-rotate-left text-warning"></i><span>Easy returns</span>
            </div>
          </div>

          <div class="mt-3 small text-muted">Category: {{ $product->category->name ?? 'Uncategorized' }}</div>
        </div>
      </div>
    </div>

    {{-- =================== DESCRIPTION (old layout) =================== --}}
    <div class="mt-4 p-3 border rounded bg-white">
      <h5 class="fw-semibold mb-3">Product Description</h5>
      <div class="small">{!! nl2br(e($product->description)) !!}</div>
    </div>

    {{-- =================== SELLER CARD (old layout) =================== --}}
    <div class="mt-5 p-3 border rounded bg-white">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <img
            src="{{ image_url($seller?->profile_picture) }}"
            alt="{{ $seller?->username ?? 'Seller' }}" class="rounded-circle"
            style="width:64px;height:64px;object-fit:cover;">
          <div>
            <div class="fw-semibold">{{ $seller?->username ?? $seller?->name ?? 'Seller' }}</div>
            <div class="small text-muted">Joined {{ optional($storeStats['member_since'])->diffForHumans() ?? '—' }}
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
          <a href="{{ route('shop.view', $seller->id) }}" class="btn btn-outline-secondary">
            <i class="fa-regular fa-store me-1"></i> View Shop
          </a>
        </div>
      </div>

      <div class="d-flex flex-wrap gap-4 mt-3 small">
        <div><span class="text-muted">Ratings:</span> {{ number_format($storeStats['ratings_count']) }}</div>
        @if($storeStats['response_rate'])
          <div><span class="text-muted">Response Rate:</span> {{ $storeStats['response_rate'] }}</div>
        @endif
        @if($storeStats['response_time'])
          <div><span class="text-muted">Response Time:</span> {{ $storeStats['response_time'] }}</div>
        @endif
      </div>
    </div>

    @php
      $farmAddress = $product->farm_address ?? $seller->farm_address ?? null;
    @endphp

    {{-- =================== SPECS (old layout) =================== --}}
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
        <dd class="col-8 col-md-9" style="white-space: pre-line;">{{ $farmAddress ?? '—' }}</dd>
      </dl>
    </div>

    {{-- =================== RATINGS (old layout) =================== --}}
    <div id="ratings" class="mt-4 p-3 border rounded bg-white">
      <h5 class="fw-semibold mb-3">Customer Ratings & Reviews</h5>

      @if(($ratingsCount ?? 0) > 0)
        <div class="d-flex align-items-center gap-2 small text-muted mb-3">
          <span class="text-warning">
            @for($i = 1; $i <= 5; $i++)
              <i class="fa{{ $i <= round($avgRating ?? 0) ? 's' : 'r' }} fa-star"></i>
            @endfor
          </span>
          <span class="fw-semibold text-dark">{{ number_format($avgRating, 1) }}</span>
          <span>· {{ $ratingsCount }} review{{ $ratingsCount > 1 ? 's' : '' }}</span>
        </div>

        @php
          $uniquePhotos = (isset($reviewPhotos) && $reviewPhotos instanceof \Illuminate\Support\Collection)
            ? $reviewPhotos->unique()->values()
            : collect();
        @endphp
        @if($uniquePhotos->count() > 1)
          <div class="mb-3">
            <div class="small text-muted mb-2">Photos from buyers</div>
            <div class="d-flex flex-wrap gap-2">
              @foreach($uniquePhotos as $p)
                <a href="{{ image_url($p) }}" target="_blank" class="d-inline-block">
  <img src="{{ image_url($p) }}" alt="Buyer photo" class="rounded border"
    style="width:64px;height:64px;object-fit:cover;">
</a>

              @endforeach
            </div>
          </div>
        @endif

        <ul class="list-group list-group-flush">
          @foreach($reviews as $rev)
            <li class="list-group-item py-3">
              <div class="d-flex align-items-start gap-3">
                <img
                  src="{{ image_url($rev->user?->profile_picture) }}"
                  alt="{{ $rev->user?->name ?? 'User' }}" class="rounded-circle border"
                  style="width:40px;height:40px;object-fit:cover;">

                <div class="flex-grow-1">
                  <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-semibold">{{ $rev->show_username ? ($rev->user?->name ?? 'User') : 'Anonymous' }}</span>
                    <span class="text-warning small">
                      @for($i = 1; $i <= 5; $i++)
                        <i class="fa{{ $i <= (int) $rev->rating ? 's' : 'r' }} fa-star"></i>
                      @endfor
                    </span>
                    <span class="small text-muted">{{ $rev->created_at->diffForHumans() }}</span>
                  </div>

                  @if($rev->review)
                    <div class="small mb-2">{{ $rev->review }}</div>
                  @endif

                  @php
                    $stripHasThis = $uniquePhotos->isNotEmpty() && $rev->photo_path
                      ? $uniquePhotos->contains($rev->photo_path)
                      : false;
                    $showReviewPhoto = $rev->photo_path && ($uniquePhotos->count() <= 1 || !$stripHasThis);
                  @endphp
                  <div class="d-flex flex-wrap gap-2">
                    @if($showReviewPhoto)
                      <a href="{{ asset('storage/' . $rev->photo_path) }}" target="_blank">
                        <img src="{{ image_url($rev->photo_path) }}" alt="Review photo" class="rounded border"
                          style="width:110px;height:110px;object-fit:cover;">
                      </a>
                    @endif

                    @if($rev->video_path)
                      <video controls class="rounded border" style="max-width:200px;">
                        <source src="{{ asset('storage/' . $rev->video_path) }}" type="video/mp4">
                      </video>
                    @endif
                  </div>
                </div>
              </div>
            </li>
          @endforeach
        </ul>
      @else
        <div class="small text-muted">No reviews yet. Be the first to review this product after purchase!</div>
      @endif
    </div>

  </div> {{-- /container --}}

  {{-- === Global Report Modal (local include only for this page) === --}}
  @include('partials.ReportModal')

  {{-- Helper for setting report target dynamically --}}
  <script>
    function setReportTarget(id, type) {
      const targetId = document.getElementById('reportTargetId');
      const targetType = document.getElementById('reportTargetType');
      if (targetId && targetType) {
        targetId.value = id || '';
        targetType.value = type || 'general';
      }
    }
  </script>


  {{-- =================== STYLES =================== --}}
  <style>
    :root {
      --pdp-border: #e9ecef;
      --pdp-ink: #222;
    }

    .shadow-xs {
      box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
    }

    .pdp-card {
      padding: 1rem 1rem 1.25rem;
    }

    @media (min-width: 768px) {
      .pdp-card {
        padding: 1.25rem 1.25rem 1.5rem;
      }
    }

    .pdp-title {
      color: var(--pdp-ink);
      letter-spacing: .2px;
    }

    /* Main image area (fixed size) */
    .product-gallery .main-image {
      width: 100%;
      max-width: 520px;
      height: 420px;
      margin-inline: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      border: 1px solid var(--pdp-border);
      border-radius: 12px;
      position: relative;
    }

    .product-gallery .main-image img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      position: relative;
      z-index: 1;
    }

    @media (max-width: 991.98px) {
      .product-gallery .main-image {
        height: 360px;
      }
    }

    /* Thumbs rail */
    .thumbs-rail {
      -webkit-overflow-scrolling: touch;
      padding-bottom: .25rem;
    }

    .thumbs .thumb-img {
      width: 72px;
      height: 72px;
      object-fit: cover;
      border: 1px solid #e5e5e5;
      border-radius: 10px;
      transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }

    .thumbs .thumb.active .thumb-img,
    .thumbs .thumb-img:hover {
      border-color: #d0011b;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .price-box {
      background: #fff5f6;
      border: 1px solid #ffd9de;
    }

    /* Visible stock badge */
    .stock-badge {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .35rem .6rem;
      font-weight: 700;
      border-radius: 9999px;
      font-size: .82rem;
      letter-spacing: .2px;
      line-height: 1;
      border: 1px solid transparent;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    }

    .stock--in {
      background: #16a34a;
      color: #fff;
      border-color: #0f8a3a;
    }

    .stock--out {
      background: #ef4444;
      color: #fff;
      border-color: #dc2626;
    }

    /* Quantity control (compact) */
    .qty-group {
      display: inline-flex;
      align-items: center;
      border: 1px solid #dde1e6;
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
    }

    .qty-group.qty-sm .qty-btn {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      background: #fff;
      border: 0;
      color: #475569;
      font-size: 20px;
      line-height: 1;
      font-weight: 600;
      transition: background .15s ease, color .15s ease;
      user-select: none;
    }

    .qty-btn:hover {
      background: #f3f4f6;
      color: #111827;
    }

    .qty-btn:active {
      transform: scale(.98);
    }

    .qty-btn:disabled {
      opacity: .35;
      pointer-events: none;
    }

    .qty-group.qty-sm .qty-input {
      width: 64px;
      height: 34px;
      text-align: center;
      border: 0;
      outline: none;
      font-size: 18px;
      font-weight: 600;
      color: #111827;
      background: transparent;
    }

    .qty-input:focus {
      box-shadow: inset 0 0 0 2px #e5e7eb;
      border-radius: 8px;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .qty-input[type=number] {
      -moz-appearance: textfield;
    }

    /* Arrows (visible on hover desktop, always clickable) */
    .product-gallery .main-image .img-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background: rgba(0, 0, 0, .55);
      color: #fff;
      display: grid;
      place-items: center;
      cursor: pointer;
      z-index: 5;
      opacity: .9;
      transition: background .15s ease, transform .15s ease, opacity .15s ease;
    }

    .product-gallery .main-image .img-nav:hover {
      background: rgba(0, 0, 0, .7);
      transform: translateY(-50%) scale(1.05);
    }

    .product-gallery .main-image .img-nav:active {
      transform: translateY(-50%) scale(.98);
    }

    .product-gallery .main-image .img-nav-prev {
      left: .5rem;
    }

    .product-gallery .main-image .img-nav-next {
      right: .5rem;
    }

    @media (min-width:992px) {
      .product-gallery .main-image .img-nav {
        opacity: 0;
      }

      .product-gallery .main-image:hover .img-nav {
        opacity: .9;
      }
    }

    /* Ratings small helpers */
    #ratings .fa-star {
      color: #ffc107;
    }

    #ratings .list-group-item {
      border-color: #f1f1f1;
    }
  </style>

  {{-- =================== SCRIPTS =================== --}}
  <script>
    // thumbs swap on click + simple AJAX for add-to-cart
    document.addEventListener('DOMContentLoaded', () => {
      const qtyInput = document.getElementById('qtyInput');
      const qtyField = document.getElementById('qtyField');
      const min = parseInt(qtyInput.min || 1, 10);
      const max = parseInt(qtyInput.max || 9999, 10);
      function sync() { qtyField.value = qtyInput.value; }

      document.getElementById('decQty').addEventListener('click', () => {
        qtyInput.value = Math.max(min, +qtyInput.value - 1); sync();
      });
      document.getElementById('incQty').addEventListener('click', () => {
        qtyInput.value = Math.min(max, +qtyInput.value + 1); sync();
      });
      qtyInput.addEventListener('change', () => {
        let v = +qtyInput.value || min;
        qtyInput.value = Math.max(min, Math.min(max, v)); sync();
      });

      document.querySelectorAll('.thumbs .thumb').forEach(btn => {
        btn.addEventListener('click', () => {
          document.getElementById('mainImg').src = btn.querySelector('img').src;
          document.querySelectorAll('.thumbs .thumb').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        });
      });

      const form = document.getElementById('addToCartForm');
      if (form) {
        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const btn = form.querySelector('button[type="submit"]');

          // 🧩 Prevent double-clicks
          if (btn.disabled) return;
          btn.disabled = true;
          const original = btn.innerHTML;
          btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

          try {
            const response = await fetch(form.action, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              body: new FormData(form)
            });

            const data = await response.json();

            if (response.ok) {
              alert(data.message || 'Added to cart!');
            } else {
              alert(data.message || 'Failed to add to cart.');
            }
          } catch (err) {
            alert('Something went wrong adding to cart.');
          } finally {
            // 🧩 Restore button after delay
            setTimeout(() => {
              btn.disabled = false;
              btn.innerHTML = original;
            }, 800);
          }
        });
      }
    });
  </script>

  <script>
    // gallery arrows + keyboard + swipe
    document.addEventListener('DOMContentLoaded', () => {
      const gallery = @json($gallery);
      const mainImg = document.getElementById('mainImg');
      const thumbButtons = Array.from(document.querySelectorAll('.thumbs .thumb'));
      const prevBtn = document.querySelector('.img-nav-prev');
      const nextBtn = document.querySelector('.img-nav-next');

      let current = 0;
      function setIndex(i) {
        if (!gallery.length) return;
        current = (i + gallery.length) % gallery.length;
        if (mainImg) mainImg.src = gallery[current];
        thumbButtons.forEach(btn => btn.classList.remove('active'));
        const active = thumbButtons.find(b => Number(b.dataset.index) === current);
        if (active) active.classList.add('active');
      }

      thumbButtons.forEach(btn => btn.addEventListener('click', () => setIndex(Number(btn.dataset.index || 0))));
      prevBtn?.addEventListener('click', () => setIndex(current - 1));
      nextBtn?.addEventListener('click', () => setIndex(current + 1));

      document.addEventListener('keydown', (e) => {
        if (!gallery || gallery.length < 2) return;
        if (e.key === 'ArrowLeft') { e.preventDefault(); setIndex(current - 1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); setIndex(current + 1); }
      });

      let startX = null;
      mainImg?.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
      mainImg?.addEventListener('touchend', (e) => {
        if (startX == null) return;
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) { if (dx < 0) setIndex(current + 1); else setIndex(current - 1); }
        startX = null;
      }, { passive: true });

      setIndex(0);
    });
  </script>
</x-app-layout>