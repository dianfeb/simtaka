<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'guru';
    }

    public function isParent(): bool
    {
        return $this->role === 'orang_tua';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    // Relationships
    
    // Jika user adalah orang tua
    public function children()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    // Jika user adalah guru
    public function classes()
    {
        return $this->hasMany(ClassRoom::class, 'teacher_id');
    }

    public function attendancesCreated()
    {
        return $this->hasMany(Attendance::class, 'created_by');
    }

    public function gradesCreated()
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

    public function savingsTransactionsCreated()
    {
        return $this->hasMany(SavingsTransaction::class, 'created_by');
    }

    public function paymentsVerified()
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }
}