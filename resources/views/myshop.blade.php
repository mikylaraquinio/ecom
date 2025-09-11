<x-app-layout>
    <div class="container py-4">
        <div class="row">
        <nav class="navbar navbar-expand-lg navbar-light bg-success shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="#">
                    <i class="fas fa-tractor me-2"></i> Farm Seller Dashboard
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="#" data-target="order-status">
                                <i class="fas fa-seedling me-1"></i> Order Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-target="my-shop">
                                <i class="fas fa-store-alt me-1"></i> My Farm Shop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-target="add-product">
                                <i class="fas fa-plus-circle me-1"></i> Add Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-target="analytics">
                                <i class="fas fa-chart-line me-1"></i> Analytics
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
                <div class="tab-content mt-3">
                    <!--Orders Status-->
                    @if(auth()->check() && auth()->user()->role === 'seller')
                    <div class="tab-pane fade show active" id="order-status">
                        <h5 class="text-center fw-bold">Your Orders</h5>
                        <div class="table-responsive shadow-sm rounded bg-white p-3" style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd;">
                            @if(isset($orders) && $orders->count() > 0)
                                <table class="table text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Buyer</th>
                                            <th>Shipping Address</th>
                                            <th>Products</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->buyer->name }}</td>
                                                <td>
                                                    @if($order->shippingAddress)
                                                        @if($order->shippingAddress->floor_unit_number)
                                                            {{ $order->shippingAddress->floor_unit_number }},
                                                        @endif
                                                        {{ $order->shippingAddress->barangay }}, {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }}<br>
                                                        <strong>Contact:</strong> {{ $order->shippingAddress->mobile_number }}
                                                    @else
                                                        <span class="text-danger">No Shipping Address Provided</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="list-unstyled">
                                                        @foreach($order->orderItems as $item)
                                                            <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge text-white 
                                                        @if($order->status == 'pending') bg-warning text-dark
                                                        @elseif($order->status == 'accepted') bg-success
                                                        @elseif($order->status == 'denied' || $order->status == 'canceled') bg-danger
                                                        @elseif($order->status == 'shipped') bg-primary
                                                        @elseif($order->status == 'completed') bg-info
                                                        @elseif($order->status == 'cancel_requested') bg-warning
                                                        @else bg-secondary
                                                        @endif">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($order->status == 'pending')
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to accept this order?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="accepted">
                                                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                                            </form>
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="denied">
                                                                <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                                            </form>
                                                        </div>

                                                    @elseif($order->status == 'accepted')
                                                        <div class="d-flex justify-content-center">
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="shipped">
                                                                <button type="submit" class="btn btn-primary btn-sm">Mark as Shipped</button>
                                                            </form>
                                                        </div>

                                                    @elseif($order->status == 'shipped')
                                                        <div class="d-flex justify-content-center">
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-success btn-sm">Mark as Completed</button>
                                                            </form>
                                                        </div>

                                                    @elseif($order->status == 'canceled')
                                                        <span class="badge bg-danger">Canceled by Buyer</span>

                                                    @elseif($order->status == 'cancel_requested')
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <form action="{{ route('seller.approveCancel', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-danger btn-sm">Approve Cancellation</button>
                                                            </form>
                                                            <form action="{{ route('seller.denyCancel', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm">Deny Cancellation</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center">
                                    {{ $orders->links('pagination::bootstrap-5') }}
                                </div>
                            @else
                                <p class="text-center text-muted">No orders available.</p>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade" id="my-shop">
                        <div class="shop-header bg-white shadow-sm p-1 rounded">
                            <h4 class="fw-bold mb-1">{{ auth()->user()->farm_name ?? 'My Shop' }}</h4>
                            <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i> {{ auth()->user()->farm_address ?? 'No location provided' }}</p>
                        </div>

                        <!-- My Products Section -->
                        <div class="pt-2">
                            <h5 class="fw-bold mb-2">My Products</h5>
                            @php
                                $products = \App\Models\Product::where('user_id', auth()->id())->get();
                            @endphp

                            @if($products->isNotEmpty())
                                <div class="row g-2 overflow-auto" style="max-height: 500px;">
                                    @foreach($products as $product)
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                            <div class="product-card border rounded bg-white p-1">
                                                <img src="{{ asset('storage/' . $product->image) }}" class="w-100 rounded" style="height: 150px; object-fit: cover;">
                                                <div class="p-1">
                                                    <h6 class="fw-bold text-truncate m-0" title="{{ $product->name }}">{{ $product->name }}</h6>
                                                    <p class="text-danger fw-bold mb-1">₱{{ number_format($product->price, 2) }}</p>
                                                    <p class="text-muted small mb-1">Stock: {{ $product->stock }}</p>
                                                    
                                                    <button class="btn btn-sm btn-info mt-1" data-bs-toggle="modal" data-bs-target="#viewProductModal{{ $product->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mt-1"
                                                            onclick="return confirm('Are you sure you want to delete this product?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
           

                        <!-- View Product Modal -->
                        <div class="modal fade" id="viewProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="viewProductModalLabel{{ $product->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="viewProductModalLabel{{ $product->id }}" style="color: #222;">
                                            {{ $product->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <img class="img-fluid rounded w-100" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                            </div>
                                            <div class="col-md-7">
                                                <h6 class="fw-bold" style="color: #222;">{{ $product->name }}</h6>
                                                <p style="color: #444;">{{ $product->description ?? 'No description available.' }}</p>
                                                <p class="fw-bold" style="color: #2d6a4f;">Price: ₱{{ number_format($product->price, 2) }}</p>
                                                <p style="color: #666;">Stock: {{ $product->stock }}</p>
                                                <p style="color: #666;">Category: {{ $product->category->name ?? 'Uncategorized' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Product Modal -->
                        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Product</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <label>Product Image</label>
                                                @if($product->image)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" width="100">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                <small>Leave empty to keep existing image</small>
                                            </div>

                                            <div class="form-group">
                                                <label>Product Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="description" required>{{ $product->description }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" class="form-control" name="price" step="0.01" value="{{ $product->price }}" required>
                                            </div>

                                            <!-- ✅ Weight Field -->
                                            <div class="form-group">
                                                <label>Weight (kg)</label>
                                                <input type="number" class="form-control" name="weight" step="0.01" value="{{ $product->weight }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Stock Quantity</label>
                                                <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" required min="0">
                                            </div>

                                            <!-- ✅ Category Dropdown Fix -->
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select class="form-control" name="category" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($mainCategories as $category)
                                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                        @foreach ($category->subcategories as $subCategory)
                                                            <option value="{{ $subCategory->id }}" {{ $product->category_id == $subCategory->id ? 'selected' : '' }}>
                                                                &nbsp;&nbsp; ├─ {{ $subCategory->name }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-success mt-2">Update Product</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted">No products available.</p>
                @endif
            </div>

                    </div>


                    <div class="tab-pane fade" id="add-product">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-success text-white">
                                <h5 class="m-0"><i class="fas fa-plus-circle me-2"></i> Add New Product</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <!-- Left Column: Product Image -->
                                        <div class="col-md-4 text-center">
                                            <label class="form-label fw-bold">Product Image</label>
                                            <div class="border rounded p-2">
                                                <input type="file" class="form-control" name="image" accept="image/*" required>
                                            </div>
                                        </div>

                                        <!-- Right Column: Product Details -->
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Product Name</label>
                                                <input type="text" class="form-control" name="name" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea class="form-control" name="description" rows="3" required></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Price</label>
                                                    <input type="number" class="form-control" name="price" step="0.01" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Unit</label>
                                                    <select class="form-control" name="unit" required>
                                                        <option value="">Select Unit</option>
                                                        <option value="kg">Kilogram (kg)</option>
                                                        <option value="piece">Piece</option>
                                                        <option value="bundle">Bundle</option>
                                                        <option value="sack">Sack</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Stock Quantity</label>
                                                    <input type="number" class="form-control" name="stock" required min="0">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Minimum Order Quantity</label>
                                                    <input type="number" class="form-control" name="min_order_qty" min="1">
                                                </div>

                                                <!-- ✅ New Weight Field -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Weight (kg)</label>
                                                    <input type="number" class="form-control" name="weight" step="0.01" min="0.01" required>
                                                    <small class="text-muted">Enter the weight per unit of this product (e.g., 1 piece of mango = 0.25kg).</small>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="category-dropdown" class="fw-bold text-success">
                                                    Category
                                                    <i class="fas fa-info-circle text-primary" data-bs-toggle="tooltip" title="Select a category from the list."></i>
                                                </label>
                                                <select class="form-control" id="category-dropdown" name="category">
                                                    <option value="">Select Category</option>
                                                    @foreach($mainCategories as $category)
                                                        @php $mainIcon = getCategoryIcon($category->name); @endphp
                                                        <option value="{{ $category->id }}" class="fw-bold">
                                                            {{ $mainIcon }} {{ $category->name }}
                                                        </option>
                                                        @if ($category->subcategories->count() > 0)
                                                            @foreach ($category->subcategories as $subCategory)
                                                                @php $subIcon = getCategoryIcon($subCategory->name, $category->name); @endphp
                                                                <option value="{{ $subCategory->id }}">
                                                                    &nbsp;&nbsp;&nbsp; ├─ {{ $subIcon }} {{ $subCategory->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle"></i> Add Product
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                                        <div class="tab-pane fade" id="analytics">
                                            <h4 class="fw-bold mb-3">Analytics Dashboard</h4>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="card shadow-sm border-0 p-3 bg-success text-white">
                                                        <h6>Total Sales</h6>
                                                        <h4>₱50,000.00</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card shadow-sm border-0 p-3 bg-primary text-white">
                                                        <h6>Total Orders</h6>
                                                        <h4>150</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card shadow-sm border-0 p-3 bg-warning text-dark">
                                                        <h6>Top Selling Product</h6>
                                                        <h4>Organic Tomatoes</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card shadow-sm border-0 p-3 bg-danger text-white">
                                                        <h6>Low Stock Alerts</h6>
                                                        <h4>5 Items</h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <h5 class="fw-bold">Revenue Trends</h5>
                                                <canvas id="salesChart"></canvas>
                                            </div>
                                        </div>


                                        @else
                                            <p class="text-danger">You do not have permission to access this page.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            datasets: [{
                label: 'Sales Revenue',
                data: [5000, 7000, 8000, 6000, 9000, 11000, 15000],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

    <!-- Ensure jQuery & Bootstrap are Loaded -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>




<!-- JavaScript for Navigation -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".nav-link");
        const tabPanes = document.querySelectorAll(".tab-pane");

        navLinks.forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                
                // Remove active class from all links
                navLinks.forEach(nav => nav.classList.remove("active"));
                
                // Hide all tab panes
                tabPanes.forEach(tab => tab.classList.remove("show", "active"));
                
                // Activate the clicked tab
                this.classList.add("active");
                const target = document.getElementById(this.getAttribute("data-target"));
                target.classList.add("show", "active");
            });
        });
    });
</script>

<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/YOUR_KIT_CODE.js" crossorigin="anonymous"></script>

</x-app-layout>