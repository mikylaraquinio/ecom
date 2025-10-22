<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('welcome', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('welcome', absolute: false).'?verified=1');
    }

    public function sendVerificationCode(Request $request)
    {
        $user = $request->user();

        // generate 6-digit code
        $code = rand(100000, 999999);

        $user->update([
            'email_verification_code' => $code,
            'email_verification_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        // send email using Hostinger SMTP
        Mail::raw("Your FarmSmart verification code is: {$code}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your Email Verification Code');
        });

        return back()->with('message', 'Verification code sent to your email!');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $user = $request->user();

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

            return redirect('/welcome')->with('status', 'Email verified successfully!');
        }

        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

}
