<x-app-layout>
    <div class="container light-style flex-grow-1 container-p-y">
        <div class="row mt-4">
            <!-- Sidebar Navigation -->
             <div class="col-md-3">
            <div class="card p-2">
                <!-- Profile Section -->
                <div class="card p-3 d-flex align-items-center bg-light rounded">
                    <div class="d-flex align-items-center w-100 position-relative">
                        <div class="position-relative">
                            <img src="{{ asset(auth()->user()->profile_picture ?? 'images/default-profile.jpg') }}"
                                alt="Profile Picture" class="rounded-circle" width="100" height="100">
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h4 class="font-weight-bold mb-1">{{ auth()->user()->username }}</h4>
                            <p class="text-muted mb-0"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <!-- Navigation Links -->
                <div class="card p-2 d-flex flex-column" style="min-height: 500px;">
                    <div class="list-group">
                        <!-- Always visible (for both buyers & sellers) -->
                        <a class="list-group-item list-group-item-action active" data-toggle="pill"
                            href="#user-dashboard">Dashboard</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill"
                            href="#account-general">General Settings</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill"
                            href="#account-change-password">Change Password</a>

                        <!-- Button (Push to Right) -->
                        @if(auth()->user()->role !== 'seller')
                            <div class="mt-3 text-center">
                                <a href="{{ route('farmers.sell') }}" class="btn btn-success" data-toggle="modal"
                                    data-target="#ModalCreate">
                                    <i class="fas fa-store mr-2"></i> Start Selling
                                </a>
                            </div>
                        @endif

                        <!-- Only for sellers -->
                        @if(auth()->user()->role === 'seller')
                            <div class="dropdown mt-2">
                                <button class="btn btn-primary dropdown-toggle w-100" 
                                    type="button" id="myShopDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    My Shop
                                </button>
                                <div class="dropdown-menu w-100" aria-labelledby="myShopDropdown">
                                    <a class="dropdown-item tab-link" href="#order-status">Order Status</a>
                                    <a class="dropdown-item tab-link" href="#my-products">My Products</a>
                                    <a class="dropdown-item text-success tab-link" href="#add-product">
                                        <i class="fas fa-plus-circle"></i> Add Product
                                    </a>
                                </div>
                            </div>
                        @endif
                        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                        <script>
                            $(document).ready(function () {
                                $('.tab-link').click(function (e) {
                                    e.preventDefault();
                                    $('.tab-pane').removeClass('show active');
                                    $($(this).attr('href')).addClass('show active');
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card p-3">
                    <div class="tab-content">
                        <!-- Dashboard Section -->
                        <div class="tab-pane fade show active" id="user-dashboard">
                            <h5>Your Orders</h5>
                            <ul class="nav nav-tabs" id="orderTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="pill" href="#to-ship">To Ship</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#to-receive">To Receive</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="pill" href="#to-review">To Review</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-2">
                                <div class="tab-pane fade show active" id="to-ship">
                                    <p>No orders to ship.</p>
                                </div>
                                <div class="tab-pane fade" id="to-receive">
                                    <p>No orders to receive.</p>
                                </div>
                                <div class="tab-pane fade" id="to-review">
                                    <p>No orders to review.</p>
                                </div>
                            </div>
                        </div>

                        <!-- General Settings -->
                        <div class="tab-pane fade" id="account-general">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" class="form-control" name="username"
                                                value="{{ old('username', auth()->user()->username) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ old('phone', auth()->user()->phone) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Birthdate</label>
                                            <input type="date" class="form-control" name="birthdate"
                                                value="{{ old('birthdate', auth()->user()->birthdate) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ old('name', auth()->user()->name) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="male" {{ auth()->user()->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ auth()->user()->gender === 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email', auth()->user()->email) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>


                        <!-- Change Password Section -->
                        <div class="tab-pane fade" id="account-change-password">
                            <form method="POST" action="{{ route('profile.updatePassword') }}">
                                @csrf

                                <div>
                                    <label for="current_password">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                    @error('current_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    @error('new_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="new_password_confirmation">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control"
                                        required>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>



                        <!-- Seller's Product Management --> 
                        @if(auth()->user()->role === 'seller')
                            <!-- Order Status Section -->
                            <div class="tab-pane fade" id="order-status">
                                <h5>Order Status</h5>
                                <p>View and manage the status of your orders here.</p>
                            </div>

                            <!-- My Products Section -->
                            <div class="tab-pane fade" id="my-products">
                                <h5>My Products</h5>
                                <p>Manage and edit your existing products.</p>
                                <div class="row">
                                    @foreach(auth()->user()->products as $product) <!-- Loop through the seller's products -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <!-- Display Product Image -->
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                                    alt="{{ $product->name }}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product->name }}</h5>
                                                    <p class="card-text">{{ $product->description }}</p>
                                                    <p class="card-text"><strong>${{ $product->price }}</strong></p>

                                                    <!-- Display Stock Quantity -->
                                                    <p class="card-text">
                                                        <strong>Stock:</strong> {{ $product->stock }} 
                                                    </p>

                                                    <!-- Edit Button (Triggers Modal) -->
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#editProductModal{{ $product->id }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>

                                                    <!-- Delete Form -->
                                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
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
                                        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" role="dialog"
                                            aria-labelledby="editProductModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Product</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')

                                                            <!-- Product Image -->
                                                            <div class="form-group">
                                                                <label>Product Image</label>
                                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                                <small>Leave empty to keep existing image</small>
                                                            </div>

                                                            <!-- Product Name -->
                                                            <div class="form-group">
                                                                <label>Product Name</label>
                                                                <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                                                            </div>

                                                            <!-- Product Description -->
                                                            <div class="form-group">
                                                                <label>Description</label>
                                                                <textarea class="form-control" name="description" rows="4" required>{{ $product->description }}</textarea>
                                                            </div>

                                                            <!-- Product Price -->
                                                            <div class="form-group">
                                                                <label>Price</label>
                                                                <input type="number" class="form-control" name="price" step="0.01"
                                                                    value="{{ $product->price }}" required>
                                                            </div>

                                                            <!-- Product Stock -->
                                                            <div class="form-group">
                                                                <label>Stock Quantity</label>
                                                                <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" required min="0">
                                                            </div>

                                                            <!-- Category -->
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="form-control" name="category_id" required>
                                                                    @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" 
                                                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Submit Button -->
                                                            <button type="submit" class="btn btn-success">Update Product</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Add New Product Section -->
                            <div class="tab-pane fade" id="add-product">
                                <h5>Add New Product</h5>
                                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Product Image -->
                                    <div class="form-group">
                                        <label>Product Image</label>
                                        <input type="file" class="form-control" name="image" accept="image/*" required>
                                    </div>

                                    <!-- Product Name -->
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>

                                    <!-- Product Description -->
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="4" required></textarea>
                                    </div>

                                    <!-- Product Price -->
                                    <div class="form-group">
                                        <label>Price</label>
                                        <input type="number" class="form-control" name="price" step="0.01" required>
                                    </div>

                                    <!-- Product Stock -->
                                    <div class="form-group">
                                        <label>Stock Quantity</label>
                                        <input type="number" class="form-control" name="stock" required min="0">
                                    </div>

                                    <!-- Category -->
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" name="category_id" required>
                                            <option value="">Select a Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-success">Add Product</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function uploadProfilePicture(event) {
            let file = event.target.files[0];
            if (file) {
                let formData = new FormData();
                formData.append('profile_picture', file);

                fetch({
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
                    .then(data => location.reload())
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    


    @include('farmers.modal.sell')
</x-app-layout>