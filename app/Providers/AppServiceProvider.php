<?php

namespace App\Providers;

use App\Models\AcademicPeriod;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, string $verificationUrl): MailMessage {
            $viewData = [
                'student' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'expirationMinutes' => config('auth.verification.expire', 60),
            ];

            return (new MailMessage)
                ->subject('Verify your email | Northeastern Cebu Colleges')
                ->view('emails.verify-email', $viewData)
                ->text('emails.verify-email-text', $viewData);
        });

        view()->composer('*', function ($view): void {
            $request = request();
            if (! $request->attributes->has('active_academic_period')) {
                $request->attributes->set(
                    'active_academic_period',
                    AcademicPeriod::query()
                        ->where('is_active', true)
                        ->first()
                );
            }

            $view->with('activeAcademicPeriod', $request->attributes->get('active_academic_period'));
        });
    }
}
