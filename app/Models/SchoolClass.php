<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'code',
        'subject_id',
        'teacher_id',
        'description',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    /** The teacher who owns this class */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /** Subject this class covers */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /** Tasks assigned to this class */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Task::class,
            TeacherSubject::class,
            'class_id', // Foreign key on teacher_subjects table
            'teacher_subject_id', // Foreign key on tasks table
            'id', // Local key on school_classes table
            'id' // Local key on teacher_subjects table
        );
    }

    /** Students enrolled in this class (via pivot) */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'class_enrollments',
            'class_id',
            'student_id'
        )->withTimestamps();
    }

    /** Enrollment records */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }
}