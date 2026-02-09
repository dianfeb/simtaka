<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// ============ GRADE ============
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

    // Helper untuk auto-convert score ke grade
    public function setScoreAttribute($value)
    {
        $this->attributes['score'] = $value;
        
        // Auto set grade berdasarkan score
        if ($value >= 90) {
            $this->attributes['grade'] = 'A';
            $this->attributes['predicate'] = 'BSB';
        } elseif ($value >= 80) {
            $this->attributes['grade'] = 'B';
            $this->attributes['predicate'] = 'BSH';
        } elseif ($value >= 70) {
            $this->attributes['grade'] = 'C';
            $this->attributes['predicate'] = 'BSH';
        } elseif ($value >= 60) {
            $this->attributes['grade'] = 'D';
            $this->attributes['predicate'] = 'MB';
        } else {
            $this->attributes['grade'] = 'E';
            $this->attributes['predicate'] = 'MB';
        }
    }
}