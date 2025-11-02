<x-app-layout>
<div class="container my-5">

  {{-- 🧭 Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
      <li class="breadcrumb-item active" aria-current="page">
        {{ $seller->seller->shop_name ?? $seller->name }}
      </li>
    </ol>
  </nav>

  {{-- 🧑‍🌾 Seller Info --}}
  <div class="card border-0 shadow-sm p-4 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ $seller->profile_picture ? asset('storage/'.$seller->profile_picture) : asset('assets/default.png') }}"
             alt="{{ $seller->seller->shop_name ?? $seller->name }}"
             class="rounded-circle border"
             width="80" height="80"
             style="object-fit:cover;">
        <div>
          <h4 class="mb-1 fw-semibold text-success">
            {{ $seller->seller->shop_name ?? $seller->name }}
          </h4>
          <small class="text-muted d-block">Joined {{ $seller->created_at->format('F Y') }}</small>
          <small class="text-muted">{{ $products->count() }} products</small>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('chat', ['receiverId' => $seller->id]) }}" class="btn btn-outline-success">
          <i class="fa-regular fa-comments me-1"></i> Chat Now
        </a>
        <a href="{{ route('shop') }}" class="btn btn-outline-secondary">
          <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    @if(!empty($seller->seller->bio))
      <p class="mt-3 mb-0 text-muted">{{ $seller->seller->bio }}</p>
    @endif
  </div>

  {{-- 🛍️ Seller Products --}}
  <section>
    <h5 class="fw-bold mb-3 text-success">
      {{ $seller->seller->shop_name ?? $seller->name }}’s Products
    </h5>

    @include('partials.product-list', ['products' => $products])
  </section>

</div>
</x-app-layout>
