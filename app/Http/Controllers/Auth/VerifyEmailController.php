<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VerifyEmailController extends Controller
{
    /**
     * Keep this for Laravel’s built-in link system (safe to retain)
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('welcome').'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('welcome').'?verified=1');
    }

    /**
     * ✅ Send a 6-digit OTP verification code via email
     */
    public function sendVerificationCode(Request $request, $user = null)
{
    // 🔹 If user not passed (via parameter), try to get the authenticated one
    $user = $user ?? $request->user();

    // 🔹 If still null, abort with a clear message
    if (!$user) {
        return back()->withErrors(['email' => 'User not found when sending verification code.']);
    }

    // 🔹 Generate 6-digit code
    $code = rand(100000, 999999);

    // 🔹 Store in database
    $user->update([
        'email_verification_code' => $code,
        'email_verification_expires_at' => \Carbon\Carbon::now()->addMinutes(10),
    ]);

    // 🔹 Send email (works with Hostinger SMTP)
    \Mail::raw("Your FarmSmart verification code is: {$code}", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Your FarmSmart Email Verification Code');
    });

    return back()->with('message', 'Verification code sent to your email!');
}


    /**
     * ✅ Verify the entered OTP code
     */
    public function verifyCode(Request $request)
{
    $request->validate(['code' => 'required|digits:6']);

    // Try to get user from session or database
    $user = $request->user();

    if (!$user && $request->has('email')) {
        $user = \App\Models\User::where('email', $request->input('email'))->first();
    }

    if (!$user) {
        return back()->withErrors(['email' => 'Unable to find user. Please re-enter your email.']);
    }

    if (
        $user->email_verification_code === $request->code &&
        $user->email_verification_expires_at &&
        $user->email_verification_expires_at->isFuture()
    ) {
        $user->markEmailAsVerified();
        $user->update([
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ]);

        // Optional: log them in automatically after verification
        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('welcome')->with('status', '✅ Email verified successfully!');
    }

    return back()->withErrors(['code' => 'Invalid or expired verification code.']);
}

}
