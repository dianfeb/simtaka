<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


// ============ PAYMENT ============
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_code',
        'student_id',
        'payment_type_id',
        'academic_year_id',
        'amount',
        'month',
        'payment_date',
        'proof_image',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    // Auto-generate payment code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_code) {
                $payment->payment_code = self::generatePaymentCode();
            }
        });
    }

    public static function generatePaymentCode()
    {
        $year = date('Y');
        $lastPayment = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayment ? (int)substr($lastPayment->payment_code, -4) + 1 : 1;

        return 'PAY-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}