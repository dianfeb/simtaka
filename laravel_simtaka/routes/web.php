<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Owner\OwnerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Payment Verification
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminController::class, 'payments'])->name('index');
        Route::post('/{payment}/verify', [AdminController::class, 'verifyPayment'])->name('verify');
        
        // Payment Report
        Route::get('/report', [AdminController::class, 'paymentReport'])->name('report');
        
        // Manual Payment Entry
        Route::get('/manual/create', [AdminController::class, 'createManualPayment'])->name('manual.create');
        Route::post('/manual/store', [AdminController::class, 'storeManualPayment'])->name('manual.store');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('create');
        Route::post('/store', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('delete');
    });
    
    // Student Management
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [AdminController::class, 'students'])->name('index');
        Route::get('/{student}', [AdminController::class, 'showStudent'])->name('show');
    });
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    
    // Attendance
    Route::get('/attendance', [TeacherController::class, 'attendance'])->name('attendance.index');
    Route::post('/attendance/store', [TeacherController::class, 'storeAttendance'])->name('attendance.store');
    
    // Grades
    Route::get('/grades', [TeacherController::class, 'grades'])->name('grades.index');
    Route::post('/grades/store', [TeacherController::class, 'storeGrades'])->name('grades.store');
    
    // Savings
    Route::get('/savings', [TeacherController::class, 'savings'])->name('savings.index');
    Route::post('/savings/transaction', [TeacherController::class, 'savingsTransaction'])->name('savings.transaction');
});

/*
|--------------------------------------------------------------------------
| Parent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:orang_tua'])->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    
    // Student Registration
    Route::get('/students/register', [ParentController::class, 'registerStudent'])->name('students.register');
    Route::post('/students/store', [ParentController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{student}', [ParentController::class, 'showStudent'])->name('students.show');
    
    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [ParentController::class, 'payments'])->name('index');
        Route::get('/create', [ParentController::class, 'createPayment'])->name('create');
        Route::post('/store', [ParentController::class, 'storePayment'])->name('store');
    });
    
    // Report Card
    Route::get('/report-card/{student}', [ParentController::class, 'reportCard'])->name('report-card');
    
    // Savings Book
    Route::get('/savings/{student}', [ParentController::class, 'savings'])->name('savings');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial', [OwnerController::class, 'financialReport'])->name('financial');
        Route::get('/attendance', [OwnerController::class, 'attendanceReport'])->name('attendance');
        Route::get('/student', [OwnerController::class, 'studentReport'])->name('student');
        Route::get('/savings', [OwnerController::class, 'savingsReport'])->name('savings');
    });
});