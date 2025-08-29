@if(auth()->check())
<form action="{{ route('farmers.storeSeller') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Seller Registration Modal -->
    <div class="modal fade" id="ModalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-2xl shadow-lg border border-green-100">

                <!-- Header -->
                <div class="modal-header bg-green-100 px-6 py-4 border-b">
                    <h4 class="modal-title text-xl font-bold text-green-900">🌱 Register as a Seller</h4>
                    <button type="button" class="text-green-700 hover:text-green-900" data-dismiss="modal" aria-label="Close">✖</button>
                </div>

                <!-- Body -->
                <div class="modal-body px-6 py-6 space-y-8 bg-white">

                    <!-- Farm Info -->
                    <div>
                        <h5 class="text-lg font-semibold text-green-800 mb-4">🏡 Farm Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Farm Name</label>
                                <input type="text" name="farm_name" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Green Valley Farms">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Farm Address</label>
                                <input type="text" name="farm_address" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="123 Farm Road, City, Country">
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div>
                        <h5 class="text-lg font-semibold text-green-800 mb-4">📄 Verification Documents</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Government ID 
                                    <span class="text-xs text-gray-500 block">e.g., National ID, SSS, Voter's ID</span>
                                </label>
                                <input type="file" name="gov_id" class="mt-1 w-full p-2.5 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Farm Registration Certificate</label>
                                <input type="file" name="farm_certificate" class="mt-1 w-full p-2.5 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div>
                        <h5 class="text-lg font-semibold text-green-800 mb-4">💳 Payment (Optional)</h5>
                        <label class="text-sm font-medium text-gray-700">Mobile Payment Number</label>
                        <input type="text" name="mobile_money" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="e.g., 09123456789">
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-2">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1 h-5 w-5 text-green-600 rounded border-gray-300">
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the 
                            <a href="#" class="text-green-600 underline" data-toggle="modal" data-target="#termsModal">Terms & Conditions</a>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button 
    type="submit" 
    class="reg-btn"
    id="registerButton"
    disabled
>
    Register
</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
