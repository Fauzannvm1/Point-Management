<?php

namespace App\Http\Controllers;

use App\Models\TeacherSubject;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherSubjectController extends Controller
{
    // GET /admin/teacher-subjects/form-data
    public function formData()
    {
        $teachers = User::where('role', 'teacher')
            ->get()
            ->map(fn ($u) => ['id' => $u->teacher?->id, 'name' => $u->name])
            ->filter(fn ($t) => $t['id'])
            ->values();

        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        $classes  = Classes::orderBy('name')->get(['id', 'name']);

        return response()->json(compact('teachers', 'subjects', 'classes'));
    }

    // GET /admin/teacher-subjects
    public function index()
    {
        $rows = TeacherSubject::with(['teacher.user', 'subject', 'class'])
            ->get()
            ->map(fn ($ts) => $this->mapRow($ts));

        return response()->json($rows);
    }

    // POST /admin/teacher-subjects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        if (TeacherSubject::where($validated)->exists()) {
            return response()->json(['message' => 'This assignment already exists.'], 422);
        }

        $ts = TeacherSubject::create($validated);
        $ts->load(['teacher.user', 'subject', 'class']);

        return response()->json($this->mapRow($ts), 201);
    }

    // PUT /admin/teacher-subjects/{ts}
    public function update(Request $request, TeacherSubject $ts)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        if (TeacherSubject::where($validated)->where('id', '!=', $ts->id)->exists()) {
            return response()->json(['message' => 'This assignment already exists.'], 422);
        }

        $ts->update($validated);
        $ts->load(['teacher.user', 'subject', 'class']);

        return response()->json($this->mapRow($ts));
    }

    // DELETE /admin/teacher-subjects/{ts}
    public function destroy(TeacherSubject $ts)
    {
        if ($ts->tasks()->count() > 0 || $ts->points()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — this assignment has tasks or points linked to it.',
            ], 422);
        }

        $ts->delete();

        return response()->json(['message' => 'Assignment removed.']);
    }

    // GET /teacher/subjects/create  — dropdown data for the teacher modal
    public function teacherCreate()
    {
        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        $classes  = Classes::orderBy('name')->get(['id', 'name']);

        return response()->json(compact('subjects', 'classes'));
    }

    // GET /teacher/subjects
    public function teacherIndex()
    {
        $teacher = Auth::user()->teacher;

        $subjects = TeacherSubject::with(['subject', 'class'])
            ->where('teacher_id', $teacher->id)
            ->withCount('tasks')
            ->get()
            ->map(fn ($ts) => [
                'id'             => $ts->id,
                'subject_name'   => $ts->subject->name ?? '--',
                'class_name'     => $ts->class->name   ?? '--',
                'tasks_count'    => $ts->tasks_count,
                'students_count' => Student::where('class_id', $ts->class_id)->count(),
            ]);

        return response()->json($subjects);
    }

    // POST /teacher/subjects
    public function teacherStore(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        $teacher = Auth::user()->teacher;

        $exists = TeacherSubject::where('teacher_id',  $teacher->id)
                                 ->where('subject_id', $request->subject_id)
                                 ->where('class_id',   $request->class_id)
                                 ->exists();

        if ($exists) {
            return response()->json(['message' => 'This subject-class combination is already assigned.'], 422);
        }

        $ts = TeacherSubject::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class_id'   => $request->class_id,
        ]);

        return response()->json($ts->load(['subject', 'class']), 201);
    }

    // DELETE /teacher/subjects/{id}
    public function teacherDestroy($id)
    {
        $ts = TeacherSubject::where('id', $id)
                             ->where('teacher_id', Auth::user()->teacher->id)
                             ->firstOrFail();

        $ts->delete();

        return response()->json(['message' => 'Subject removed.']);
    }

    private function mapRow(TeacherSubject $ts): array
    {
        return [
            'id'         => $ts->id,
            'teacher_id' => $ts->teacher_id,
            'subject_id' => $ts->subject_id,
            'class_id'   => $ts->class_id,
            'teacher'    => $ts->teacher->user->name ?? '--',
            'subject'    => $ts->subject->name       ?? '--',
            'class'      => $ts->class->name         ?? '--',
        ];
    }
}