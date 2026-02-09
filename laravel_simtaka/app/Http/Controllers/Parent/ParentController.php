<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    /**
     * Parent Dashboard
     */
    public function dashboard()
    {
        $parent = auth()->user();
        $students = $parent->children()
            ->with(['currentEnrollment.classRoom', 'savingsBook'])
            ->get();

        $stats = [
            'total_children' => $students->count(),
            'pending_payments' => Payment::whereIn('student_id', $students->pluck('id'))
                ->where('status', 'pending')
                ->count(),
        ];

        return view('parent.dashboard', compact('students', 'stats'));
    }

    /**
     * Student Registration
     */
    public function registerStudent()
    {
        return view('parent.students.create');
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'address' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_job' => 'nullable|string|max:100',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'mother_job' => 'nullable|string|max:100',
        ]);

        // Generate NIS
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)->latest()->first();
        $number = $lastStudent ? (int)substr($lastStudent->nis, -4) + 1 : 1;
        $nis = $year . str_pad($number, 4, '0', STR_PAD_LEFT);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create([
            'nis' => $nis,
            'name' => $validated['name'],
            'nickname' => $validated['nickname'] ?? null,
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'birth_place' => $validated['birth_place'],
            'address' => $validated['address'],
            'photo' => $photoPath,
            'parent_id' => auth()->id(),
            'father_name' => $validated['father_name'],
            'father_phone' => $validated['father_phone'] ?? null,
            'father_job' => $validated['father_job'] ?? null,
            'mother_name' => $validated['mother_name'],
            'mother_phone' => $validated['mother_phone'] ?? null,
            'mother_job' => $validated['mother_job'] ?? null,
            'status' => 'pending', // Status pending, menunggu approval admin
            'registration_date' => today(),
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Data anak berhasil didaftarkan dengan NIS: ' . $nis . '. Menunggu verifikasi admin.');
    }

    /**
     * View Student Details
     */
    public function showStudent(Student $student)
    {
        // Pastikan parent hanya bisa lihat anaknya sendiri
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $student->load([
            'currentEnrollment.classRoom.teacher',
            'attendances' => function ($query) {
                $query->latest()->take(30);
            },
            'grades.subject',
            'savingsBook.transactions',
            'payments.paymentType',
        ]);

        return view('parent.students.show', compact('student'));
    }

    /**
     * Payment Management
     */
    public function payments()
    {
        $parent = auth()->user();
        $students = $parent->children;

        $payments = Payment::with(['student', 'paymentType', 'verifiedBy'])
            ->whereIn('student_id', $students->pluck('id'))
            ->latest()
            ->paginate(20);

        return view('parent.payments.index', compact('payments'));
    }

    public function createPayment()
    {
        $parent = auth()->user();
        $students = $parent->children()->where('status', 'active')->get();
        $paymentTypes = PaymentType::where('is_active', true)->get();
        $academicYear = AcademicYear::where('is_active', true)->first();

        return view('parent.payments.create', compact('students', 'paymentTypes', 'academicYear'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'month' => 'nullable|date_format:Y-m',
            'payment_date' => 'required|date',
            'proof_image' => 'required|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Verify student belongs to parent
        $student = Student::findOrFail($validated['student_id']);
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get payment amount
        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);
        $academicYear = AcademicYear::where('is_active', true)->first();

        // Handle proof image upload
        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');

        Payment::create([
            'student_id' => $validated['student_id'],
            'payment_type_id' => $validated['payment_type_id'],
            'academic_year_id' => $academicYear->id,
            'amount' => $paymentType->amount,
            'month' => $validated['month'] ?? null,
            'payment_date' => $validated['payment_date'],
            'proof_image' => $proofPath,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('parent.payments.index')
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * View Report Card / Nilai Semester
     */
    public function reportCard(Student $student)
    {
        // Pastikan parent hanya bisa lihat anaknya sendiri
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $academicYear = request('academic_year_id') 
            ? AcademicYear::find(request('academic_year_id'))
            : AcademicYear::where('is_active', true)->first();

        $semester = request('semester', '1');

        $grades = $student->grades()
            ->with(['subject', 'teacher'])
            ->where('academic_year_id', $academicYear->id)
            ->where('semester', $semester)
            ->get()
            ->sortBy('subject.order');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('parent.report-card', compact('student', 'grades', 'academicYear', 'semester', 'academicYears'));
    }

    /**
     * View Savings Book
     */
   public function savings(Student $student)
    {
        // Pastikan parent hanya bisa lihat anaknya sendiri
        if ($student->parent_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $savingsBook = $student->savingsBook()->with('transactions.createdBy')->first();

        return view('parent.savings', compact('student', 'savingsBook'));
    }
}