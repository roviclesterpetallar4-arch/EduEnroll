<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('course_user', 'grade')) {
            Schema::table('course_user', function (Blueprint $table) {
                $table->dropColumn('grade');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('course_user', 'grade')) {
            Schema::table('course_user', function (Blueprint $table) {
                $table->string('grade')->nullable();
            });
        }
    }
};
