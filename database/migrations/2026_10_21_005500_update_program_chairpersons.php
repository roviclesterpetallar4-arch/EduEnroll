<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $departmentChairpersons = [
            'EDSO' => 'Dr. Maria Liza Laborte Moscosa',
            'ITO' => 'Dr. Jomar Lavador',
            'BA' => 'Dr. Eden Hornido',
            'HM' => 'Dr. Francisco C. Gatdula',
            'BTS' => 'Ms. Rogen Semblante',
        ];

        foreach ($departmentChairpersons as $code => $chairperson) {
            DB::table('departments')
                ->where('code', $code)
                ->update(['head' => $chairperson]);
        }

        $courseChairpersons = [
            'ACT' => 'Dr. Jomar Lavador',
            'BSIT' => 'Dr. Jomar Lavador',
            'BSBA' => 'Dr. Eden Hornido',
            'BSHM' => 'Dr. Francisco C. Gatdula',
            'BSTM' => 'Ms. Rogen Semblante',
            'BEEd' => 'Dr. Maria Liza Laborte Moscosa',
            'BSEd-ENG' => 'Dr. Maria Liza Laborte Moscosa',
            'BSEd-FIL' => 'Dr. Maria Liza Laborte Moscosa',
            'BSEd-MATH' => 'Dr. Maria Liza Laborte Moscosa',
        ];

        foreach ($courseChairpersons as $code => $chairperson) {
            DB::table('courses')
                ->where('code', $code)
                ->update(['instructor' => $chairperson]);
        }
    }

    public function down(): void
    {
        $previousDepartmentHeads = [
            'EDSO' => 'Dr. Rodrigo Perez, Ed.D.',
            'ITO' => 'Prof. Michael G. Ceniza, MIT',
            'BA' => 'Dr. Ma. Theresa Roble, DBA',
            'HM' => 'Prof. Catherine Gomez, MSHM',
            'BTS' => 'Prof. Kevin Durano, MSTM',
        ];

        foreach ($previousDepartmentHeads as $code => $head) {
            DB::table('departments')
                ->where('code', $code)
                ->update(['head' => $head]);
        }

        $previousCourseInstructors = [
            'ACT' => 'Prof. Michael G. Ceniza, MIT',
            'BSIT' => 'Prof. Jason Montes, MSIT',
            'BSBA' => 'Dr. Ma. Theresa Roble, DBA',
            'BSHM' => 'Prof. Catherine Gomez, MSHM',
            'BSTM' => 'Prof. Kevin Durano, MSTM',
            'BEEd' => 'Dr. Rodrigo Perez, Ed.D.',
            'BSEd-ENG' => 'Prof. Lorna Enriquez, MAEd',
            'BSEd-FIL' => 'Prof. Maria Santos, MAEd',
            'BSEd-MATH' => 'Prof. Danilo Navarro, MST-Math',
        ];

        foreach ($previousCourseInstructors as $code => $instructor) {
            DB::table('courses')
                ->where('code', $code)
                ->update(['instructor' => $instructor]);
        }
    }
};
