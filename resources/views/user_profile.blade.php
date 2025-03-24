<x-app-layout>
    <div class="light-style flex-grow-1 container-p-y">
        <div class="row mt-4">
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px;">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                        <span class="fs-4">Sidebar</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#user-dashboard" class="nav-link active" aria-current="page" data-toggle="pill">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="#account-general" class="nav-link link-dark" data-toggle="pill">
                                General Settings
                            </a>
                        </li>
                        <li>
                            <a href="#account-change-password" class="nav-link link-dark" data-toggle="pill">
                                Change Password
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset(auth()->user()->profile_picture ?? 'images/default-profile.jpg') }}" alt="" width="32" height="32" class="rounded-circle me-2">
                            <strong>{{ auth()->user()->username }}</strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                            <li><a class="dropdown-item" href="#">New project...</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card p-3">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="user-dashboard">
                            <h5>Your Orders</h5>
                            <p>No orders available.</p>
                        </div>
                        <div class="tab-pane fade" id="account-general">
                            <h5>General Settings</h5>
                        </div>
                        <div class="tab-pane fade" id="account-change-password">
                            <h5>Change Password</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
