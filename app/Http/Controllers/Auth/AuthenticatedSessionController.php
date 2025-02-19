<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Define allowed email domains
        $allowedDomains = ['gmail.com', 'yahoo.com', 'icloud.com'];
        
        // Extract domain from email
        $email = $request->input('email');
        $domain = substr(strrchr($email, "@"), 1);

        // Validate email and password
        $request->validate([
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($allowedDomains, $domain) {
                    if (!in_array($domain, $allowedDomains)) {
                        $fail("Only Gmail, Yahoo, and iCloud emails are allowed.");
                    }
                },
            ],
            'password' => 'required',
        ]);

        // Attempt authentication
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return back()->withErrors(['email' => 'Invalid login credentials.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('welcome', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
