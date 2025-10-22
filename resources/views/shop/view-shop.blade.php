<x-app-layout>
  <div class="container my-5">

    {{-- 🧭 Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ $seller->seller->shop_name ?? 'Shop' }}
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
              style="width:80px;height:80px;object-fit:cover;">

          <div>
            <h4 class="mb-1 fw-semibold">
              {{ $seller->seller->shop_name ?? $seller->name }}
            </h4>
            <div class="small text-muted">Joined {{ $seller->created_at->diffForHumans() }}</div>
            <div class="small text-muted">{{ $products->count() }} products</div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('chat', ['receiverId' => $seller->id]) }}" class="btn btn-outline-success">
            <i class="fa-regular fa-comments me-1"></i> Chat Now
          </a>
        </div>
      </div>
    </div>

    {{-- 🛍️ Seller Products --}}
    <section>
      <h5 class="fw-bold mb-3 text-success">
        {{ $seller->seller->shop_name ?? $seller->name }}’s Products
      </h5>

      {{-- Include the reusable product list partial --}}
      @include('partials.product-list', ['products' => $products])
    </section>

  </div>

  {{-- ✅ Wishlist JS --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const token = document.querySelector('meta[name="csrf-token"]').content;

      document.body.addEventListener('click', async function (e) {
        const btn = e.target.closest('.wishlist-btn');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const productId = btn.dataset.productId;
        const icon = btn.querySelector('i');

        try {
          const res = await fetch(`/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json'
            }
          });

          const data = await res.json();

          if (res.status === 401 || data.status === 'unauthenticated') {
            Swal.fire({
              icon: 'warning',
              title: 'Login Required',
              text: 'Please log in to add items to your wishlist 💚',
              confirmButtonColor: '#71b127'
            });
            return;
          }

          if (data.status === 'added') {
            icon.classList.remove('far');
            icon.classList.add('fas');
            Swal.fire({
              icon: 'success',
              title: 'Added!',
              text: 'Item added to wishlist ❤️',
              timer: 1500,
              showConfirmButton: false
            });
          } else if (data.status === 'removed') {
            icon.classList.remove('fas');
            icon.classList.add('far');
            Swal.fire({
              icon: 'info',
              title: 'Removed',
              text: 'Item removed from wishlist 💔',
              timer: 1500,
              showConfirmButton: false
            });
          }

        } catch (err) {
          console.error('Wishlist toggle error:', err);
          Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Something went wrong while updating your wishlist.',
          });
        }
      });
    });
  </script>
</x-app-layout>
