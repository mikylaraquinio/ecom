<x-app-layout>
    <div class="container py-4">
        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-md-3">
                <div class="list-group" id="sidebar-menu" role="tablist">
                    <a href="#order-status" class="list-group-item list-group-item-action active" data-bs-toggle="tab"
                        role="tab">Order Status</a>
                    <a href="#my-shop" class="list-group-item list-group-item-action" data-bs-toggle="tab" role="tab">My
                        Shop</a> <!-- Added My Shop tab -->
                    <a href="#my-products" class="list-group-item list-group-item-action" data-bs-toggle="tab"
                        role="tab">My Products</a>
                    <a href="#add-product" class="list-group-item list-group-item-action" data-bs-toggle="tab"
                        role="tab">Add Product</a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <div class="tab-content">
                    @if(auth()->check() && auth()->user()->role === 'seller')
                    <div class="tab-pane fade show active" id="order-status" role="tabpanel">
                        <h5>Your Orders</h5>
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                @if(isset($orders) && $orders->count() > 0)
                                    <table class="table">
                                        <thead>
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
                                                            {{ $order->shippingAddress->full_name }}<br>
                                                            {{ $order->shippingAddress->floor_unit_number ? $order->shippingAddress->floor_unit_number . ', ' : '' }}
                                                            {{ $order->shippingAddress->barangay }}, {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }}<br>
                                                            <strong>Contact:</strong> {{ $order->shippingAddress->mobile_number }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            @foreach($order->orderItems as $item)
                                                                <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge text-white 
                                                            @if($order->status == 'pending') bg-warning
                                                            @elseif($order->status == 'accepted') bg-success
                                                            @elseif($order->status == 'denied') bg-danger
                                                            @elseif($order->status == 'shipped') bg-primary
                                                            @endif">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($order->status == 'pending')
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="accepted">
                                                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                                            </form>
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="denied">
                                                                <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                                            </form>
                                                        @elseif($order->status == 'accepted')
                                                            <form action="{{ route('seller.updateOrderStatus', $order->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="shipped">
                                                                <button type="submit" class="btn btn-primary btn-sm">Mark as Shipped</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>No orders available.</p>
                                @endif
                            </div>
                        </div>

                        <!-- My Shop Section -->
                        <div class="tab-pane fade" id="my-shop" role="tabpanel">
                            <h5>My Shop</h5>

                            @if(auth()->check() && auth()->user()->role === 'seller')
                            <div class="shop-details">
                                <h5>Seller Information</h5>
                                <p><strong>Farm Name:</strong> {{ auth()->user()->farm_name ?? 'N/A' }}</p>
                                <p><strong>Location:</strong> {{ auth()->user()->farm_address ?? 'N/A' }}</p>

                                @if(auth()->user()->government_id)
                                    <p><strong>Government ID:</strong>
                                        <a href="{{ asset('storage/' . auth()->user()->government_id) }}" target="_blank">View</a>
                                    </p>
                                @endif

                                @if(auth()->user()->farm_registration_certificate)
                                    <p><strong>Farm Certificate:</strong>
                                        <a href="{{ asset('storage/' . auth()->user()->farm_registration_certificate) }}" target="_blank">View</a>
                                    </p>
                                @endif

                                <p><strong>Mobile Money:</strong> 
                                    {{ auth()->user()->mobile_money ?? 'Not provided' }}</p>
                            </div>

                                <!-- Display Products -->
                                <h5 class="mt-4">My Products</h5>

                                @php
                                    $products = \App\Models\Product::where('user_id', auth()->id())->get();
                                @endphp

                                @if($products->isNotEmpty())
                                    <div class="row">
                                        @foreach($products as $product)
                                            <div class="col-md-4">
                                                <div class="card mb-3">
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $product->name }}</h5>
                                                        <p class="card-text"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                                        <p class="card-text"><strong>Stock:</strong> {{ $product->stock }}</p>
                                                        <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">View Details</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p>No products found.</p>
                                @endif
                            @else
                                <p>You need to be a seller to view this section.</p>
                            @endif
                        </div>

                        <!-- My Products Section -->
                        <div class="tab-pane fade" id="my-products" role="tabpanel">
                            <h5>My Products</h5>
                            <p>Manage and edit your existing products.</p>

                            @if(auth()->user()->products->count() > 0)
                                <div class="row">
                                    @foreach(auth()->user()->products as $product)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                                    alt="{{ $product->name }}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product->name }}</h5>
                                                    <p class="card-text">{{ $product->description }}</p>
                                                    <p class="card-text"><strong>{{ number_format($product->price, 2) }}</strong>
                                                    </p>
                                                    <p class="card-text"><strong>Stock:</strong> {{ $product->stock }}</p>

                                                    <!-- Edit Button -->
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editProductModal{{ $product->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this product?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
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
                                                        <form method="POST" action="{{ route('products.update', $product->id) }}"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="form-group">
                                                                <label>Product Image</label>

                                                                <!-- Show Existing Image Preview -->
                                                                @if($product->image)
                                                                    <div class="mb-2">
                                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" width="100">
                                                                    </div>
                                                                @endif

                                                                <!-- File Input for New Image -->
                                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                                <small>Leave empty to keep existing image</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Product Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                    value="{{ $product->name }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Description</label>
                                                                <textarea class="form-control" name="description"
                                                                    required>{{ $product->description }}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Price</label>
                                                                <input type="number" class="form-control" name="price" step="0.01"
                                                                    value="{{ $product->price }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Stock Quantity</label>
                                                                <input type="number" class="form-control" name="stock"
                                                                    value="{{ $product->stock }}" required min="0">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="category-dropdown" class="fw-bold text-success">Category</label>
                                                                <select class="form-control" id="category-dropdown" name="category">
                                                                    <option value="">Select Category</option>

                                                                    @foreach($mainCategories as $category)
                                                                        <!-- Main Category -->
                                                                        <option value="{{ $category->id }}" class="fw-bold" 
                                                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>

                                                                        <!-- Subcategories -->
                                                                        @if ($category->subcategories->count() > 0)
                                                                            @foreach ($category->subcategories as $subCategory)
                                                                                <option value="{{ $subCategory->id }}" 
                                                                                    {{ $product->category_id == $subCategory->id ? 'selected' : '' }}>
                                                                                    &nbsp;&nbsp;&nbsp; ├─ {{ $subCategory->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-success">Update Product</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center">No products found. Add a new product below.</p>
                            @endif
                        </div>

                        <!-- Add New Product Section -->
                        <div class="tab-pane fade" id="add-product" role="tabpanel">
                            <h5>Add New Product</h5>
                            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required>
                                </div>
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" class="form-control" name="price" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock" required min="0">
                                </div>
                                <div class="form-group">
                                    <label for="category-dropdown" class="fw-bold text-success">
                                        Category
                                        <i class="fas fa-info-circle text-primary" data-bs-toggle="tooltip" title="Select a category from the list."></i>
                                    </label>
                                    <select class="form-control" id="category-dropdown" name="category">
                                        <option value="">Select Category</option>

                                        @foreach($mainCategories as $category)
                                            @php
                                                $mainIcon = getCategoryIcon($category->name); // Get main category's icon
                                            @endphp

                                            <!-- Main Category -->
                                            <option value="{{ $category->id }}" class="fw-bold">
                                                {{ $mainIcon }} {{ $category->name }}
                                            </option>

                                            <!-- Loop through Subcategories -->
                                            @if ($category->subcategories->count() > 0)
                                                @foreach ($category->subcategories as $subCategory)
                                                    @php
                                                        $subIcon = getCategoryIcon($subCategory->name, $category->name); // Inherit main category's icon
                                                    @endphp
                                                    <option value="{{ $subCategory->id }}">
                                                        &nbsp;&nbsp;&nbsp; ├─ {{ $subIcon }} {{ $subCategory->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Add Product</button>
                            </form>
                        </div>

                    @else
                        <p class="text-danger">You do not have permission to access this page.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

</x-app-layout>