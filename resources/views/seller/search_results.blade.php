<x-app-layout>
<div class="container py-5">
  <h4 class="fw-bold text-success mb-4">
    Search Results for “{{ $term }}”
  </h4>

  @if($sellers->count())
    <div class="row g-3">
      @foreach($sellers as $seller)
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3">
              <img src="{{ $seller->user->profile_picture ? asset('storage/'.$seller->user->profile_picture) : asset('assets/default-avatar.png') }}"
                   class="rounded-circle" width="60" height="60">
              <div>
                <h6 class="fw-semibold mb-0">{{ $seller->shop_name }}</h6>
                <small class="text-muted">{{ $seller->user->name }}</small>
              </div>
            </div>
            <div class="mt-3">
              <a href="{{ route('shop.view', $seller->id) }}" class="btn btn-outline-secondary">
                <i class="fa-regular fa-store me-1"></i> View Shop
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p class="text-muted">No sellers or shops found matching your search.</p>
  @endif
</div>
</x-app-layout>
