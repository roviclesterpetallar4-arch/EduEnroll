@extends('layouts.app')

@section('title', 'Create Student Account')

@section('content')
<div class="mx-auto max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-ncc-maroon">Student Portal</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Create your account</h1>
        <p class="mt-2 text-sm text-slate-500">We’ll email you a link to verify your address.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5" data-single-submit data-clear-on-load autocomplete="off">
        @csrf
        <div>
            <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Full name</label>
            <input id="name" name="name" type="text" autocomplete="off" maxlength="255" required autofocus
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
            @error('name') <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email address</label>
            <input id="email" name="email" type="email" autocomplete="off" maxlength="255" required
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
            @error('email') <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact_number" class="mb-1.5 block text-sm font-semibold text-slate-700">Contact number</label>
            <input id="contact_number" name="contact_number" type="tel" autocomplete="off" maxlength="30" required
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
            @error('contact_number') <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="student_type" class="mb-1.5 block text-sm font-semibold text-slate-700">Student type</label>
            <select id="student_type" name="student_type" required
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
                <option value="incoming_first_year">Incoming 1st Year</option>
                <option value="transferee">Transferee</option>
            </select>
            @error('student_type') <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
            <input id="password" name="password" type="password" autocomplete="off" minlength="8" required
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
            @error('password') <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="off" minlength="8" required
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20">
        </div>

        <button type="submit" class="w-full rounded-xl bg-ncc-maroon px-4 py-3 font-bold text-white transition hover:bg-ncc-maroonHover focus:outline-none focus:ring-2 focus:ring-ncc-gold focus:ring-offset-2">
            Create Account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Already registered?
        <a href="{{ route('login') }}" class="font-bold text-ncc-maroon hover:underline">Sign in</a>
    </p>
</div>
@endsection
