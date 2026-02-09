<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\SavingsBook;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Teacher Dashboard
     */
    public function dashboard()
    {
        $teacher = auth()->user();
        $classes = $teacher->classes()->with('activeStudents')->get();
        
        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $classes->sum(fn($class) => $class->activeStudents->count()),
            'today_attendance' => Attendance::whereDate('date', today())
                ->where('created_by', $teacher->id)
                ->count(),
        ];

        return view('teacher.dashboard', compact('stats', 'classes'));
    }

    /**
     * Attendance Management
     */
    public function attendanceIndex(ClassRoom $class)
    {
        $this->authorize('view', $class); // Pastikan guru hanya bisa lihat kelasnya sendiri

        $date = request('date', today()->format('Y-m-d'));
        
        $students = $class->activeStudents()
            ->with(['attendances' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }])
            ->get();

        return view('teacher.attendance.index', compact('class', 'students', 'date'));
    }

    public function storeAttendance(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpha',
            'attendances.*.check_in' => 'nullable|date_format:H:i',
            'attendances.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $data) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $data['student_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'class_id' => $class->id,
                        'status' => $data['status'],
                        'check_in' => $data['check_in'] ?? null,
                        'notes' => $data['notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'Absensi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Grade Management
     */
    public function gradeIndex(ClassRoom $class)
    {
        $this->authorize('view', $class);

        $academicYear = AcademicYear::where('is_active', true)->first();
        $semester = request('semester', '1');
        $subjects = Subject::orderBy('order')->get();

        $students = $class->activeStudents()
            ->with(['grades' => function ($query) use ($academicYear, $semester) {
                $query->where('academic_year_id', $academicYear->id)
                    ->where('semester', $semester)
                    ->with('subject');
            }])
            ->get();

        return view('teacher.grades.index', compact('class', 'students', 'subjects', 'semester', 'academicYear'));
    }

    public function storeGrade(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|in:1,2',
            'score' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $academicYear = AcademicYear::where('is_active', true)->first();

        Grade::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'academic_year_id' => $academicYear->id,
                'semester' => $validated['semester'],
            ],
            [
                'class_id' => $class->id,
                'score' => $validated['score'],
                'description' => $validated['description'] ?? null,
                'teacher_id' => auth()->id(),
            ]
        );

        return back()->with('success', 'Nilai berhasil disimpan');
    }

    /**
     * Savings Book Management
     */
    public function savingsIndex(ClassRoom $class)
    {
        $this->authorize('view', $class);

        $students = $class->activeStudents()
            ->with('savingsBook')
            ->get();

        return view('teacher.savings.index', compact('class', 'students'));
    }

    public function savingsShow(Student $student)
    {
        $savingsBook = $student->savingsBook()->with('transactions')->first();

        if (!$savingsBook) {
            // Create savings book if not exists
            $savingsBook = SavingsBook::create([
                'student_id' => $student->id,
            ]);
        }

        return view('teacher.savings.show', compact('student', 'savingsBook'));
    }

    public function savingsTransaction(Request $request, Student $student)
    {
        $validated = $request->validate([
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string',
        ]);

        $savingsBook = $student->savingsBook;

        if (!$savingsBook) {
            $savingsBook = SavingsBook::create(['student_id' => $student->id]);
        }

        try {
            if ($validated['type'] === 'debit') {
                $savingsBook->deposit(
                    $validated['amount'],
                    $validated['description'] ?? 'Setoran tabungan',
                    auth()->id()
                );
            } else {
                $savingsBook->withdraw(
                    $validated['amount'],
                    $validated['description'] ?? 'Penarikan tabungan',
                    auth()->id()
                );
            }

            return back()->with('success', 'Transaksi berhasil');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}