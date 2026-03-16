<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'teacher_subject_id', 'point_category_id',
        'title', 'description', 'due_date', 'max_grade',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function teacherSubject() 
    { 
        return $this->belongsTo(TeacherSubject::class); 
    }
    public function submissions()    
    { 
        return $this->hasMany(TaskSubmission::class); 
    }

    public function isTakenBy(int $studentId): bool
    {
        return $this->submissions()->where('student_id', $studentId)->exists();
    }

    public function isExpired(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
}