<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GradeSeeder extends Seeder
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

        // Get all subjects
        $subjects = Subject::all();

        if ($subjects->isEmpty()) {
            echo "✗ No subjects found!\n";
            return;
        }

        // Get all active enrollments
        $enrollments = Enrollment::where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->with(['student', 'classRoom'])
            ->get();

        if ($enrollments->isEmpty()) {
            echo "✗ No active enrollments found!\n";
            return;
        }

        $gradeCount = 0;

        // Create grades for each student
        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $class = $enrollment->classRoom;
            $teacherId = $class->teacher_id;

            // Create grades for each subject
            foreach ($subjects as $subject) {
                // Semester 1
                $this->createGrade(
                    $student->id,
                    $subject->id,
                    $academicYear->id,
                    $class->id,
                    '1',
                    $teacherId
                );
                $gradeCount++;

                // Semester 2 (optional - bisa dikomentari jika belum waktunya)
                // $this->createGrade(
                //     $student->id,
                //     $subject->id,
                //     $academicYear->id,
                //     $class->id,
                //     '2',
                //     $teacherId
                // );
                // $gradeCount++;
            }
        }

        echo "✓ {$gradeCount} grades created\n";
        echo "  - Students: {$enrollments->count()}\n";
        echo "  - Subjects: {$subjects->count()}\n";
    }

    /**
     * Create a single grade entry
     */
    private function createGrade($studentId, $subjectId, $academicYearId, $classId, $semester, $teacherId)
    {
        // Generate random score between 60-100
        $score = rand(60, 100);

        // Determine grade based on score
        $grade = $this->getGrade($score);

        // Determine predicate for TK (MB, BSH, BSB)
        $predicate = $this->getPredicate($score);

        // Generate description based on predicate
        $descriptions = [
            'MB' => [
                'Mulai menunjukkan perkembangan dalam aspek ini',
                'Perlu pendampingan lebih intensif',
                'Masih memerlukan bimbingan guru',
                'Sedang dalam proses berkembang',
            ],
            'BSH' => [
                'Berkembang dengan baik sesuai harapan',
                'Menunjukkan perkembangan yang konsisten',
                'Mampu mengikuti kegiatan dengan baik',
                'Perkembangan sesuai dengan usianya',
            ],
            'BSB' => [
                'Berkembang sangat baik dan melampaui ekspektasi',
                'Menunjukkan kemampuan yang sangat baik',
                'Mampu mengembangkan kemampuan secara mandiri',
                'Perkembangan melampaui anak seusianya',
            ],
        ];

        $description = $descriptions[$predicate][array_rand($descriptions[$predicate])];

        Grade::create([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'academic_year_id' => $academicYearId,
            'class_id' => $classId,
            'semester' => $semester,
            'score' => $score,
            'grade' => $grade,
            'predicate' => $predicate,
            'description' => $description,
            'teacher_id' => $teacherId,
        ]);
    }

    /**
     * Get letter grade from score
     */
    private function getGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }

    /**
     * Get predicate for TK from score
     */
    private function getPredicate($score)
    {
        if ($score >= 85) return 'BSB'; // Berkembang Sangat Baik
        if ($score >= 70) return 'BSH'; // Berkembang Sesuai Harapan
        return 'MB'; // Mulai Berkembang
    }
}