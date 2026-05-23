<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\Student;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    // GET /admin/students-for-points  — student list for the give-points dropdown
    public function studentsForPoints()
    {
        $students = Student::with(['user', 'class'])
            ->get()
            ->map(fn ($s) => [
                'id'    => $s->id,
                'name'  => $s->user->name  ?? '--',
                'class' => $s->class->name ?? '--',
            ]);

        return response()->json($students);
    }

    // POST /admin/points  — admin manually awards or deducts points
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'student_id'         => 'required|exists:students,id',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'point'              => 'required|integer|not_in:0',
            'description'        => 'nullable|string|max:255',
            'point_date'         => 'nullable|date',
            'task_submission_id' => 'nullable|exists:task_submissions,id',
        ]);

        $point = Point::create([
            'student_id'         => $validated['student_id'],
            'teacher_subject_id' => $validated['teacher_subject_id'],
            'task_submission_id' => $validated['task_submission_id'] ?? null,
            'point'              => $validated['point'],
            'description'        => $validated['description'] ?? null,
            'point_date'         => $validated['point_date']  ?? today()->toDateString(),
            'type'               => $validated['point'] >= 0 ? 'positive' : 'negative',
        ]);

        if (! empty($validated['task_submission_id'])) {
            TaskSubmission::where('id', $validated['task_submission_id'])
                ->update([
                    'status' => 'graded',
                    'grade'  => abs($validated['point']),
                ]);
        }

        return response()->json($point, 201);
    }

    // GET /teacher/points
    public function teacherIndex()
    {
        $teacher = Auth::user()->teacher;
        $tsIds   = TeacherSubject::where('teacher_id', $teacher->id)->pluck('id');

        $points = Point::with(['student.user', 'teacherSubject.subject'])
            ->whereIn('teacher_subject_id', $tsIds)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn ($p) => $this->mapPoint($p));

        return response()->json($points);
    }

    // GET /teacher/points/stats
    public function teacherStats()
    {
        $teacher = Auth::user()->teacher;
        $tsIds   = TeacherSubject::where('teacher_id', $teacher->id)->pluck('id');

        return response()->json([
            'total'    => Point::whereIn('teacher_subject_id', $tsIds)->sum('point'),
            'awarded'  => Point::whereIn('teacher_subject_id', $tsIds)->where('point', '>', 0)->sum('point'),
            'deducted' => Point::whereIn('teacher_subject_id', $tsIds)->where('point', '<', 0)->sum('point'),
        ]);
    }

    private function mapPoint(Point $p): array
    {
        return [
            'student'     => $p->student->user->name              ?? '--',
            'subject'     => $p->teacherSubject->subject->name     ?? '--',
            'description' => $p->description,
            'points'      => $p->point,
            'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
            'date'        => $p->point_date,
        ];
    }
}