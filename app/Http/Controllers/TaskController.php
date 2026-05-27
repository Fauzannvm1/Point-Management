<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use App\Models\Point;
use App\Models\Subject;
use App\Models\Classes;   
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Handles core application logic for Tasks, Submissions, Points, and Teacher Subjects.
 * 
 * Provides API endpoints for Teachers to manage tasks/grades, Students to view/submit tasks,
 * and Admins to oversee tasks, subjects, users, points, and reports.
 */
class TaskController extends Controller
{
    // Private Helpers

    /**
     * Get the authenticated teacher profile.
     *
     * @return \App\Models\Teacher
     */
    private function teacher(): Teacher
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            $this->forceLogout();
            abort(403, 'Teacher profile not found.');
        }
        return $teacher;
    }

    /**
     * Get the authenticated student profile.
     *
     * @return \App\Models\Student
     */
    private function student(): Student
    {
        $student = Auth::user()->student;
        if (!$student) {
            $this->forceLogout();
            abort(403, 'Student profile not found.');
        }
        return $student;
    }

    /**
     * Force logout the user.
     *
     * @return void
     */
    private function forceLogout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Get IDs of all subjects assigned to the authenticated teacher.
     *
     * @return \Illuminate\Support\Collection
     */
    private function teacherSubjectIds()
    {
        return TeacherSubject::where('teacher_id', $this->teacher()->id)->pluck('id');
    }

    /**
     * Authorize that the authenticated teacher owns the given subject assignment.
     *
     * @param int $teacherSubjectId
     * @return void
     */
    private function authorizeTeacherSubject(int $teacherSubjectId): void
    {
        $owns = TeacherSubject::where('id', $teacherSubjectId)
                              ->where('teacher_id', $this->teacher()->id)
                              ->exists();
        if (!$owns) abort(403, 'Unauthorized subject.');
    }

    /**
     * Authorize that the authenticated teacher owns the given task.
     *
     * @param Task $task
     * @return void
     */
    private function authorizeTask(Task $task): void
    {
        if ($task->teacherSubject->teacher_id !== $this->teacher()->id) {
            abort(403, 'Unauthorized task.');
        }
    }

    /**
     * Authorize that the authenticated teacher has access to the given submission.
     *
     * @param TaskSubmission $submission
     * @return void
     */
    private function authorizeSubmissionAsTeacher(TaskSubmission $submission): void
    {
        if ($submission->task->teacherSubject->teacher_id !== $this->teacher()->id) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Handle file uploads for a task.
     *
     * @param Task $task
     * @param Request $request
     * @return void
     */
    private function handleTaskFiles(Task $task, Request $request): void
    {
        if (!$request->hasFile('files')) return;

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();
            $path = $this->compressAndStoreFile($file, 'task-files');

            if (str_starts_with($mime, 'image/')) {
                $task->images()->create([
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            } else {
                $task->attachments()->create([
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }
    }

    /**
     * Compress images and set correct permissions to prevent 403.
     */
    private function compressAndStoreFile($file, $folder)
    {
        $mime = $file->getMimeType();
        $ext = $file->getClientOriginalExtension() ?: 'bin';
        $fileName = \Illuminate\Support\Str::random(40) . '.' . $ext;
        $path = $folder . '/' . $fileName;
        $absolutePath = storage_path('app/public/' . $path);

        if (!file_exists(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        if (str_starts_with($mime, 'image/')) {
            $image = null;
            if (extension_loaded('gd')) {
                if ($mime == 'image/jpeg' || $mime == 'image/jpg') $image = @imagecreatefromjpeg($file->getRealPath());
                elseif ($mime == 'image/png') $image = @imagecreatefrompng($file->getRealPath());
                elseif ($mime == 'image/webp') $image = @imagecreatefromwebp($file->getRealPath());
            }

            if ($image !== false && $image !== null) {
                imagejpeg($image, $absolutePath, 75);
                imagedestroy($image);
                @chmod($absolutePath, 0644);
                return $path;
            }
        }

        $path = $file->store($folder, 'public');
        @chmod(storage_path('app/public/' . $path), 0644);
        return $path;
    }

    // Main Dashboard GET /dashboard

    /**
     * Route the authenticated user to the correct role-based dashboard.
     *
     * @return View|RedirectResponse
     */
    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            'teacher' => $this->teacherDashboard(),
            'student' => $this->studentDashboardView(),
            'admin'   => $this->adminDashboard(),
            'parent'  => view('dashboard.parent'),
            default   => abort(403),
        };
    }

    /**
     * Build the teacher dashboard view.
     *
     * @return View
     */
    private function teacherDashboard(): View
    {
        $tsIds = $this->teacherSubjectIds();

        $totalTasks     = Task::whereIn('teacher_subject_id', $tsIds)->count();
        $pendingGrading = TaskSubmission::whereHas('task', fn($q) =>
                              $q->whereIn('teacher_subject_id', $tsIds)
                          )->whereIn('status', ['submitted', 'correcting'])->count();
        $totalGraded    = TaskSubmission::whereHas('task', fn($q) =>
                              $q->whereIn('teacher_subject_id', $tsIds)
                          )->where('status', 'graded')->count();
        $pointsGiven    = Point::whereIn('teacher_subject_id', $tsIds)
                              ->whereDate('point_date', today())
                              ->sum('point');

        $recentActivity = TaskSubmission::with(['student.user', 'task.teacherSubject.subject'])
            ->whereHas('task', fn($q) => $q->whereIn('teacher_subject_id', $tsIds))
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn($s) => [
                'student' => $s->student->user?->name                 ?? '--',
                'task'    => $s->task->title                          ?? '--',
                'subject' => $s->task->teacherSubject->subject?->name  ?? '--',
                'status'  => $s->status,
                'grade'   => $s->grade,
            ]);

        return view('dashboard.teacher', compact(
            'totalTasks', 'pendingGrading', 'totalGraded', 'pointsGiven', 'recentActivity'
        ));
    }

    /**
     * Build the student dashboard view.
     *
     * @return View
     */
    private function studentDashboardView(): View
    {
        $student = $this->student();

        $classIds = \App\Models\ClassEnrollment::where('student_id', Auth::id())->pluck('class_id');

        $totalTasks     = Task::whereHas('teacherSubject',
                              fn($q) => $q->whereIn('class_id', $classIds)
                          )->count();
        $pendingTasks   = TaskSubmission::where('student_id', $student->id)
                              ->where('status', 'taken')->count();
        $submittedTasks = TaskSubmission::where('student_id', $student->id)
                              ->whereIn('status', ['submitted', 'correcting', 'graded'])->count();
        $totalPoints    = Point::where('student_id', $student->id)->sum('point');

        $recentTasks = Task::with(['teacherSubject.subject'])
            ->whereHas('teacherSubject', fn($q) => $q->whereIn('class_id', $classIds))
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($t) use ($student) {
                $sub = TaskSubmission::where('task_id', $t->id)
                                     ->where('student_id', $student->id)->first();
                return [
                    'title'   => $t->title,
                    'subject' => $t->teacherSubject->subject?->name ?? '--',
                    'due'     => $t->due_date
                                   ? \Carbon\Carbon::parse($t->due_date)->format('d M')
                                   : '--',
                    'status'  => $sub ? $sub->status : 'taken',
                ];
            });

        return view('dashboard.student', compact(
            'totalTasks', 'pendingTasks', 'submittedTasks', 'totalPoints', 'recentTasks'
        ));
    }

    /**
     * Build the admin dashboard view.
     *
     * @return View
     */
    private function adminDashboard(): View
    {
        $totalStudents    = User::where('role', 'student')->count();
        $totalTeachers    = User::where('role', 'teacher')->count();
        $totalTasks       = Task::count();
        $totalPoints      = Point::sum('point');
        $pointsToday      = Point::whereDate('point_date', today())->sum('point');
        $newStudentsMonth = User::where('role', 'student')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();
        $newTeachersMonth = User::where('role', 'teacher')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();

        $recentActivity = Point::with(['student.user', 'teacherSubject.subject'])
            ->orderByDesc('point_date')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'student' => $p->student->user?->name              ?? '--',
                'teacher' => $p->teacherSubject->teacher->user?->name ?? '--',
                'points'  => $p->point,
                'type'    => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'subject' => $p->teacherSubject->subject?->name     ?? '--',
            ]);

        return view('dashboard.admin', compact(
            'totalStudents', 'totalTeachers', 'totalTasks', 'totalPoints',
            'pointsToday', 'newStudentsMonth', 'newTeachersMonth', 'recentActivity'
        ));
    }

    // Shared GET /task/{task}/detail

    /**
     * Get detailed information for a specific task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        $task = Task::with([
            'teacherSubject.subject',
            'teacherSubject.class',
            'teacherSubject.teacher.user',
            'images',
            'attachments',
        ])->findOrFail($id);

        $submission = null;
        if (Auth::user()->role === 'student') {
            $student    = $this->student();
            $submission = TaskSubmission::where('task_id', $id)
                              ->where('student_id', $student->id)
                              ->first();
        }

        return response()->json([
            'id'          => $task->id,
            'title'       => $task->title,
            'description' => $task->description,
            'subject'     => $task->teacherSubject->subject?->name           ?? '--',
            'class'       => $task->teacherSubject->class?->name             ?? '--',
            'teacher'     => $task->teacherSubject->teacher->user?->name     ?? '--',
            'due_date'    => $task->due_date,
            'max_grade'   => $task->max_grade,
            'images'      => $task->images->map(fn($i) => [
                'url'  => Storage::url($i->path),
                'name' => $i->original_name ?? basename($i->path),
            ]),
            'attachments' => $task->attachments->map(fn($a) => [
                'url'  => Storage::url($a->path),
                'name' => $a->original_name ?? basename($a->path),
            ]),
            'my_submission' => $submission ? [
                'status'   => $submission->status,
                'grade'    => $submission->grade,
                'feedback' => $submission->feedback,
                'notes'    => $submission->note,
                'files'    => $submission->files()->get()->map(fn($f) => [
                    'url'  => Storage::url($f->path),
                    'name' => $f->original_name ?? basename($f->path),
                ]),
            ] : null,
        ]);
    }

    // Teacher – Tasks

    /** 
     * GET /teacher/tasks
     * 
     * List all tasks assigned by the authenticated teacher.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tsIds = $this->teacherSubjectIds();

        $tasks = Task::with(['teacherSubject.subject', 'teacherSubject.class', 'images'])
            ->whereIn('teacher_subject_id', $tsIds)
            ->withCount('submissions')
            ->withCount(['submissions as pending_count' => fn($q) =>
                $q->whereIn('status', ['submitted', 'correcting'])
            ])
            ->withCount(['images as images_count'])
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    /** 
     * GET /teacher/tasks/create 
     * 
     * Get form data required for creating a new task.
     * 
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $tsIds = $this->teacherSubjectIds();

        $teacherSubjects = TeacherSubject::with(['subject', 'class'])
            ->whereIn('id', $tsIds)
            ->get()
            ->map(fn($ts) => [
                'id'      => $ts->id,
                'subject' => $ts->subject?->name ?? '--',
                'class'   => $ts->class?->name   ?? '--',
            ]);

        return response()->json(compact('teacherSubjects'));
    }

    /** 
     * POST /teacher/tasks 
     * 
     * Store a new task.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'title'              => 'required|string|max:150',
            'description'        => 'nullable|string',
            'due_date'           => 'nullable|date',
            'max_grade'          => 'nullable|integer|min:1|max:10000',
            'files.*'            => 'nullable|file|max:10240',
        ]);

        $this->authorizeTeacherSubject($validated['teacher_subject_id']);

        $task = Task::create([
            'teacher_subject_id' => $validated['teacher_subject_id'],
            'title'              => $validated['title'],
            'description'        => $validated['description'] ?? null,
            'due_date'           => $validated['due_date']     ?? null,
            'max_grade'          => $validated['max_grade']    ?? 100,
        ]);

        $this->handleTaskFiles($task, $request);

        return response()->json($task->load(['teacherSubject.subject', 'teacherSubject.class']), 201);
    }

    /** 
     * POST /teacher/tasks/{task}  (_method=PUT via FormData) 
     * 
     * Update an existing task.
     * 
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'teacher_subject_id' => 'sometimes|exists:teacher_subjects,id',
            'title'              => 'sometimes|string|max:150',
            'description'        => 'nullable|string',
            'due_date'           => 'nullable|date',
            'max_grade'          => 'sometimes|integer|min:1|max:10000',
            'files.*'            => 'nullable|file|max:10240',
        ]);

        if (isset($validated['teacher_subject_id'])) {
            $this->authorizeTeacherSubject($validated['teacher_subject_id']);
        }

        $task->update([
            'teacher_subject_id' => $validated['teacher_subject_id'] ?? $task->teacher_subject_id,
            'title'              => $validated['title']               ?? $task->title,
            'description'        => $validated['description']         ?? $task->description,
            'due_date'           => $validated['due_date']            ?? $task->due_date,
            'max_grade'          => $validated['max_grade']           ?? $task->max_grade,
        ]);

        $this->handleTaskFiles($task, $request);

        return response()->json($task->load(['teacherSubject.subject', 'teacherSubject.class']));
    }

    /** 
     * DELETE /teacher/tasks/{task} 
     * 
     * Delete a task and its associated files.
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        foreach ($task->images      as $img) Storage::delete($img->path);
        foreach ($task->attachments as $att) Storage::delete($att->path);

        $task->delete();

        return response()->json(['message' => 'Task deleted.']);
    }

    // Teacher – Submissions

    /** 
     * GET /teacher/submissions 
     * 
     * List all submissions for tasks assigned by the teacher.
     * 
     * @param Request $request
     * @param Task|null $task
     * @return JsonResponse
     */
    public function submissionIndex(Request $request, Task $task = null): JsonResponse
    {
        $tsIds = $this->teacherSubjectIds();

        $query = TaskSubmission::with([
            'student.user',
            'task.teacherSubject.subject',
            'task.teacherSubject.class',
            'files',
        ])->whereHas('task', fn($q) => $q->whereIn('teacher_subject_id', $tsIds));

        if ($task) {
            $this->authorizeTask($task);
            $query->where('task_id', $task->id);
        }

        if ($request->filled('task_id')) $query->where('task_id', $request->task_id);
        if ($request->filled('status'))  $query->where('status',  $request->status);

        $submissions = $query->orderByDesc('updated_at')->get()->map(fn($s) => [
            'id'           => $s->id,
            'student_name' => $s->student->user?->name                 ?? '--',
            'class'        => $s->task->teacherSubject->class?->name    ?? '--',
            'task_title'   => $s->task->title                          ?? '--',
            'subject'      => $s->task->teacherSubject->subject?->name  ?? '--',
            'status'       => $s->status,
            'grade'        => $s->grade,
            'max_grade'    => $s->task->max_grade                      ?? 100,
            'feedback'     => $s->feedback,
            'note'         => $s->note,
            'files'        => $s->files->map(fn($f) => [
                'url'  => Storage::url($f->path),
                'name' => $f->original_name ?? basename($f->path),
            ]),
            'submitted_at' => $s->submitted_at,
        ]);

        return response()->json($submissions);
    }

    /** 
     * POST /teacher/submissions/{submission}/correcting 
     * 
     * Mark a submission as currently being corrected.
     * 
     * @param TaskSubmission $submission
     * @return JsonResponse
     */
    public function markCorrecting(TaskSubmission $submission): JsonResponse
    {
        $this->authorizeSubmissionAsTeacher($submission);

        if ($submission->status !== 'submitted') {
            return response()->json(['message' => 'Submission must be submitted first.'], 422);
        }

        $submission->update(['status' => 'correcting']);

        return response()->json(['message' => 'Marked as correcting.']);
    }

    /** 
     * POST /teacher/submissions/{submission}/status 
     * 
     * Update the status of a submission.
     * 
     * @param Request $request
     * @param TaskSubmission $submission
     * @return JsonResponse
     */
    public function submissionSetStatus(Request $request, TaskSubmission $submission): JsonResponse
    {
        $this->authorizeSubmissionAsTeacher($submission);

        $request->validate([
            'status' => 'required|in:taken,submitted,correcting,graded',
        ]);

        $submission->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated.']);
    }

    /** 
     * POST /teacher/submissions/{submission}/grade 
     * 
     * Grade a submission.
     * 
     * @param Request $request
     * @param TaskSubmission $submission
     * @return JsonResponse
     */
    public function grade(Request $request, TaskSubmission $submission): JsonResponse
    {
        $this->authorizeSubmissionAsTeacher($submission);

        if (!in_array($submission->status, ['submitted', 'correcting'])) {
            return response()->json(['message' => 'Submission must be submitted or correcting first.'], 422);
        }

        $request->validate([
            'grade'    => 'required|numeric|min:0|max:' . $submission->task->max_grade,
            'feedback' => 'nullable|string',
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

    // Teacher – Subjects

    /** 
     * GET /teacher/subjects 
     * 
     * List all subjects assigned to the teacher.
     * 
     * @return JsonResponse
     */
    public function subjectIndex(): JsonResponse
    {
        $teacher = $this->teacher();

        $subjects = TeacherSubject::with(['subject', 'class'])
            ->where('teacher_id', $teacher->id)
            ->withCount('tasks')
            ->get()
            ->map(fn($ts) => [
                'id'             => $ts->id,
                'subject_name'   => $ts->subject?->name ?? '--',
                'class_name'     => $ts->class?->name   ?? '--',
                'tasks_count'    => $ts->tasks_count,
                'students_count' => Student::where('class_id', $ts->class_id)->count(),
            ]);

        return response()->json($subjects);
    }

    /** 
     * GET /teacher/subjects/create 
     * 
     * Get form data required for assigning a new subject.
     * 
     * @return JsonResponse
     */
    public function subjectCreate(): JsonResponse
    {
        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        $classes  = SchoolClass::orderBy('name')->get(['id', 'name']);

        return response()->json(compact('subjects', 'classes'));
    }

    /** 
     * POST /teacher/subjects 
     * 
     * Assign a subject to the teacher.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function subjectStore(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        $teacher = $this->teacher();

        $exists = TeacherSubject::where('teacher_id',  $teacher->id)
                                 ->where('subject_id', $request->subject_id)
                                 ->where('class_id',   $request->class_id)
                                 ->exists();

        if ($exists) {
            return response()->json(['message' => 'This subject-class combination is already assigned.'], 422);
        }

        $ts = TeacherSubject::create([
            'teacher_id'  => $teacher->id,
            'subject_id'  => $request->subject_id,
            'class_id'    => $request->class_id,
        ]);

        return response()->json($ts->load(['subject', 'class']), 201);
    }

    /** 
     * DELETE /teacher/subjects/{id} 
     * 
     * Remove a subject assignment from the teacher.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function subjectDestroy(int $id): JsonResponse
    {
        $teacher = $this->teacher();

        $ts = TeacherSubject::where('id', $id)
                             ->where('teacher_id', $teacher->id)
                             ->firstOrFail();
        $ts->delete();

        return response()->json(['message' => 'Subject removed.']);
    }

    // Teacher – Points & Report

    /** 
     * GET /teacher/points 
     * 
     * List all points awarded by the teacher.
     * 
     * @return JsonResponse
     */
    public function teacherPoints(): JsonResponse
    {
        $tsIds = $this->teacherSubjectIds();

        $points = Point::with(['student.user', 'teacherSubject.subject'])
            ->whereIn('teacher_subject_id', $tsIds)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn($p) => [
                'student'     => $p->student->user?->name              ?? '--',
                'subject'     => $p->teacherSubject->subject?->name     ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'date'        => $p->point_date,
            ]);

        return response()->json($points);
    }

    /** 
     * GET /teacher/points/stats 
     * 
     * Get aggregate statistics for points awarded by the teacher.
     * 
     * @return JsonResponse
     */
    public function teacherPointStats(): JsonResponse
    {
        $tsIds    = $this->teacherSubjectIds();
        $total    = Point::whereIn('teacher_subject_id', $tsIds)->sum('point');
        $awarded  = Point::whereIn('teacher_subject_id', $tsIds)->where('point', '>', 0)->sum('point');
        $deducted = Point::whereIn('teacher_subject_id', $tsIds)->where('point', '<', 0)->sum('point');

        return response()->json(compact('total', 'awarded', 'deducted'));
    }

    /** 
     * GET /teacher/report?period=week|month|all&ts_id= 
     * 
     * Generate a report for the teacher based on a specific period and subject.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function report(Request $request): JsonResponse
    {
        $teacher = $this->teacher();

        $from = match ($request->period ?? 'month') {
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        $tsQuery = TeacherSubject::with(['subject', 'class'])->where('teacher_id', $teacher->id);
        if ($request->filled('ts_id')) $tsQuery->where('id', $request->ts_id);
        $teacherSubjects = $tsQuery->get();
        $filteredTsIds   = $teacherSubjects->pluck('id');

        $taskQuery = Task::whereIn('teacher_subject_id', $filteredTsIds);
        if ($from)  $taskQuery->where('created_at', '>=', $from);
        $taskIds   = $taskQuery->pluck('id');

        $subQ    = TaskSubmission::whereIn('task_id', $taskIds);
        $summary = [
            'tasks'       => $taskIds->count(),
            'submissions' => (clone $subQ)->count(),
            'graded'      => (clone $subQ)->where('status', 'graded')->count(),
            'avg_grade'   => (clone $subQ)->where('status', 'graded')->avg('grade'),
        ];

        $breakdown = $teacherSubjects->map(function ($ts) use ($from) {
            $ids = Task::where('teacher_subject_id', $ts->id)
                ->when($from, fn($q) => $q->where('created_at', '>=', $from))
                ->pluck('id');
            $sq = TaskSubmission::whereIn('task_id', $ids);
            return [
                'class_name'      => $ts->class?->name   ?? '--',
                'subject_name'    => $ts->subject?->name ?? '--',
                'total_students'  => Student::where('class_id', $ts->class_id)->count(),
                'submitted_count' => (clone $sq)->whereIn('status', ['submitted', 'correcting', 'graded'])->count(),
                'graded_count'    => (clone $sq)->where('status', 'graded')->count(),
                'avg_grade'       => (clone $sq)->where('status', 'graded')->avg('grade'),
            ];
        });

        $points = Point::with(['student.user', 'teacherSubject.subject'])
            ->whereIn('teacher_subject_id', $filteredTsIds)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn($p) => [
                'student'     => $p->student->user?->name              ?? '--',
                'subject'     => $p->teacherSubject->subject?->name     ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'date'        => $p->point_date,
            ]);

        return response()->json(compact('summary', 'breakdown', 'points'));
    }

    // Student – Dashboard JSON GET /student/dashboard

    /**
     * Get JSON data for the student dashboard.
     * 
     * @return JsonResponse
     */
    public function studentDashboard(): JsonResponse
    {
        $student = $this->student();

        $pending   = TaskSubmission::where('student_id', $student->id)->where('status', 'taken')->count();
        $submitted = TaskSubmission::where('student_id', $student->id)->where('status', 'submitted')->count();
        $graded    = TaskSubmission::where('student_id', $student->id)->where('status', 'graded')->count();
        $totalPts  = Point::where('student_id', $student->id)->sum('point');

        $upcoming = Task::with(['teacherSubject.subject'])
            ->whereHas('teacherSubject', fn($q) => $q->where('class_id', $student->class_id))
            ->where(fn($q) => $q->whereNull('due_date')->orWhere('due_date', '>=', today()))
            ->orderBy('due_date')
            ->limit(6)
            ->get()
            ->map(function ($t) use ($student) {
                $sub = TaskSubmission::where('task_id', $t->id)
                                     ->where('student_id', $student->id)->first();
                return [
                    'id'        => $t->id,
                    'title'     => $t->title,
                    'subject'   => $t->teacherSubject->subject?->name ?? '--',
                    'due_date'  => $t->due_date,
                    'my_status' => $sub ? $sub->status : 'available',
                ];
            });

        $recentPoints = Point::with('teacherSubject.subject')
            ->where('student_id', $student->id)
            ->orderByDesc('point_date')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'subject'     => $p->teacherSubject->subject?->name ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'date'        => $p->point_date,
            ]);

        return response()->json([
            'pending'       => $pending,
            'submitted'     => $submitted,
            'graded'        => $graded,
            'total_points'  => $totalPts,
            'upcoming'      => $upcoming,
            'recent_points' => $recentPoints,
        ]);
    }

    // Student – Tasks

    /** 
     * GET /student/tasks 
     * 
     * List all tasks assigned to the student's class.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function studentTasks(Request $request): JsonResponse
    {
        $student = $this->student();

        $classIds = \App\Models\ClassEnrollment::where('student_id', Auth::id())->pluck('class_id');

        $tasks = Task::with(['teacherSubject.subject', 'teacherSubject.teacher.user'])
            ->whereHas('teacherSubject', fn($q) => $q->whereIn('class_id', $classIds))
            ->latest()
            ->get()
            ->map(function ($t) use ($student) {
                $sub = TaskSubmission::where('task_id', $t->id)
                                     ->where('student_id', $student->id)->first();
                return [
                    'id'          => $t->id,
                    'title'       => $t->title,
                    'description' => $t->description,
                    'subject'     => $t->teacherSubject->subject?->name       ?? '--',
                    'teacher'     => $t->teacherSubject->teacher->user?->name ?? '--',
                    'due_date'    => $t->due_date,
                    'max_grade'   => $t->max_grade,
                    'my_status'   => $sub ? $sub->status : 'available',
                    'my_grade'    => $sub ? $sub->grade  : null,
                ];
            });

        if ($request->filled('status')) {
            $filter = $request->status;
            $tasks  = $tasks->filter(fn($t) =>
                $filter === 'available'
                    ? $t['my_status'] === 'available'
                    : $t['my_status'] === $filter
            )->values();
        }

        return response()->json($tasks);
    }

    /** 
     * GET /student/my-submissions 
     * 
     * List all submissions made by the student.
     * 
     * @return JsonResponse
     */
    public function mySubmissions(): JsonResponse
    {
        $student = $this->student();

        $submissions = TaskSubmission::where('student_id', $student->id)
            ->with(['task.teacherSubject.subject'])
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'task_title'   => $s->task->title,
                'subject'      => $s->task->teacherSubject->subject?->name ?? '--',
                'status'       => $s->status,
                'grade'        => $s->grade,
                'max_grade'    => $s->task->max_grade,
                'feedback'     => $s->feedback,
                'submitted_at' => $s->submitted_at,
                'due_date'     => $s->task->due_date,
            ]);

        return response()->json($submissions);
    }

    /** 
     * POST /student/tasks/{task}/accept 
     * 
     * Accept a task (mark as 'taken').
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function studentAccept(Task $task): JsonResponse
    {
        $student = $this->student();

        if ($task->due_date && $task->due_date < now()->toDateString()) {
            return response()->json(['message' => 'This task is past its due date.'], 422);
        }

        $belongs = TeacherSubject::where('id', $task->teacher_subject_id)
                                  ->where('class_id', $student->class_id)
                                  ->exists();
        if (!$belongs) {
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

    /** 
     * POST /student/tasks/{task}/submit 
     * 
     * Submit a task.
     * 
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function studentSubmit(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'note'    => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240',
        ]);

        $student    = $this->student();
        $submission = TaskSubmission::firstOrCreate(
            ['task_id' => $task->id, 'student_id' => $student->id],
            ['status' => 'taken']
        );

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $this->compressAndStoreFile($file, 'submissions/' . $submission->id);
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

    // Student – Points

    /** 
     * GET /student/points 
     * 
     * List all points awarded to the student.
     * 
     * @return JsonResponse
     */
    public function studentPoints(): JsonResponse
    {
        $student = $this->student();

        $points = Point::with('teacherSubject.subject')
            ->where('student_id', $student->id)
            ->orderByDesc('point_date')
            ->get()
            ->map(fn($p) => [
                'subject'     => $p->teacherSubject->subject?->name ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
                'date'        => $p->point_date,
            ]);

        return response()->json($points);
    }

    /** 
     * GET /student/points/stats 
     * 
     * Get aggregate statistics for points awarded to the student.
     * 
     * @return JsonResponse
     */
    public function studentPointStats(): JsonResponse
    {
        $student  = $this->student();
        $net      = Point::where('student_id', $student->id)->sum('point');
        $earned   = Point::where('student_id', $student->id)->where('point', '>', 0)->sum('point');
        $deducted = Point::where('student_id', $student->id)->where('point', '<', 0)->sum('point');

        return response()->json(compact('net', 'earned', 'deducted'));
    }

    /** 
     * GET /student/report 
     * 
     * Generate a report for the student.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function studentReport(Request $request): JsonResponse
    {
        $student = $this->student();

        $from = $request->period === 'all' ? null : now()->startOfMonth();

        $tsIds = TeacherSubject::where('class_id', $student->class_id)->pluck('id');

        $taskIds = Task::whereIn('teacher_subject_id', $tsIds)
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->pluck('id');

        $subQ      = TaskSubmission::where('student_id', $student->id)->whereIn('task_id', $taskIds);
        $summary   = [
            'tasks'     => $taskIds->count(),
            'submitted' => (clone $subQ)->whereIn('status', ['submitted', 'correcting', 'graded'])->count(),
            'points'    => Point::where('student_id', $student->id)->sum('point'),
            'avg_grade' => (clone $subQ)->where('status', 'graded')->avg('grade'),
        ];

        $breakdown = TeacherSubject::with(['subject', 'teacher.user'])
            ->whereIn('id', $tsIds)
            ->get()
            ->map(function ($ts) use ($student, $from) {
                $ids  = Task::where('teacher_subject_id', $ts->id)
                    ->when($from, fn($q) => $q->where('created_at', '>=', $from))
                    ->pluck('id');
                $sq   = TaskSubmission::where('student_id', $student->id)->whereIn('task_id', $ids);
                return [
                    'subject_name' => $ts->subject?->name       ?? '--',
                    'teacher_name' => $ts->teacher->user?->name ?? '--',
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
            ->map(fn($s) => [
                'task_title' => $s->task->title,
                'subject'    => $s->task->teacherSubject->subject?->name ?? '--',
                'grade'      => $s->grade,
                'max_grade'  => $s->task->max_grade,
                'feedback'   => $s->feedback,
                'graded_at'  => $s->graded_at,
            ]);

        return response()->json(compact('summary', 'breakdown', 'graded'));
    }

    // Admin – Users

    /** GET /users?search=&role= */
    public function usersIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json($query->latest()->get());
    }

    /** POST /users */
    public function usersStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,teacher,student,parent',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Create role profile
        match ($request->role) {
            'teacher' => Teacher::create(['user_id' => $user->id]),
            'student' => \App\Models\Student::create(['user_id' => $user->id]),
            default   => null,
        };

        return response()->json($user, 201);
    }

    /** PUT /users/{user} */
    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'sometimes|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'role'     => 'sometimes|in:admin,teacher,student,parent',
        ]);

        $user->update($request->only('name', 'username', 'email', 'role'));

        return response()->json($user);
    }

    /** DELETE /users/{user} */
    public function usersDestroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }

    // Admin – Subjects

    /** GET /subjects */
    public function adminSubjectsIndex()
    {
        return response()->json(Subject::orderBy('name')->get());
    }

    /** POST /subjects */
    public function adminSubjectsStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:subjects,name']);
        $subject = Subject::create(['name' => $request->name]);
        return response()->json($subject, 201);
    }

    /** PUT /subjects/{subject} */
    public function adminSubjectsUpdate(Request $request, Subject $subject)
    {
        $request->validate(['name' => 'required|string|max:100|unique:subjects,name,' . $subject->id]);
        $subject->update(['name' => $request->name]);
        return response()->json($subject);
    }

    /** DELETE /subjects/{subject} */
    public function adminSubjectsDestroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted.']);
    }

    // Admin – Classes

    /** GET /admin/classes */
    public function classesIndex()
    {
        $classes = Classes::withCount('students')->orderBy('name')->get();
        return response()->json($classes);
    }

    /** POST /admin/classes */
    public function classesStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:classes,name',
            'description' => 'nullable|string|max:255',
        ]);

        $class = Classes::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($class, 201);
    }

    /** PUT /admin/classes/{class} */
    public function classesUpdate(Request $request, Classes $class)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:classes,name,' . $class->id,
            'description' => 'nullable|string|max:255',
        ]);

        $class->update([
            'name'        => $request->name,
            'description' => $request->description ?? $class->description,
        ]);

        return response()->json($class);
    }

    /** DELETE /admin/classes/{class} */
    public function classesDestroy(Classes $class)
    {
        // Prevent deletion if students are enrolled
        if ($class->students()->count() > 0) {
            return response()->json(['message' => 'Cannot delete class with enrolled students.'], 422);
        }

        $class->delete();
        return response()->json(['message' => 'Class deleted.']);
    }

    // Admin – Teacher-Subject Assignments

    /** GET /admin/teacher-subjects */
    public function adminTeacherSubjectsIndex()
    {
        $rows = TeacherSubject::with(['teacher.user', 'subject', 'class'])
            ->get()
            ->map(fn($ts) => [
                'id'         => $ts->id,
                'teacher_id' => $ts->teacher_id,
                'subject_id' => $ts->subject_id,
                'class_id'   => $ts->class_id,
                'teacher'    => $ts->teacher->user?->name ?? '--',
                'subject'    => $ts->subject?->name       ?? '--',
                'class'      => $ts->class?->name         ?? '--',
            ]);

        return response()->json($rows);
    }

    /** GET /admin/teacher-subjects/form-data */
    public function adminTeacherSubjectsFormData()
    {
        $teachers = User::where('role', 'teacher')
            ->get()
            ->map(fn($u) => ['id' => $u->teacher?->id, 'name' => $u->name])
            ->filter(fn($t) => $t['id']);

        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        $classes  = SchoolClass::orderBy('name')->get(['id', 'name']);

        return response()->json(compact('teachers', 'subjects', 'classes'));
    }

    /** POST /admin/teacher-subjects */
    public function adminTeacherSubjectsStore(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer|exists:users,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        $teacher = Teacher::where('user_id', $request->teacher_id)->firstOrFail();

        $exists = TeacherSubject::where('teacher_id', $teacher->id)
                                 ->where('subject_id', $request->subject_id)
                                 ->where('class_id',   $request->class_id)
                                 ->exists();

        if ($exists) {
            return response()->json(['message' => 'This assignment already exists.'], 422);
        }

        $ts = TeacherSubject::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class_id'   => $request->class_id,
        ]);

        return response()->json($ts->load(['teacher.user', 'subject', 'class']), 201);
    }

    /** PUT /admin/teacher-subjects/{ts} */
    public function adminTeacherSubjectsUpdate(Request $request, TeacherSubject $ts)
    {
        $request->validate([
            'teacher_id' => 'required|integer|exists:users,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id'   => 'required|integer|exists:classes,id',
        ]);

        $teacher = Teacher::where('user_id', $request->teacher_id)->firstOrFail();

        $ts->update([
            'teacher_id' => $teacher->id,
            'subject_id' => $request->subject_id,
            'class_id'   => $request->class_id,
        ]);

        return response()->json($ts->load(['teacher.user', 'subject', 'class']));
    }

    /** DELETE /admin/teacher-subjects/{ts} */
    public function adminTeacherSubjectsDestroy(TeacherSubject $ts)
    {
        $ts->delete();
        return response()->json(['message' => 'Assignment removed.']);
    }

    // Admin – Tasks

    /** GET /admin/tasks */
    public function adminTasksIndex()
    {
        $tasks = Task::with(['teacherSubject.teacher.user', 'teacherSubject.subject'])
            ->withCount('submissions')
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    /** POST /admin/tasks */
    public function adminTasksStore(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:150',
            'description'        => 'nullable|string',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'due_date'           => 'nullable|date',
            'max_grade'          => 'nullable|integer|min:1',
        ]);

        $task = Task::create($request->only('title', 'description', 'teacher_subject_id', 'due_date', 'max_grade'));

        return response()->json($task->load(['teacherSubject.subject', 'teacherSubject.class']), 201);
    }

    /** PUT /admin/tasks/{task} */
    public function adminTasksUpdate(Request $request, Task $task)
    {
        $request->validate([
            'title'              => 'sometimes|string|max:150',
            'description'        => 'nullable|string',
            'teacher_subject_id' => 'sometimes|exists:teacher_subjects,id',
            'due_date'           => 'nullable|date',
            'max_grade'          => 'nullable|integer|min:1',
        ]);

        $task->update($request->only('title', 'description', 'teacher_subject_id', 'due_date', 'max_grade'));

        return response()->json($task->load(['teacherSubject.subject', 'teacherSubject.class']));
    }

    /** DELETE /admin/tasks/{task} */
    public function adminTasksDestroy(Task $task)
    {
        foreach ($task->images      as $img) Storage::delete($img->path);
        foreach ($task->attachments as $att) Storage::delete($att->path);
        $task->delete();

        return response()->json(['message' => 'Task deleted.']);
    }

    /** GET /admin/tasks/{task}/submissions */
    public function adminTaskSubmissions(Task $task)
    {
        $submissions = TaskSubmission::where('task_id', $task->id)
            ->with(['student.user', 'student.class'])
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'student_id'   => $s->student_id,
                'student_name' => $s->student->user?->name  ?? '--',
                'class'        => $s->student->class?->name ?? '--',
                'status'       => $s->status,
                'grade'        => $s->grade,
                'feedback'     => $s->feedback,
                'submitted_at' => $s->submitted_at,
            ]);

        return response()->json($submissions);
    }

    // Admin – Points

    /** POST /admin/points */
    public function adminPointsStore(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,id',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'point'              => 'required|numeric',
            'description'        => 'nullable|string',
            'point_date'         => 'nullable|date',
            'task_submission_id' => 'nullable|exists:task_submissions,id',
        ]);

        $point = Point::create([
            'student_id'         => $request->student_id,
            'teacher_subject_id' => $request->teacher_subject_id,
            'task_submission_id' => $request->task_submission_id,
            'point'              => $request->point,
            'description'        => $request->description,
            'point_date'         => $request->point_date ?? today()->toDateString(),
            'type'               => $request->point >= 0 ? 'positive' : 'negative',
        ]);

        return response()->json($point, 201);
    }

    /** GET /admin/students-for-points */
    public function adminStudentsForPoints()
    {
        $students = \App\Models\Student::with(['user', 'class'])
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'name'  => $s->user?->name  ?? '--',
                'class' => $s->class?->name ?? '--',
            ]);

        return response()->json($students);
    }

    // Admin – Reports

    /** GET /reports?search=&type= */
    public function adminReports(Request $request)
    {
        $query = Point::with(['student.user', 'teacherSubject.subject', 'teacherSubject.teacher.user']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student.user', fn($q) => $q->where('name', 'like', "%$s%"))
                  ->orWhereHas('teacherSubject.teacher.user', fn($q) => $q->where('name', 'like', "%$s%"));
        }
        if ($request->type === 'positive') $query->where('point', '>', 0);
        if ($request->type === 'negative') $query->where('point', '<', 0);

        $records = $query->orderByDesc('point_date')->get()->map(fn($p) => [
            'student'     => $p->student->user?->name                    ?? '--',
            'teacher'     => $p->teacherSubject->teacher->user?->name     ?? '--',
            'subject'     => $p->teacherSubject->subject?->name           ?? '--',
            'description' => $p->description,
            'points'      => $p->point,
            'type'        => $p->type ?? ($p->point >= 0 ? 'positive' : 'negative'),
            'date'        => $p->point_date,
        ]);

        return response()->json($records);
    }

    /** GET /reports/stats */
    public function adminReportStats()
    {
        $total        = Point::sum('point');
        $awarded      = Point::where('point', '>', 0)->sum('point');
        $deducted     = Point::where('point', '<', 0)->sum('point');
        $transactions = Point::count();

        return response()->json(compact('total', 'awarded', 'deducted', 'transactions'));
    }
}
