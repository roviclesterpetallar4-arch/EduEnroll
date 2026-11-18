<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Services\VerificationEmailService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class StudentAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mail.mailers.smtp.password', '1234567890123456');
    }

    public function test_guest_can_open_login_and_registration_pages(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign in to your account')
            ->assertSee('data-single-submit', false)
            ->assertSee('data-clear-on-load', false);
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your account')
            ->assertSee('data-single-submit', false)
            ->assertSee('data-clear-on-load', false);
    }

    public function test_registration_creates_unverified_account_and_sends_verification_email(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'Student Example',
            'email' => 'STUDENT@example.com',
            'contact_number' => '09123456789',
            'student_type' => 'incoming_first_year',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $user = User::where('email', 'student@example.com')->firstOrFail();

        $response->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'Your account was created. The mail server accepted the verification email.');
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_email_uses_ncc_branded_html_and_plain_text_views(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Jordan Student']);
        $notification = new VerifyEmail;
        $message = $notification->toMail($user);

        $this->assertSame('Verify your email | Northeastern Cebu Colleges', $message->subject);
        $this->assertSame('emails.verify-email', $message->view['html']);
        $this->assertSame('emails.verify-email-text', $message->view['text']);

        $html = view($message->view['html'], $message->viewData)->render();
        $plainText = view($message->view['text'], $message->viewData)->render();

        $this->assertStringContainsString('Welcome to NCC, Jordan Student', $html);
        $this->assertStringContainsString('Verify Email Address', $html);
        $this->assertStringContainsString('verification link expires in 60 minutes', $html);
        $this->assertStringContainsString('images/ncc-logo.png', $html);
        $this->assertStringContainsString('Welcome to NCC, Jordan Student', $plainText);
        $this->assertStringContainsString('http://localhost/email/verify/', $plainText);
    }

    public function test_registration_shows_recoverable_message_if_verification_email_cannot_be_sent(): void
    {
        $this->mock(VerificationEmailService::class)
            ->shouldReceive('send')
            ->once()
            ->andReturn('Verification email unavailable.');

        $response = $this->post(route('register'), [
            'name' => 'Student Example',
            'email' => 'student@example.com',
            'contact_number' => '09123456789',
            'student_type' => 'incoming_first_year',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'Your account was created.')
            ->assertSessionHas('email_error');

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'email_verified_at' => null,
        ]);
    }

    public function test_registration_requires_valid_unique_email_and_confirmed_password(): void
    {
        User::factory()->create(['email' => 'student@example.com']);

        $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Student Example',
                'email' => 'student@example.com',
                'password' => 'secure-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->get(route('register'))
            ->assertDontSee('value="Student Example"', false)
            ->assertDontSee('value="student@example.com"', false);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_login_authenticates_a_registered_user(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'secure-password',
        ]);

        $this->post(route('login'), [
            'email' => 'STUDENT@example.com',
            'password' => 'secure-password',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_locks_after_five_failed_attempts_for_five_minutes(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'secure-password',
        ]);
        $credentials = [
            'email' => 'student@example.com',
            'password' => 'incorrect-password',
        ];

        foreach (range(1, 4) as $attempt) {
            $response = $this->post(route('login'), $credentials);
            $response->assertSessionHasErrors('email');
        }

        $this->travel(4)->minutes();

        $response = $this->post(route('login'), $credentials);
        $response->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Try again in 5 minute(s).',
            session('errors')->first('email')
        );

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertSessionHasErrors('email');

        $this->travel(1)->minute();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertSessionHasErrors('email');

        $this->travel(4)->minutes();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_cannot_open_a_protected_student_page(): void
    {
        $this->get(route('student.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_reopen_login_or_registration_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect();

        $this->get(route('register'))
            ->assertRedirect();
    }

    public function test_unverified_user_is_redirected_to_verification_notice_from_portal(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('student.portal'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_student_can_open_dashboard_route(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $course->students()->attach($user->id, [
            'status' => 'enrolled',
        ]);

        $this->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Student Portal')
            ->assertSee('Accepted — Awaiting Exam & Interview')
            ->assertDontSee('>enrolled<', false);
    }

    public function test_signed_verification_link_verifies_email_and_opens_student_portal(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('student.portal'))
            ->assertSessionHas('status');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_unverified_user_can_request_another_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'The mail server accepted the verification email. Check your inbox, Spam, and Promotions folders.');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_email_delivery_failure_is_reported_and_logged(): void
    {
        $user = \Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(42);
        $user->shouldReceive('sendEmailVerificationNotification')
            ->once()
            ->andThrow(new TransportException('SMTP unavailable'));

        Log::shouldReceive('error')
            ->once()
            ->with('Failed to send student email verification.', [
                'user_id' => 42,
                'reason' => 'smtp_transport_failure',
            ]);

        $this->assertNotNull(app(VerificationEmailService::class)->send($user));
    }

    public function test_non_gmail_mailer_can_send_verification_without_gmail_credentials(): void
    {
        config()->set('mail.default', 'log');

        $user = \Mockery::mock(User::class);
        $user->shouldReceive('sendEmailVerificationNotification')->once();

        $this->assertNull(app(VerificationEmailService::class)->send($user));
    }

    public function test_invalid_gmail_app_password_length_is_reported_without_sending(): void
    {
        config()->set('mail.default', 'smtp');
        config()->set('mail.mailers.smtp.host', 'smtp.gmail.com');
        config()->set('mail.mailers.smtp.password', 'not-a-16-char-password');

        $user = \Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(42);
        $user->shouldNotReceive('sendEmailVerificationNotification');

        Log::shouldReceive('warning')
            ->once()
            ->with('Student email verification was not sent because the Gmail App Password is not 16 characters.', ['user_id' => 42]);

        $this->assertStringContainsString(
            '16-character Google App Password',
            app(VerificationEmailService::class)->send($user)
        );
    }
}
