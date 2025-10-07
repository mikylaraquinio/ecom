@extends('layouts.app')

@section('content')
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Notifications</h4>
    <button class="btn btn-sm btn-outline-secondary" id="markAllRead">Mark all as read</button>
  </div>

  @forelse($notifications as $n)
    @php($d = $n->data ?? [])
    <div class="card mb-2 {{ is_null($n->read_at) ? 'border-primary' : '' }}">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div class="me-3">
          <div class="fw-semibold">{{ $d['title'] ?? 'Notification' }}</div>
          @if(!empty($d['message']))
            <div class="text-muted small">{{ $d['message'] }}</div>
          @endif
          <div class="text-muted small mt-1">{{ $n->created_at->diffForHumans() }}</div>

          {{-- Optional: show per-seller items nicely if present --}}
          @if(!empty($d['items']))
            <ul class="small mt-2 mb-0">
              @foreach($d['items'] as $it)
                <li>{{ $it['product_name'] ?? 'Item' }} × {{ $it['quantity'] ?? 1 }}</li>
              @endforeach
            </ul>
          @endif
        </div>
        <div class="text-end">
          @if(!empty($d['url']))
            <a href="{{ $d['url'] }}" class="btn btn-sm btn-primary mb-2">View</a>
          @endif
          @if(is_null($n->read_at))
            <button class="btn btn-sm btn-outline-secondary mark-read" data-id="{{ $n->id }}">Mark read</button>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="text-muted">No notifications yet.</div>
  @endforelse

  <div class="mt-3">
    {{ $notifications->links() }}
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Mark all as read
  document.getElementById('markAllRead')?.addEventListener('click', async () => {
    const r = await fetch('{{ route("notifications.readAll") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    });
    if (r.ok) location.reload();
  });

  // Mark one as read
  document.querySelectorAll('.mark-read').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const r = await fetch('{{ url("/notifications/read") }}/' + id, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      if (r.ok) location.reload();
    });
  });
});
</script>
@endsection