<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\SavingsBook;
use App\Models\SavingsTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Get teacher's class
     */
    private function getMyClass()
    {
        return ClassRoom::where('teacher_id', auth()->id())->first();
    }

    /**
     * Teacher Dashboard
     */
       public function dashboard()
    {
        $myClass = $this->getMyClass();

        if (!$myClass) {
            return view('teacher.dashboard')->with([
                'myClass' => null,
                'stats' => [
                    'total_students' => 0,
                    'attendance_today' => 0,
                    'pending_grades' => 0,
                    'grades_inputted' => 0,
                    'total_savings' => 0,
                ],
                'todayAttendance' => collect([]),
                'weekStats' => [],
                'recentActivities' => collect([]),
            ]);
        }

        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        // Get students in this class for current academic year
        $students = Student::whereHas('enrollments', function($q) use ($myClass, $currentAcademicYear) {
            $q->where('class_id', $myClass->id);
            if ($currentAcademicYear) {
                $q->where('academic_year_id', $currentAcademicYear->id);
            }
        })->get();

        // Calculate grades statistics
        $totalSubjects = Subject::count();
        $totalStudents = $students->count();
        
        // Determine current semester
        $currentSemester = $this->getCurrentSemester();
        
        // Total nilai yang harus diinput = total siswa × total mata pelajaran
        $totalGradesExpected = $totalStudents * $totalSubjects;
        
        // Hitung berapa nilai yang sudah diinput
        $gradesInputted = 0;
        if ($currentAcademicYear) {
            $gradesInputted = Grade::whereIn('student_id', $students->pluck('id'))
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('semester', $currentSemester)
                ->count();
        }
        
        // Nilai yang belum diinput
        $pendingGrades = max(0, $totalGradesExpected - $gradesInputted);

        $stats = [
            'total_students' => $totalStudents,
            'attendance_today' => Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereDate('date', today())
                ->where('status', 'hadir')
                ->count(),
            'pending_grades' => $pendingGrades,
            'grades_inputted' => $gradesInputted,
            'total_expected' => $totalGradesExpected,
            'total_savings' => SavingsBook::whereIn('student_id', $students->pluck('id'))
                ->sum('balance'),
        ];

        $todayAttendance = Attendance::with('student')
            ->whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', today())
            ->latest()
            ->get();

        // Week stats
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $weekAttendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();

        $weekStats = [
            'hadir' => $weekAttendances->where('status', 'hadir')->count(),
            'izin' => $weekAttendances->where('status', 'izin')->count(),
            'sakit' => $weekAttendances->where('status', 'sakit')->count(),
            'alpha' => $weekAttendances->where('status', 'alpha')->count(),
        ];

        // Recent activities
        $recentActivities = collect([]);

        return view('teacher.dashboard', compact('myClass', 'stats', 'todayAttendance', 'weekStats', 'recentActivities'));
    }

    /**
     * Determine current semester based on month
     * Adjust this logic based on your school's academic calendar
     */
    private function getCurrentSemester()
{
    $currentMonth = now()->month;
    
    // Contoh: Semester 1 (Agustus-Desember), Semester 2 (Januari-Juni)
    if ($currentMonth >= 8 || $currentMonth <= 12) {
        return '1';
    } else {
        return '2';
    }
}

    /**
     * Attendance Management
     */
    public function attendance()
    {
        $class = $this->getMyClass();
        
        if (!$class) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Anda belum memiliki kelas');
        }

        $date = request('date', date('Y-m-d'));

        $students = Student::whereHas('enrollments', function($q) use ($class) {
            $q->where('class_id', $class->id)
              ->whereHas('academicYear', function($q2) {
                  $q2->where('is_active', true);
              });
        })->with(['attendances' => function($q) use ($date) {
            $q->where('date', $date);
        }])->orderBy('name')->get();

        $hasAttendance = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)
            ->exists();

        // Attendance history (last 7 days)
        $attendanceHistory = collect();
        for ($i = 6; $i >= 0; $i--) {
            $checkDate = Carbon::today()->subDays($i);
            $dayAttendances = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereDate('date', $checkDate)
                ->get();

            $attendanceHistory->push((object)[
                'date' => $checkDate,
                'hadir' => $dayAttendances->where('status', 'hadir')->count(),
                'izin' => $dayAttendances->where('status', 'izin')->count(),
                'sakit' => $dayAttendances->where('status', 'sakit')->count(),
                'alpha' => $dayAttendances->where('status', 'alpha')->count(),
                'total' => $dayAttendances->count(),
            ]);
        }

        return view('teacher.attendance.index', compact('class', 'students', 'date', 'hasAttendance', 'attendanceHistory'));
    }

   public function storeAttendance(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'class_id' => 'required|exists:classes,id',
        'students' => 'required|array',
    ]);

    $class = ClassRoom::findOrFail($request->class_id);

    // Verify teacher owns this class
    if ($class->teacher_id !== auth()->id()) {
        return back()->with('error', 'Anda tidak memiliki akses ke kelas ini');
    }

    DB::beginTransaction();
    try {
        foreach ($request->students as $studentId => $studentData) {
            // Skip jika tidak ada status yang dipilih
            if (!isset($studentData['status']) || empty($studentData['status'])) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $class->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $studentData['status'],
                    'check_in' => isset($studentData['check_in']) && $studentData['check_in'] 
                        ? Carbon::parse($request->date . ' ' . $studentData['check_in']) 
                        : null,
                    'notes' => $studentData['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]
            );
        }

        DB::commit();

        return redirect()->route('teacher.attendance.index', ['date' => $request->date])
            ->with('success', 'Absensi berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    /**
     * Grades Management
     */
    public function grades()
    {
        $class = $this->getMyClass();
        
        if (!$class) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Anda belum memiliki kelas');
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();
        $subjects = Subject::orderBy('order')->get();

        $selectedAcademicYear = null;
        $selectedSubject = null;
        $selectedSemester = request('semester', '1'); // Default semester 1
        $students = collect();

        if (request('academic_year') && request('subject_id')) {
            $selectedAcademicYear = AcademicYear::find(request('academic_year'));
            $selectedSubject = Subject::find(request('subject_id'));

            if ($selectedAcademicYear && $selectedSubject) {
                $students = Student::whereHas('enrollments', function($q) use ($class, $selectedAcademicYear) {
                    $q->where('class_id', $class->id)
                      ->where('academic_year_id', $selectedAcademicYear->id);
                })->with(['grades' => function($q) use ($selectedSubject, $selectedAcademicYear, $selectedSemester) {
                    $q->where('subject_id', $selectedSubject->id)
                      ->where('academic_year_id', $selectedAcademicYear->id)
                      ->where('semester', $selectedSemester);
                }])->orderBy('name')->get();
            }
        }

        return view('teacher.grades.index', compact(
            'class', 
            'academicYears', 
            'currentAcademicYear', 
            'subjects', 
            'selectedAcademicYear', 
            'selectedSubject',
            'selectedSemester',
            'students'
        ));
    }

    public function storeGrades(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'semester' => 'required|in:1,2',
            'grades' => 'required|array',
            'grades.*.score' => 'nullable|numeric|min:0|max:100',
        ]);

        $class = ClassRoom::findOrFail($request->class_id);

        // Verify teacher owns this class
        if ($class->teacher_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke kelas ini');
        }

        DB::beginTransaction();
        try {
            foreach ($request->grades as $studentId => $gradeData) {
                // Skip jika tidak ada score
                if (!isset($gradeData['score']) || $gradeData['score'] === '' || $gradeData['score'] === null) {
                    continue;
                }

                Grade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $request->subject_id,
                        'academic_year_id' => $request->academic_year_id,
                        'class_id' => $request->class_id,
                        'semester' => $request->semester,
                    ],
                    [
                        'score' => $gradeData['score'],
                        'grade' => $gradeData['grade'] ?? $this->calculateGrade($gradeData['score']),
                        'predicate' => $gradeData['predicate'] ?? $this->calculatePredicate($gradeData['score']),
                        'description' => $gradeData['description'] ?? null,
                        'teacher_id' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('teacher.grades.index', [
                'academic_year' => $request->academic_year_id,
                'subject_id' => $request->subject_id,
                'semester' => $request->semester,
            ])->with('success', 'Nilai berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade from score
     */
    private function calculateGrade($score)
    {
        if ($score >= 86) return 'A';
        if ($score >= 71) return 'B';
        if ($score >= 56) return 'C';
        if ($score >= 41) return 'D';
        return 'E';
    }

    /**
     * Calculate predicate from score
     */
    private function calculatePredicate($score)
    {
        if ($score >= 86) return 'BSB';
        if ($score >= 71) return 'BSH';
        return 'MB';
    }


    /**
     * Savings Management
     */
    public function savings()
    {
        $class = $this->getMyClass();
        
        if (!$class) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Anda belum memiliki kelas');
        }

        $students = Student::whereHas('enrollments', function($q) use ($class) {
            $q->where('class_id', $class->id)
              ->whereHas('academicYear', function($q2) {
                  $q2->where('is_active', true);
              });
        })->with(['savingsBook.transactions' => function($q) {
            $q->latest();
        }])->orderBy('name')->get();

        $totalSavings = SavingsBook::whereIn('student_id', $students->pluck('id'))->sum('balance');
        $activeSavers = SavingsBook::whereIn('student_id', $students->pluck('id'))
            ->where('balance', '>', 0)
            ->count();
        $monthlyTransactions = SavingsTransaction::whereHas('savingsBook', function($q) use ($students) {
            $q->whereIn('student_id', $students->pluck('id'));
        })->whereMonth('created_at', now()->month)->count();

        return view('teacher.savings.index', compact(
            'class', 
            'students', 
            'totalSavings', 
            'activeSavers', 
            'monthlyTransactions'
        ));
    }

    public function savingsTransaction(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'type' => 'required|in:deposit,withdraw',
        'amount' => 'required|numeric|min:1000',
        'notes' => 'nullable|string',
    ]);

    $student = Student::findOrFail($request->student_id);

    $class = $this->getMyClass();
    if (!$student->enrollments()->where('class_id', $class->id)->exists()) {
        return back()->with('error', 'Siswa tidak ada di kelas Anda');
    }

    DB::beginTransaction();
    try {
        $savingsBook = SavingsBook::firstOrCreate(
            ['student_id' => $student->id],
            [
                'book_number' => $this->generateBookNumber(),
                'balance' => 0,
                'opened_date' => now(),
            ]
        );

        if ($request->type === 'withdraw' && $savingsBook->balance < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        $balanceBefore = $savingsBook->balance;
        $balanceAfter = $request->type === 'deposit'
            ? $balanceBefore + $request->amount
            : $balanceBefore - $request->amount;

        SavingsTransaction::create([
            'savings_book_id' => $savingsBook->id,
            'transaction_code' => $this->generateTransactionCode(),
            'transaction_date' => now(),
            'type' => $request->type,
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $savingsBook->update([
            'balance' => $balanceAfter
        ]);

        DB::commit();

        return redirect()->route('teacher.savings.index')
            ->with('success', $request->type === 'deposit'
                ? 'Setoran berhasil disimpan'
                : 'Penarikan berhasil diproses'
            );

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}


    /**
     * Generate Book Number
     */
    private function generateBookNumber()
    {
        $lastBook = SavingsBook::whereYear('created_at', now()->year)->latest()->first();
        $lastNumber = $lastBook ? intval(substr($lastBook->book_number, -4)) : 0;
        return 'TAB-' . now()->format('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Transaction Code
     */
    private function generateTransactionCode()
    {
        $lastTrx = SavingsTransaction::whereYear('created_at', now()->year)->latest()->first();
        $lastNumber = $lastTrx ? intval(substr($lastTrx->transaction_code, -4)) : 0;
        return 'TRX-' . now()->format('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}