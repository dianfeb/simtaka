<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique(); // Nomor Induk Siswa
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['L', 'P']); // Laki-laki, Perempuan
            $table->date('birth_date');
            $table->string('birth_place');
            $table->text('address');
            $table->string('photo')->nullable();
            
            // Data Orang Tua
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->string('father_name');
            $table->string('father_phone')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_name');
            $table->string('mother_phone')->nullable();
            $table->string('mother_job')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'graduated'])->default('active');
            $table->date('registration_date');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};