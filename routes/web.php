<?php

use App\Http\Controllers\Admin\AcademicPeriodController;
use App\Http\Controllers\Admin\CourseEnrollmentRequestController;
use App\Http\Controllers\Admin\EnrollmentRequestController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RelationshipsController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Routes for the NCC EduEnroll Course Enrollment System
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/student-portal', StudentPortalController::class)
        ->middleware('verified')
        ->name('student.portal');
    Route::get('/student/dashboard', StudentPortalController::class)
        ->middleware('verified')
        ->name('student.dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.enrollment-requests'))->name('dashboard');
    Route::get('/enrollment-requests', [EnrollmentRequestController::class, 'index'])->name('enrollment-requests');
    Route::post('/enrollment/{enrollmentRequest}/record-history', [EnrollmentRequestController::class, 'recordHistory'])
        ->name('enrollment-history.record');
    Route::post('/academic-periods', [AcademicPeriodController::class, 'store'])->name('academic-periods.store');
    Route::post('/academic-periods/{academicPeriod}/activate', [AcademicPeriodController::class, 'activate'])->name('academic-periods.activate');
    Route::post('/enrollment/{enrollmentRequest}/accept', [EnrollmentRequestController::class, 'accept'])
        ->name('enrollment.accept');
    Route::post('/enrollment/{enrollmentRequest}/reject', [EnrollmentRequestController::class, 'reject'])
        ->name('enrollment.reject');
    Route::post('/course-enrollment/{courseEnrollmentRequest}/approve', [CourseEnrollmentRequestController::class, 'approve'])
        ->name('course-enrollment.approve');
    Route::post('/course-enrollment/{courseEnrollmentRequest}/reject', [CourseEnrollmentRequestController::class, 'reject'])
        ->name('course-enrollment.reject');
    Route::post('/course-enrollment/{courseEnrollmentRequest}/approve-cancellation', [CourseEnrollmentRequestController::class, 'approveCancellation'])
        ->name('course-enrollment.approve-cancellation');
    Route::post('/course-enrollment/{courseEnrollmentRequest}/reject-cancellation', [CourseEnrollmentRequestController::class, 'rejectCancellation'])
        ->name('course-enrollment.reject-cancellation');
});

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/relationships', [RelationshipsController::class, 'index'])->name('relationships.index');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{id}/enroll/sign-in', [CourseController::class, 'signInForEnrollment'])
    ->name('courses.enroll.sign-in');
Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])
    ->middleware('auth')
    ->name('courses.enroll');
Route::post('/courses/{id}/enrollment/cancel', [CourseController::class, 'cancelEnrollmentRequest'])
    ->middleware('auth')
    ->name('courses.enrollment.cancel');
