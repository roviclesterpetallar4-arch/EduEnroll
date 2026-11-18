<?php

namespace Tests\Feature;

use App\Mail\EnrollmentApprovedMail;
use App\Models\AcademicPeriod;
use App\Models\Course;
use App\Models\CourseEnrollmentRequest;
use App\Models\Department;
use App\Models\EnrollmentRequest;
use App\Models\User;
use App\Services\VerificationEmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_enrollment_requests(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.enrollment-requests'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_create_command_creates_a_verified_admin_account(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Site Administrator')
            ->expectsQuestion('Administrator email', 'admin@example.com')
            ->expectsQuestion('Administrator password', 'strong-password')
            ->expectsQuestion('Confirm password', 'strong-password')
            ->expectsOutput('Administrator account created for admin@example.com.')
            ->assertExitCode(0);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue($admin->isAdmin());
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('strong-password', $admin->password));
    }

    public function test_students_cannot_access_the_admin_enrollment_dashboard(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('admin.enrollment-requests'))
            ->assertForbidden();
    }

    public function test_admin_can_create_future_academic_period_and_activate_it_for_header(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertSee('Academic year')
            ->assertSee('AY 2024-2025')
            ->assertSee('Current: AY 2024-2025')
            ->assertDontSee('Semester');

        $this->actingAs($admin)
            ->post(route('admin.academic-periods.store'), [
                'year_start' => 2027,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $futurePeriod = AcademicPeriod::query()
            ->where('year_start', 2027)
            ->firstOrFail();

        $this->assertSame(2028, $futurePeriod->year_end);
        $this->assertFalse($futurePeriod->is_active);

        $this->actingAs($admin)
            ->post(route('admin.academic-periods.activate', $futurePeriod))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('academic_periods', [
            'id' => $futurePeriod->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('academic_periods', [
            'year_start' => 2024,
            'is_active' => false,
        ]);

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('AY 2027-2028');
    }

    public function test_admin_cannot_create_duplicate_academic_period_records(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.academic-periods.store'), [
                'year_start' => 2024,
            ])
            ->assertSessionHasErrors('year_start');
    }

    public function test_students_cannot_manage_academic_periods(): void
    {
        $student = User::factory()->create();
        $period = AcademicPeriod::query()->firstOrFail();

        $this->actingAs($student)
            ->post(route('admin.academic-periods.activate', $period))
            ->assertForbidden();
    }

    public function test_admin_dashboard_combines_student_requests_in_one_row_and_details_modal(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create(['name' => 'Taylor Student', 'email' => 'taylor@example.com']);
        $student->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_PENDING,
        ]);
        $department = Department::factory()->create();
        $firstCourse = Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'IT-101',
            'title' => 'Web Development',
        ]);
        $secondCourse = Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'IT-102',
            'title' => 'Database Systems',
        ]);
        $student->courseEnrollmentRequests()->create([
            'course_id' => $firstCourse->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
        $student->courseEnrollmentRequests()->create([
            'course_id' => $secondCourse->id,
            'status' => CourseEnrollmentRequest::STATUS_APPROVED,
        ]);

        $confirmedStudent = User::factory()->create(['name' => 'Morgan Confirmed']);
        $confirmedStudent->enrollmentRequest()->create([
            'contact_number' => '09111111111',
            'student_type' => EnrollmentRequest::TYPE_INCOMING_FIRST_YEAR,
            'status' => EnrollmentRequest::STATUS_APPROVED,
        ]);

        $courseOnlyStudent = User::factory()->create(['name' => 'Course Only Student']);
        $courseOnlyStudent->courseEnrollmentRequests()->create([
            'course_id' => $firstCourse->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.enrollment-requests'));

        $response->assertOk()
            ->assertSee('Students')
            ->assertSee('View Details')
            ->assertSee('Pending review')
            ->assertSee('Confirmed')
            ->assertSee('No application')
            ->assertSee('taylor@example.com')
            ->assertSee('09123456789')
            ->assertSee('Transferee')
            ->assertSee('Web Development')
            ->assertSee('Database Systems')
            ->assertSee('No admission application has been submitted.')
            ->assertSee('Course Only Student');

        $this->assertSame(1, substr_count($response->getContent(), 'data-student-row="'.$student->id.'"'));
    }

    public function test_admin_can_view_enrollment_requests_and_approve_a_student(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create(['name' => 'Alex Student']);
        $enrollmentRequest = $student->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_INCOMING_FIRST_YEAR,
            'status' => EnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertSee('Alex Student')
            ->assertSee('09123456789')
            ->assertSee('Incoming 1st Year')
            ->assertSee('Submission Date')
            ->assertSee('Pending Review')
            ->assertSee('Total Applications')
            ->assertSee('Confirmed')
            ->assertSee('Rejected')
            ->assertSee('Pending')
            ->assertSee('Accept Student');

        $organization = Department::factory()->create([
            'name' => 'Information Technology Organization',
            'code' => 'ITO',
        ]);
        $academicPeriod = AcademicPeriod::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.enrollment.accept', $enrollmentRequest), [
                'department_id' => $organization->id,
                'academic_period_id' => $academicPeriod->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollment_requests', [
            'id' => $enrollmentRequest->id,
            'status' => EnrollmentRequest::STATUS_APPROVED,
            'department_id' => $organization->id,
            'academic_period_id' => $academicPeriod->id,
        ]);
        $this->assertNotNull($enrollmentRequest->fresh()->accepted_at);
        Mail::assertSent(EnrollmentApprovedMail::class, function (EnrollmentApprovedMail $mail) use ($student): bool {
            return $mail->hasTo($student->email)
                && $mail->enrollmentRequest->studentTypeLabel() === 'Incoming 1st Year'
                && $mail->enrollmentRequest->department->code === 'ITO'
                && $mail->enrollmentRequest->academicPeriod->year_start === 2024;
        });
    }

    public function test_admin_can_view_accepted_students_by_academic_year_and_all_organizations(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $academicPeriod = AcademicPeriod::query()->firstOrFail();
        $organizations = collect([
            ['name' => 'Education and Society Organization', 'code' => 'EDSO'],
            ['name' => 'Information Technology Organization', 'code' => 'ITO'],
            ['name' => 'Tourism Management Society', 'code' => 'TMS'],
            ['name' => 'Hospitality Management Society', 'code' => 'HMS'],
            ['name' => 'Junior Management Society', 'code' => 'JMS'],
        ])->map(fn (array $attributes) => Department::factory()->create($attributes));

        foreach ($organizations as $index => $organization) {
            $student = User::factory()->create(['name' => "Accepted Student {$index}"]);
            $student->enrollmentRequest()->create([
                'contact_number' => '09123456789',
                'student_type' => EnrollmentRequest::TYPE_INCOMING_FIRST_YEAR,
                'status' => EnrollmentRequest::STATUS_APPROVED,
                'department_id' => $organization->id,
                'academic_period_id' => $academicPeriod->id,
                'accepted_at' => now(),
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.enrollment-requests', ['history_period' => $academicPeriod->id]))
            ->assertOk();

        $response
            ->assertSee('Accepted Students by Organization')
            ->assertSee('Education and Society Organization')
            ->assertSee('Information Technology Organization')
            ->assertSee('Tourism Management Society')
            ->assertSee('Hospitality Management Society')
            ->assertSee('Junior Management Society')
            ->assertSee('Accepted Student 0')
            ->assertSee('Accepted Student 4')
            ->assertSee('1 accepted');

        $firstOrganizationId = $organizations->first()->id;
        $response
            ->assertSee('data-organization-dialog="accepted-students-'.$firstOrganizationId.'"', false)
            ->assertSee('<dialog id="accepted-students-'.$firstOrganizationId.'"', false);
    }

    public function test_admin_can_record_missing_history_details_for_older_approved_students(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $academicPeriod = AcademicPeriod::query()->firstOrFail();
        $organization = Department::factory()->create([
            'name' => 'Information Technology Organization',
            'code' => 'ITO',
        ]);
        $fullyUnrecordedStudent = User::factory()->create(['name' => 'Older Approved Student']);
        $fullyUnrecordedRequest = $fullyUnrecordedStudent->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_INCOMING_FIRST_YEAR,
            'status' => EnrollmentRequest::STATUS_APPROVED,
        ]);
        $missingOrganizationStudent = User::factory()->create(['name' => 'Student Missing Organization']);
        $missingOrganizationRequest = $missingOrganizationStudent->enrollmentRequest()->create([
            'contact_number' => '09111111111',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_APPROVED,
            'academic_period_id' => $academicPeriod->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertSee('2 accepted students need history details')
            ->assertSee($fullyUnrecordedStudent->name)
            ->assertSee($missingOrganizationStudent->name);

        $this->post(route('admin.enrollment-history.record', $fullyUnrecordedRequest), [
            'academic_period_id' => $academicPeriod->id,
            'department_id' => $organization->id,
        ])->assertRedirect()->assertSessionHas('success');

        $this->post(route('admin.enrollment-history.record', $missingOrganizationRequest), [
            'academic_period_id' => $academicPeriod->id,
            'department_id' => $organization->id,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('enrollment_requests', [
            'id' => $fullyUnrecordedRequest->id,
            'academic_period_id' => $academicPeriod->id,
            'department_id' => $organization->id,
        ]);
        $this->assertDatabaseHas('enrollment_requests', [
            'id' => $missingOrganizationRequest->id,
            'academic_period_id' => $academicPeriod->id,
            'department_id' => $organization->id,
        ]);

        $this->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertDontSee('accepted students need history details')
            ->assertSee($fullyUnrecordedStudent->name);
    }

    public function test_approval_email_contains_student_type_requirements_and_office_contacts(): void
    {
        $student = User::factory()->create(['name' => 'Taylor Transferee']);
        $enrollmentRequest = $student->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_APPROVED,
        ]);

        $rendered = (new EnrollmentApprovedMail($enrollmentRequest))->render();

        $this->assertStringContainsString('Transcript of Records (TOR) for evaluation purposes', $rendered);
        $this->assertStringContainsString('Certificate of Good Moral Character (Original)', $rendered);
        $this->assertStringContainsString('Monday – Friday, 8:00 AM – 5:00 PM', $rendered);
        $this->assertStringContainsString('nccdanao@gmail.com', $rendered);
        $this->assertStringContainsString('032 326 9294 / (+63) 922 828 2445', $rendered);
        $this->assertStringContainsString('/NortheasternCebuCollegesInc', $rendered);
    }

    public function test_only_pending_requests_can_be_approved_or_rejected(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create();
        $enrollmentRequest = $student->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_REJECTED,
        ]);
        $organization = Department::factory()->create(['code' => 'ITO']);
        $academicPeriod = AcademicPeriod::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.enrollment.accept', $enrollmentRequest), [
                'department_id' => $organization->id,
                'academic_period_id' => $academicPeriod->id,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('enrollment_requests', [
            'id' => $enrollmentRequest->id,
            'status' => EnrollmentRequest::STATUS_REJECTED,
        ]);
        Mail::assertNothingSent();
    }

    public function test_admin_can_reject_a_pending_enrollment_request(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $student = User::factory()->create();
        $enrollmentRequest = $student->enrollmentRequest()->create([
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.enrollment.reject', $enrollmentRequest))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollment_requests', [
            'id' => $enrollmentRequest->id,
            'status' => EnrollmentRequest::STATUS_REJECTED,
        ]);
        Mail::assertNothingSent();
    }

    public function test_registration_creates_a_pending_enrollment_request(): void
    {
        $this->mock(VerificationEmailService::class)
            ->shouldReceive('send')
            ->once()
            ->andReturn(null);

        $this->post('/register', [
            'name' => 'Jamie Newstudent',
            'email' => 'jamie@example.com',
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('verification.notice'));

        $student = User::where('email', 'jamie@example.com')->firstOrFail();
        $this->assertDatabaseHas('enrollment_requests', [
            'user_id' => $student->id,
            'contact_number' => '09123456789',
            'student_type' => EnrollmentRequest::TYPE_TRANSFEREE,
            'status' => EnrollmentRequest::STATUS_PENDING,
        ]);
    }
}
