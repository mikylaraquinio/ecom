<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        // ✅ Always re-fetch latest data from database (to get updated email_verified_at)
        $user->refresh();

        // ✅ Check if email is verified before login
        if (is_null($user->email_verified_at)) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                \Log::error('Failed to resend verification email', ['error' => $e->getMessage()]);
            }

            return back()->withErrors([
                'email' => 'Please verify your email before logging in. A new verification link has been sent.',
            ]);
        }

        // ✅ Passed all checks, proceed to login
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // ✅ Redirect based on role
        if ($user->is_admin) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('welcome');
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Log::info("User Logging Out", ['user_id' => Auth::id()]);

        // Logout user
        Auth::guard('web')->logout();

        // Invalidate session properly
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info("User Logged Out Successfully");

        return redirect('/');
    }
}
