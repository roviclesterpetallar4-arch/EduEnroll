<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')
            ->where('code', 'BSIT')
            ->where('prerequisites', 'Senior High School Graduate (STEM/ICT Track preferred)')
            ->update([
                'prerequisites' => 'Graduate of the Associate in Computer Technology (ACT) program at Northeastern Cebu Colleges',
            ]);
    }

    public function down(): void
    {
        DB::table('courses')
            ->where('code', 'BSIT')
            ->where('prerequisites', 'Graduate of the Associate in Computer Technology (ACT) program at Northeastern Cebu Colleges')
            ->update([
                'prerequisites' => 'Senior High School Graduate (STEM/ICT Track preferred)',
            ]);
    }
};
