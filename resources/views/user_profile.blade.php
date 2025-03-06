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
                                <a href="{{ route('myshop') }}" class="btn btn-primary w-100">
                                    My Shop
                                </a>
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