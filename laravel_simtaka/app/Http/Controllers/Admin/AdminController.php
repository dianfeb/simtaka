<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => User::where('role', 'guru')->where('is_active', true)->count(),
            'total_parents' => User::where('role', 'orang_tua')->where('is_active', true)->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $recentPayments = Payment::with(['student', 'paymentType'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPayments'));
    }

    /**
     * Payment Verification List
     */
    public function payments()
    {
        $status = request('status', 'pending');

        // Get statistics
        $stats = [
            'pending' => Payment::where('status', 'pending')->count(),
            'verified' => Payment::where('status', 'verified')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
            'total_month' => Payment::where('status', 'verified')
                ->whereMonth('verified_at', now()->month)
                ->whereYear('verified_at', now()->year)
                ->sum('amount'),
        ];

        $payments = Payment::with(['student.parent', 'paymentType', 'verifiedBy'])
            ->where('status', $status)
            ->latest()
            ->paginate(12);

        return view('admin.payments.index', compact('payments', 'status', 'stats'));
    }

    /**
     * Verify or Reject Payment
     */
    public function verifyPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'rejection_reason' => 'required_if:action,reject',
        ]);

        if ($request->action === 'verify') {
            $payment->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            return redirect()->route('admin.payments.index', ['status' => 'verified'])
                ->with('success', 'Pembayaran ' . $payment->payment_code . ' berhasil diverifikasi');
        } else {
            $payment->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            return redirect()->route('admin.payments.index', ['status' => 'rejected'])
                ->with('success', 'Pembayaran ' . $payment->payment_code . ' ditolak');
        }
    }

    /**
     * Payment Report
     */
    public function paymentReport()
    {
        $period = request('period', 'month');
        $paymentTypeId = request('payment_type');
        $status = request('status');

        // Date range
        $query = Payment::with(['student', 'paymentType', 'verifiedBy']);

        switch ($period) {
            case 'today':
                $query->whereDate('payment_date', today());
                break;
            case 'week':
                $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('payment_date', now()->month)
                      ->whereYear('payment_date', now()->year);
                break;
            case 'year':
                $query->whereYear('payment_date', now()->year);
                break;
            case 'custom':
                if (request('start_date') && request('end_date')) {
                    $query->whereBetween('payment_date', [request('start_date'), request('end_date')]);
                }
                break;
        }

        // Filter by payment type
        if ($paymentTypeId) {
            $query->where('payment_type_id', $paymentTypeId);
        }

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        // Summary
        $summary = [
            'total_count' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'verified_count' => $payments->where('status', 'verified')->count(),
            'verified_amount' => $payments->where('status', 'verified')->sum('amount'),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
        ];

        $paymentTypes = PaymentType::all();

        return view('admin.payments.report', compact('payments', 'summary', 'paymentTypes'));
    }

    /**
     * Show Manual Payment Form
     */
    public function createManualPayment()
    {
        $students = Student::with('parent')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $paymentTypes = PaymentType::where('is_active', true)->get();

        $recentPayments = Payment::with(['student', 'paymentType'])
            ->where('status', 'verified')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.payments.manual', compact('students', 'paymentTypes', 'recentPayments'));
    }

    /**
     * Store Manual Payment (Auto Verified)
     */
    public function storeManualPayment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'month' => 'nullable|date_format:Y-m',
            'payment_method' => 'required|in:cash,transfer',
            'notes' => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);

        // Get current academic year
        $academicYear = AcademicYear::where('is_active', true)->first();

        // Generate payment code
        $lastPayment = Payment::whereYear('created_at', now()->year)->latest()->first();
        $lastNumber = $lastPayment ? intval(substr($lastPayment->payment_code, -4)) : 0;
        $paymentCode = 'PAY-' . now()->format('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        // Create payment (auto verified)
        Payment::create([
            'payment_code' => $paymentCode,
            'student_id' => $validated['student_id'],
            'academic_year_id' => $academicYear ? $academicYear->id : null,
            'payment_type_id' => $validated['payment_type_id'],
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'month' => $validated['month'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $validated['payment_method'],
            'status' => 'verified', // Auto verified
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'proof_image' => null, // No proof for manual entry
        ]);

        return redirect()->route('admin.payments.index', ['status' => 'verified'])
            ->with('success', 'Pembayaran manual berhasil disimpan dan terverifikasi');
    }

    /**
     * Manage Users (Guru & Orang Tua)
     */
    public function users()
    {
        $users = User::whereIn('role', ['guru', 'orang_tua'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:guru,orang_tua',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin' || $user->role === 'owner') {
            return back()->with('error', 'Tidak dapat menghapus admin atau owner');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Manage Students
     */
    public function students()
    {
        $stats = [
            'total' => Student::count(),
            'pending' => Student::where('status', 'pending')->count(),
            'active' => Student::where('status', 'active')->count(),
            'rejected' => Student::where('status', 'rejected')->count(),
        ];

        $students = Student::with(['parent', 'currentEnrollment.classRoom'])
            ->latest()
            ->paginate(20);

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function showStudent(Student $student)
    {
        $student->load([
            'parent',
            'enrollments.classRoom',
            'enrollments.academicYear',
            'payments.paymentType',
            'savingsBook.transactions',
        ]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Student Approval List
     */
    /**
     * Student Approval List
     */
    public function studentsApproval()
    {
        $status = request('status', 'pending');

        $stats = [
            'pending' => Student::where('status', 'pending')->count(),
            'active' => Student::where('status', 'active')->count(),
            'rejected' => Student::where('status', 'rejected')->count(),
            'total' => Student::count(),
        ];

        $students = Student::with('parent')
            ->where('status', $status)
            ->latest()
            ->paginate(12);

        $classes = \App\Models\ClassRoom::all();

        return view('admin.students.approval', compact('students', 'status', 'stats', 'classes'));
    }

    /**
     * Approve or Reject Student
     */
    public function approveStudent(Request $request, Student $student)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject',
            'class_id' => 'required_if:action,approve|exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Update student status to active
                $student->update([
                    'status' => 'active',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);

                // Get active academic year
                $academicYear = AcademicYear::where('is_active', true)->first();

                // Create enrollment
                if ($academicYear && $request->class_id) {
                    \App\Models\Enrollment::create([
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'academic_year_id' => $academicYear->id,
                        'enrollment_date' => now(),
                        'status' => 'active',
                    ]);
                }

                DB::commit();

                // TODO: Send email notification to parent
                // Mail::to($student->parent->email)->send(new StudentApproved($student));

                return redirect()->route('admin.students.approval', ['status' => 'active'])
                    ->with('success', 'Siswa ' . $student->name . ' berhasil diterima dan di-enroll ke kelas');

            } else {
                // Reject student
                $student->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'rejected_at' => now(),
                    'rejected_by' => auth()->id(),
                ]);

                DB::commit();

                // TODO: Send email notification to parent
                // Mail::to($student->parent->email)->send(new StudentRejected($student));

                return redirect()->route('admin.students.approval', ['status' => 'rejected'])
                    ->with('success', 'Pendaftaran ' . $student->name . ' ditolak');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * CLASSES MANAGEMENT
     * ==========================================
     */
    public function classes()
{
    $classes = \App\Models\ClassRoom::with('teacher')
        ->withCount(['students as active_students_count' => function($q) {
            $q->where('students.status', 'active'); // Specify the table name
        }])
        ->get();
    
    $teachers = User::where('role', 'guru')->get();
    
    return view('admin.classes.index', compact('classes', 'teachers'));
}

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|in:A,B',
            'teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        \App\Models\ClassRoom::create($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|in:A,B',
            'teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $class = \App\Models\ClassRoom::findOrFail($id);
        $class->update($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diupdate');
    }

    public function destroyClass($id)
    {
        $class = \App\Models\ClassRoom::findOrFail($id);
        
        // Check if class has students
        if ($class->students()->exists()) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa');
        }
        
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * ==========================================
     * SUBJECTS MANAGEMENT
     * ==========================================
     */
    public function subjects()
    {
        $subjects = Subject::orderBy('order')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function storeSubject(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:10|unique:subjects,code',
        'description' => 'nullable|string',
    ]);

    $lastOrder = Subject::max('order') ?? 0;

    Subject::create([
        'name' => $request->name,
        'code' => $request->code,
        'description' => $request->description,
        'is_active' => $request->is_active == 1, // Ubah ini
        'order' => $lastOrder + 1,
    ]);

    return redirect()->route('admin.subjects.index')
        ->with('success', 'Mata pelajaran berhasil ditambahkan');
}

public function updateSubject(Request $request, Subject $subject)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
        'description' => 'nullable|string',
    ]);

    $subject->update([
        'name' => $request->name,
        'code' => $request->code,
        'description' => $request->description,
        'is_active' => $request->is_active == 1, // Ubah ini
    ]);

    return redirect()->route('admin.subjects.index')
        ->with('success', 'Mata pelajaran berhasil diupdate');
}

    public function destroySubject(Subject $subject)
    {
        // Check if subject has grades
        if ($subject->grades()->exists()) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Tidak dapat menghapus mata pelajaran yang sudah memiliki nilai');
        }
        
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus');
    }

    public function reorderSubjects(Request $request)
    {
        foreach ($request->order as $item) {
            Subject::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * ==========================================
     * PAYMENT TYPES MANAGEMENT
     * ==========================================
     */
    public function paymentTypes()
    {
        $paymentTypes = PaymentType::all();
        return view('admin.payment-types.index', compact('paymentTypes'));
    }

    public function storePaymentType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'is_monthly' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        PaymentType::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'is_monthly' => $request->has('is_monthly'),
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.payment-types.index')
            ->with('success', 'Jenis pembayaran berhasil ditambahkan');
    }

    public function updatePaymentType(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'is_monthly' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $paymentType->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'is_monthly' => $request->has('is_monthly'),
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.payment-types.index')
            ->with('success', 'Jenis pembayaran berhasil diupdate');
    }

    public function destroyPaymentType(PaymentType $paymentType)
    {
        // Check if payment type has payments
        if ($paymentType->payments()->exists()) {
            return redirect()->route('admin.payment-types.index')
                ->with('error', 'Tidak dapat menghapus jenis pembayaran yang sudah memiliki transaksi');
        }
        
        $paymentType->delete();

        return redirect()->route('admin.payment-types.index')
            ->with('success', 'Jenis pembayaran berhasil dihapus');
    }

    /**
     * ==========================================
     * ACADEMIC YEARS MANAGEMENT
     * ==========================================
     */
    public function academicYears()
    {
        $academicYears = AcademicYear::withCount('enrollments')
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'semester' => 'required|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        AcademicYear::create([
            'name' => $request->name,
            'semester' => $request->semester,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => false,
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    public function updateAcademicYear(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'semester' => 'required|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $academicYear->update([
            'name' => $request->name,
            'semester' => $request->semester,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diupdate');
    }

    public function activateAcademicYear(AcademicYear $academicYear)
    {
        // Deactivate all other academic years
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        
        // Activate this academic year
        $academicYear->update(['is_active' => true]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran ' . $academicYear->name . ' berhasil diaktifkan');
    }

    public function destroyAcademicYear(AcademicYear $academicYear)
    {
        // Cannot delete active academic year
        if ($academicYear->is_active) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }
        
        // Check if has enrollments
        if ($academicYear->enrollments()->exists()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang sudah memiliki siswa terdaftar');
        }
        
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    
}