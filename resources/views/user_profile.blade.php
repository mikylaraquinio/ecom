<x-app-layout>
    <div class="container light-style flex-grow-1 container-p-y">
        <!-- Profile Section -->
        <div class="card p-3 d-flex align-items-center bg-light rounded">
    <div class="d-flex align-items-center w-100 position-relative">
        
        <!-- Profile Picture -->
        <div class="position-relative">
            <img src="{{ asset(auth()->user()->profile_picture ?? 'images/default-profile.jpg') }}" 
                 alt="Profile Picture" class="rounded-circle" width="100" height="100">
            <label for="profile-pic-upload" class="position-absolute" 
                   style="bottom: 0; right: 0; background: rgba(0,0,0,0.5); border-radius: 50%; padding: 5px; cursor: pointer;">
                <i class="fas fa-camera text-white"></i>
            </label>
            <input type="file" id="profile-pic-upload" class="d-none" onchange="uploadProfilePicture(event)">
        </div>
        
        <!-- User Info (Take Remaining Space) -->
        <div class="ml-3 flex-grow-1">
            <h4 class="font-weight-bold mb-1">{{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0"><strong>Email:</strong> {{ auth()->user()->email }}</p>
        </div>

        <!-- Button (Push to Right) -->
        @if(auth()->user()->role !== 'seller')
            <div>
                <a href="{{ route('farmers.sell') }}" class="btn btn-success" data-toggle="modal" data-target="#ModalCreate">
                    <i class="fas fa-store mr-2"></i> Start Selling
                </a>
            </div>
        @endif

    </div>
</div>


        <div class="row mt-4">
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <div class="card p-2">
                    <div class="list-group">
                        <!-- Always visible (for both buyers & sellers) -->
                        <a class="list-group-item list-group-item-action active" data-toggle="pill" href="#user-dashboard">Dashboard</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill" href="#account-general">General Settings</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill" href="#account-change-password">Change Password</a>

                        <!-- Only for sellers -->
                        @if(auth()->user()->role === 'seller')
                            <a class="list-group-item list-group-item-action" data-toggle="pill" href="#order-status">Order Status</a>
                            <a class="list-group-item list-group-item-action" data-toggle="pill" href="#my-products">My Products</a>
                            <a class="list-group-item list-group-item-action text-success" data-toggle="pill" href="#add-product">
                                <i class="fas fa-plus-circle"></i> Add Product
                            </a>
                        @endif
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
                                <div class="tab-pane fade show active" id="to-ship"><p>No orders to ship.</p></div>
                                <div class="tab-pane fade" id="to-receive"><p>No orders to receive.</p></div>
                                <div class="tab-pane fade" id="to-review"><p>No orders to review.</p></div>
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
                                        <input type="text" class="form-control" name="username" value="{{ old('username', auth()->user()->username) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" value="{{ old('birthdate', auth()->user()->birthdate) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required>
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
                                        <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>

                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        </div>

                        <!-- Change Password Section -->
                        <div class="tab-pane fade" id="account-change-password">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Repeat New Password</label>
                                    <input type="password" class="form-control" name="new_password_confirmation" required>
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
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product->name }}</h5>
                                                    <p class="card-text">{{ $product->description }}</p>
                                                    <p class="card-text"><strong>${{ $product->price }}</strong></p>
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

                                    <!-- Category Selection -->
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" name="category_id" required>
                                            <option value="1">Fresh Produce</option>
                                            <option value="2">Dairy Products</option>
                                            <option value="3">Grains and Pulses</option>
                                            <option value="4">Meat and Poultry</option>
                                            <option value="5">Livestock</option>
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
                
                fetch( {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                  .then(data => location.reload())
                  .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


    @include('farmers.modal.sell')
</x-app-layout>