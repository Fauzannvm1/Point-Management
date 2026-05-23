<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a student's submission for a specific task.
 *
 * Lifecycle: taken → submitted → correcting → graded.
 * Each student can submit only once per task (unique constraint on task_id + student_id).
 *
 * @property int              $id
 * @property int              $task_id
 * @property int              $student_id
 * @property string           $status        One of: taken, submitted, correcting, graded
 * @property string|null      $photo_path
 * @property int|null         $grade
 * @property string|null      $feedback
 * @property string|null      $note          Student's note attached to the submission
 * @property \Carbon\Carbon|null $submitted_at
 * @property \Carbon\Carbon|null $graded_at
 * @property \Carbon\Carbon   $created_at
 * @property \Carbon\Carbon   $updated_at
 */
class TaskSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'student_id',
        'status',
        'photo_path',
        'grade',
        'feedback',
        'note',
        'submitted_at',
        'graded_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
    ];

    /**
     * Get the task this submission belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the student who created this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the files attached to this submission.
     */
    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    /**
     * Determine if this submission has been graded.
     */
    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    /**
     * Determine if this submission has been submitted and is awaiting review.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Determine if this submission is currently being corrected.
     */
    public function isCorrecting(): bool
    {
        return $this->status === 'correcting';
    }

    /**
     * Determine if this submission has been taken but not yet submitted.
     */
    public function isTaken(): bool
    {
        return $this->status === 'taken';
    }
}