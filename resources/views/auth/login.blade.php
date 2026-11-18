@extends('layouts.app')

@section('title', 'Student Sign In')
@section('main-class', 'flex w-full flex-grow items-center justify-center px-4 py-10')

@section('content')
<section class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-900/5 sm:p-8">
    <div class="mb-7">
        <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-3 text-sm font-bold text-slate-700 hover:text-ncc-maroon">
            <img src="{{ asset('images/ncc-logo.png') }}" alt="" class="h-10 w-10 rounded-full object-contain">
            <span>EduEnroll</span>
        </a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Sign in to your account</h1>
    </div>

    @if (session('status'))
        <p role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" data-single-submit data-clear-on-load autocomplete="off">
        @csrf
        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email address</label>
            <input id="email" name="email" type="email" autocomplete="off" required autofocus
                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                   placeholder="you@example.com"
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-ncc-maroon focus:ring-2 focus:ring-ncc-maroon/15">
            @error('email') <p role="alert" class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
            <input id="password" name="password" type="password" autocomplete="off" required
                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                   placeholder="Enter your password"
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-ncc-maroon focus:ring-2 focus:ring-ncc-maroon/15">
            @error('password') <p role="alert" class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <label class="flex w-fit cursor-pointer items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-ncc-maroon focus:ring-ncc-maroon">
            Remember me
        </label>

        <button type="submit" class="w-full rounded-xl bg-ncc-maroon px-4 py-3 font-bold text-white transition hover:bg-ncc-maroonHover focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2">
            Sign In
        </button>
    </form>

    <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-600">
        New to the portal?
        <a href="{{ route('register') }}" class="font-bold text-ncc-maroon hover:underline">Create an account</a>
    </p>
</section>
@endsection
