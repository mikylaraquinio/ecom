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
        <div>
            <a href="#" class="btn btn-success d-flex align-items-center">
                <i class="fas fa-store mr-2"></i> Start Selling
            </a>
        </div>

    </div>
</div>


        <div class="row mt-4">
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <div class="card p-2">
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action active" data-toggle="pill" href="#user-dashboard">Dashboard</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill" href="#account-general">General Settings</a>
                        <a class="list-group-item list-group-item-action" data-toggle="pill" href="#account-change-password">Change Password</a>
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
                                            <input type="text" class="form-control" name="username" value="{{ auth()->user()->username }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Birthdate</label>
                                            <input type="date" class="form-control" name="birthdate" value="{{ auth()->user()->birthdate }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <input type="text" class="form-control" name="gender" value="{{ auth()->user()->gender }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
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
</x-app-layout>
