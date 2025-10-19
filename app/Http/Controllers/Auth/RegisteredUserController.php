<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

        // Combine Address
        $addressParts = [];
        if ($request->street) $addressParts[] = $request->street;
        $addressParts[] = "Brgy. " . $request->barangay;
        $addressParts[] = $request->town;
        $addressParts[] = "Pangasinan";
        $fullAddress = implode(', ', $addressParts);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $fullAddress,
            'password' => Hash::make($request->password),
        ]);

        // Send verification email
        event(new Registered($user));

        // ✅ Do NOT auto login yet
        return redirect()->route('verify.notice.guest')->with('status', 'Verification link sent. Please check your email to activate your account.');
    }

}
