<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Point;
use App\Models\Student;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // =========================================================
    // TEACHER METHODS
    // =========================================================

    /**
     * GET /teacher/tasks
     * List all tasks created by the logged-in teacher
     */
    public function index()
    {
        $teacher = Auth::user()->teacher;

        $tasks = Task::whereHas('teacherSubject', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->with(['teacherSubject.subject', 'category'])
                    ->withCount('submissions')
                    ->latest()
                    ->get();

        return response()->json($tasks);
    }

    /**
     * POST /teacher/tasks
     * Create a new task
     */
    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'point_category_id'  => 'nullable|exists:point_categories,id',
            'title'              => 'required|string|max:150',
            'description'        => 'nullable|string',
            'due_date'           => 'nullable|date|after:today',
            'max_grade'          => 'integer|min:1|max:100',
        ]);

        // Make sure the teacher_subject belongs to this teacher
        $owns = TeacherSubject::where('id', $validated['teacher_subject_id'])
                              ->where('teacher_id', $teacher->id)
                              ->exists();

        if (!$owns) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $task = Task::create($validated);

        return response()->json($task->load(['teacherSubject.subject', 'category']), 201);
    }

    /**
     * PUT /teacher/tasks/{task}
     * Update a task
     */
    public function update(Request $request, Task $task)
    {
        $teacher = Auth::user()->teacher;

        // Make sure this teacher owns the task
        if ($task->teacherSubject->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'             => 'sometimes|string|max:150',
            'description'       => 'nullable|string',
            'due_date'          => 'nullable|date',
            'max_grade'         => 'sometimes|integer|min:1|max:100',
            'point_category_id' => 'nullable|exists:point_categories,id',
        ]);

        $task->update($validated);

        return response()->json($task->load(['teacherSubject.subject', 'category']));
    }

    /**
     * DELETE /teacher/tasks/{task}
     * Delete a task
     */
    public function destroy(Task $task)
    {
        $teacher = Auth::user()->teacher;

        if ($task->teacherSubject->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted.']);
    }

    /**
     * GET /teacher/tasks/{task}/submissions
     * View all student submissions for a task
     */
    public function submissions(Task $task)
    {
        $teacher = Auth::user()->teacher;

        if ($task->teacherSubject->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $submissions = $task->submissions()
                            ->with(['student.user'])
                            ->get()
                            ->map(function ($s) {
                                return [
                                    'id'           => $s->id,
                                    'student_name' => $s->student->user->name,
                                    'status'       => $s->status,
                                    'grade'        => $s->grade,
                                    'feedback'     => $s->feedback,
                                    'photo_path'   => $s->photo_path,
                                    'submitted_at' => $s->submitted_at,
                                ];
                            });

        return response()->json($submissions);
    }

    /**
     * PUT /teacher/submissions/{submission}/correcting
     * Teacher marks a submission as being corrected
     * Status: submitted → correcting
     */
    public function markCorrecting(TaskSubmission $submission)
    {
        $teacher = Auth::user()->teacher;

        // Verify teacher owns this task
        if ($submission->task->teacherSubject->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($submission->status !== 'submitted') {
            return response()->json([
                'message' => 'Submission must be in submitted status first.'
            ], 422);
        }

        $submission->update(['status' => 'correcting']);

        return response()->json(['message' => 'Marked as correcting.', 'submission' => $submission]);
    }

    /**
     * PUT /teacher/submissions/{submission}/grade
     * Teacher gives a grade + auto-awards points
     * Status: correcting → graded
     */
    public function grade(Request $request, TaskSubmission $submission)
    {
        $teacher = Auth::user()->teacher;

        if ($submission->task->teacherSubject->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($submission->status !== 'correcting') {
            return response()->json([
                'message' => 'Submission must be in correcting status first.'
            ], 422);
        }

        $validated = $request->validate([
            'grade'    => 'required|integer|min:0|max:' . $submission->task->max_grade,
            'feedback' => 'nullable|string',
        ]);

        // Update submission to graded
        $submission->update([
            'grade'    => $validated['grade'],
            'feedback' => $validated['feedback'] ?? null,
            'status'   => 'graded',
        ]);

        // Auto-award points based on grade percentage
        $pointsAwarded = 0;

        if ($submission->task->point_category_id) {
            $percentage   = $validated['grade'] / $submission->task->max_grade;
            $maxPoints    = $submission->task->category->default_points;
            $pointsAwarded = (int) round($percentage * $maxPoints);

            Point::create([
                'student_id'         => $submission->student_id,
                'teacher_subject_id' => $submission->task->teacher_subject_id,
                'category_id'        => $submission->task->point_category_id,
                'point'              => $pointsAwarded,
                'description'        => "Grade {$validated['grade']}/{$submission->task->max_grade} — {$submission->task->title}",
                'point_date'         => now()->toDateString(),
            ]);
        }

        return response()->json([
            'message'        => 'Graded successfully.',
            'grade'          => $validated['grade'],
            'points_awarded' => $pointsAwarded,
        ]);
    }

    // =========================================================
    // STUDENT METHODS
    // =========================================================

    /**
     * GET /student/tasks
     * List all available tasks the student hasn't taken yet
     */
    public function available()
    {
        $student = Auth::user()->student;

        // Get IDs of tasks already taken
        $takenTaskIds = TaskSubmission::where('student_id', $student->id)
                                      ->pluck('task_id');

        $tasks = Task::whereNotIn('id', $takenTaskIds)
                     ->with(['teacherSubject.subject', 'teacherSubject.teacher.user', 'category'])
                     ->when(now(), function ($q) {
                         // Show tasks not yet past due date, or with no due date
                         $q->where(function ($q2) {
                             $q2->whereNull('due_date')
                                ->orWhere('due_date', '>=', now()->toDateString());
                         });
                     })
                     ->latest()
                     ->get()
                     ->map(function ($t) {
                         return [
                             'id'          => $t->id,
                             'title'       => $t->title,
                             'description' => $t->description,
                             'due_date'    => $t->due_date,
                             'max_grade'   => $t->max_grade,
                             'subject'     => $t->teacherSubject->subject->name ?? '-',
                             'teacher'     => $t->teacherSubject->teacher->user->name ?? '-',
                             'category'    => $t->category->name ?? '-',
                         ];
                     });

        return response()->json($tasks);
    }

    /**
     * POST /student/tasks/{task}/take
     * Student accepts/takes a task
     * Creates submission with status: taken
     */
    public function take(Task $task)
    {
        $student = Auth::user()->student;

        // Check if already taken
        $alreadyTaken = TaskSubmission::where('task_id', $task->id)
                                      ->where('student_id', $student->id)
                                      ->exists();

        if ($alreadyTaken) {
            return response()->json(['message' => 'You already took this task.'], 422);
        }

        // Check due date
        if ($task->due_date && $task->due_date < now()->toDateString()) {
            return response()->json(['message' => 'This task is past its due date.'], 422);
        }

        $submission = TaskSubmission::create([
            'task_id'    => $task->id,
            'student_id' => $student->id,
            'status'     => 'taken',
        ]);

        return response()->json([
            'message'    => 'Task taken successfully.',
            'submission' => $submission,
        ], 201);
    }

    /**
     * PUT /student/submissions/{submission}/submit
     * Student marks their work as submitted
     * Status: taken → submitted
     */
    public function submit(Request $request, TaskSubmission $submission)
    {
        $student = Auth::user()->student;

        if ($submission->student_id !== $student->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($submission->status !== 'taken') {
            return response()->json(['message' => 'Task already submitted.'], 422);
        }

        $validated = $request->validate([
            'photo_path' => 'nullable|string', // or 'file|image' if uploading
        ]);

        $submission->update([
            'status'       => 'submitted',
            'photo_path'   => $validated['photo_path'] ?? null,
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Submitted successfully.', 'submission' => $submission]);
    }

    /**
     * GET /student/my-submissions
     * Student views their own submissions and grades
     */
    public function mySubmissions()
    {
        $student = Auth::user()->student;

        $submissions = TaskSubmission::where('student_id', $student->id)
                                     ->with(['task.teacherSubject.subject'])
                                     ->latest()
                                     ->get()
                                     ->map(function ($s) {
                                         return [
                                             'id'           => $s->id,
                                             'task_title'   => $s->task->title,
                                             'subject'      => $s->task->teacherSubject->subject->name ?? '-',
                                             'status'       => $s->status,
                                             'grade'        => $s->grade,
                                             'max_grade'    => $s->task->max_grade,
                                             'feedback'     => $s->feedback,
                                             'submitted_at' => $s->submitted_at,
                                             'due_date'     => $s->task->due_date,
                                         ];
                                     });

        return response()->json($submissions);
    }
}