<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\User;
use App\Models\Attendance;
use App\Models\SavingsBook;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    /**
     * Owner Dashboard
     */
    public function dashboard()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => User::where('role', 'guru')->where('is_active', true)->count(),
            'total_parents' => User::where('role', 'orang_tua')->where('is_active', true)->count(),
            'total_revenue_month' => Payment::where('status', 'verified')
                ->whereMonth('verified_at', now()->month)
                ->sum('amount'),
            'total_revenue_year' => Payment::where('status', 'verified')
                ->whereYear('verified_at', now()->year)
                ->sum('amount'),
        ];

        // Revenue per month (current year)
        $revenuePerMonth = Payment::select(
            DB::raw('MONTH(verified_at) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->where('status', 'verified')
            ->whereYear('verified_at', now()->year)
            ->groupBy('month')
            ->get()
            ->pluck('total', 'month');

        return view('owner.dashboard', compact('stats', 'revenuePerMonth'));
    }

    /**
     * Financial Report
     */
    public function financialReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Total pembayaran yang sudah verified
        $verifiedPayments = Payment::with(['student', 'paymentType'])
            ->where('status', 'verified')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $verifiedPayments->sum('amount');

        // Breakdown by payment type
        $revenueByType = $verifiedPayments->groupBy('payment_type_id')->map(function ($payments) {
            return [
                'type' => $payments->first()->paymentType->name,
                'count' => $payments->count(),
                'total' => $payments->sum('amount'),
            ];
        });

        // Pending payments
        $pendingPayments = Payment::with(['student', 'paymentType'])
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPending = $pendingPayments->sum('amount');

        return view('owner.reports.financial', compact(
            'startDate',
            'endDate',
            'verifiedPayments',
            'totalRevenue',
            'revenueByType',
            'pendingPayments',
            'totalPending'
        ));
    }

    /**
     * Attendance Report
     */
    public function attendanceReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Total absensi by status
        $attendanceSummary = Attendance::select('status', DB::raw('count(*) as total'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Attendance by class
        $attendanceByClass = Attendance::with(['classRoom'])
            ->select('class_id', 'status', DB::raw('count(*) as total'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('class_id', 'status')
            ->get()
            ->groupBy('class_id');

        // Students with high absence rate (> 3 kali alpha/sakit/izin)
        $studentsWithHighAbsence = Student::with('currentEnrollment.classRoom')
            ->whereHas('attendances', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereIn('status', ['alpha', 'sakit', 'izin']);
            }, '>', 3)
            ->withCount([
                'attendances as absence_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate])
                        ->whereIn('status', ['alpha', 'sakit', 'izin']);
                }
            ])
            ->get();

        return view('owner.reports.attendance', compact(
            'startDate',
            'endDate',
            'attendanceSummary',
            'attendanceByClass',
            'studentsWithHighAbsence'
        ));
    }

    /**
     * Student Report
     */
    public function studentReport()
    {
        $academicYear = request('academic_year_id')
            ? AcademicYear::find(request('academic_year_id'))
            : AcademicYear::where('is_active', true)->first();

        // Total students by class
        $studentsByClass = DB::table('enrollments')
            ->join('classes', 'enrollments.class_id', '=', 'classes.id')
            ->where('enrollments.academic_year_id', $academicYear->id)
            ->where('enrollments.status', 'active')
            ->select('classes.name', DB::raw('count(*) as total'))
            ->groupBy('classes.id', 'classes.name')
            ->get();

        // Total students by gender
        $studentsByGender = Student::select('gender', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender');

        // New students this academic year
        $newStudents = Student::with('parent')
            ->whereBetween('registration_date', [$academicYear->start_date, $academicYear->end_date])
            ->get();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('owner.reports.student', compact(
            'academicYear',
            'academicYears',
            'studentsByClass',
            'studentsByGender',
            'newStudents'
        ));
    }

    /**
     * Savings Report
     */
    public function savingsReport()
    {
        $totalSavings = SavingsBook::sum('balance');
        
        $savingsBooks = SavingsBook::with(['student.currentEnrollment.classRoom'])
            ->where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->get();

        // Total transactions this month
        $monthlyTransactions = DB::table('savings_transactions')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->select(
                'type',
                DB::raw('count(*) as count'),
                DB::raw('sum(amount) as total')
            )
            ->groupBy('type')
            ->get();

        return view('owner.reports.savings', compact(
            'totalSavings',
            'savingsBooks',
            'monthlyTransactions'
        ));
    }
}