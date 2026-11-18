<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VerificationEmailService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): RedirectResponse|View
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.enrollment-requests' : 'student.portal');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request, string|int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if (! $request->hasValidSignature()) {
            abort(403, 'Verification link expired or invalid.');
        }

        // Log the user in if they aren't logged in or are logged in as someone else
        if (! Auth::check() || Auth::id() != $user->id) {
            Auth::login($user);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.enrollment-requests');
        }

        return redirect()->route('student.portal')
            ->with('status', 'Your email address has been verified! You can now browse courses and enroll.');
    }

    public function send(Request $request, VerificationEmailService $verificationEmail): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.enrollment-requests' : 'student.portal');
        }

        if ($emailError = $verificationEmail->send($request->user())) {
            return back()->with('email_error', $emailError);
        }

        return back()->with('status', 'The mail server accepted the verification email. Check your inbox, Spam, and Promotions folders.');
    }
}
