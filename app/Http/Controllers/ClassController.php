<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\ClassEnrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    // ADMIN

    /**
     * GET /admin/classes
     * List all classes with optional filters.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = SchoolClass::with(['teacher:id,name', 'subject:id,name'])
            ->withCount([
                'students',
                'tasks',
            ]);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                                       ->orWhere('code', 'like', "%{$s}%"));
        }

        $classes  = $query->orderBy('created_at', 'desc')->get()->map(fn ($c) => $this->mapClass($c));
        $subjects = Subject::select('id', 'name')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->select('id', 'name')->orderBy('name')->get();

        return response()->json(compact('classes', 'subjects', 'teachers'));
    }

    /**
     * GET /admin/classes/{id}
     * Single class detail.
     */
    public function adminShow(int $id): JsonResponse
    {
        $class = SchoolClass::with([
            'teacher:id,name',
            'subject:id,name',
            'students:id,name,email',
            'tasks:id,title,due_date,class_id',
        ])->findOrFail($id);

        return response()->json($this->mapClassDetail($class));
    }

    /**
     * POST /admin/classes
     * Create a new class.
     */
    public function adminStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'subject_id'  => ['required', 'exists:subjects,id'],
            'teacher_id'  => ['required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'code'        => ['nullable', 'string', 'max:20', 'unique:school_classes,code'],
        ]);

        $data['code'] = strtoupper($data['code'] ?? $this->generateCode());

        $class = SchoolClass::create($data);
        $class->load(['teacher:id,name', 'subject:id,name']);

        return response()->json([
            'message' => 'Class created successfully.',
            'class'   => $this->mapClass($class),
        ], 201);
    }

    /**
     * PUT /admin/classes/{id}
     * Update a class.
     */
    public function adminUpdate(Request $request, int $id): JsonResponse
    {
        $class = SchoolClass::findOrFail($id);

        $data = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:100'],
            'subject_id'  => ['sometimes', 'required', 'exists:subjects,id'],
            'teacher_id'  => ['sometimes', 'required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'code'        => ['nullable', 'string', 'max:20', "unique:school_classes,code,{$id}"],
        ]);

        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $class->update($data);
        $class->load(['teacher:id,name', 'subject:id,name']);

        return response()->json([
            'message' => 'Class updated successfully.',
            'class'   => $this->mapClass($class),
        ]);
    }

    /**
     * DELETE /admin/classes/{id}
     * Delete a class.
     */
    public function adminDestroy(int $id): JsonResponse
    {
        $class = SchoolClass::findOrFail($id);

        // Manually delete related enrollments and teacher_subjects to prevent constraint errors
        \App\Models\ClassEnrollment::where('class_id', $id)->delete();
        
        $teacherSubjects = \App\Models\TeacherSubject::where('class_id', $id)->get();
        foreach ($teacherSubjects as $ts) {
            $ts->tasks()->delete();
            $ts->delete();
        }

        $class->delete();

        return response()->json(['message' => 'Class deleted successfully.']);
    }

    /**
     * GET /admin/classes/{id}/students
     * List students enrolled in a class.
     */
    public function adminStudents(int $id): JsonResponse
    {
        $class    = SchoolClass::findOrFail($id);
        $students = $this->getStudentList($class);

        return response()->json(['students' => $students]);
    }

    /**
     * POST /admin/classes/{id}/students
     * Manually enroll a student. Body: { "student_id": 5 }
     */
    public function adminAddStudent(Request $request, int $id): JsonResponse
    {
        $request->validate(['student_id' => ['required', 'exists:users,id']]);

        $class = SchoolClass::findOrFail($id);

        if (ClassEnrollment::where('class_id', $class->id)->where('student_id', $request->student_id)->exists()) {
            return response()->json(['message' => 'Student is already enrolled.'], 422);
        }

        ClassEnrollment::create(['class_id' => $class->id, 'student_id' => $request->student_id]);

        return response()->json(['message' => 'Student enrolled successfully.'], 201);
    }

    /**
     * DELETE /admin/classes/{classId}/students/{studentId}
     * Remove a student from a class.
     */
    public function adminRemoveStudent(int $classId, int $studentId): JsonResponse
    {
        $deleted = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', $studentId)
            ->delete();

        if (! $deleted) {
            return response()->json(['message' => 'Enrollment not found.'], 404);
        }

        return response()->json(['message' => 'Student removed from class.']);
    }

    // TEACHER

    /**
     * GET /teacher/classes
     * List classes assigned to the authenticated teacher.
     */
    public function teacherStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'subject_id'  => ['required', 'exists:subjects,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'join_code'   => ['nullable', 'string', 'max:20'],
        ]);

        $data['code'] = strtoupper($data['join_code'] ?? $this->generateCode());
        unset($data['join_code']);

        $data['teacher_id'] = Auth::id();

        $class = SchoolClass::create($data);
        $class->load(['subject:id,name']);

        return response()->json([
            'message' => 'Class created successfully.',
            'class'   => [
                'id'            => $class->id,
                'name'          => $class->name,
                'code'          => $class->code,
                'description'   => $class->description,
                'subject_name'  => $class->subject?->name,
                'student_count' => 0,
                'total_tasks'   => 0,
                'active_tasks'  => 0,
                'created_at'    => $class->created_at,
            ],
        ], 201);
    }

    public function teacherUpdate(Request $request, int $id): JsonResponse
    {
        $class = SchoolClass::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'subject_id'  => ['nullable', 'exists:subjects,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'join_code'   => ['nullable', 'string', 'max:20'],
        ]);

        if (isset($data['join_code'])) {
            $data['code'] = strtoupper($data['join_code']);
            unset($data['join_code']);
        }
        
        if (empty($data['subject_id'])) {
            unset($data['subject_id']);
        }

        $class->update($data);

        return response()->json([
            'message' => 'Class updated successfully.',
        ]);
    }

    public function teacherDestroy(int $id): JsonResponse
    {
        $class = SchoolClass::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        
        if ($class->students()->count() > 0) {
            return response()->json(['message' => 'Cannot delete class with enrolled students.'], 422);
        }

        $class->delete();
        return response()->json(['message' => 'Class deleted successfully.']);
    }

    public function teacherIndex(): JsonResponse
    {
        $classes = SchoolClass::with(['subject:id,name'])
            ->where('teacher_id', Auth::id())
            ->withCount([
                'students',
                'tasks',
                'tasks as active_tasks' => fn ($q) => $q->whereDate('due_date', '>=', now()),
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id'            => $c->id,
                'name'          => $c->name,
                'code'          => $c->code,
                'description'   => $c->description,
                'subject_name'  => $c->subject?->name,
                'student_count' => $c->students_count,
                'total_tasks'   => $c->tasks_count,
                'active_tasks'  => $c->active_tasks,
                'created_at'    => $c->created_at,
            ]);

        return response()->json(['classes' => $classes]);
    }

    /**
     * GET /teacher/classes/{id}
     * Single class detail — teacher must own it.
     */
    public function teacherShow(int $id): JsonResponse
    {
        $class = $this->findTeacherClass($id);

        return response()->json([
            ...$this->mapClassDetail($class),
            'students' => $this->getStudentList($class),
            'tasks'    => $class->tasks()->select('id', 'title', 'due_date', 'max_grade')->orderBy('due_date')->get(),
        ]);
    }

    /**
     * GET /teacher/classes/{id}/students
     * Students in one of the teacher's classes.
     */
    public function teacherStudents(int $id): JsonResponse
    {
        $class = $this->findTeacherClass($id);

        return response()->json([
            'class_name' => $class->name,
            'students'   => $this->getStudentList($class),
        ]);
    }

    /**
     * DELETE /teacher/classes/{classId}/students/{studentId}
     * Remove a student from one of the teacher's classes.
     */
    public function teacherRemoveStudent(int $classId, int $studentId): JsonResponse
    {
        $this->findTeacherClass($classId); // ownership check

        $deleted = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', $studentId)
            ->delete();

        if (! $deleted) {
            return response()->json(['message' => 'Student not found in this class.'], 404);
        }

        return response()->json(['message' => 'Student removed from class.']);
    }

    /**
     * GET /teacher/classes/{id}/code
     * Get the join code to share with students.
     */
    public function teacherCode(int $id): JsonResponse
    {
        $class = $this->findTeacherClass($id);

        return response()->json(['code' => $class->code, 'class_name' => $class->name]);
    }


    // STUDENT

    /**
     * GET /student/classes
     * List classes the authenticated student is enrolled in.
     */
    public function studentIndex(): JsonResponse
    {
        $classes = SchoolClass::query()
            ->join('class_enrollments', 'class_enrollments.class_id', '=', 'school_classes.id')
            ->where('class_enrollments.student_id', Auth::id())
            ->with(['teacher:id,name', 'subject:id,name'])
            ->withCount([
                'tasks as total_tasks',
                'tasks as active_tasks' => fn ($q) => $q->whereDate('due_date', '>=', now()),
                'students as student_count',
            ])
            ->select('school_classes.*', 'class_enrollments.created_at as joined_at')
            ->orderBy('class_enrollments.created_at', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id'            => $c->id,
                'name'          => $c->name,
                'code'          => $c->code,
                'subject_name'  => $c->subject?->name,
                'teacher_name'  => $c->teacher?->name,
                'total_tasks'   => $c->total_tasks,
                'active_tasks'  => $c->active_tasks,
                'student_count' => $c->student_count,
                'joined_at'     => $c->joined_at,
            ]);

        return response()->json(['classes' => $classes]);
    }

    /**
     * POST /student/classes/join
     * Join a class by code. Body: { "code": "MTH-2024" }
     */
    public function studentJoin(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:20']]);

        $code  = strtoupper(trim($request->code));
        $class = SchoolClass::with(['teacher:id,name', 'subject:id,name'])
            ->where('code', $code)
            ->first();

        if (! $class) {
            return response()->json(['message' => 'Class not found. Please check the code and try again.'], 404);
        }

        if (ClassEnrollment::where('class_id', $class->id)->where('student_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You are already enrolled in this class.'], 422);
        }

        ClassEnrollment::create(['class_id' => $class->id, 'student_id' => Auth::id()]);

        return response()->json([
            'message' => 'Successfully joined the class.',
            'class'   => [
                'id'           => $class->id,
                'name'         => $class->name,
                'code'         => $class->code,
                'subject_name' => $class->subject?->name,
                'teacher_name' => $class->teacher?->name,
            ],
        ], 201);
    }

    /**
     * DELETE /student/classes/{id}/leave
     * Leave a class.
     */
    public function studentLeave(int $id): JsonResponse
    {
        $deleted = ClassEnrollment::where('class_id', $id)
            ->where('student_id', Auth::id())
            ->delete();

        if (! $deleted) {
            return response()->json(['message' => 'You are not enrolled in this class.'], 404);
        }

        return response()->json(['message' => 'You have left the class.']);
    }

    // PRIVATE HELPERS

    private function findTeacherClass(int $id): SchoolClass
    {
        $class = SchoolClass::with(['subject:id,name'])->findOrFail($id);

        if ($class->teacher_id !== Auth::id()) {
            abort(403, 'You do not have access to this class.');
        }

        return $class;
    }

    private function getStudentList(SchoolClass $class): \Illuminate\Support\Collection
    {
        return $class->students()
            ->select('users.id', 'users.name', 'users.email')
            ->withPivot('created_at')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($s) => [
                'id'        => $s->id,
                'name'      => $s->name,
                'email'     => $s->email,
                'joined_at' => $s->pivot->created_at,
            ]);
    }

    private function mapClass(SchoolClass $c): array
    {
        return [
            'id'            => $c->id,
            'name'          => $c->name,
            'code'          => $c->code,
            'description'   => $c->description,
            'subject_id'    => $c->subject_id,
            'subject_name'  => $c->subject?->name,
            'teacher_id'    => $c->teacher_id,
            'teacher_name'  => $c->teacher?->name,
            'student_count' => $c->students_count ?? $c->students?->count(),
            'task_count'    => $c->tasks_count    ?? $c->tasks?->count(),
            'created_at'    => $c->created_at,
        ];
    }

    private function mapClassDetail(SchoolClass $c): array
    {
        return [
            'id'           => $c->id,
            'name'         => $c->name,
            'code'         => $c->code,
            'description'  => $c->description,
            'subject_name' => $c->subject?->name,
            'teacher_name' => $c->teacher?->name,
            'created_at'   => $c->created_at,
        ];
    }

    private function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(3) . '-' . rand(1000, 9999));
        } while (SchoolClass::where('code', $code)->exists());

        return $code;
    }
}