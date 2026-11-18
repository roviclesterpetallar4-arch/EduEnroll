<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollmentRequest;
use App\Models\Department;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_student_profile_relationship(): void
    {
        $user = User::factory()->create();
        $profile = StudentProfile::factory()->create([
            'user_id' => $user->id,
            'student_number' => 'STD-12345',
            'year_level' => 3,
            'major' => 'Computer Science',
        ]);

        $this->assertInstanceOf(StudentProfile::class, $user->studentProfile);
        $this->assertEquals($profile->id, $user->studentProfile->id);
        $this->assertEquals($user->id, $profile->user->id);
    }

    public function test_department_has_many_courses_relationship(): void
    {
        $department = Department::factory()->create();
        $courses = Course::factory(3)->create(['department_id' => $department->id]);

        $this->assertCount(3, $department->courses);
        $this->assertEquals($department->id, $courses->first()->department->id);
    }

    public function test_user_and_course_many_to_many_relationship_via_pivot(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $course->students()->attach($user->id, [
            'status' => 'enrolled',
        ]);

        $this->assertCount(1, $course->students);
        $this->assertEquals('enrolled', $course->students->first()->pivot->status);
        $this->assertCount(1, $user->courses);
        $this->assertEquals('enrolled', $user->courses->first()->pivot->status);
    }

    public function test_database_relationships_dashboard_demonstrates_all_four_relationship_types(): void
    {
        $department = Department::factory()->create([
            'name' => 'College of Computing',
            'code' => 'CC',
        ]);
        $course = Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'CS-101',
            'title' => 'Computer Science',
        ]);
        $student = User::factory()->create(['name' => 'Jordan Student']);
        $profile = StudentProfile::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'student_number' => 'STD-24680',
            'major' => $course->title,
        ]);
        $course->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);

        $this->assertSame($profile->id, $student->studentProfile->id);
        $this->assertSame($department->id, $course->department->id);
        $this->assertSame($student->id, $course->students->first()->id);
        $this->assertSame($profile->id, $department->students->first()->id);

        $this->get(route('relationships.index'))
            ->assertOk()
            ->assertSee('Database Relationships Demonstration')
            ->assertSee('Student &amp; profile', false)
            ->assertSee('Department &amp; programs', false)
            ->assertSee('Students &amp; courses', false)
            ->assertSee('Department &amp; students', false)
            ->assertSee('Jordan Student')
            ->assertSee('Computer Science');
    }

    public function test_courses_index_page_loads_with_courses_and_departments(): void
    {
        $department = Department::factory()->create(['name' => 'College of Computing']);
        $course = Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'CS-101',
            'title' => 'Data Structures',
        ]);

        $response = $this->get('/courses');

        $response->assertStatus(200);
        $response->assertSee('Data Structures');
        $response->assertSee('CS-101');
        $response->assertSee('College of Computing');
        $response->assertSee(' units');
    }

    public function test_courses_index_displays_education_and_society_organization(): void
    {
        $department = Department::factory()->create([
            'name' => 'Education and Society Organization',
            'code' => 'EDSO',
        ]);
        Course::factory()->create(['department_id' => $department->id]);

        $this->get('/courses')
            ->assertOk()
            ->assertSee('Education and Society Organization (EDSO)');
    }

    public function test_courses_index_displays_correct_management_society_names(): void
    {
        foreach ([
            ['Junior Management Society', 'JMS'],
            ['Hospitality Management Society', 'HMS'],
            ['Tourism Management Society', 'TMS'],
        ] as [$name, $code]) {
            $department = Department::factory()->create(compact('name', 'code'));
            Course::factory()->create(['department_id' => $department->id]);
        }

        $this->get('/courses')
            ->assertOk()
            ->assertSee('Junior Management Society (JMS)')
            ->assertSee('Hospitality Management Society (HMS)')
            ->assertSee('Tourism Management Society (TMS)')
            ->assertDontSee('Business Administration (BA)')
            ->assertDontSee('Hospitality Management (HM)')
            ->assertDontSee('Bachelor of Tourism Studies (BTS)');
    }

    public function test_course_catalog_and_footer_name_all_five_academic_organizations(): void
    {
        foreach ([
            ['Education and Society Organization', 'EDSO'],
            ['Information Technology Organization', 'ITO'],
            ['Tourism Management Society', 'TMS'],
            ['Hospitality Management Society', 'HMS'],
            ['Junior Management Society', 'JMS'],
        ] as [$name, $code]) {
            Department::factory()->create(compact('name', 'code'));
        }

        $this->get('/courses')
            ->assertOk()
            ->assertSee('five academic organizations')
            ->assertSee('Education and Society Organization (EDSO)')
            ->assertSee('Information Technology Organization (ITO)')
            ->assertSee('Tourism Management Society (TMS)')
            ->assertSee('Hospitality Management Society (HMS)')
            ->assertSee('Junior Management Society (JMS)')
            ->assertDontSee('Built for Class Assignment Demonstration');
    }

    public function test_courses_index_can_filter_by_search_query(): void
    {
        $department = Department::factory()->create();
        Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'IT-101',
            'title' => 'Web Development 1',
        ]);
        Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'ENG-201',
            'title' => 'Digital Logic Design',
        ]);

        $response = $this->get('/courses?search=Web+Development');

        $response->assertStatus(200);
        $response->assertSee('Web Development 1');
        $response->assertDontSee('Digital Logic Design');
    }

    public function test_courses_index_can_filter_by_department(): void
    {
        $dept1 = Department::factory()->create(['name' => 'Computer Science']);
        $dept2 = Department::factory()->create(['name' => 'Information Technology']);

        Course::factory()->create(['department_id' => $dept1->id, 'title' => 'Algorithms']);
        Course::factory()->create(['department_id' => $dept2->id, 'title' => 'Network Admin']);

        $response = $this->get("/courses?department={$dept1->id}");

        $response->assertStatus(200);
        $response->assertSee('Algorithms');
        $response->assertDontSee('Network Admin');
    }

    public function test_courses_show_page_hides_enrolled_student_personal_data(): void
    {
        $department = Department::factory()->create(['head' => 'Dr. Jane Dean']);
        $course = Course::factory()->create([
            'department_id' => $department->id,
            'instructor' => 'Prof. Alan Turing',
            'prerequisites' => 'CS-101',
        ]);
        $student = User::factory()->has(StudentProfile::factory()->state([
            'student_number' => 'STD-99999',
            'major' => 'Software Engineering',
        ]))->create(['name' => 'John Doe']);

        $course->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);

        $response = $this->get("/courses/{$course->id}");

        $response->assertStatus(200);
        $response->assertSee($course->title);
        $response->assertDontSee('Enrolled Students');
        $response->assertDontSee('John Doe');
        $response->assertDontSee('STD-99999');
        $response->assertDontSee($student->email);
        $response->assertDontSee('Software Engineering');
        $response->assertSee('Prof. Alan Turing');
        $response->assertSee('Program Chairperson');
        $response->assertSee('CS-101');
    }

    public function test_bsit_course_details_show_act_graduation_as_a_prerequisite(): void
    {
        $department = Department::factory()->create();
        $course = Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'BSIT',
            'title' => 'Bachelor of Science in Information Technology',
            'prerequisites' => 'Graduate of the Associate in Computer Technology (ACT) program at Northeastern Cebu Colleges',
        ]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Graduate of the Associate in Computer Technology (ACT) program at Northeastern Cebu Colleges');
    }

    public function test_guest_is_prompted_to_sign_in_and_returns_to_the_course(): void
    {
        $course = Course::factory()->create();

        $this->get("/courses/{$course->id}")
            ->assertOk()
            ->assertSee('Sign In to Enroll')
            ->assertDontSee('Select Student')
            ->assertDontSee('-- Choose a student --');

        $this->get(route('courses.enroll.sign-in', $course))
            ->assertRedirect(route('login'));

        $student = User::factory()->create();
        $this->post('/login', [
            'email' => $student->email,
            'password' => 'password',
        ])->assertRedirect(route('courses.show', $course));

        $this->assertAuthenticatedAs($student);
    }

    public function test_student_requests_enrollment_for_themselves_without_immediate_enrollment(): void
    {
        $course = Course::factory()->create();
        $student = User::factory()->create(['name' => 'Jane Smith']);
        $otherStudent = User::factory()->create();

        $this->actingAs($student)
            ->post("/courses/{$course->id}/enroll", ['user_id' => $otherStudent->id])
            ->assertRedirect("/courses/{$course->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('course_enrollment_requests', [
            'course_id' => $course->id,
            'user_id' => $otherStudent->id,
        ]);
        $this->assertDatabaseMissing('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);

        $this->actingAs($student)
            ->get("/courses/{$course->id}")
            ->assertSee('Request pending review');

        $this->actingAs($student)
            ->get(route('student.portal'))
            ->assertOk()
            ->assertSee('Course Requests')
            ->assertSee('Pending');
    }

    public function test_student_cannot_submit_a_second_pending_request_for_the_same_course(): void
    {
        $course = Course::factory()->create();
        $student = User::factory()->create();
        CourseEnrollmentRequest::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$course->id}/enroll")
            ->assertSessionHas('error');

        $this->assertDatabaseCount('course_enrollment_requests', 1);
    }

    public function test_student_can_resubmit_a_rejected_course_request(): void
    {
        $course = Course::factory()->create();
        $student = User::factory()->create();
        CourseEnrollmentRequest::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_REJECTED,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$course->id}/enroll")
            ->assertSessionHas('success');

        $this->assertDatabaseCount('course_enrollment_requests', 1);
        $this->assertDatabaseHas('course_enrollment_requests', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
    }

    public function test_student_must_cancel_a_pending_request_before_requesting_a_different_course(): void
    {
        $firstCourse = Course::factory()->create();
        $secondCourse = Course::factory()->create();
        $student = User::factory()->create();
        $firstRequest = CourseEnrollmentRequest::create([
            'course_id' => $firstCourse->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$secondCourse->id}/enroll")
            ->assertSessionHas('error');

        $this->actingAs($student)
            ->post(route('courses.enrollment.cancel', $firstCourse), [
                'cancellation_reason' => 'changing_program',
                'cancellation_details' => 'I selected a different program.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $firstRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_CANCELLED,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$secondCourse->id}/enroll")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'course_id' => $secondCourse->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
    }

    public function test_approved_course_must_be_cancelled_by_admin_before_student_can_switch(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $firstCourse = Course::factory()->create();
        $secondCourse = Course::factory()->create();
        $student = User::factory()->create();
        $courseRequest = CourseEnrollmentRequest::create([
            'course_id' => $firstCourse->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_APPROVED,
        ]);
        $firstCourse->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);

        $this->actingAs($student)
            ->post(route('courses.enrollment.cancel', $firstCourse), [
                'cancellation_reason' => 'changing_program',
                'cancellation_details' => 'I selected a different program.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_user', [
            'course_id' => $firstCourse->id,
            'user_id' => $student->id,
        ]);
        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $courseRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_CANCELLATION_PENDING,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$secondCourse->id}/enroll")
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertSee('Approve Cancellation')
            ->assertSee('Reject Cancellation')
            ->assertSee('Changing academic program')
            ->assertSee('I selected a different program.');

        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.reject-cancellation', $courseRequest))
            ->assertSessionHas('success');
        Mail::assertSent(\App\Mail\CourseEnrollmentStatusMail::class, function ($mail) use ($student): bool {
            return $mail->hasTo($student->email)
                && $mail->notificationType === \App\Mail\CourseEnrollmentStatusMail::CANCELLATION_REJECTED;
        });

        $this->assertDatabaseHas('course_user', [
            'course_id' => $firstCourse->id,
            'user_id' => $student->id,
        ]);

        $this->actingAs($student)
            ->post(route('courses.enrollment.cancel', $firstCourse), [
                'cancellation_reason' => 'changing_program',
            ])
            ->assertSessionHas('success');
        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.approve-cancellation', $courseRequest))
            ->assertSessionHas('success')
            ->assertSessionHas('success', 'The course enrollment was cancelled, the student may request another course, and an email notification was sent.');
        Mail::assertSent(\App\Mail\CourseEnrollmentStatusMail::class, function ($mail) use ($student): bool {
            return $mail->hasTo($student->email)
                && $mail->notificationType === \App\Mail\CourseEnrollmentStatusMail::CANCELLATION_APPROVED;
        });

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $firstCourse->id,
            'user_id' => $student->id,
        ]);
        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $courseRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_CANCELLED,
        ]);

        $this->actingAs($student)
            ->post("/courses/{$secondCourse->id}/enroll")
            ->assertSessionHas('success');

        $newCourseRequest = CourseEnrollmentRequest::query()
            ->where('course_id', $secondCourse->id)
            ->where('user_id', $student->id)
            ->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.approve', $newCourseRequest))
            ->assertSessionHas('success', 'The student was enrolled in the course and an email notification was sent.');
        Mail::assertSent(\App\Mail\CourseEnrollmentStatusMail::class, function ($mail) use ($student): bool {
            return $mail->hasTo($student->email)
                && $mail->notificationType === \App\Mail\CourseEnrollmentStatusMail::ENROLLMENT_APPROVED;
        });
        Mail::assertSent(\App\Mail\CourseEnrollmentStatusMail::class, 3);
    }

    public function test_admin_cannot_approve_a_second_course_for_a_student_with_an_active_enrollment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $enrolledCourse = Course::factory()->create();
        $requestedCourse = Course::factory()->create();
        $student = User::factory()->create();
        $enrolledCourse->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);
        $request = CourseEnrollmentRequest::create([
            'course_id' => $requestedCourse->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.approve', $request))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $request->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('course_user', [
            'course_id' => $requestedCourse->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_cancellation_requires_a_valid_reason(): void
    {
        $course = Course::factory()->create();
        $student = User::factory()->create();
        $course->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);

        $this->actingAs($student)
            ->from("/courses/{$course->id}")
            ->post(route('courses.enrollment.cancel', $course), [
                'cancellation_reason' => 'not_a_reason',
            ])
            ->assertRedirect("/courses/{$course->id}")
            ->assertSessionHasErrors('cancellation_reason');

        $this->assertDatabaseMissing('course_enrollment_requests', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_admin_can_approve_or_reject_course_enrollment_requests(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = Course::factory()->create();
        $student = User::factory()->create();
        $approvedRequest = CourseEnrollmentRequest::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enrollment-requests'))
            ->assertOk()
            ->assertSee('Course Enrollment Requests')
            ->assertSee($course->title)
            ->assertSee($student->name);

        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.approve', $approvedRequest))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $approvedRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_APPROVED,
        ]);
        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'enrolled',
        ]);
        $this->actingAs($student)
            ->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Accepted — Awaiting Exam & Interview Date')
            ->assertSee('Please wait for NCC to contact you with your exam and interview schedule.');

        $rejectedCourse = Course::factory()->create();
        $rejectedStudent = User::factory()->create();
        $rejectedRequest = CourseEnrollmentRequest::create([
            'course_id' => $rejectedCourse->id,
            'user_id' => $rejectedStudent->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.course-enrollment.reject', $rejectedRequest))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $rejectedRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_REJECTED,
        ]);
        $this->assertDatabaseMissing('course_user', [
            'course_id' => $rejectedCourse->id,
            'user_id' => $rejectedStudent->id,
        ]);
    }

    public function test_only_an_admin_can_review_course_enrollment_requests(): void
    {
        $student = User::factory()->create();
        $course = Course::factory()->create();
        $enrollmentRequest = CourseEnrollmentRequest::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);

        $this->actingAs($student)
            ->post(route('admin.course-enrollment.approve', $enrollmentRequest))
            ->assertForbidden();

        $this->assertDatabaseHas('course_enrollment_requests', [
            'id' => $enrollmentRequest->id,
            'status' => CourseEnrollmentRequest::STATUS_PENDING,
        ]);
    }

    public function test_courses_display_program_chairperson_instead_of_instructor(): void
    {
        $department = Department::factory()->create([
            'head' => 'Dr. Jomar Lavador',
        ]);
        Course::factory()->create([
            'department_id' => $department->id,
            'code' => 'ACT',
            'instructor' => 'Dr. Jomar Lavador',
        ]);

        $this->get('/courses')
            ->assertOk()
            ->assertSee('Program Chairperson:');
    }

    public function test_student_who_is_already_enrolled_cannot_submit_a_course_request(): void
    {
        $course = Course::factory()->create();
        $student = User::factory()->create();

        $course->students()->attach($student->id, [
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($student)
            ->post("/courses/{$course->id}/enroll");

        $response->assertSessionHas('error');
        $this->assertCount(1, $course->fresh()->students);
    }
}
