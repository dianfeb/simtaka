<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Agama, Bahasa, Matematika, dll
            $table->string('code')->unique(); // AGM, BHS, MTK
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->enum('semester', ['1', '2']); // Semester 1 atau 2
            
            // Nilai
            $table->decimal('score', 5, 2)->nullable(); // Nilai angka (0-100)
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable(); // Nilai huruf
            $table->enum('predicate', ['MB', 'BSH', 'BSB'])->nullable(); // MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), BSB (Berkembang Sangat Baik)
            $table->text('description')->nullable(); // Deskripsi perkembangan
            
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // guru yang menginput
            $table->timestamps();
            $table->softDeletes();
            
            // Satu siswa tidak bisa punya dua nilai untuk mata pelajaran yang sama di semester yang sama
            $table->unique(['student_id', 'subject_id', 'academic_year_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('subjects');
    }
};