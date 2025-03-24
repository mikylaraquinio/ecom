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

                        <!-- Order Status Section -->
                        <div class="tab-pane fade show active" id="order-status" role="tabpanel">
                            <h5>Order Status</h5>
                            <p>View and manage the status of your orders here.</p>
                        </div>

                        <!-- My Shop Section -->
                        <div class="tab-pane fade" id="my-shop" role="tabpanel">
                            <h5>My Shop</h5>

                            @if(auth()->check() && auth()->user()->role === 'seller')
                                @if(auth()->user()->shop)
                                    <div class="shop-details">
                                        <h5>{{ auth()->user()->shop->name }}</h5>
                                        <p><strong>Farm Name:</strong> {{ auth()->user()->storeseller->farm_name ?? 'N/A' }}</p>
                                        <p><strong>Location:</strong> {{ auth()->user()->storeseller->farm_address ?? 'N/A' }}</p>

                                        @if(auth()->user()->storeseller->gov_id)
                                            <p><strong>Government ID:</strong>
                                                <a href="{{ asset('storage/' . auth()->user()->storeseller->gov_id) }}"
                                                    target="_blank">View</a>
                                            </p>
                                        @endif

                                        @if(auth()->user()->storeseller->farm_certificate)
                                            <p><strong>Farm Certificate:</strong>
                                                <a href="{{ asset('storage/' . auth()->user()->storeseller->farm_certificate) }}"
                                                    target="_blank">View</a>
                                            </p>
                                        @endif

                                        <p><strong>Mobile Money:</strong>
                                            {{ auth()->user()->storeseller->mobile_money ?? 'Not provided' }}</p>
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
                                                                <input type="file" class="form-control" name="image"
                                                                    accept="image/*">
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

                                                            <div class="form-group position-relative">
                                                                <label for="category-dropdown"
                                                                    class="fw-bold text-success">Category</label>
                                                                <div class="dropdown w-100" data-bs-auto-close="outside">
                                                                    <button class="btn btn-outline-success dropdown-toggle w-100"
                                                                        type="button" id="categoryDropdownButton"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        Select Category
                                                                    </button>
                                                                    <div class="dropdown-menu w-100 p-0 position-absolute big-dropdown"
                                                                        id="categoryDropdown">
                                                                        <div class="d-flex">
                                                                            <div class="shortcut-links p-2 border-end">
                                                                                <ul class="list-group">
                                                                                    <li
                                                                                        class="list-group-item fw-bold text-success text-center">
                                                                                        Quick Access</li>
                                                                                    @foreach($mainCategories as $category)
                                                                                        <li>
                                                                                            <a href="#"
                                                                                                class="list-group-item list-group-item-action shortcut-category"
                                                                                                data-target="category-section-{{ $category->id }}">
                                                                                                ⏩
                                                                                                {!! getCategoryIcon($category->name) !!}
                                                                                                {{ $category->name }}
                                                                                            </a>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                            <div class="flex-grow-1 p-2">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    @foreach($mainCategories as $category)
                                                                                        <li id="category-section-{{ $category->id }}">
                                                                                            <a href="#"
                                                                                                class="dropdown-item category-option"
                                                                                                data-id="{{ $category->id }}">
                                                                                                {!! getCategoryIcon($category->name) !!}
                                                                                                {{ $category->name }}
                                                                                            </a>
                                                                                        </li>
                                                                                        @foreach($category->children as $subCategory)
                                                                                            <li>
                                                                                                <a href="#"
                                                                                                    class="dropdown-item category-option ms-3"
                                                                                                    data-id="{{ $subCategory->id }}">
                                                                                                    &nbsp;&nbsp; ├─
                                                                                                    {!! getCategoryIcon($subCategory->name, $category->name) !!}
                                                                                                    {{ $subCategory->name }}
                                                                                                </a>
                                                                                            </li>
                                                                                            @foreach($subCategory->children as $subSubCategory)
                                                                                                <li>
                                                                                                    <a href="#"
                                                                                                        class="dropdown-item category-option ms-5"
                                                                                                        data-id="{{ $subSubCategory->id }}">
                                                                                                        &nbsp;&nbsp;&nbsp;&nbsp; ├─
                                                                                                        {!! getCategoryIcon($subSubCategory->name, $subCategory->name, $category->name) !!}
                                                                                                        {{ $subSubCategory->name }}
                                                                                                    </a>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endforeach
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="category_id" id="selectedCategoryId">
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
                                <div class="form-group position-relative">
                                    <label for="category-dropdown" class="fw-bold text-success"></label>
                                    <div class="dropdown w-100" data-bs-auto-close="outside">
                                        <button class="btn btn-outline-success dropdown-toggle w-100" type="button"
                                            id="categoryDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            Select Category
                                        </button>
                                        <div class="dropdown-menu w-100 p-0 position-absolute big-dropdown"
                                            id="categoryDropdown">
                                            <div class="d-flex">
                                                <div class="shortcut-links p-2 border-end">
                                                    <ul class="list-group">
                                                        <li class="list-group-item fw-bold text-success text-center">Quick
                                                            Access</li>
                                                        @foreach($mainCategories as $category)
                                                            <li>
                                                                <a href="#"
                                                                    class="list-group-item list-group-item-action shortcut-category"
                                                                    data-target="category-section-{{ $category->id }}">
                                                                    ⏩ {!! getCategoryIcon($category->name) !!}
                                                                    {{ $category->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="flex-grow-1 p-2">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($mainCategories as $category)
                                                            <li id="category-section-{{ $category->id }}">
                                                                <a href="#" class="dropdown-item category-option"
                                                                    data-id="{{ $category->id }}">
                                                                    {!! getCategoryIcon($category->name) !!}
                                                                    {{ $category->name }}
                                                                </a>
                                                            </li>
                                                            @foreach($category->children as $subCategory)
                                                                <li>
                                                                    <a href="#" class="dropdown-item category-option ms-3"
                                                                        data-id="{{ $subCategory->id }}">
                                                                        &nbsp;&nbsp; ├─
                                                                        {!! getCategoryIcon($subCategory->name, $category->name) !!}
                                                                        {{ $subCategory->name }}
                                                                    </a>
                                                                </li>
                                                                @foreach($subCategory->children as $subSubCategory)
                                                                    <li>
                                                                        <a href="#" class="dropdown-item category-option ms-5"
                                                                            data-id="{{ $subSubCategory->id }}">
                                                                            &nbsp;&nbsp;&nbsp;&nbsp; ├─
                                                                            {!! getCategoryIcon($subSubCategory->name, $subCategory->name, $category->name) !!}
                                                                            {{ $subSubCategory->name }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            @endforeach
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="category_id" id="selectedCategoryId">
                                </div>
                                <br>
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
        document.addEventListener('DOMContentLoaded', function () {
            function initializeCategoryDropdown(modal) {
                const dropdownMenu = modal.querySelector('.big-dropdown');
                const categoryButton = modal.querySelector('#categoryDropdownButton');
                const hiddenInput = modal.querySelector('#selectedCategoryId');

                // Handle category selection
                modal.querySelectorAll('.category-option').forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        const categoryId = this.getAttribute('data-id');
                        const categoryName = this.innerText.trim();

                        if (hiddenInput && categoryButton) {
                            hiddenInput.value = categoryId;
                            categoryButton.innerText = categoryName;
                        }
                    });
                });

                // Handle quick access scrolling
                modal.querySelectorAll('.shortcut-category').forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation(); // Prevent dropdown from closing

                        const targetId = this.getAttribute('data-target');
                        const targetElement = modal.querySelector(`#${targetId}`);

                        if (targetElement && dropdownMenu) {
                            const targetPosition = targetElement.offsetTop - dropdownMenu.offsetTop;

                            dropdownMenu.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
            }

            // Initialize dropdowns for all modals (Edit and Add)
            document.querySelectorAll('.modal').forEach(modal => {
                initializeCategoryDropdown(modal);
            });

            // Also explicitly target the Add Product form
            const addProductForm = document.querySelector('#add-product');
            if (addProductForm) {
                initializeCategoryDropdown(addProductForm);
            }
        });
    </script>

    <style>
        .form-container {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .category-shortcuts {
            display: flex;
            flex-direction: column;
            gap: 5px;
            max-width: 100px;
            position: sticky;
            top: 10px;
            height: max-content;
        }

        .category-shortcuts button {
            padding: 3px 5px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-align: left;
            width: 100%;
        }

        .category-shortcuts button:hover {
            background: rgb(255, 255, 255);
        }

        .form-group {
            flex-grow: 1;
        }

        .big-dropdown {
            max-height: 400px;
            /* Set the height you want for your dropdown */
            overflow-y: auto;
            width: 800px;
            /* Make the dropdown bigger */
        }

        .shortcut-links {
            position: sticky;
            top: 0;
            height: 100%;
            background: white;
        }
    </style>

</x-app-layout>