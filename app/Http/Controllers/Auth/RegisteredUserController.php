<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentRequest;
use App\Models\User;
use App\Services\VerificationEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, VerificationEmailService $verificationEmail): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:30'],
            'student_type' => ['required', 'in:incoming_first_year,transferee'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->enrollmentRequest()->create([
                'contact_number' => $validated['contact_number'],
                'student_type' => $validated['student_type'],
                'status' => EnrollmentRequest::STATUS_PENDING,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $redirect = redirect()->route('verification.notice')
            ->with('status', 'Your account was created.');

        if ($emailError = $verificationEmail->send($user)) {
            return $redirect->with('email_error', $emailError);
        }

        return $redirect->with('status', 'Your account was created. The mail server accepted the verification email.');
    }
}
