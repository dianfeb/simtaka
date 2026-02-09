<?php

namespace App\Models;

use id;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ============ SAVINGS BOOK ============
class SavingsBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'book_number',
        'balance',
        'opened_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'opened_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Auto-generate book number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (!$book->book_number) {
                $book->book_number = self::generateBookNumber();
            }
            if (!$book->opened_date) {
                $book->opened_date = now();
            }
        });
    }

    public static function generateBookNumber()
    {
        $year = date('Y');
        $lastBook = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastBook ? (int)substr($lastBook->book_number, -4) + 1 : 1;

        return 'TAB-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    // Helper methods
    public function deposit($amount, $description = null, $userId = null)
    {
        return SavingsTransaction::create([
            'savings_book_id' => $this->id,
            'transaction_date' => now(),
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance + $amount,
            'description' => $description,
            'created_by' => $userId ?? auth()->id(),
        ]);
    }

    public function withdraw($amount, $description = null, $userId = null)
    {
        if ($amount > $this->balance) {
            throw new \Exception('Saldo tidak mencukupi');
        }

        return SavingsTransaction::create([
            'savings_book_id' => $this->id,
            'transaction_date' => now(),
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance - $amount,
            'description' => $description,
            'created_by' => $userId ?? auth()->id(),
        ]);
    }
}

// ============ SAVINGS TRANSACTION ============
class SavingsTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'savings_book_id',
        'transaction_code',
        'transaction_date',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    // Auto-generate transaction code and update balance
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_code) {
                $transaction->transaction_code = self::generateTransactionCode();
            }
        });

        static::created(function ($transaction) {
            // Update balance di savings book
            $savingsBook = $transaction->savingsBook;
            $savingsBook->balance = $transaction->balance_after;
            $savingsBook->save();
        });
    }

    public static function generateTransactionCode()
    {
        $year = date('Y');
        $lastTransaction = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTransaction ? (int)substr($lastTransaction->transaction_code, -4) + 1 : 1;

        return 'TRX-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function savingsBook()
    {
        return $this->belongsTo(SavingsBook::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeDebit($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year);
    }
}