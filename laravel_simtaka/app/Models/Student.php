<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'name',
        'nickname',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'photo',
        'parent_id',
        'father_name',
        'father_phone',
        'father_job',
        'mother_name',
        'mother_phone',
        'mother_job',
        'status',
        'registration_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registration_date' => 'date',
        ];
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(Enrollment::class)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->where('status', 'active');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function savingsBook()
    {
        return $this->hasOne(SavingsBook::class);
    }

    // Helper Methods
   public function getAgeAttribute()
{
    if (!$this->birth_date) {
        return null;
    }
    
    return $this->birth_date->age;
}

    public function getFullNameAttribute()
    {
        return $this->nickname ? "{$this->name} ({$this->nickname})" : $this->name;
    }
}