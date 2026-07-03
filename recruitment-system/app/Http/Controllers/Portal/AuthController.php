<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('portal.auth.login');
    }

    /**
     * Handle authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => $seconds]),
            ]);
        }

        if (Auth::guard('candidate')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            Log::info('Candidate logged in successfully', ['candidate_id' => Auth::guard('candidate')->id()]);

            return redirect()->intended(route('candidate.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Show the set password form for signed URL.
     */
    public function showSetPassword(Request $request): View|RedirectResponse
    {
        if (!$request->hasValidSignature()) {
            Log::warning('Invalid or expired signed URL accessed for password setup.', ['ip' => $request->ip()]);
            return redirect()->route('candidate.login')->with('error', 'This activation link has expired or is invalid.');
        }

        $email = $request->input('email');
        return view('portal.auth.set-password', compact('email'));
    }

    /**
     * Save first-time password and log in.
     */
    public function setPassword(Request $request): RedirectResponse
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('candidate.login')->with('error', 'This activation link has expired or is invalid.');
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:candidates,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $candidate = Candidate::where('email', $request->email)->firstOrFail();

        $candidate->update([
            'password' => Hash::make($request->password),
            'password_set_at' => now(),
            'email_verified_at' => now(),
        ]);

        Log::info('Candidate set their password successfully.', ['candidate_id' => $candidate->id]);

        Auth::guard('candidate')->login($candidate);

        return redirect()->route('candidate.dashboard')->with('success', 'Account activated successfully! Welcome to your dashboard.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword(): View
    {
        return view('portal.auth.forgot-password');
    }

    /**
     * Send password reset link email.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $throttleKey = 'password-reset|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            return back()->withErrors(['email' => 'Too many reset requests. Please try again later.']);
        }

        RateLimiter::hit($throttleKey, 300); // 5 minute lock

        $status = Password::broker('candidates')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form.
     */
    public function showResetPassword(Request $request, string $token): View
    {
        return view('portal.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Perform password reset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('candidates')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_set_at' => now(),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('candidate.login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Log out session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('candidate')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('candidate.login')->with('success', 'Logged out successfully.');
    }
}
