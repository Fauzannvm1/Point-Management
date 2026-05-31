<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use App\Models\Point;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // GET /student/tasks?status=
    public function tasks(Request $request)
    {
        $student = $this->student();

        $tasksQuery = Task::with(['teacherSubject.subject', 'teacherSubject.teacher.user'])
            ->whereHas('teacherSubject', fn ($q) => $q->where('class_id', $student->class_id))
            ->latest()
            ->get();

        $taskIds = $tasksQuery->pluck('id');
        $submissions = TaskSubmission::whereIn('task_id', $taskIds)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('task_id');

        $tasks = $tasksQuery->map(function ($t) use ($submissions) {
                $sub = $submissions->get($t->id);
                return [
                    'id'          => $t->id,
                    'title'       => $t->title,
                    'description' => $t->description,
                    'subject'     => $t->teacherSubject->subject->name       ?? '--',
                    'teacher'     => $t->teacherSubject->teacher->user->name ?? '--',
                    'due_date'    => $t->due_date,
                    'max_grade'   => $t->max_grade,
                    'my_status'   => $sub ? $sub->status : 'available',
                    'my_grade'    => $sub ? $sub->grade  : null,
                ];
            });

        if ($request->filled('status')) {
            $filter = $request->status;
            $tasks  = $tasks->filter(fn ($t) =>
                $filter === 'available'
                    ? $t['my_status'] === 'available'
                    : $t['my_status'] === $filter
            )->values();
        }

        return response()->json($tasks);
    }

    // GET /student/my-submissions
    public function mySubmissions()
    {
        $student = $this->student();

        $submissions = TaskSubmission::where('student_id', $student->id)
            ->with(['task.teacherSubject.subject'])
            ->latest()
            ->get()
            ->map(fn ($s) => [
                'id'           => $s->id,
                'task_title'   => $s->task->title,
                'subject'      => $s->task->teacherSubject->subject->name ?? '--',
                'status'       => $s->status,
                'grade'        => $s->grade,
                'max_grade'    => $s->task->max_grade,
                'feedback'     => $s->feedback,
                'submitted_at' => $s->submitted_at,
                'due_date'     => $s->task->due_date,
            ]);

        return response()->json($submissions);
    }

    // POST /student/tasks/{task}/accept
    public function acceptTask(Task $task)
    {
        $student = $this->student();

        if ($task->due_date && $task->due_date < now()->toDateString()) {
            return response()->json(['message' => 'This task is past its due date.'], 422);
        }

        $belongs = TeacherSubject::where('id', $task->teacher_subject_id)
                                  ->where('class_id', $student->class_id)
                                  ->exists();

        if (! $belongs) {
            return response()->json(['message' => 'This task is not for your class.'], 403);
        }

        $already = TaskSubmission::where('task_id', $task->id)
                                  ->where('student_id', $student->id)
                                  ->exists();

        if ($already) {
            return response()->json(['message' => 'You have already accepted this task.'], 422);
        }

        $submission = TaskSubmission::create([
            'task_id'    => $task->id,
            'student_id' => $student->id,
            'status'     => 'taken',
        ]);

        return response()->json(['message' => 'Task accepted.', 'submission' => $submission], 201);
    }

    // POST /student/tasks/{task}/submit
    public function submitTask(Request $request, Task $task)
    {
        $request->validate([
            'note'    => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ]);

        $student = $this->student();

        $submission = TaskSubmission::where('task_id', $task->id)
                                     ->where('student_id', $student->id)
                                     ->where('status', 'taken')
                                     ->firstOrFail();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions/' . $submission->id, 'public');
                $submission->files()->create([
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                ]);
            }
        }

        $submission->update([
            'note'         => $request->note,
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Submitted successfully.']);
    }

    // GET /student/points
    public function points()
    {
        $student = $this->student();

        $points = Point::with('teacherSubject.subject')
            ->where('student_id', $student->id)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn ($p) => [
                'subject'     => $p->teacherSubject->subject->name ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'date'        => $p->point_date,
            ]);

        return response()->json($points);
    }

    // GET /student/points/stats
    public function pointStats()
    {
        $student = $this->student();

        return response()->json([
            'net'      => Point::where('student_id', $student->id)->sum('point'),
            'earned'   => Point::where('student_id', $student->id)->where('point', '>', 0)->sum('point'),
            'deducted' => Point::where('student_id', $student->id)->where('point', '<', 0)->sum('point'),
        ]);
    }

    // GET /student/report?period=month|all
    public function report(Request $request)
    {
        $student = $this->student();

        $from  = $request->period === 'all' ? null : now()->startOfMonth();
        $tsIds = TeacherSubject::where('class_id', $student->class_id)->pluck('id');

        $taskIds = Task::whereIn('teacher_subject_id', $tsIds)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->pluck('id');

        $subQ = TaskSubmission::where('student_id', $student->id)->whereIn('task_id', $taskIds);

        $summary = [
            'tasks'     => $taskIds->count(),
            'submitted' => (clone $subQ)->whereIn('status', ['submitted', 'correcting', 'graded'])->count(),
            'points'    => Point::where('student_id', $student->id)->sum('point'),
            'avg_grade' => (clone $subQ)->where('status', 'graded')->avg('grade'),
        ];

        $breakdown = TeacherSubject::with(['subject', 'teacher.user'])
            ->whereIn('id', $tsIds)
            ->get()
            ->map(function ($ts) use ($student, $from) {
                $ids = Task::where('teacher_subject_id', $ts->id)
                    ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                    ->pluck('id');

                $sq = TaskSubmission::where('student_id', $student->id)->whereIn('task_id', $ids);

                return [
                    'subject_name' => $ts->subject->name       ?? '--',
                    'teacher_name' => $ts->teacher->user->name ?? '--',
                    'total_tasks'  => $ids->count(),
                    'submitted'    => (clone $sq)->whereIn('status', ['submitted', 'correcting', 'graded'])->count(),
                    'avg_grade'    => (clone $sq)->where('status', 'graded')->avg('grade'),
                ];
            });

        $graded = TaskSubmission::where('student_id', $student->id)
            ->where('status', 'graded')
            ->with(['task.teacherSubject.subject'])
            ->latest('graded_at')
            ->get()
            ->map(fn ($s) => [
                'task_title' => $s->task->title,
                'subject'    => $s->task->teacherSubject->subject->name ?? '--',
                'grade'      => $s->grade,
                'max_grade'  => $s->task->max_grade,
                'feedback'   => $s->feedback,
                'graded_at'  => $s->graded_at,
            ]);

        return response()->json(compact('summary', 'breakdown', 'graded'));
    }

    private function student(): Student
    {
        $student = Auth::user()->student;

        if (! $student) {
            abort(403, 'Student profile not found.');
        }

        return $student;
    }
}