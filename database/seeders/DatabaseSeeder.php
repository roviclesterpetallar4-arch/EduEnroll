<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database with the exact 9 collegiate courses offered by
     * Northeastern Cebu Colleges, Inc. (Danao City, Cebu), structured
     * under the official student organizations / academic course clusters:
     * - Education and Society Organization (EDSO)
     * - Information Technology Organization (ITO)
     * - Tourism Management Society (TMS)
     * - Hospitality Management Society (HMS)
     * - Junior Management Society (JMS)
     */
    public function run(): void
    {
        // 1. Education and Society Organization (EDSO)
        $deptEDSO = Department::create([
            'name' => 'Education and Society Organization',
            'code' => 'EDSO',
            'head' => 'Dr. Maria Liza Laborte Moscosa',
            'description' => 'Academic organization for elementary and secondary teacher education programs.',
        ]);

        // 2. Information Technology Organization (ITO)
        $deptITO = Department::create([
            'name' => 'Information Technology Organization',
            'code' => 'ITO',
            'head' => 'Dr. Jomar Lavador',
            'description' => 'Academic cluster and student organization for computing and practical technology programs (ACT and BSIT).',
        ]);

        // 3. Tourism Management Society (TMS)
        $deptTMS = Department::create([
            'name' => 'Tourism Management Society',
            'code' => 'TMS',
            'head' => 'Ms. Rogen Semblante',
            'description' => 'Academic cluster and society for tourism management, ecotourism, travel geography, and destination planning.',
        ]);

        // 4. Hospitality Management Society (HMS)
        $deptHMS = Department::create([
            'name' => 'Hospitality Management Society',
            'code' => 'HMS',
            'head' => 'Dr. Francisco C. Gatdula',
            'description' => 'Academic cluster and society for hotel front-office administration, culinary arts, and guest services.',
        ]);

        // 5. Junior Management Society (JMS)
        $deptJMS = Department::create([
            'name' => 'Junior Management Society',
            'code' => 'JMS',
            'head' => 'Dr. Eden Hornido',
            'description' => 'Academic cluster and society for corporate management, financial stewardship, marketing, and entrepreneurship.',
        ]);

        // The EXACT 9 Courses Offered by Northeastern Cebu Colleges
        $coursesData = [
            // ITO Courses (2 Courses)
            [
                'department_id' => $deptITO->id,
                'code' => 'ACT',
                'title' => 'Associate in Computer Technology',
                'units' => 3,
                'instructor' => 'Dr. Jomar Lavador',
                'prerequisites' => 'Senior High School Graduate',
                'description' => 'A comprehensive two-year technical program covering PC hardware maintenance, Python programming, logic formulation, networking basics, and web application development.',
            ],
            [
                'department_id' => $deptITO->id,
                'code' => 'BSIT',
                'title' => 'Bachelor of Science in Information Technology',
                'units' => 3,
                'instructor' => 'Dr. Jomar Lavador',
                'prerequisites' => 'Graduate of the Associate in Computer Technology (ACT) program at Northeastern Cebu Colleges',
                'description' => 'A four-year degree program in software engineering, database systems, web systems development (Laravel/PHP), systems integration, cybersecurity, and cloud solutions.',
            ],

            // BA Course (1 Course)
            [
                'department_id' => $deptJMS->id,
                'code' => 'BSBA',
                'title' => 'Bachelor of Science in Business Administration',
                'units' => 3,
                'instructor' => 'Dr. Eden Hornido',
                'prerequisites' => 'Senior High School Graduate (ABM Track preferred)',
                'description' => 'A four-year collegiate course in corporate management, financial stewardship, market research, human resource administration, and business entrepreneurship.',
            ],

            // HM Course (1 Course)
            [
                'department_id' => $deptHMS->id,
                'code' => 'BSHM',
                'title' => 'Bachelor of Science in Hospitality Management',
                'units' => 3,
                'instructor' => 'Dr. Francisco C. Gatdula',
                'prerequisites' => 'Senior High School Graduate (TVL-HE Track preferred)',
                'description' => 'A four-year professional degree program providing intensive training in hotel front office procedures, culinary arts, banquet operations, and guest service standards.',
            ],

            // TMS Course (1 Course)
            [
                'department_id' => $deptTMS->id,
                'code' => 'BSTM',
                'title' => 'Bachelor of Science in Tourism Management',
                'units' => 3,
                'instructor' => 'Ms. Rogen Semblante',
                'prerequisites' => 'Senior High School Graduate',
                'description' => 'A four-year program emphasizing Philippine tourism geography, airline ticketing, global travel operations, ecotourism planning, and destination marketing.',
            ],

            // EDSO Courses (4 Courses)
            [
                'department_id' => $deptEDSO->id,
                'code' => 'BEEd',
                'title' => 'Bachelor of Elementary Education',
                'units' => 3,
                'instructor' => 'Dr. Maria Liza Laborte Moscosa',
                'prerequisites' => 'Senior High School Graduate (LET Track)',
                'description' => 'A four-year teacher education degree preparing future elementary educators in child development, primary grade mathematics, elementary science, and values education.',
            ],
            [
                'department_id' => $deptEDSO->id,
                'code' => 'BSEd-ENG',
                'title' => 'Bachelor of Secondary Education Major in English',
                'units' => 3,
                'instructor' => 'Dr. Maria Liza Laborte Moscosa',
                'prerequisites' => 'Senior High School Graduate with English Proficiency',
                'description' => 'A four-year specialized secondary education program focusing on English language mastery, linguistics, literary analysis, language curriculum design, and secondary pedagogy.',
            ],
            [
                'department_id' => $deptEDSO->id,
                'code' => 'BSEd-FIL',
                'title' => 'Bachelor of Secondary Education Major in Filipino',
                'units' => 3,
                'instructor' => 'Dr. Maria Liza Laborte Moscosa',
                'prerequisites' => 'Senior High School Graduate with Filipino Proficiency',
                'description' => 'Isang apat na taong kurso sa edukasyong sekundarya na nakatuon sa pagsusuri ng balarila, panitikang Pilipino, pagpapahalagang pampanitikan, at makabagong pamamaraan ng pagtuturo.',
            ],
            [
                'department_id' => $deptEDSO->id,
                'code' => 'BSEd-MATH',
                'title' => 'Bachelor of Secondary Education Major in Math',
                'units' => 3,
                'instructor' => 'Dr. Maria Liza Laborte Moscosa',
                'prerequisites' => 'Senior High School Graduate (STEM Track preferred)',
                'description' => 'A four-year secondary education degree specializing in advanced algebra, plane geometry, trigonometry, calculus, statistics, and student-centered mathematics instruction.',
            ],
        ];

        $createdCourses = collect();

        foreach ($coursesData as $courseItem) {
            $course = Course::create($courseItem);
            $createdCourses->push($course);
        }

        // Realistic Students Enrolled Across All 9 NCC Courses
        $studentsData = [
            ['name' => 'Alexander B. Wright', 'email' => 'a.wright@ncc.edu.ph', 'student_number' => 'NCC-2024-00101', 'year_level' => 2, 'major' => 'Associate in Computer Technology'],
            ['name' => 'Sophia Mae Delgado', 'email' => 's.delgado@ncc.edu.ph', 'student_number' => 'NCC-2024-00102', 'year_level' => 3, 'major' => 'Bachelor of Science in Information Technology'],
            ['name' => 'Liam Joshua Chen', 'email' => 'l.chen@ncc.edu.ph', 'student_number' => 'NCC-2024-00103', 'year_level' => 1, 'major' => 'Associate in Computer Technology'],
            ['name' => 'Maya Angela Patel', 'email' => 'm.patel@ncc.edu.ph', 'student_number' => 'NCC-2024-00104', 'year_level' => 2, 'major' => 'Bachelor of Science in Information Technology'],
            ['name' => 'Lucas Mark Morales', 'email' => 'l.morales@ncc.edu.ph', 'student_number' => 'NCC-2024-00105', 'year_level' => 3, 'major' => 'Bachelor of Science in Business Administration'],
            ['name' => 'Emma Louise Watson', 'email' => 'e.watson@ncc.edu.ph', 'student_number' => 'NCC-2024-00106', 'year_level' => 2, 'major' => 'Bachelor of Science in Hospitality Management'],
            ['name' => 'Noah James Rivera', 'email' => 'n.rivera@ncc.edu.ph', 'student_number' => 'NCC-2024-00107', 'year_level' => 1, 'major' => 'Bachelor of Science in Tourism Management'],
            ['name' => 'Isabella Grace Santos', 'email' => 'i.santos@ncc.edu.ph', 'student_number' => 'NCC-2024-00108', 'year_level' => 4, 'major' => 'Bachelor of Elementary Education'],
            ['name' => 'Ethan Dave Hayes', 'email' => 'e.hayes@ncc.edu.ph', 'student_number' => 'NCC-2024-00109', 'year_level' => 3, 'major' => 'Bachelor of Secondary Education Major in English'],
            ['name' => 'Olivia Rose Ramos', 'email' => 'o.ramos@ncc.edu.ph', 'student_number' => 'NCC-2024-00110', 'year_level' => 2, 'major' => 'Bachelor of Secondary Education Major in Filipino'],
            ['name' => 'Gabriel Kenjie Tan', 'email' => 'g.tan@ncc.edu.ph', 'student_number' => 'NCC-2024-00111', 'year_level' => 2, 'major' => 'Bachelor of Secondary Education Major in Math'],
            ['name' => 'Chloe Anne Mercado', 'email' => 'c.mercado@ncc.edu.ph', 'student_number' => 'NCC-2024-00112', 'year_level' => 1, 'major' => 'Bachelor of Science in Hospitality Management'],
            ['name' => 'Joshua Paul Villa', 'email' => 'j.villa@ncc.edu.ph', 'student_number' => 'NCC-2024-00113', 'year_level' => 3, 'major' => 'Bachelor of Science in Business Administration'],
            ['name' => 'Hannah Marie Yap', 'email' => 'h.yap@ncc.edu.ph', 'student_number' => 'NCC-2024-00114', 'year_level' => 2, 'major' => 'Bachelor of Science in Tourism Management'],
            ['name' => 'Jelo Cristobal', 'email' => 'j.cristobal@ncc.edu.ph', 'student_number' => 'NCC-2024-00115', 'year_level' => 1, 'major' => 'Bachelor of Elementary Education'],
        ];

        foreach ($studentsData as $studentItem) {
            $user = User::create([
                'name' => $studentItem['name'],
                'email' => $studentItem['email'],
                'password' => Hash::make('password'),
            ]);

            $primaryCourse = $createdCourses->firstWhere('title', $studentItem['major']);
            StudentProfile::create([
                'user_id' => $user->id,
                'course_id' => $primaryCourse?->id,
                'student_number' => $studentItem['student_number'],
                'year_level' => $studentItem['year_level'],
                'major' => $studentItem['major'],
            ]);

            // Enroll student into 1 or 2 matching courses
            $enrolledCourses = $createdCourses->random(rand(1, 2));
            foreach ($enrolledCourses as $course) {
                $course->students()->attach($user->id, [
                    'status' => 'enrolled',
                    'created_at' => now()->subDays(rand(2, 20)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
