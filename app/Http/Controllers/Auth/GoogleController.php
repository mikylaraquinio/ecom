<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class GoogleController extends Controller
{
    public function redirect()
    {
        // 👇 Normal redirect to Google's account picker
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // ✅ Find existing user or create new without verifying immediately
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                // 🚫 remove auto-verification
                'password' => bcrypt(Str::random(16)),
            ]
        );

        // ✅ Fire Laravel's "Registered" event so it sends a verification email
        if (! $user->hasVerifiedEmail()) {
            event(new Registered($user));
        }

        // ✅ Log in the user
        Auth::login($user);

        // ✅ Redirect to verify email page if not verified
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // ✅ Otherwise, go to welcome page
        return redirect('/welcome');
    }
}
