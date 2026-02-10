<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Payment;
use App\Models\SavingsBook;
use App\Models\SavingsTransaction;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerController extends Controller
{
    /**
     * Owner Dashboard
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'today');

        // Date range based on period
        switch ($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default: // today
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
        }

        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'pending_students' => Student::where('status', 'pending')->count(),
            'total_classes' => ClassRoom::count(),
            'total_teachers' => User::where('role', 'guru')->count(),
            'revenue' => Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'verified_payments' => Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_savings' => SavingsBook::sum('balance'),
            'active_savers' => SavingsBook::where('balance', '>', 0)->count(),
        ];

        // Chart data - Revenue last 7 days
        $revenueLast7Days = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData['revenue'] = [
            'labels' => $revenueLast7Days->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray(),
            'values' => $revenueLast7Days->pluck('total')->toArray(),
        ];

        // Chart data - Attendance this week
        $attendanceThisWeek = Attendance::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('DATE(date) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $weekDates = collect();
        for ($i = 0; $i < 7; $i++) {
            $weekDates->push(now()->startOfWeek()->addDays($i)->format('Y-m-d'));
        }

        $chartData['attendance'] = [
            'labels' => $weekDates->map(fn($d) => Carbon::parse($d)->format('D'))->toArray(),
            'hadir' => $weekDates->map(fn($d) => $attendanceThisWeek->get($d, collect())->where('status', 'hadir')->sum('count'))->toArray(),
            'izin' => $weekDates->map(fn($d) => $attendanceThisWeek->get($d, collect())->where('status', 'izin')->sum('count'))->toArray(),
            'sakit' => $weekDates->map(fn($d) => $attendanceThisWeek->get($d, collect())->where('status', 'sakit')->sum('count'))->toArray(),
            'alpha' => $weekDates->map(fn($d) => $attendanceThisWeek->get($d, collect())->where('status', 'alpha')->sum('count'))->toArray(),
        ];

        // Recent activities (mock - you can implement actual activity log)
        $recentActivities = collect([]);

        return view('owner.dashboard', compact('stats', 'period', 'chartData', 'recentActivities'));
    }

    /**
     * Financial Report
     */
    public function financialReport(Request $request)
    {
        $period = $request->get('period', 'month');

        // Date range
        if ($period == 'custom') {
            $startDate = Carbon::parse($request->start);
            $endDate = Carbon::parse($request->end);
        } elseif ($period == 'year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else { // month
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $summary = [
            'total_revenue' => Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'total_count' => Payment::whereBetween('payment_date', [$startDate, $endDate])->count(),
            'verified' => Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'verified_count' => Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'pending' => Payment::where('status', 'pending')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'pending_count' => Payment::where('status', 'pending')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'rejected' => Payment::where('status', 'rejected')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'rejected_count' => Payment::where('status', 'rejected')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
        ];

        $byPaymentType = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->selectRaw('payment_types.name, COUNT(*) as count, SUM(payments.amount) as total, AVG(payments.amount) as average')
            ->groupBy('payment_types.id', 'payment_types.name')
            ->get();

        $monthlyBreakdown = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m")'))
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::parse($item->month . '-01')->format('F Y');
                // You can add breakdown by type here if needed
                $item->spp = 0;
                $item->pangkal = 0;
                $item->seragam = 0;
                $item->kegiatan = 0;
                return $item;
            });

        return view('owner.reports.financial', compact('summary', 'byPaymentType', 'monthlyBreakdown'));
    }

    /**
     * Attendance Report
     */
    public function attendanceReport(Request $request)
    {
        $period = $request->get('period', 'week');
        $classId = $request->get('class_id');

        // Date range
        if ($period == 'year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } elseif ($period == 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } else { // week
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        }

        $query = Attendance::whereBetween('date', [$startDate, $endDate]);
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $totalAttendances = $query->count();

        $summary = [
            'hadir' => $query->clone()->where('status', 'hadir')->count(),
            'izin' => $query->clone()->where('status', 'izin')->count(),
            'sakit' => $query->clone()->where('status', 'sakit')->count(),
            'alpha' => $query->clone()->where('status', 'alpha')->count(),
        ];

        $summary['hadir_percentage'] = $totalAttendances > 0 ? ($summary['hadir'] / $totalAttendances) * 100 : 0;
        $summary['izin_percentage'] = $totalAttendances > 0 ? ($summary['izin'] / $totalAttendances) * 100 : 0;
        $summary['sakit_percentage'] = $totalAttendances > 0 ? ($summary['sakit'] / $totalAttendances) * 100 : 0;
        $summary['alpha_percentage'] = $totalAttendances > 0 ? ($summary['alpha'] / $totalAttendances) * 100 : 0;

        $byClass = ClassRoom::withCount([
            'attendances as hadir' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'hadir'),
            'attendances as izin' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'izin'),
            'attendances as sakit' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'sakit'),
            'attendances as alpha' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'alpha'),
        ])->get()->map(function ($class) use ($startDate, $endDate) {
            $class->total_students = $class->students()->count();
            $totalAtt = $class->hadir + $class->izin + $class->sakit + $class->alpha;
            $class->attendance_rate = $totalAtt > 0 ? ($class->hadir / $totalAtt) * 100 : 0;
            return $class;
        });

        $dailyTrend = Attendance::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(function ($group, $date) {
                return (object)[
                    'date' => Carbon::parse($date),
                    'hadir' => $group->where('status', 'hadir')->sum('count'),
                    'izin' => $group->where('status', 'izin')->sum('count'),
                    'sakit' => $group->where('status', 'sakit')->sum('count'),
                    'alpha' => $group->where('status', 'alpha')->sum('count'),
                    'total' => $group->sum('count'),
                ];
            })->values();

        $classes = ClassRoom::all();

        return view('owner.reports.attendance', compact('summary', 'byClass', 'dailyTrend', 'classes'));
    }

    /**
     * Student Report
     */
    public function studentReport()
    {
        $stats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'pending' => Student::where('status', 'pending')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'male' => Student::where('gender', 'L')->count(),
            'female' => Student::where('gender', 'P')->count(),
        ];

        $stats['active_percentage'] = $stats['total'] > 0 ? ($stats['active'] / $stats['total']) * 100 : 0;
        $stats['pending_percentage'] = $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0;
        $stats['graduated_percentage'] = $stats['total'] > 0 ? ($stats['graduated'] / $stats['total']) * 100 : 0;
        $stats['male_percentage'] = $stats['total'] > 0 ? ($stats['male'] / $stats['total']) * 100 : 0;
        $stats['female_percentage'] = $stats['total'] > 0 ? ($stats['female'] / $stats['total']) * 100 : 0;

        $ageDistribution = Student::selectRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) as age, COUNT(*) as count')
            ->groupBy(DB::raw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE())'))
            ->orderBy('age')
            ->get()
            ->map(function ($item) use ($stats) {
                $item->percentage = $stats['total'] > 0 ? ($item->count / $stats['total']) * 100 : 0;
                return $item;
            });

        $byClass = ClassRoom::with('teacher')->withCount([
            'students as active_students' => fn($q) => $q->where('students.status', 'active'),
            'students as male' => fn($q) => $q->where('students.status', 'active')->where('gender', 'L'),
            'students as female' => fn($q) => $q->where('students.status', 'active')->where('gender', 'P'),
        ])->get()->map(function ($class) {
            $class->occupancy = $class->capacity > 0 ? ($class->active_students / $class->capacity) * 100 : 0;
            return $class;
        });

        $registrationTrend = Student::selectRaw('DATE_FORMAT(registration_date, "%Y-%m") as month, COUNT(*) as count')
            ->where('registration_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $registrationTrend = [
            'labels' => $registrationTrend->pluck('month')->map(fn($m) => Carbon::parse($m . '-01')->format('M Y'))->toArray(),
            'values' => $registrationTrend->pluck('count')->toArray(),
        ];

        $recentRegistrations = Student::with(['parent', 'currentEnrollment.classRoom'])
            ->latest('registration_date')
            ->take(10)
            ->get();

        return view('owner.reports.student', compact('stats', 'ageDistribution', 'byClass', 'registrationTrend', 'recentRegistrations'));
    }

    /**
     * Savings Report
     */
    public function savingsReport(Request $request)
{
    $period = $request->get('period', 'month');
    $classId = $request->get('class_id');

    // Date range
    if ($period == 'year') {
        $startDate = now()->startOfYear();
        $endDate = now()->endOfYear();
    } elseif ($period == 'all') {
        $startDate = null;
        $endDate = null;
    } else { // month
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
    }

    $summary = [
        'total_balance' => SavingsBook::sum('balance'),
        'total_books' => SavingsBook::count(),
        'active_savers' => SavingsBook::where('balance', '>', 0)->count(),
        'total_students' => Student::where('status', 'active')->count(),
    ];

    $trxQuery = SavingsTransaction::query();
    if ($startDate && $endDate) {
        $trxQuery->whereBetween('created_at', [$startDate, $endDate]);
    }

    // UBAH DARI debit/credit MENJADI deposit/withdraw
    $summary['total_deposits'] = $trxQuery->clone()->where('type', 'deposit')->sum('amount');
    $summary['deposit_count'] = $trxQuery->clone()->where('type', 'deposit')->count();
    $summary['total_withdrawals'] = $trxQuery->clone()->where('type', 'withdraw')->sum('amount');
    $summary['withdrawal_count'] = $trxQuery->clone()->where('type', 'withdraw')->count();

    $topSavers = SavingsBook::with('student.currentEnrollment.classRoom')
        ->orderBy('balance', 'desc')
        ->take(10)
        ->get();

    $recentTransactions = SavingsTransaction::with('savingsBook.student')
        ->latest()
        ->take(10)
        ->get();

    $byClass = ClassRoom::with(['students' => function ($q) {
        $q->where('students.status', 'active')->has('savingsBook');
    }, 'students.savingsBook'])
        ->get()
        ->map(function ($class) {
            $savingsBooks = $class->students->pluck('savingsBook')->filter();
            return (object)[
                'name' => $class->name,
                'savers_count' => $savingsBooks->count(),
                'total_balance' => $savingsBooks->sum('balance'),
                'average_balance' => $savingsBooks->avg('balance') ?? 0,
                'max_balance' => $savingsBooks->max('balance') ?? 0,
                'min_balance' => $savingsBooks->where('balance', '>', 0)->min('balance') ?? 0,
            ];
        });

    // Monthly trend
    $monthlyTrendData = SavingsTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, type, SUM(amount) as total')
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), 'type')
        ->orderBy('month')
        ->get()
        ->groupBy('month');

    $months = collect();
    for ($i = 11; $i >= 0; $i--) {
        $months->push(now()->subMonths($i)->format('Y-m'));
    }

    // UBAH DARI debit/credit MENJADI deposit/withdraw
    $monthlyTrend = [
        'labels' => $months->map(fn($m) => Carbon::parse($m . '-01')->format('M Y'))->toArray(),
        'deposits' => $months->map(fn($m) => $monthlyTrendData->get($m, collect())->where('type', 'deposit')->sum('total'))->toArray(),
        'withdrawals' => $months->map(fn($m) => $monthlyTrendData->get($m, collect())->where('type', 'withdraw')->sum('total'))->toArray(),
    ];

    $classes = ClassRoom::all();

    return view('owner.reports.savings', compact('summary', 'topSavers', 'recentTransactions', 'byClass', 'monthlyTrend', 'classes'));
}
}
