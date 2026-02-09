<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $this->createUsers();

        // 2. Create Academic Year
        $this->createAcademicYears();

        // 3. Create Classes
        $this->createClasses();

        // 4. Create Subjects
        $this->createSubjects();

        // 5. Create Payment Types
        $this->createPaymentTypes();

        // 6. Create Students (50 siswa dummy)
        Student::factory(50)->create();
        
        // 7. Create Enrollments - TAMBAHKAN INI!
        $this->call(EnrollmentSeeder::class);
    }

    private function createUsers()
    {
        // Admin
        User::create([
            'name' => 'Admin TK',
            'email' => 'admin@tk.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Owner/Kepala Sekolah
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'owner@tk.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        // Guru 1
        User::create([
            'name' => 'Ibu Siti Guru TK A',
            'email' => 'guru1@tk.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        // Guru 2
        User::create([
            'name' => 'Ibu Ani Guru TK B',
            'email' => 'guru2@tk.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '081234567893',
            'is_active' => true,
        ]);

        // Orang Tua Demo
        User::create([
            'name' => 'Bapak Ahmad (Orang Tua)',
            'email' => 'ortu@tk.com',
            'password' => Hash::make('password'),
            'role' => 'orang_tua',
            'phone' => '081234567894',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'is_active' => true,
        ]);

        echo "✓ Users created\n";
    }

    private function createAcademicYears()
    {
        AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-15',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-15',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        echo "✓ Academic years created\n";
    }

    private function createClasses()
    {
        ClassRoom::create([
            'name' => 'TK A - Kelas Mawar',
            'level' => 'A',
            'teacher_id' => 3, // Ibu Siti
            'capacity' => 20,
            'description' => 'Kelas untuk anak usia 4-5 tahun',
        ]);

        ClassRoom::create([
            'name' => 'TK B - Kelas Melati',
            'level' => 'B',
            'teacher_id' => 4, // Ibu Ani
            'capacity' => 20,
            'description' => 'Kelas untuk anak usia 5-6 tahun',
        ]);

        echo "✓ Classes created\n";
    }

    private function createSubjects()
    {
        $subjects = [
            ['name' => 'Pendidikan Agama', 'code' => 'AGM', 'order' => 1],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'order' => 2],
            ['name' => 'Matematika', 'code' => 'MTK', 'order' => 3],
            ['name' => 'Seni & Kreativitas', 'code' => 'SNI', 'order' => 4],
            ['name' => 'Motorik & Olahraga', 'code' => 'MTR', 'order' => 5],
            ['name' => 'Sosial & Emosional', 'code' => 'SOS', 'order' => 6],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        echo "✓ Subjects created\n";
    }

    private function createPaymentTypes()
    {
        PaymentType::create([
            'name' => 'Uang Pangkal',
            'description' => 'Biaya pendaftaran awal masuk TK',
            'amount' => 2000000,
            'frequency' => 'once',
            'is_active' => true,
        ]);

        PaymentType::create([
            'name' => 'SPP Bulanan',
            'description' => 'Biaya SPP per bulan',
            'amount' => 500000,
            'frequency' => 'monthly',
            'is_active' => true,
        ]);

        PaymentType::create([
            'name' => 'Seragam',
            'description' => 'Biaya seragam sekolah',
            'amount' => 350000,
            'frequency' => 'once',
            'is_active' => true,
        ]);

        PaymentType::create([
            'name' => 'Buku & Alat Tulis',
            'description' => 'Biaya buku dan alat tulis per tahun',
            'amount' => 300000,
            'frequency' => 'yearly',
            'is_active' => true,
        ]);

        echo "✓ Payment types created\n";
    }
}