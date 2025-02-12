<form action="{{ route('profile.sell') }}" method="post" enctype="multipart/form-data"> 
    @csrf
    <div class="modal fade text-left" id="ModalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content bg-white shadow-xl rounded-2xl overflow-hidden">
                
                <!-- Modal Header -->
                <div class="modal-header bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                    <h4 class="modal-title text-lg font-semibold text-gray-800">{{ __('Seller Registration') }}</h4>
                    <button type="button" class="text-gray-600 hover:text-gray-800 transition" data-dismiss="modal" aria-label="Close">
                        ✖
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body px-6 py-6 space-y-6">
                    
                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="John Doe" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                            <input type="email" name="email" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="johndoe@example.com" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="+1234567890" required>
                    </div>

                    <!-- Farm Information -->
                    <div class="bg-gray-50 p-5 rounded-lg shadow-inner">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">{{ __('Farm Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Farm Name') }}</label>
                                <input type="text" name="farm_name" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="Green Valley Farms" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Farm Address') }}</label>
                                <input type="text" name="farm_address" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="123 Farm Road, City, Country" required>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Documents -->
                    <div class="bg-gray-50 p-5 rounded-lg shadow-inner">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">{{ __('Verification Documents') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Government ID') }}</label>
                                <input type="file" name="gov_id" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Farm Registration Certificate (if applicable)') }}</label>
                                <input type="file" name="farm_certificate" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Mobile Payment Number (Optional)') }}</label>
                            <input type="text" name="mobile_money" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-indigo-300 focus:border-indigo-400" placeholder="+1234567890">
                        </div>
                    </div>

                    <!-- Agreement -->
                    <div class="flex items-center">
                        <input type="checkbox" name="terms" id="terms" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200">
                        <label for="terms" class="ml-2 text-sm text-gray-700">
                            I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-800 transition">Terms & Conditions</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="reg-btn w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md transition">
                        Register as Seller
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
