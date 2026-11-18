<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departments')
            ->where('code', 'EDSO')
            ->where('name', 'Education and Society Courses')
            ->update([
                'name' => 'Education and Society Organization',
                'description' => 'Academic organization for elementary and secondary teacher education programs.',
            ]);
    }

    public function down(): void
    {
        DB::table('departments')
            ->where('code', 'EDSO')
            ->where('name', 'Education and Society Organization')
            ->update([
                'name' => 'Education and Society Courses',
                'description' => 'Academic cluster and society for elementary and secondary teacher education programs.',
            ]);
    }
};
