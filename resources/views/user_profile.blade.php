<x-app-layout>
    <div class="container-fluid">
        <div class="row mt-4">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                <div class="text-center py-4 border-bottom">
                        <!-- Profile Picture with Edit Icon -->
                        <label for="profilePictureInput" class="position-relative d-inline-block">
                        <img src="{{ auth()->user()->profile_picture 
                                ? asset('storage/' . auth()->user()->profile_picture) 
                                : asset('assets/default.png') }}" 
                                alt="Profile Picture" 
                                class="rounded-circle mb-2" 
                                width="80" height="80"
                                style="border: 3px solid #fff; object-fit: cover; aspect-ratio: 1/1;">

                                <!-- Edit Icon -->
                                <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center">
                                    <div class="bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width: 30px; height: 30px; position: absolute; bottom: 5px; right: 5px;">
                                        <i class="fas fa-pen text-white"></i>
                                    </div>
                                </div>

                                <input type="file" id="profilePictureInput" class="d-none" accept="image/*">
                        </label>

                        <!-- User Info -->
                        <h5 class="text-white">{{ auth()->user()->username }}</h5>
                        <p class="text-white mb-0">{{ auth()->user()->email }}</p>
                    </div>

                    <script>
                        document.getElementById('profilePictureInput').addEventListener('change', function(event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
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
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-general" data-toggle="pill">
                                <i class="fas fa-cog me-2"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#account-change-password" data-toggle="pill">
                                <i class="fas fa-lock me-2"></i> Change Password
                            </a>
                        </li>
                        @if(auth()->user()->role !== 'seller')
                            <li class="nav-item mt-3 text-center">
                                <a href="{{ route('farmers.sell') }}" class="btn btn-success">
                                    <i class="fas fa-store me-2"></i> Start Selling
                                </a>
                            </li>
                        @endif
                        @if(auth()->user()->role === 'seller')
                            <li class="nav-item mt-2 text-center">
                                <a href="{{ route('myshop') }}" class="btn btn-primary">
                                    My Shop
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
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
                    </div>
                    
                    <div class="tab-pane fade" id="account-change-password">
                        <form method="POST" action="{{ route('profile.updatePassword') }}">
                            @csrf
                            <div>
                                <label for="current_password">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div>
                                <label for="new_password">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div>
                                <label for="new_password_confirmation">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
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
