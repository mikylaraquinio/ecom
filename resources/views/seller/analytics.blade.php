<x-app-layout>
<div class="dashboard-wrapper bg-light min-vh-100 py-4">
  <div class="container-lg">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
      <div>
        <h5 class="fw-bold text-success mb-1">Seller Analytics Dashboard</h5>
        <small class="text-muted">Track your performance and insights in real time</small>
      </div>
      <div>
        <select id="filterType" class="form-select form-select-sm border-success shadow-sm rounded-3">
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly" selected>Monthly</option>
          <option value="yearly">Yearly</option>
        </select>
      </div>
    </div>

    {{-- ===== MAIN METRIC CARDS ===== --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card metric-card gradient-green text-white shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <small>Total Revenue</small>
              <i class="fas fa-wallet small opacity-75"></i>
            </div>
            <h5 class="fw-bold mb-0 mt-1">₱{{ number_format($completedSales, 2) }}</h5>
            <small class="opacity-75">This Month: ₱{{ number_format($monthlyRevenue, 2) }}</small>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card metric-card gradient-blue text-white shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <small>Total Orders</small>
              <i class="fas fa-shopping-cart small opacity-75"></i>
            </div>
            <h5 class="fw-bold mb-0 mt-1">{{ $totalOrders }}</h5>
            <small class="opacity-75">Completed: {{ $completedOrders }} | Pending: {{ $pendingOrders }}</small>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card metric-card gradient-orange text-white shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <small>Avg. Order Value</small>
              <i class="fas fa-receipt small opacity-75"></i>
            </div>
            <h5 class="fw-bold mb-0 mt-1">₱{{ number_format($avgOrderValue, 2) }}</h5>
            <small class="opacity-75">Per Order</small>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card metric-card gradient-pink text-white shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <small>Customers</small>
              <i class="fas fa-users small opacity-75"></i>
            </div>
            <h5 class="fw-bold mb-0 mt-1">{{ $uniqueCustomers }}</h5>
            <small class="opacity-75">Repeat: {{ $repeatCustomers }}</small>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== CHARTS ===== --}}
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-semibold text-success mb-0"><i class="fas fa-chart-line me-2"></i>Revenue Trends</h6>
            </div>
            <canvas id="salesChart" height="140"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <h6 class="fw-semibold text-success mb-3"><i class="fas fa-chart-pie me-2"></i>Order Status</h6>
            <canvas id="orderStatusChart" height="180"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== MID SECTION ===== --}}
    <div class="row g-3 mt-3">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm gradient-blue text-white h-100">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-2"><i class="fas fa-trophy me-2"></i>Top Product</h6>
            @if($mostSoldProduct)
              <h5 class="fw-bold">{{ $mostSoldProduct['product']->name }}</h5>
              <small>{{ $mostSoldProduct['total_quantity'] }} sold</small>
            @else
              <p class="small mb-0">No sales yet.</p>
            @endif
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm gradient-green text-white h-100">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-2"><i class="fas fa-truck me-2"></i>Fulfillment</h6>
            <canvas id="fulfillmentChart" height="130"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm gradient-orange text-white h-100">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-2"><i class="fas fa-boxes me-2"></i>Low Stock</h6>
            @if($lowStockProducts->count())
              <ul class="list-group list-group-flush small">
                @foreach($lowStockProducts as $prod)
                  <li class="list-group-item bg-transparent border-0 d-flex justify-content-between text-white">
                    {{ $prod->name }}
                    <span class="badge bg-light text-dark">{{ $prod->stock }}</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="small mb-0">All stocks sufficient.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center px-4">
        <h6 class="fw-semibold text-success mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h6>
        <a href="{{ route('myshop') }}" class="text-success small fw-semibold">View All →</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $order)
              <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>₱{{ number_format($order->total_amount, 2) }}</td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ 
                    $order->status == 'completed' ? 'success' : 
                    ($order->status == 'pending' ? 'warning' : 
                    ($order->status == 'canceled' ? 'danger' : 'info')) 
                  }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted small">No recent orders.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ===== STYLES ===== --}}
<style>
  .metric-card { border-radius: 12px; font-size: 0.9rem; }
  .gradient-green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
  .gradient-blue { background: linear-gradient(135deg, #3498db, #2980b9); }
  .gradient-orange { background: linear-gradient(135deg, #f39c12, #e67e22); }
  .gradient-pink { background: linear-gradient(135deg, #e91e63, #c2185b); }
  .card { transition: transform .2s ease, box-shadow .2s ease; }
  .card:hover { transform: translateY(-3px); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
  .table th { font-size: 0.85rem; color: #198754; font-weight: 600; }
  .table td { font-size: 0.85rem; }
  @media (max-width: 768px) {
    .metric-card h5 { font-size: 1rem; }
    .metric-card small { font-size: 0.75rem; }
  }
</style>

{{-- ===== CHARTS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
      labels: {!! json_encode(array_keys($salesTrends)) !!},
      datasets: [{
        label: 'Revenue (₱)',
        data: {!! json_encode(array_values($salesTrends)) !!},
        borderColor: '#28a745',
        backgroundColor: 'rgba(40,167,69,0.1)',
        fill: true,
        tension: 0.4
      }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('orderStatusChart'), {
    type: 'doughnut',
    data: {
      labels: ['Pending', 'Accepted', 'Shipped', 'Completed', 'Canceled'],
      datasets: [{
        data: [{{ $pendingOrders }}, {{ $acceptedOrders }}, {{ $shippedOrders }}, {{ $completedOrders }}, {{ $canceledOrders }}],
        backgroundColor: ['#ffc107', '#0dcaf0', '#007bff', '#198754', '#dc3545']
      }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('fulfillmentChart'), {
    type: 'pie',
    data: {
      labels: ['Delivery', 'Pickup'],
      datasets: [{ data: [{{ $deliveryOrders }}, {{ $pickupOrders }}], backgroundColor: ['#0d6efd', '#20c997'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
  });
});
</script>
</x-app-layout>
