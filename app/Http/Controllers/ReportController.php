<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use App\Models\ClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // GET /reports?search=&type=
    public function adminIndex(Request $request)
    {
        $query = Point::with([
            'student.user',
            'teacherSubject.teacher.user',
            'teacherSubject.subject',
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('student.user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('teacherSubject.teacher.user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->type === 'positive') {
            $query->where('point', '>', 0);
        } elseif ($request->type === 'negative') {
            $query->where('point', '<', 0);
        }

        $records = $query->orderByDesc('point_date')->get()->map(fn ($p) => [
            'student'     => $p->student->user->name                   ?? '--',
            'teacher'     => $p->teacherSubject->teacher->user->name   ?? '--',
            'subject'     => $p->teacherSubject->subject->name         ?? '--',
            'description' => $p->description,
            'points'      => $p->point,
            'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
            'date'        => $p->point_date,
        ]);

        return response()->json($records);
    }

    // GET /reports/stats
    public function adminStats()
    {
        return response()->json([
            'total'        => Point::sum('point'),
            'awarded'      => Point::where('point', '>', 0)->sum('point'),
            'deducted'     => abs(Point::where('point', '<', 0)->sum('point')),
            'transactions' => Point::count(),
        ]);
    }

    // GET /teacher/report?period=week|month|all&ts_id=
    public function teacherReport(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $from = match ($request->input('period', 'month')) {
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        $tsQuery = TeacherSubject::with(['subject', 'class'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('ts_id')) {
            $tsQuery->where('id', $request->ts_id);
        }

        $teacherSubjects = $tsQuery->get();
        $filteredTsIds   = $teacherSubjects->pluck('id');

        $taskQuery = Task::whereIn('teacher_subject_id', $filteredTsIds);
        if ($from) {
            $taskQuery->where('created_at', '>=', $from);
        }
        $taskIds = $taskQuery->pluck('id');

        $subQ = TaskSubmission::whereIn('task_id', $taskIds);

        $summary = [
            'tasks'       => $taskIds->count(),
            'submissions' => (clone $subQ)->count(),
            'graded'      => (clone $subQ)->where('status', 'graded')->count(),
            'avg_grade'   => (clone $subQ)->where('status', 'graded')->avg('grade'),
        ];

        $breakdown = $teacherSubjects->map(function ($ts) use ($from) {
            $ids = Task::where('teacher_subject_id', $ts->id)
                ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
                ->pluck('id');

            $sq = TaskSubmission::whereIn('task_id', $ids);

            return [
                'class_name'      => $ts->class->name   ?? '--',
                'subject_name'    => $ts->subject->name ?? '--',
                'total_students'  => ClassEnrollment::where('class_id', $ts->class_id)->count(),
                'submitted_count' => (clone $sq)->whereIn('status', ['submitted', 'correcting', 'graded'])->count(),
                'graded_count'    => (clone $sq)->where('status', 'graded')->count(),
                'avg_grade'       => (clone $sq)->where('status', 'graded')->avg('grade'),
            ];
        });

        $points = Point::with(['student.user', 'teacherSubject.subject'])
            ->whereIn('teacher_subject_id', $filteredTsIds)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn ($p) => [
                'student'     => $p->student->user->name              ?? '--',
                'subject'     => $p->teacherSubject->subject->name     ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'date'        => $p->point_date,
            ]);

        return response()->json(compact('summary', 'breakdown', 'points'));
    }
}