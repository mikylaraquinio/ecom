<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">Checkout</h2>

        @if($cartItems->count() > 0)
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start">

                        <!-- Left Side (Shipping Address + Cart Items) -->
                        <div class="w-70">
                            <!-- Shipping Address -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>Shipping Address</h5>
                                    <!-- Edit Address Button (inside Checkout Form) -->
                                    <button type="button" class="btn btn-secondary btn-sm" id="edit-address-btn">
                                        Edit
                                    </button>
                                </div>

                                @if($user->addresses->count() > 0)
                                                        @php
                                                            // Check if a selected address exists in session or request input
                                                            $selectedAddressId = session('selected_address_id') ?? request()->input('address_id');
                                                            $selectedAddress = $user->addresses->where('id', $selectedAddressId)->first() ?? $user->addresses->first();
                                                        @endphp
                                                        <div class="card mb-3" id="displayed-address">
                                                            <div class="card-body">
                                                                <div class="card-body">
                                                                    <strong>{{ $selectedAddress->full_name }} -
                                                                        {{ $selectedAddress->mobile_number }}</strong><br>
                                                                    {{ $selectedAddress->floor_unit_number }}, {{ $selectedAddress->barangay }},
                                                                    {{ $selectedAddress->city }},
                                                                    {{ $selectedAddress->province }}<br>
                                                                    {{ $selectedAddress->notes }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="address_id" value="{{ $selectedAddress->id }}">
                                @else
                                    <button class="btn btn-success" id="add-address-btn">+ Add Address</button>
                                @endif
                            </div>

                            <!-- Cart Items -->
                            @foreach($cartItems as $cartItem)
                                <div class="card mb-3">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <img src="{{ asset('storage/' . $cartItem->product->image) }}"
                                                alt="{{ $cartItem->product->name }}" width="80">
                                            <strong>{{ $cartItem->product->name }}</strong>
                                            <p>Price: ₱{{ number_format($cartItem->product->price, 2) }}</p>
                                            <p>Quantity: {{ $cartItem->quantity }}</p>
                                        </div>
                                        <input type="checkbox" name="selected_items[]" value="{{ $cartItem->id }}" checked>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Right Side (Payment Method) -->
                        <div class="w-30">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-3">Payment Method</h5>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card"
                                            value="credit_card" checked required>
                                        <label class="form-check-label" for="credit_card">Credit Card</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal"
                                            value="paypal">
                                        <label class="form-check-label" for="paypal">PayPal</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                            value="cod">
                                        <label class="form-check-label" for="cod">Cash on Delivery</label>
                                    </div>
                                    <button type="button" id="proceed-btn" class="btn btn-primary w-100 mt-3">Proceed to
                                        Checkout</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <p>Your cart is empty. <a href="{{ route('shop') }}">Go back to shop</a></p>
        @endif
    </div>

    {{-- Add Address Modal --}}
    <div id="addressModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header mb-4">
                <h4>Add New Shipping Address</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="addressId" value="">
                <div class="modal-text">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" class="form-control" placeholder="First Last">
                    </div>
                    <div class="mb-3">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label>Other Notes</label>
                        <input type="text" class="form-control" placeholder="Enter notes">
                    </div>
                </div>
                <div class="modal-address">
                    <div class="mb-3">
                        <label>Floor/Unit Number</label>
                        <input type="text" class="form-control" placeholder="Enter floor/unit number">
                    </div>
                    <div class="mb-3">
                        <label>Province</label>
                        <input type="text" class="form-control" placeholder="Enter province">
                    </div>
                    <div class="mb-3">
                        <label>City</label>
                        <input type="text" class="form-control" placeholder="Enter city">
                    </div>
                    <div class="mb-3">
                        <label>Barangay</label>
                        <input type="text" class="form-control" placeholder="Enter barangay">
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" id="cancelAddressBtn">Cancel</button>
                <button class="btn btn-primary" id="saveAddressBtn">Save</button>
            </div>
        </div>
    </div>
    <div id="editAddressModal" class="modal right-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h4>Edit Address</h4>
                <button class="btn btn-success" id="openAddAddressModal">Add</button>
            </div>
            <div class="modal-body">
                @foreach($addresses as $address)
                    <div class="address-card">
                        <input type="radio" name="selected_address" id="address-{{ $address->id }}"
                            value="{{ $address->id }}" class="form-check-input"
                            @if(session('selected_address_id') == $address->id) checked @endif>

                        <label for="address-{{ $address->id }}">
                            <strong>{{ $address->full_name }} - {{ $address->mobile_number }}</strong><br>
                            {{ $address->floor_unit_number }}, {{ $address->barangay }}, {{ $address->city }},
                            {{ $address->province }}<br>
                            {{ $address->notes }}
                        </label>

                        <button class="edit-address-btn" data-id="{{ $address->id }}"
                            data-full_name="{{ $address->full_name }}" data-mobile_number="{{ $address->mobile_number }}"
                            data-notes="{{ $address->notes }}" data-floor_unit_number="{{ $address->floor_unit_number }}"
                            data-province="{{ $address->province }}" data-city="{{ $address->city }}"
                            data-barangay="{{ $address->barangay }}">
                            Edit
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" id="closeEditAddressBtn">Close</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addressModal = document.getElementById('addressModal');
            const editAddressModal = document.getElementById('editAddressModal');
            const addAddressBtn = document.getElementById('add-address-btn');
            const editAddressBtn = document.getElementById('edit-address-btn');
            const openAddAddressModal = document.getElementById('openAddAddressModal');
            const closeEditAddressBtn = document.getElementById('closeEditAddressBtn');
            const cancelAddressBtn = document.getElementById('cancelAddressBtn');
            const saveAddressBtn = document.getElementById('saveAddressBtn');
            const addressIdInput = document.getElementById('addressId');
            const displayedAddress = document.getElementById('displayed-address');;
            const addressInputs = document.querySelectorAll('input[name="selected_address"]');
            const proceedBtn = document.getElementById('proceed-btn');

            // Prevent "Edit Address" from triggering form submission
            editAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();  // Prevent unintended submission
                e.stopPropagation(); // Prevent bubbling issues
                editAddressModal.style.display = 'flex';
                console.log("Edit Address Clicked, Showing Modal...");
            });

            // Show Add Address Modal
            addAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                addressIdInput.value = '';
                clearAddressForm();
                addressModal.style.display = 'flex';
                editAddressModal.style.display = 'none';
                console.log("Adding New Address...");
            });

            // Open Add Address Modal from Edit Modal
            openAddAddressModal?.addEventListener('click', (e) => {
                e.preventDefault();
                clearAddressForm();
                addressIdInput.value = '';
                editAddressModal.style.display = 'none';
                addressModal.style.display = 'flex';
                console.log("Switching to Add Address Modal...");
            });

            // Close Edit Address Modal
            closeEditAddressBtn?.addEventListener('click', () => {
                editAddressModal.style.display = 'none';
            });

            // Close Add Address Modal
            cancelAddressBtn?.addEventListener('click', () => {
                addressModal.style.display = 'none';
            });

            // Show Edit Address Modal for Each Address
            document.querySelectorAll('.edit-address-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const data = this.dataset;
                    document.querySelector('input[placeholder="First Last"]').value = data.full_name;
                    document.querySelector('input[placeholder="Enter phone number"]').value = data.mobile_number;
                    document.querySelector('input[placeholder="Enter notes"]').value = data.notes;
                    document.querySelector('input[placeholder="Enter floor/unit number"]').value = data.floor_unit_number;
                    document.querySelector('input[placeholder="Enter province"]').value = data.province;
                    document.querySelector('input[placeholder="Enter city"]').value = data.city;
                    document.querySelector('input[placeholder="Enter barangay"]').value = data.barangay;
                    addressIdInput.value = data.id;
                    addressModal.style.display = 'flex';
                    editAddressModal.style.display = 'none';
                    console.log("Editing Address ID:", data.id);
                });
            });

            // Save Address Functionality
            saveAddressBtn?.addEventListener('click', (e) => {
                e.preventDefault();

                const data = {
                    full_name: document.querySelector('input[placeholder="First Last"]').value,
                    mobile_number: document.querySelector('input[placeholder="Enter phone number"]').value,
                    notes: document.querySelector('input[placeholder="Enter notes"]').value,
                    floor_unit_number: document.querySelector('input[placeholder="Enter floor/unit number"]').value,
                    province: document.querySelector('input[placeholder="Enter province"]').value,
                    city: document.querySelector('input[placeholder="Enter city"]').value,
                    barangay: document.querySelector('input[placeholder="Enter barangay"]').value,
                };

                const addressId = addressIdInput.value;
                let url = '{{ route("checkout.saveAddress") }}';
                let method = 'POST';

                if (addressId) {
                    url = `/checkout/updateAddress/${addressId}`;
                    method = 'PUT';
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(responseData => {
                        if (responseData.success) {
                            alert('Address saved successfully!');
                            addressModal.style.display = 'none';
                            location.reload();
                        } else {
                            alert(responseData.error || 'Something went wrong!');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            document.querySelectorAll('.form-check-input[name="selected_address"]').forEach(input => {
                input.addEventListener('change', function () {
                    const selectedAddress = this.closest('.address-card');

                    // Extracting details safely
                    const fullName = selectedAddress.querySelector("strong").innerText.trim();
                    const addressLines = selectedAddress.querySelector("label").innerHTML.split("<br>");

                    // Ensure values exist before accessing them
                    const floorUnitNumber = addressLines[1]?.trim() || "";
                    const addressParts = addressLines[2]?.split(",").map(part => part.trim()) || [];
                    const notes = addressLines[3]?.trim() || "";

                    // Construct a properly formatted address
                    const formattedAddress = [
                        floorUnitNumber,
                        ...addressParts.filter(part => part) // Remove any empty values
                    ].join(", ");

                    // Update displayed address properly
                    document.getElementById("displayed-address").innerHTML = `
                        <div class="card-body">
                            <strong>${fullName}</strong><br>
                            ${formattedAddress}<br>
                            ${notes}
                        </div>
                    `;

                    // Update hidden input field with selected address ID
                    document.querySelector('input[name="address_id"]').value = this.value;

                    console.log("Address Selected:", this.value);
                });
            });


            // Proceed to Checkout
            proceedBtn?.addEventListener('click', function (e) {
                e.preventDefault();

                const selectedAddress = document.querySelector('input[name="address_id"]').value;
                if (!selectedAddress) {
                    alert('Please select a shipping address.');
                    return;
                }

                const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
                if (!selectedPayment) {
                    alert('Please select a payment method.');
                    return;
                }

                const selectedItems = Array.from(document.querySelectorAll("input[name='selected_items[]']:checked"))
                    .map(item => item.value);

                if (selectedItems.length === 0) {
                    alert('Please select at least one product.');
                    return;
                }

                console.log("Proceeding with checkout...");
                console.log("Address ID:", selectedAddress);
                console.log("Payment Method:", selectedPayment.value);
                console.log("Selected Items:", selectedItems);

                fetch("{{ route('checkout.process') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        address_id: selectedAddress,
                        selected_items: selectedItems,
                        payment_method: selectedPayment.value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.error || "Something went wrong!");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });

            // Helper Functions
            function clearAddressForm() {
                document.querySelectorAll('#addressModal input').forEach(input => input.value = '');
            }

            function updateDisplayedAddress(address) {
                displayedAddress.innerHTML = `
            <div class="card-body">
                <strong>${address.full_name} - ${address.mobile_number}</strong><br>
                ${address.floor_unit_number}, ${address.barangay}, ${address.city}, ${address.province}<br>
                ${address.notes}
            </div>`;
            }
        });
    </script>

    <style>
        .w-70 {
            width: 70%;
        }

        .w-30 {
            width: 28%;
        }

        /* Form Checkbox */
        .form-check-input {
            accent-color: rgb(13, 226, 24);
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        /* General Modal Styling */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Right Side Modal */
        .right-modal {
            justify-content: flex-end;
            display: flex;
            align-items: flex-start;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Modal Header */
        .modal-header {
            text-align: left;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Modal Body for Addresses and Text */
        .modal-body {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        /* Left and Right Sections */
        .modal-text,
        .modal-address {
            width: 48%;
        }

        /* Right Side Modal Content */
        .right-modal .modal-content {
            width: 400px;
            height: 100%;
            position: absolute;
            right: 0;
            top: 0;
            box-shadow: -3px 0 10px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            padding: 20px;
        }

        /* Actions (Buttons) */
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        /* Form Controls */
        .form-control {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-check-input {
            accent-color: rgb(13, 226, 24);
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
        }

        .address-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 14px;
            line-height: 1.6;
            width: 100%;
            height: 45%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .address-card.selected {
            border-color: rgb(13, 226, 24);
            background-color: #eaffea;
        }

        .address-card label {
            flex: 1;
            cursor: pointer;
        }


        .address-card h4 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .address-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .address-card button {
            margin-top: 10px;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* Scrollable Modal Body */
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Scrollbar Styling */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 8px;
        }
    </style>

</x-app-layout>