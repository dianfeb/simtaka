<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnrollmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            echo "✗ No active academic year found!\n";
            return;
        }

        // Get all classes
        $tkA = ClassRoom::where('level', 'A')->first();
        $tkB = ClassRoom::where('level', 'B')->first();

        if (!$tkA || !$tkB) {
            echo "✗ Classes not found!\n";
            return;
        }

        // Get all students
        $students = Student::all();

        if ($students->isEmpty()) {
            echo "✗ No students found!\n";
            return;
        }

        // Split students: half to TK A, half to TK B
        $studentsArray = $students->shuffle();
        $halfCount = ceil($studentsArray->count() / 2);
        
        $studentsForTKA = $studentsArray->take($halfCount);
        $studentsForTKB = $studentsArray->skip($halfCount);

        $enrollmentCount = 0;

        // Enroll students to TK A
        foreach ($studentsForTKA as $student) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $tkA->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'active',
                'enrollment_date' => $academicYear->start_date,
                'notes' => 'Enrolled via seeder',
            ]);
            $enrollmentCount++;
        }

        // Enroll students to TK B
        foreach ($studentsForTKB as $student) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $tkB->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'active',
                'enrollment_date' => $academicYear->start_date,
                'notes' => 'Enrolled via seeder',
            ]);
            $enrollmentCount++;
        }

        echo "✓ {$enrollmentCount} enrollments created\n";
        echo "  - TK A: {$studentsForTKA->count()} students\n";
        echo "  - TK B: {$studentsForTKB->count()} students\n";
    }
}