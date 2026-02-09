<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_year_id',
        'class_id',
        'semester',
        'score',
        'grade',
        'predicate',
        'description',
        'teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Scopes
    public function scopeSemester1($query)
    {
        return $query->where('semester', '1');
    }

    public function scopeSemester2($query)
    {
        return $query->where('semester', '2');
    }

    // Accessor untuk badge colors
    public function getPredicateBadgeAttribute()
    {
        $badges = [
            'BSB' => 'bg-green',
            'BSH' => 'bg-blue',
            'MB' => 'bg-yellow',
        ];

        return $badges[$this->predicate] ?? 'bg-secondary';
    }

    public function getGradeBadgeAttribute()
    {
        $badges = [
            'A' => 'bg-green',
            'B' => 'bg-blue',
            'C' => 'bg-yellow',
            'D' => 'bg-orange',
            'E' => 'bg-red',
        ];

        return $badges[$this->grade] ?? 'bg-secondary';
    }

    // Helper method (bukan auto-setter)
    public static function calculateGradeFromScore($score)
    {
        if ($score >= 86) return 'A';
        if ($score >= 71) return 'B';
        if ($score >= 56) return 'C';
        if ($score >= 41) return 'D';
        return 'E';
    }

    public static function calculatePredicateFromScore($score)
    {
        if ($score >= 86) return 'BSB';
        if ($score >= 71) return 'BSH';
        return 'MB';
    }
}