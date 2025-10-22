<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|min:3|max:255|regex:/^[A-Za-z\s\-\'\.]+$/',
        'email' => [
            'required',
            'email',
            'max:100',
            'unique:users,email',
            function ($attribute, $value, $fail) {
                $allowedDomains = ['gmail.com', 'yahoo.com', 'icloud.com', 'phinmaed.com'];
                $domain = substr(strrchr($value, "@"), 1);
                if (!in_array($domain, $allowedDomains)) {
                    $fail("Only Gmail, Yahoo, iCloud, and Phinmaed emails are allowed.");
                }
            },
        ],
        'phone' => 'required|regex:/^09\d{9}$/|digits:11|unique:users,phone',
        'barangay' => 'required|string|max:100',
        'town' => 'required|string|max:100',
        'street' => 'nullable|string|max:150',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // ✅ Verify email format
    if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        return back()->withErrors(['email' => 'Invalid email address.']);
    }

    // ✅ Check if the email domain can receive mail
    $domain = substr(strrchr($request->email, "@"), 1);
    if (!checkdnsrr($domain, 'MX')) {
        return back()->withErrors([
            'email' => 'This email domain cannot receive mail. Please use a valid email.',
        ]);
    }

    // ✅ Build address
    $addressParts = [];
    if ($request->street) $addressParts[] = $request->street;
    $addressParts[] = "Brgy. " . $request->barangay;
    $addressParts[] = $request->town;
    $addressParts[] = "Pangasinan";
    $fullAddress = implode(', ', $addressParts);

    // ✅ Create user (unverified)
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $fullAddress,
        'password' => Hash::make($request->password),
    ]);

    // ✅ Create address record
    Address::create([
        'user_id' => $user->id,
        'full_name' => $user->name,
        'mobile_number' => $user->phone,
        'barangay' => $request->barangay,
        'city' => $request->town,
        'province' => 'Pangasinan',
        'floor_unit_number' => $request->street ?? '',
        'notes' => 'Default address from registration',
    ]);

   app(\App\Http\Controllers\Auth\VerifyEmailController::class)
    ->sendVerificationCode(new \Illuminate\Http\Request(), $user);


    // ✅ Redirect user to code entry page
    return redirect()->route('verification.code')
        ->with('status', 'A verification code has been sent to your email. Please enter it below to verify your account.');
}

}
