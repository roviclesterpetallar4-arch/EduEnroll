<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create', function () {
    $attributes = [
        'name' => trim((string) $this->ask('Administrator name')),
        'email' => strtolower(trim((string) $this->ask('Administrator email'))),
        'password' => (string) $this->secret('Administrator password'),
        'password_confirmation' => (string) $this->secret('Confirm password'),
    ];

    $validator = Validator::make($attributes, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if ($validator->fails()) {
        $this->error($validator->errors()->first());

        return 1;
    }

    DB::transaction(function () use ($attributes): void {
        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
        ]);

        $user->forceFill([
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();
    });

    $this->info("Administrator account created for {$attributes['email']}.");

    return 0;
})->purpose('Create an administrator account for the enrollment dashboard');
