<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a point transaction (positive or negative) awarded to a student.
 *
 * Points are linked to a specific teacher-subject assignment and optionally
 * to a graded task submission. Positive points reward achievement; negative
 * points represent deductions.
 *
 * @property int         $id
 * @property int         $student_id
 * @property int         $teacher_subject_id
 * @property int|null    $task_submission_id
 * @property int         $point
 * @property string|null $description
 * @property string|null $type
 * @property \Carbon\Carbon $point_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Point extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'teacher_subject_id',
        'task_submission_id',
        'point',
        'description',
        'point_date',
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'point_date' => 'date',
    ];

    /**
     * Get the student who received this point.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the teacher-subject assignment that awarded this point.
     */
    public function teacherSubject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class, 'teacher_subject_id');
    }

    /**
     * Get the task submission this point originated from (if any).
     */
    public function taskSubmission(): BelongsTo
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }
}