<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->onDelete('cascade');
            $table->string('book_number')->unique(); // TAB-2024-0001
            $table->decimal('balance', 12, 2)->default(0); // Saldo saat ini
            $table->date('opened_date'); // Tanggal buku dibuka
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_book_id')->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique(); // TRX-2024-0001
            $table->date('transaction_date');
            $table->enum('type', ['debit', 'credit']); // debit = masuk, credit = keluar
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 12, 2); // Saldo sebelum transaksi
            $table->decimal('balance_after', 12, 2); // Saldo setelah transaksi
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // guru yang input
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('savings_books');
    }
};