<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * All tables are created in specific foreign key dependency order.
     */
    public function up(): void
    {
        // 1. Users Table (Students)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Password Reset Tokens & Sessions for Laravel web session driver
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. Student Profiles Table (One-to-One with User)
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('student_number')->unique();
            $table->integer('year_level')->default(1);
            $table->string('major');
            $table->timestamps();
        });

        // 3. Departments Table (One-to-Many with Course)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('head')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Courses Table (Belongs to Department, Many-to-Many with User)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->integer('units')->default(3);
            $table->string('instructor')->nullable();
            $table->string('prerequisites')->default('None');
            $table->text('description');
            $table->timestamps();
        });

        // 5. Course User Pivot Table (Many-to-Many enrollment with status and timestamps)
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('enrolled');
            $table->timestamps();

            // Prevent duplicate enrollment for the same student in the same course
            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations in exact reverse dependency order.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
