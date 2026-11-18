<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departments')->where('code', 'BA')->update([
            'name' => 'Junior Management Society',
            'code' => 'JMS',
        ]);

        DB::table('departments')->where('code', 'HM')->update([
            'name' => 'Hospitality Management Society',
            'code' => 'HMS',
        ]);

        DB::table('departments')->where('code', 'BTS')->update([
            'name' => 'Tourism Management Society',
            'code' => 'TMS',
        ]);
    }

    public function down(): void
    {
        DB::table('departments')->where('code', 'JMS')->update([
            'name' => 'Business Administration',
            'code' => 'BA',
        ]);

        DB::table('departments')->where('code', 'HMS')->update([
            'name' => 'Hospitality Management',
            'code' => 'HM',
        ]);

        DB::table('departments')->where('code', 'TMS')->update([
            'name' => 'Bachelor of Tourism Studies',
            'code' => 'BTS',
        ]);
    }
};
