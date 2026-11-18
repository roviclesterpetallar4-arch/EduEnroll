<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $periods = DB::table('academic_periods')
            ->orderBy('year_start')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get(['id', 'year_start']);
        $seenYears = [];
        $duplicateIds = [];

        foreach ($periods as $period) {
            if (isset($seenYears[$period->year_start])) {
                $duplicateIds[] = $period->id;

                continue;
            }

            $seenYears[$period->year_start] = true;
        }

        if ($duplicateIds !== []) {
            DB::table('academic_periods')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('academic_periods', function (Blueprint $table) {
            $table->dropUnique(['year_start', 'semester']);
            $table->dropColumn('semester');
            $table->unique('year_start');
        });
    }

    public function down(): void
    {
        Schema::table('academic_periods', function (Blueprint $table) {
            $table->dropUnique(['year_start']);
            $table->string('semester', 10)->default('1st');
            $table->unique(['year_start', 'semester']);
        });
    }
};
