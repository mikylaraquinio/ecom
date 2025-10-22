{{-- resources/views/partials/review-modal.blade.php --}}
@if($order->status === 'completed')
  @foreach($order->orderItems as $item)
    <div class="modal fade" id="reviewModal-{{ $item->id }}" tabindex="-1" aria-labelledby="reviewModalLabel-{{ $item->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0">
          {{-- HEADER --}}
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="reviewModalLabel-{{ $item->id }}">
              <i class="fas fa-star me-2"></i> Leave a Review — {{ $item->product->name }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          {{-- FORM --}}
          <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              {{-- Hidden fields --}}
              <input type="hidden" name="product_id" value="{{ $item->product->id }}">
              <input type="hidden" name="order_item_id" value="{{ $item->id }}">

              {{-- Rating --}}
              <div class="mb-3">
                <label class="form-label fw-semibold">Rating</label>
                <select name="rating" class="form-select" required>
                  <option value="">Select rating</option>
                  <option value="5">⭐⭐⭐⭐⭐ — Excellent</option>
                  <option value="4">⭐⭐⭐⭐ — Good</option>
                  <option value="3">⭐⭐⭐ — Average</option>
                  <option value="2">⭐⭐ — Poor</option>
                  <option value="1">⭐ — Terrible</option>
                </select>
              </div>

              {{-- Review text --}}
              <div class="mb-3">
                <label class="form-label fw-semibold">Your Review</label>
                <textarea name="review" class="form-control" rows="4" placeholder="Share your experience with this product…" required></textarea>
              </div>

              {{-- Optional photo upload --}}
              <div class="mb-3">
                <label class="form-label fw-semibold">Upload Photo (optional)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <small class="text-muted">Max size: 2MB. Accepted formats: JPG, PNG.</small>
              </div>

              {{-- Optional video upload --}}
              <div class="mb-3">
                <label class="form-label fw-semibold">Upload Video (optional)</label>
                <input type="file" name="video" class="form-control" accept="video/mp4,video/avi,video/mpeg">
                <small class="text-muted">Max size: 10MB. Accepted formats: MP4, AVI, MPEG.</small>
              </div>

              {{-- Show username --}}
              <div class="form-check">
                <input type="checkbox" name="show_username" id="showUsername-{{ $item->id }}" class="form-check-input">
                <label for="showUsername-{{ $item->id }}" class="form-check-label">Show my username on this review</label>
              </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bg-light">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane me-1"></i> Submit Review
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($order->status === 'completed')
        @foreach($order->orderItems as $item)
            var modal{{ $item->id }} = document.getElementById('reviewModal-{{ $item->id }}');
            if(modal{{ $item->id }}) {
                modal{{ $item->id }}.addEventListener('hidden.bs.modal', function () {
                    // Reload the page when the modal is closed
                    location.reload();
                });
            }
        @endforeach
    @endif
});
</script>

