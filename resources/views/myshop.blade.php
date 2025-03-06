<x-app-layout>
    <div class="container py-4">
        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-md-3">
                <div class="list-group" id="sidebar-menu" role="tablist">
                    <a href="#order-status" class="list-group-item list-group-item-action active" data-bs-toggle="tab" role="tab">Order Status</a>
                    <a href="#my-shop" class="list-group-item list-group-item-action" data-bs-toggle="tab" role="tab">My Shop</a> <!-- Added My Shop tab -->
                    <a href="#my-products" class="list-group-item list-group-item-action" data-bs-toggle="tab" role="tab">My Products</a>
                    <a href="#add-product" class="list-group-item list-group-item-action" data-bs-toggle="tab" role="tab">Add Product</a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <div class="tab-content">
                    @if(auth()->check() && auth()->user()->role === 'seller')

                        <!-- Order Status Section -->
                        <div class="tab-pane fade show active" id="order-status" role="tabpanel">
                            <h5>Order Status</h5>
                            <p>View and manage the status of your orders here.</p>
                        </div>

                        <!-- My Shop Section -->
                        <!-- My Shop Section -->
<div class="tab-pane fade" id="my-shop" role="tabpanel">
    <h5>My Shop</h5>

    @if(auth()->check() && auth()->user()->role === 'seller')
        @if(auth()->user()->shop)
            <div class="shop-details">
                <h5>{{ auth()->user()->shop->name }}</h5>
                <p><strong>Shop Description:</strong> {{ auth()->user()->shop->description }}</p>
                <p><strong>Location:</strong> {{ auth()->user()->shop->location }}</p>
                <p><strong>Contact Info:</strong> {{ auth()->user()->shop->contact_info }}</p>
                <!-- Add more shop details here as needed -->
            </div>
        @else
            <p>Your shop is not yet set up. Please complete your registration.</p>
        @endif
    @else
        <p>You need to be a seller to view shop details.</p>
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
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product->name }}</h5>
                                                    <p class="card-text">{{ $product->description }}</p>
                                                    <p class="card-text"><strong>${{ number_format($product->price, 2) }}</strong></p>
                                                    <p class="card-text"><strong>Stock:</strong> {{ $product->stock }}</p>

                                                    <!-- Edit Button -->
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">
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
                                                        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="form-group">
                                                                <label>Product Image</label>
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
                                                            <div class="form-group">
                                                                <label>Stock Quantity</label>
                                                                <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" required min="0">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="form-control" name="category_id" required>
                                                                    @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
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
                                    <label>Category</label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Select a Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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

</x-app-layout>
