<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class VerificationEmailService
{
    public function send(User $user): ?string
    {
        $usesGmailSmtp = config('mail.default') === 'smtp'
            && config('mail.mailers.smtp.host') === 'smtp.gmail.com';
        $password = (string) config('mail.mailers.smtp.password');

        if ($usesGmailSmtp && strlen($password) !== 16) {
            Log::warning('Student email verification was not sent because the Gmail App Password is not 16 characters.', [
                'user_id' => $user->id,
            ]);

            return 'Gmail requires a 16-character Google App Password. Update MAIL_PASSWORD in .env with the App Password, not your regular Gmail password.';
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (TransportExceptionInterface $exception) {
            $authenticationRejected = $usesGmailSmtp
                && str_contains($exception->getMessage(), '535');

            Log::error('Failed to send student email verification.', [
                'user_id' => $user->id,
                'reason' => $authenticationRejected ? 'gmail_authentication_rejected' : 'smtp_transport_failure',
            ]);

            if ($authenticationRejected) {
                return 'Gmail rejected the sender login. Confirm MAIL_USERNAME is the Gmail account that created the App Password, then create a new 16-character App Password and update MAIL_PASSWORD.';
            }

            return 'The verification email could not be sent because of a mail transport problem. Check your mail configuration and connection, then try again.';
        }

        return null;
    }
}
