<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <div class="text-center py-4 border-bottom">
                        <!-- Profile Picture with Edit Icon -->
                        <label for="profilePictureInput" class="position-relative d-inline-block">
                            <img src="{{ auth()->user()->profile_picture
    ? asset('storage/' . auth()->user()->profile_picture)
    : asset('assets/default.png') }}" alt="Profile Picture" class="rounded-circle mb-2"
                                width="80" height="80"
                                style="border: 3px solid #fff; object-fit: cover; aspect-ratio: 1/1;">

                            <!-- Edit Icon -->
                            <div class="position-absolute d-flex justify-content-center align-items-center"
                                style="width: 30px; height: 30px; bottom: 0; right: 0;">
                                <div class="bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="fas fa-pen text-white" style="font-size: 14px;"></i>
                                </div>
                            </div>

                            <input type="file" id="profilePictureInput" class="d-none" accept="image/*">
                        </label>

                        <!-- User Info -->
                        <h5 class="text-white">{{ auth()->user()->username }}</h5>
                        <p class="text-white mb-0">{{ auth()->user()->email }}</p>
                    </div>

                    <script>
                        document.getElementById('profilePictureInput').addEventListener('change', function (event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    document.getElementById('profilePicturePreview').src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    </script>

                    <!-- FontAwesome for the Pen Icon -->
                    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="#user-dashboard" data-toggle="pill">
                                <i class="fas fa-seedling me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-general" data-toggle="pill">
                                <i class="fas fa-leaf me-2"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-change-password" data-toggle="pill">
                                <i class="fas fa-lock me-2"></i> Change Password
                            </a>
                        </li>

                        @if(auth()->user()->role !== 'seller')
                            <div class="text-center mt-3">
                                <a href="{{ route('farmers.sell') }}" class="btn btn-success shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-tractor me-2"></i> Start Selling
                                </a>
                            </div>
                        @else
                            <div class="text-center mt-3">
                                <a href="{{ route('myshop') }}" class="btn text-white shadow-sm rounded-pill px-4 d-flex align-items-center justify-content-center" style="background-color: #8B5E3C;">
                                    <i class="fas fa-basket me-2"></i> My Shop
                                </a>
                            </div>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="tab-content">
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
                            <!-- To Ship -->
                            <div class="tab-pane fade show active" id="to-ship">
                                @if($ordersToShip->isEmpty())
                                    <p>No orders to ship.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToShip as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2 object-fit-cover"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-truncate">
                                                                            {{ $product->name }}
                                                                        </h6>
                                                                        <p class="small mb-1"><strong>Price:</strong>
                                                                            ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong>
                                                                            {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong>
                                                                            {{ $product->stock }}</p>

                                                                        <!-- Order Status Badge -->
                                                                        <span class="badge 
                                                                    {{ $order->status === 'pending' ? 'bg-warning' : 'bg-success' }}">
                                                                            {{ $order->status === 'pending' ? 'Pending' : 'Ready to Ship' }}
                                                                        </span>

                                                                        <!-- Cancel Order Button (Only for Pending or Accepted) -->
                                                                        @if($order->status === 'pending' || $order->status === 'accepted')
                                                                            <form action="{{ route('buyer.cancelOrder', $order->id) }}"
                                                                                method="POST" style="display:inline;">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <input type="hidden" name="status" value="canceled">
                                                                                <button type="submit" class="btn btn-danger btn-sm mt-2">
                                                                                    Cancel
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <!-- To Receive -->
                            <div class="tab-pane fade" id="to-receive">
                                @if($ordersToReceive->isEmpty())
                                    <p>No orders to receive.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToReceive as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2 object-fit-cover"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-truncate">
                                                                            {{ $product->name }}
                                                                        </h6>
                                                                        <p class="small mb-1"><strong>Price:</strong>
                                                                            ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong>
                                                                            {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong>
                                                                            {{ $product->stock }}</p>
                                                                        <span class="badge bg-info">
                                                                            Shipped(To Receive)
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- To Review -->
                            <div class="tab-pane fade" id="to-review">
                                @if($ordersToReview->isEmpty())
                                    <p>No orders to review.</p>
                                @else
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        @foreach($ordersToReview as $order)
                                            @foreach($order->orderItems as $orderItem)
                                                @php
                                                    $product = $orderItem->product;
                                                    $imageUrl = $product && $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('assets/products.jpg');
                                                @endphp

                                                @if($product)
                                                    <div class="col">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    <img src="{{ $imageUrl }}"
                                                                        class="img-fluid rounded-start p-2 object-fit-cover"
                                                                        alt="{{ $product->name }}"
                                                                        style="width: 100%; height: 100px; object-fit: cover;">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body p-2">
                                                                        <h6 class="card-title text-truncate">
                                                                            {{ $product->name }}
                                                                        </h6>
                                                                        <p class="small mb-1"><strong>Price:</strong>
                                                                            ${{ number_format($product->price, 2) }}</p>
                                                                        <p class="small mb-1"><strong>Qty:</strong>
                                                                            {{ $orderItem->quantity }}</p>
                                                                        <p class="small mb-2"><strong>Stock:</strong>
                                                                            {{ $product->stock }}</p>
                                                                            <span class="badge bg-success">Completed</span></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="alert alert-danger small p-2 text-center">
                                                            Product information not available
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

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
                                            <option value="male" {{ auth()->user()->gender === 'male' ? 'selected' : '' }}>
                                                Male</option>
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

                    <div class="tab-pane fade" id="account-change-password">
    <div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 380px; border-radius: 12px;">
        <form method="POST" action="{{ route('profile.updatePassword') }}">
            @csrf
            <h5 class="mb-3 text-center fw-bold">Change Password</h5>

            <div class="mb-3">
                <label class="form-label fw-medium">Current Password</label>
                <input type="password" name="current_password" class="form-control rounded-3" placeholder="Enter current password" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">New Password</label>
                <input type="password" name="new_password" class="form-control rounded-3" placeholder="Enter new password" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control rounded-3" placeholder="Re-enter new password" required>
            </div>

            <button type="submit" class="btn btn-success w-100 rounded-3 d-flex align-items-center justify-content-center">
                <i class="fas fa-lock me-2"></i> Update Password
            </button>
        </form>
    </div>
</div>

                </div>
            </main>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    @include('farmers.modal.sell')
</x-app-layout>