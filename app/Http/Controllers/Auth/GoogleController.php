<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['google' => 'Failed to authenticate with Google.']);
        }

        // ✅ Only allow login if email already exists
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if (!$existingUser) {
            return redirect('/login')->withErrors([
                'google' => 'This Google account is not registered. Please sign up first.'
            ]);
        }

        // ✅ Link Google ID if not already linked
        if (is_null($existingUser->google_id)) {
            $existingUser->google_id = $googleUser->getId();
            $existingUser->save();
        }

        // ✅ Mark email as verified if Google confirms it
        if (empty($existingUser->email_verified_at) && ($googleUser->user['verified_email'] ?? false)) {
            $existingUser->email_verified_at = now();
            $existingUser->save();
        }

        // ✅ Log user in
        Auth::login($existingUser);

        // ✅ Redirect properly
        if (!$existingUser->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect('/welcome');
    }
}
