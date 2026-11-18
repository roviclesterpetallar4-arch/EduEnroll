@extends('layouts.app')

@section('title', 'Verify Email Address')

@section('content')
<div class="mx-auto max-w-xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
    <p class="text-xs font-bold uppercase tracking-widest text-ncc-maroon">Student Portal</p>
    <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Verify your email</h1>
    <p class="mt-3 text-sm leading-relaxed text-slate-600">
        Verification messages are addressed to <strong>{{ auth()->user()->email }}</strong>
        from <strong>{{ config('mail.from.address') }}</strong>. After sending or resending,
        check your Inbox, Spam, and Promotions folders.
    </p>

    @if (session('status'))
        <p role="status" class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif
    @if (session('email_error'))
        <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            <p>{{ session('email_error') }}</p>
            <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block font-semibold underline">
                Create a new Google App Password
            </a>
            <p class="mt-2">Update <code>MAIL_PASSWORD</code> in your local <code>.env</code> file, then run <code>php artisan config:clear</code>.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6" data-single-submit>
        @csrf
        <button type="submit" class="rounded-xl bg-ncc-maroon px-5 py-3 text-sm font-bold text-white transition hover:bg-ncc-maroonHover focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2">
            Resend verification email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4" data-single-submit>
        @csrf
        <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-ncc-maroon">Sign out</button>
    </form>
</div>
@endsection
