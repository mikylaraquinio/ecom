<x-guest-layout>
    <div class="login-wrapper farm-bg" style="padding-top: 80px;">
        <div class="login-box farm-style">
            <div class="d-flex flex-column align-items-center mb-4">
                <img src="assets/logo.png" alt="FarmSmart Logo" class="mb-2" style="height: 50px">
                <h2 class="mt-2" style="color: #4C7737;">Join FarmSmart</h2>
                <p class="text-muted" style="font-size: 0.9rem;">Connect with farmers, buy fresh produce, grow your business.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Name -->
                        <div class="input-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="e.g. Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Phone -->
                        <div class="input-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="09XXXXXXXXX" value="{{ old('phone') }}" required pattern="^09\d{9}$">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <!-- Password -->
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Email -->
                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="e.g. juan@email.com" value="{{ old('email') }}" required>
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Address -->
                        <div class="input-group">
                            <label for="address">Complete Address</label>
                            <textarea id="address" name="address" rows="2" placeholder="e.g. Brgy. Sampaguita, San Juan, Batangas" required style="resize: none; padding: 0.5rem; border-radius: 5px; border: 1px solid #ccc;">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <button type="submit" class="farm-btn mt-3 w-100">Create Account</button>

                <div class="signup-text mt-3 text-center">
                    Already have an account? <a href="{{ route('login') }}">Log in here</a>
                </div>
            </form>
        </div>
    </div>
    @if (session('showOtpModal'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            let otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
        });
    </script>
@endif

<!-- OTP Modal 
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    
    </form>
  </div>
</div>-->
</x-guest-layout>
