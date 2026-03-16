<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    protected $fillable = [
        'task_id', 'student_id', 'status',
        'photo_path', 'grade', 'feedback', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function task()    
    { 
        return $this->belongsTo(Task::class); 
    }
    public function student()
    { 
        return $this->belongsTo(Student::class); 
    }

    public function isGraded():     bool { return $this->status === 'graded'; }
    public function isSubmitted():  bool { return $this->status === 'submitted'; }
    public function isCorrecting(): bool { return $this->status === 'correcting'; }
    public function isTaken():      bool { return $this->status === 'taken'; }
}