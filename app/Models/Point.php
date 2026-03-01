<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_subject_id',
        'point',
        'description',
        'point_date',
    ];

    protected function casts(): array
    {
        return [
            'point_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }
}