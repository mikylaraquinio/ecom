@if(auth()->check())
    <form action="{{ route('farmers.storeSeller') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Seller Registration Modal -->
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
                                    <label class="block text-sm font-medium text-gray-700 flex items-center">
                                        {{ __('Government ID') }}
                                        <span class="ml-2 text-xs text-gray-500">(e.g., National ID, Drivers License,SSS, Voter's ID)</span>
                                    </label>
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

                        <!-- Terms and Conditions Agreement -->
                        <div class="flex items-center">
                            <input type="checkbox" name="terms" id="terms" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200">
                            <label for="terms" class="ml-2 text-sm text-gray-700">
                                I agree to the 
                                <a href="#" class="text-indigo-600 hover:underline" data-toggle="modal" data-target="#termsModal">
                                    Terms & Conditions
                                </a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md transition">
                            Register as Seller
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content bg-white rounded-2xl shadow-lg">
                
                <div class="modal-header bg-gray-100 px-6 py-4 border-b">
                    <h5 class="modal-title text-lg font-semibold" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="text-gray-600 hover:text-gray-800 transition" data-dismiss="modal" aria-label="Close">
                        ✖
                    </button>
                </div>

                <div class="modal-body px-6 py-4 space-y-4 text-gray-700 text-sm max-h-96 overflow-y-auto">
                    <p><strong>Introduction</strong><br>
                    Welcome to FarmSmart. By using our platform, you agree to these Terms and Conditions, designed to connect livestock farmers with buyers and promote sustainable farming.</p>

                    <p><strong>Services Provided</strong></p>
                    <ul class="list-disc list-inside">
                        <li>Digital marketplace for livestock transactions</li>
                        <li>Access to market data and pricing</li>
                        <li>Resources for sustainable livestock management</li>
                        <li>Networking opportunities between farmers and buyers</li>
                    </ul>

                    <p><strong>User Responsibilities</strong></p>
                    <ul class="list-disc list-inside">
                        <li>Provide accurate information</li>
                        <li>Follow fair trade and ethical practices</li>
                        <li>Secure account credentials</li>
                    </ul>

                    <p><strong>Privacy Policy</strong><br>
                    Your privacy matters to us. By using this platform, you agree to the data practices in our Privacy Policy.</p>

                    <p><strong>Contact Us</strong><br>
                    Questions? Reach us at <a href="mailto:FarmSmart@yahoo.com" class="text-indigo-600 hover:underline">FarmSmart@yahoo.com</a> / 09123456789.</p>
                </div>

                <div class="modal-footer px-6 py-4 border-t">
                    <button type="button" class="w-full bg-indigo-600 text-white py-2 rounded-xl hover:bg-indigo-700 transition" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.querySelector("form").addEventListener("submit", function(event) {
        console.log("Submitting form:", this.method, this.action);
    });
</script>
