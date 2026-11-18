<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year_start');
            $table->unsignedSmallInteger('year_end');
            $table->string('semester', 10);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['year_start', 'semester']);
        });

        DB::table('academic_periods')->insert([
            'year_start' => 2024,
            'year_end' => 2025,
            'semester' => '1st',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
