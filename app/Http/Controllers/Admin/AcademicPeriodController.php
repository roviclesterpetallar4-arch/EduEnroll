<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicPeriodController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year_start' => [
                'required',
                'integer',
                'between:2000,2099',
                'unique:academic_periods,year_start',
            ],
        ]);

        AcademicPeriod::create([
            'year_start' => $validated['year_start'],
            'year_end' => $validated['year_start'] + 1,
            'is_active' => false,
        ]);

        return back()->with('success', 'Academic period record created. Activate it when you want it shown in the header.');
    }

    public function activate(AcademicPeriod $academicPeriod): RedirectResponse
    {
        DB::transaction(function () use ($academicPeriod): void {
            AcademicPeriod::query()->lockForUpdate()->get();
            AcademicPeriod::query()->update(['is_active' => false]);
            $academicPeriod->update(['is_active' => true]);
        });

        return back()->with('success', 'The active academic period was updated.');
    }
}
