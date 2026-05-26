<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Point;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Parents;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;

/**
 * Handles authentication (login, register, logout) and role-based dashboard
 * rendering for all user types (admin, teacher, student, parent).
 *
 * Also provides admin-facing JSON API endpoints for managing users, subjects,
 * teacher-subject assignments, reports, tasks, and points.
 */
class AuthController extends Controller
{
    //  AUTHENTICATION

    /**
     * Authenticate the user with email and password.
     *
     * On success the session is regenerated and the user is redirected to the
     * dashboard. On failure the user is sent back with an error flash message.
     *
     * @param  Request  $request  Must contain 'email' and 'password'.
     * @return RedirectResponse
     */
    public function prosesLogin(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Email atau Password Salah');
    }

    /**
     * Show the login form.
     *
     * @return View
     */
    public function login(): View
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     *
     * @return View
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Route the authenticated user to the correct role-based dashboard.
     *
     * Uses a PHP 8.1+ match expression on the user's role to delegate to
     * a private dashboard builder. Returns 403 for unknown roles.
     *
     * @return View|RedirectResponse
     */
    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();

        return match($user->role) {
            'admin'   => $this->adminDashboard(),
            'teacher' => $this->teacherDashboard(),
            'student' => $this->studentDashboard(),
            'parent'  => $this->parentDashboard(),
            default   => abort(403),
        };
    }

    //  ROLE-BASED DASHBOARDS (private)

    /**
     * Build the admin dashboard view with aggregate statistics.
     *
     * Queries:
     * - Total students & teachers from the users table.
     * - Aggregate point sums (overall and today).
     * - Task count across the entire system.
     * - Class count from the legacy `classes` table.
     * - New students/teachers registered this calendar month.
     * - The 6 most recent point transactions with student, teacher, and subject
     *   details eagerly loaded to avoid N+1 queries.
     *
     * @return View  The `dashboard.admin` Blade template.
     */
    private function adminDashboard(): View
    {
        return view('dashboard.admin', [
            'totalStudents'    => User::where('role', 'student')->count(),
            'totalTeachers'    => User::where('role', 'teacher')->count(),
            'totalPoints'      => Point::sum('point'),
            'totalTasks'       => Task::count(),
            'totalClasses'     => Classes::count(),
            'newStudentsMonth' => User::where('role', 'student')
                                     ->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count(),
            'newTeachersMonth' => User::where('role', 'teacher')
                                     ->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count(),
            'pointsToday'      => Point::whereDate('point_date', today())
                                       ->where('point', '>', 0)
                                       ->sum('point'),
            'recentActivity'   => Point::with([
                                      'student.user',
                                      'teacherSubject.teacher.user',
                                      'teacherSubject.subject',
                                  ])
                                  ->latest()
                                  ->take(6)
                                  ->get()
                                  ->map(fn($r) => [
                                      'student'  => $r->student->user->name              ?? 'Unknown',
                                      'teacher'  => $r->teacherSubject->teacher->user->name ?? 'Unknown',
                                      'points'   => $r->point,
                                      'type'     => $r->point >= 0 ? 'positive' : 'negative',
                                      'date'     => $r->created_at->format('Y-m-d'),
                                  ]),
        ]);
    }

    /**
     * Build the teacher dashboard view with task and submission statistics.
     *
     * Retrieves the authenticated user's teacher profile, then gathers:
     * - Total tasks created under the teacher's subject assignments.
     * - Count of submissions pending grading (status: submitted or correcting).
     * - Count of fully graded submissions.
     * - Total points awarded by this teacher today.
     * - The 6 most recent submission activities with student and subject details.
     *
     * If the teacher profile is missing (data inconsistency), the user is
     * force-logged out and redirected with an error message.
     *
     * @return View|RedirectResponse
     */
    private function teacherDashboard(): View|RedirectResponse
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            $this->forceLogout();
            return redirect()->route('login')->with('error', 'Teacher profile not found. Please contact an administrator.');
        }

        $tsIds   = TeacherSubject::where('teacher_id', $teacher->id)->pluck('id');

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

        $recentActivity = TaskSubmission::whereHas('task', fn($q) =>
                              $q->whereIn('teacher_subject_id', $tsIds)
                          )
                          ->with(['student.user', 'task.teacherSubject.subject'])
                          ->latest()
                          ->take(6)
                          ->get()
                          ->map(fn($s) => [
                              'student' => $s->student->user->name                 ?? '—',
                              'task'    => $s->task->title                         ?? '—',
                              'subject' => $s->task->teacherSubject->subject->name ?? '—',
                              'status'  => $s->status,
                              'grade'   => $s->grade,
                              'date'    => $s->updated_at->format('Y-m-d'),
                          ]);

        return view('dashboard.teacher', compact(
            'totalTasks',
            'pendingGrading',
            'totalGraded',
            'pointsGiven',
            'recentActivity'
        ));
    }

    /**
     * Build the student dashboard view with task and point summaries.
     *
     * Retrieves the authenticated user's student profile, then computes:
     * - Counts of pending (taken), submitted, and graded submissions.
     * - Total accumulated points.
     * - Up to 6 upcoming tasks (not past due) for the student's class, each
     *   annotated with the student's current submission status.
     * - The 5 most recent point transactions.
     *
     * If the student profile is missing, the user is force-logged out.
     *
     * @return View|RedirectResponse
     */
    private function studentDashboard(): View|RedirectResponse
    {
        $student = Auth::user()->student;

        if (!$student) {
            $this->forceLogout();
            return redirect()->route('login')->with('error', 'Student profile not found. Please contact an administrator.');
        }

        $classIds = \App\Models\ClassEnrollment::where('student_id', $student->id)->pluck('class_id');
        $classNames = \App\Models\SchoolClass::whereIn('id', $classIds)->pluck('name')->implode(', ');

        // Stat cards
        $pending   = TaskSubmission::where('student_id', $student->id)
                        ->where('status', 'taken')->count();
        $submitted = TaskSubmission::where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'correcting', 'graded'])->count();
        $totalPts  = Point::where('student_id', $student->id)->sum('point');

        // Recent tasks for student's classes (including past tasks)
        $upcoming = Task::with(['teacherSubject.subject'])
            ->whereHas('teacherSubject', fn($q) =>
                $q->whereIn('class_id', $classIds)
            )
            ->latest()
            ->limit(6)
            ->get()
            ->map(function ($t) use ($student) {
                $sub = TaskSubmission::where('task_id', $t->id)
                                    ->where('student_id', $student->id)
                                    ->first();
                return [
                    'id'        => $t->id,
                    'title'     => $t->title,
                    'subject'   => $t->teacherSubject->subject->name ?? '--',
                    'due_date'  => $t->due_date,
                    'my_status' => $sub ? $sub->status : 'available',
                ];
            });

        // Recent points
        $recentPoints = Point::with('teacherSubject.subject')
            ->where('student_id', $student->id)
            ->orderByDesc('point_date')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'subject'     => $p->teacherSubject->subject->name ?? '--',
                'description' => $p->description,
                'points'      => $p->point,
                'date'        => $p->point_date,
            ]);

        return view('dashboard.student', [
            'classNames'     => $classNames,
            'totalTasks'     => $pending + $submitted,
            'pendingTasks'   => $pending,
            'submittedTasks' => $submitted,
            'totalPoints'    => $totalPts,
            'recentTasks'    => $upcoming->map(fn($t) => [
                'title'   => $t['title'],
                'subject' => $t['subject'],
                'due'     => $t['due_date']
                            ? \Carbon\Carbon::parse($t['due_date'])->format('d M')
                            : '--',
                'status'  => $t['my_status'],
            ]),
        ]);
    }

    /**
     * Build the parent dashboard view showing child's point history.
     *
     * Retrieves the first child linked to the authenticated parent profile
     * and shows their total/monthly points plus recent point transaction history.
     *
     * @return View
     */
    private function parentDashboard(): View
    {
        $parent = Auth::user()->parent;
        $child  = $parent?->students()->first();

        return view('dashboard.parent', [
            'child'       => $child,
            'totalPoints' => $child?->totalPoints(),
            'monthPoints' => $child?->monthPoints(),
            'history'     => $child?->points()
                                    ->with(['teacherSubject.teacher.user', 'teacherSubject.subject'])
                                    ->latest()
                                    ->take(10)
                                    ->get(),
        ]);
    }

    //  REGISTRATION

    /**
     * Handle new user registration.
     *
     * Validates the incoming request, creates a User record, then automatically
     * provisions the corresponding role-specific profile (Teacher, Student, or
     * Parents model). The new user is logged in immediately and redirected to
     * the dashboard.
     *
     * @param  Request  $request  Must contain name, username, email, password,
     *                            password_confirmation, and role.
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:225',
            'username' => 'required|string|max:15',
            'email'    => 'required|string|email|max:225|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,teacher,student,parent',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        match ($request->role) {
            'teacher' => \App\Models\Teacher::create(['user_id' => $user->id]),
            'student' => \App\Models\Student::create(['user_id' => $user->id]),
            'parent'  => \App\Models\Parents::create(['user_id' => $user->id]),
            default => null,
        };

        Auth::login($user);
        return redirect('/dashboard')->with('success', 'Akun berhasil dibuat.');
    }

    //  LOGOUT

    /**
     * Log the current user out, invalidate the session, and regenerate the
     * CSRF token before redirecting to the login page.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Force-logout the current user without a redirect.
     *
     * Used internally when a data-integrity issue is detected (e.g. a teacher
     * user exists but has no corresponding Teacher profile row).
     *
     * @return void
     */
    private function forceLogout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    //  ADMIN API — USERS

    /**
     * List all users, optionally filtered by search term and/or role.
     *
     * @param  Request  $request  Optional query params: ?search=&role=
     * @return JsonResponse       JSON array of user records.
     */
    public function getUsers(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name',  'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json(
            $query->latest()->get(['id','name','email','role','created_at'])
        );
    }

    /**
     * Create a new user and provision a role-specific profile record.
     *
     * @param  Request  $request  Must contain name, email, role, password.
     * @return JsonResponse       The created User (HTTP 201).
     */
    public function storeUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'role'     => 'required|in:admin,teacher,student,parent',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-create profile record based on role
        if ($user->role === 'teacher') {
            Teacher::create(['user_id' => $user->id]);
        } elseif ($user->role === 'student') {
            Student::create(['user_id' => $user->id]);
        } elseif ($user->role === 'parent') {
            Parents::create(['user_id' => $user->id]);
        }

        return response()->json($user, 201);
    }

    /**
     * Update an existing user's name, email, and role.
     *
     * @param  Request  $request  Must contain name, email, role.
     * @param  User     $user     Route-model-bound user instance.
     * @return JsonResponse       The updated User.
     */
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role'  => 'required|in:admin,teacher,student,parent',
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Delete a user account.
     *
     * Prevents self-deletion to avoid locking the admin out.
     *
     * @param  User  $user  Route-model-bound user instance.
     * @return JsonResponse
     */
    public function destroyUser(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }

    //  ADMIN API — SUBJECTS

    /**
     * List all subjects ordered by most recently created.
     *
     * @return JsonResponse  JSON array of Subject records.
     */
    public function getSubjects(): JsonResponse
    {
        return response()->json(Subject::latest()->get());
    }

    /**
     * Create a new subject.
     *
     * @param  Request  $request  Must contain a unique 'name'.
     * @return JsonResponse       The created Subject (HTTP 201).
     */
    public function storeSubject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        return response()->json(Subject::create($validated), 201);
    }

    /**
     * Update a subject's name.
     *
     * @param  Request  $request  Must contain a unique 'name'.
     * @param  Subject  $subject  Route-model-bound subject instance.
     * @return JsonResponse       The updated Subject.
     */
    public function updateSubject(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,'.$subject->id,
        ]);

        $subject->update($validated);
        return response()->json($subject);
    }

    /**
     * Delete a subject.
     *
     * Rejects deletion if the subject is currently assigned to any teacher
     * (via teacher_subjects table) to preserve referential integrity.
     *
     * @param  Subject  $subject  Route-model-bound subject instance.
     * @return JsonResponse
     */
    public function destroySubject(Subject $subject): JsonResponse
    {
        if ($subject->teacherSubjects()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — subject is assigned to a teacher.'
            ], 422);
        }

        $subject->delete();
        return response()->json(['message' => 'Subject deleted.']);
    }

    //  ADMIN API — TEACHER-SUBJECT ASSIGNMENTS

    /**
     * List all teacher-subject-class assignments with related names.
     *
     * @return JsonResponse  JSON array with teacher, subject, and class names.
     */
    public function getTeacherSubjects(): JsonResponse
    {
        return response()->json(
            TeacherSubject::with(['teacher.user', 'subject', 'class'])
                ->get()
                ->map(fn($ts) => [
                    'id'         => $ts->id,
                    'teacher_id' => $ts->teacher_id,
                    'subject_id' => $ts->subject_id,
                    'class_id'   => $ts->class_id,
                    'teacher'    => $ts->teacher->user->name ?? '—',
                    'subject'    => $ts->subject->name       ?? '—',
                    'class'      => $ts->class->name         ?? '—',
                ])
        );
    }

    /**
     * Retrieve form data needed to create/edit a teacher-subject assignment.
     *
     * Returns all existing assignments plus the raw lists of teachers,
     * subjects, and classes for dropdown population.
     *
     * @return JsonResponse
     */
    public function getTeacherSubjectFormData(): JsonResponse
    {
        return response()->json([
            TeacherSubject::with(['teacher.user', 'subject', 'class'])
            ->get()
            ->map(fn($ts) => [
                'id'         => $ts->id,
                'teacher_id' => $ts->teacher_id,
                'subject_id' => $ts->subject_id,
                'class_id'   => $ts->class_id,
                'teacher'    => $ts->teacher->user->name ?? '—',
                'subject'    => $ts->subject->name       ?? '—',
                'class'      => $ts->class->name         ?? '—',
            ])
        ]);
    }

    /**
     * Create a new teacher-subject-class assignment.
     *
     * Prevents duplicate assignments (same teacher + subject + class).
     *
     * @param  Request  $request  Must contain teacher_id, subject_id, class_id.
     * @return JsonResponse       The created TeacherSubject (HTTP 201) or 422 if duplicate.
     */
    public function storeTeacherSubject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id'   => 'required|exists:classes,id',
        ]);

        if (TeacherSubject::where($validated)->exists()) {
            return response()->json(['message' => 'This assignment already exists.'], 422);
        }

        return response()->json(TeacherSubject::create($validated), 201);
    }

    /**
     * Update an existing teacher-subject-class assignment.
     *
     * @param  Request         $request  Must contain teacher_id, subject_id, class_id.
     * @param  TeacherSubject  $ts       Route-model-bound assignment instance.
     * @return JsonResponse
     */
    public function updateTeacherSubject(Request $request, TeacherSubject $ts): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id'   => 'required|exists:classes,id',
        ]);

        if (TeacherSubject::where($validated)->where('id', '!=', $ts->id)->exists()) {
            return response()->json(['message' => 'This assignment already exists.'], 422);
        }

        $ts->update($validated);
        return response()->json($ts);
    }

    /**
     * Delete a teacher-subject assignment.
     *
     * Rejects deletion if the assignment has tasks or points linked to it
     * to prevent orphaned data.
     *
     * @param  TeacherSubject  $ts  Route-model-bound assignment instance.
     * @return JsonResponse
     */
    public function destroyTeacherSubject(TeacherSubject $ts): JsonResponse
    {
        if ($ts->tasks()->count() > 0 || $ts->points()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — this assignment has tasks or points linked to it.'
            ], 422);
        }

        $ts->delete();
        return response()->json(['message' => 'Deleted.']);
    }

    //  ADMIN API — REPORTS

    /**
     * List all point transactions with optional search and type filter.
     *
     * Supports searching by student or teacher name and filtering by point
     * type (positive / negative). Each record includes the student name,
     * awarding teacher, subject, and point details.
     *
     * @param  Request  $request  Optional query params: ?search=&type=positive|negative
     * @return JsonResponse
     */
    public function getReports(Request $request): JsonResponse
    {
        $query = Point::with([
            'student.user',
            'teacherSubject.teacher.user',
            'teacherSubject.subject',
        ]);

        if ($request->filled('search')) {
            $query->whereHas('student.user', fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
            )->orWhereHas('teacherSubject.teacher.user', fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
            );
        }

        if ($request->filled('type')) {
            $request->type === 'positive'
                ? $query->where('point', '>', 0)
                : $query->where('point', '<', 0);
        }

        return response()->json(
            $query->latest()->get()->map(fn($r) => [
                'student'     => $r->student->user->name                    ?? '—',
                'teacher'     => $r->teacherSubject->teacher->user->name    ?? '—',
                'subject'     => $r->teacherSubject->subject->name          ?? '—',
                'description' => $r->description,
                'points'      => $r->point,
                'type'        => $r->point >= 0 ? 'positive' : 'negative',
                'date'        => $r->point_date,
            ])
        );
    }

    /**
     * Return aggregate report statistics for all points in the system.
     *
     * @return JsonResponse  Contains total, awarded, deducted, and transaction count.
     */
    public function getReportStats(): JsonResponse
    {
        return response()->json([
            'total'        => Point::sum('point'),
            'awarded'      => Point::where('point', '>', 0)->sum('point'),
            'deducted'     => abs(Point::where('point', '<', 0)->sum('point')),
            'transactions' => Point::count(),
        ]);
    }

    //  ADMIN API — TASKS

    /**
     * List all tasks system-wide with teacher, subject, and submission count.
     *
     * @return JsonResponse
     */
    public function getTasks(): JsonResponse
    {
        return response()->json(
            Task::with(['teacherSubject.teacher.user', 'teacherSubject.subject'])
                ->withCount('submissions')
                ->latest()
                ->get()
        );
    }

    /**
     * Create a new task (admin override — no teacher ownership check).
     *
     * @param  Request  $request  Must contain title, teacher_subject_id.
     * @return JsonResponse       The created Task (HTTP 201).
     */
    public function storeTask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:150',
            'description'        => 'nullable|string',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'due_date'           => 'nullable|date',
            'max_grade'          => 'integer|min:1|max:100',
        ]);

        return response()->json(Task::create($validated), 201);
    }

    /**
     * Update a task (admin override — no teacher ownership check).
     *
     * @param  Request  $request  May contain title, description, due_date, max_grade.
     * @param  Task     $task     Route-model-bound task instance.
     * @return JsonResponse       The updated Task.
     */
    public function updateTask(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:150',
            'description'       => 'nullable|string',
            'due_date'          => 'nullable|date',
            'max_grade'         => 'integer|min:1|max:100',
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    /**
     * Delete a task (admin override).
     *
     * @param  Task  $task  Route-model-bound task instance.
     * @return JsonResponse
     */
    public function destroyTask(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted.']);
    }

    //  ADMIN API — POINTS (manual award)

    /**
     * List all students with their names and class for the point-award form.
     *
     * @return JsonResponse  JSON array of student id, name, and class name.
     */
    public function getStudentsForPoints(): JsonResponse
    {
        return response()->json(
            Student::with(['user', 'class'])
                ->get()
                ->map(fn($s) => [
                    'id'    => $s->id,
                    'name'  => $s->user->name  ?? '—',
                    'class' => $s->class->name ?? '—',
                ])
        );
    }

    /**
     * Manually award or deduct points for a student.
     *
     * If a `task_submission_id` is provided, the corresponding submission is
     * also marked as graded with the absolute point value as the grade.
     *
     * @param  Request  $request  Must contain student_id, teacher_subject_id, point.
     * @return JsonResponse       The created Point (HTTP 201).
     */
    public function storePoint(Request $request): JsonResponse
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
            'point'              => $validated['point'],
            'description'        => $validated['description'] ?? null,
            'point_date'         => $validated['point_date'] ?? today()->toDateString(),
        ]);

        // If the point came from grading a submission, mark the submission as graded
        if (!empty($validated['task_submission_id'])) {
            TaskSubmission::where('id', $validated['task_submission_id'])
                ->update([
                    'status' => 'graded',
                    'grade'  => abs($validated['point']),
                ]);
        }

        return response()->json($point, 201);
    }

    /**
     * List all submissions for a specific task (admin view).
     *
     * Each submission includes student details, status, grade, and the task's
     * teacher_subject_id for downstream point-award integration.
     *
     * @param  Task  $task  Route-model-bound task instance.
     * @return JsonResponse
     */
    public function getTaskSubmissions(Task $task): JsonResponse
    {
        return response()->json(
            $task->submissions()
                ->with(['student.user', 'student.class'])
                ->get()
                ->map(fn($s) => [
                    'id'                 => $s->id,
                    'student_id'         => $s->student_id,
                    'teacher_subject_id' => $task->teacher_subject_id,
                    'student_name'       => $s->student->user->name  ?? '—',
                    'class'              => $s->student->class->name ?? '—',
                    'status'             => $s->status,
                    'grade'              => $s->grade,
                    'max_grade'          => $task->max_grade,
                    'feedback'           => $s->feedback,
                    'submitted_at'       => $s->submitted_at,
                ])
        );
    }
}