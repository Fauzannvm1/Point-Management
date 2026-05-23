<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    // GET /teacher/submissions?task_id=&status=
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $tsIds   = TeacherSubject::where('teacher_id', $teacher->id)->pluck('id');

        $query = TaskSubmission::with([
            'student.user',
            'task.teacherSubject.subject',
            'task.teacherSubject.class',
        ])->whereHas('task', fn ($q) => $q->whereIn('teacher_subject_id', $tsIds));

        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($this->mapSubmissions($query->orderByDesc('updated_at')->get()));
    }

    // GET /teacher/tasks/{task}/submissions
    public function indexByTask(Task $task)
    {
        $this->authorizeTask($task);

        $query = TaskSubmission::with([
            'student.user',
            'task.teacherSubject.subject',
            'task.teacherSubject.class',
        ])->where('task_id', $task->id);

        return response()->json($this->mapSubmissions($query->orderByDesc('updated_at')->get()));
    }

    // POST /teacher/submissions/{submission}/correcting
    public function markCorrecting(TaskSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        if ($submission->status !== 'submitted') {
            return response()->json(['message' => 'Submission must be in submitted status first.'], 422);
        }

        $submission->update(['status' => 'correcting']);

        return response()->json(['message' => 'Marked as correcting.']);
    }

    // POST /teacher/submissions/{submission}/status
    public function setStatus(Request $request, TaskSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        $request->validate([
            'status' => 'required|in:taken,submitted,correcting,graded',
        ]);

        $submission->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated.', 'status' => $request->status]);
    }

    // POST /teacher/submissions/{submission}/grade
    public function grade(Request $request, TaskSubmission $submission)
    {
        $this->authorizeSubmission($submission);

        if (! in_array($submission->status, ['submitted', 'correcting'])) {
            return response()->json(['message' => 'Submission must be submitted or correcting first.'], 422);
        }

        $request->validate([
            'grade'    => 'required|numeric|min:0|max:' . $submission->task->max_grade,
            'feedback' => 'nullable|string|max:500',
            'status'   => 'nullable|in:correcting,graded',
        ]);

        $status = $request->status ?? 'graded';

        $submission->update([
            'grade'     => $request->grade,
            'feedback'  => $request->feedback,
            'status'    => $status,
            'graded_at' => $status === 'graded' ? now() : $submission->graded_at,
        ]);

        if ($status === 'graded') {
            Point::updateOrCreate(
                [
                    'student_id'         => $submission->student_id,
                    'task_submission_id' => $submission->id,
                ],
                [
                    'teacher_subject_id' => $submission->task->teacher_subject_id,
                    'point'              => (int) $request->grade,
                    'description'        => 'Grade ' . $request->grade . '/' . $submission->task->max_grade . ' — ' . $submission->task->title,
                    'point_date'         => today()->toDateString(),
                    'type'               => 'positive',
                ]
            );
        }

        return response()->json([
            'message' => 'Graded successfully.',
            'grade'   => $request->grade,
            'status'  => $status,
        ]);
    }

    private function authorizeTask(Task $task): void
    {
        $teacher = Auth::user()->teacher;

        if ($task->teacherSubject->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized task.');
        }
    }

    private function authorizeSubmission(TaskSubmission $submission): void
    {
        $teacher = Auth::user()->teacher;

        if ($submission->task->teacherSubject->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized submission.');
        }
    }

    private function mapSubmissions($submissions): array
    {
        return $submissions->map(fn ($s) => [
            'id'           => $s->id,
            'student_name' => $s->student->user->name                ?? '--',
            'class'        => $s->task->teacherSubject->class->name    ?? '--',
            'task_title'   => $s->task->title                          ?? '--',
            'subject'      => $s->task->teacherSubject->subject->name  ?? '--',
            'status'       => $s->status,
            'grade'        => $s->grade,
            'max_grade'    => $s->task->max_grade                      ?? 100,
            'feedback'     => $s->feedback,
            'submitted_at' => $s->submitted_at,
        ])->toArray();
    }
}