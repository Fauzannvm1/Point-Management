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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException;

class AuthController extends Controller
{
    public function prosesLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Email atau Password Salah');
    }
    
    public function login()
    {
        return view('auth.login');
    }


    public function showRegister()
    {
        return view('auth.register');
    }
    public function dashboard()
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

    // DASHBOARDS

    private function adminDashboard()
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

    private function teacherDashboard()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            $this->forceLogout();
            return redirect()->back()->with('error', 'Terjadi kesalahan.'); 
            return redirect('dashboard')->withErrors([
                'email' => 'Teacher Profile Not Found'
            ]);
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

    private function studentDashboard()
    {
        $student = Auth::user()->student;

        if (!$student) {
            $this->forceLogout();
            return redirect()->back()->with('error', 'Terjadi kesalahan.'); 
            return redirect('dashboard')->withErrors([
                'email' => 'Student Profile Not Found'
            ]);
        }

        // Stat cards 
        $pending   = TaskSubmission::where('student_id', $student->id)
                        ->where('status', 'taken')->count();
        $submitted = TaskSubmission::where('student_id', $student->id)
                        ->where('status', 'submitted')->count();
        $graded    = TaskSubmission::where('student_id', $student->id)
                        ->where('status', 'graded')->count();
        $totalPts  = Point::where('student_id', $student->id)->sum('point');

        //Upcoming tasks for student's class
        $upcoming = Task::with(['teacherSubject.subject'])
            ->whereHas('teacherSubject', fn($q) =>
                $q->where('class_id', $student->class_id)
            )
            ->where(fn($q) =>
                $q->whereNull('due_date')->orWhere('due_date', '>=', today())
            )
            ->orderBy('due_date')
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
            'totalTasks'     => $pending + $submitted + $graded,
            'pendingTasks'   => $pending,
            'submittedTasks' => $submitted + $graded,
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

    private function parentDashboard()
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
    public function register(Request $request)
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
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    function forceLogout()
    {
        Auth::Logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    // USERS (Admin API)

    public function getUsers(Request $request)
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

    public function storeUser(Request $request)
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

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role'  => 'required|in:admin,teacher,student,parent',
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }

    // SUBJECTS (Admin API)

    public function getSubjects()
    {
        return response()->json(Subject::latest()->get());
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        return response()->json(Subject::create($validated), 201);
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,'.$subject->id,
        ]);

        $subject->update($validated);
        return response()->json($subject);
    }

    public function destroySubject(Subject $subject)
    {
        if ($subject->teacherSubjects()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — subject is assigned to a teacher.'
            ], 422);
        }

        $subject->delete();
        return response()->json(['message' => 'Subject deleted.']);
    }

    // TEACHER SUBJECTS (Admin API)

    public function getTeacherSubjects()
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

    public function getTeacherSubjectFormData()
    {
        return response()->json([
            TeacherSubject::with(['teacher.user', 'subject', 'class'])
            ->get()
            ->map(fn($ts) => [
                'id'         => $ts->id,
                'teacher_id' => $ts->teacher_id,   // ← needed for edit
                'subject_id' => $ts->subject_id,   // ← needed for edit
                'class_id'   => $ts->class_id,     // ← needed for edit
                'teacher'    => $ts->teacher->user->name ?? '—',
                'subject'    => $ts->subject->name       ?? '—',
                'class'      => $ts->class->name         ?? '—',
            ])
        ]);
    }

    public function storeTeacherSubject(Request $request)
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

    public function updateTeacherSubject(Request $request, TeacherSubject $ts)
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

    public function destroyTeacherSubject(TeacherSubject $ts)
    {
        if ($ts->tasks()->count() > 0 || $ts->points()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — this assignment has tasks or points linked to it.'
            ], 422);
        }

        $ts->delete();
        return response()->json(['message' => 'Deleted.']);
    }

    // REPORTS (Admin API)

    public function getReports(Request $request)
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

    public function getReportStats()
    {
        return response()->json([
            'total'        => Point::sum('point'),
            'awarded'      => Point::where('point', '>', 0)->sum('point'),
            'deducted'     => abs(Point::where('point', '<', 0)->sum('point')),
            'transactions' => Point::count(),
        ]);
    }

    // TASKS (Admin API)

    public function getTasks()
    {
        return response()->json(
            Task::with(['teacherSubject.teacher.user', 'teacherSubject.subject'])
                ->withCount('submissions')
                ->latest()
                ->get()
        );
    }

    public function storeTask(Request $request)
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

    public function updateTask(Request $request, Task $task)
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

    public function destroyTask(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted.']);
    }
    // POINTS (Admin — manual award)

    public function getStudentsForPoints()
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

    public function storePoint(Request $request)
    {
        $validated = $request->validate([
            'student_id'         => 'required|exists:students,id',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'point'              => 'required|integer|not_in:0',
            'description'        => 'nullable|string|max:255',
            'point_date'         => 'nullable|date',
            'task_submission_id' => 'nullable|exists:task_submissions,id', // ← add
        ]);

        $point = Point::create([
            'student_id'         => $validated['student_id'],
            'teacher_subject_id' => $validated['teacher_subject_id'],
            'point'              => $validated['point'],
            'description'        => $validated['description'] ?? null,
            'point_date'         => $validated['point_date'] ?? today()->toDateString(),
        ]);

        // ← add: if came from a submission, mark it graded
        if (!empty($validated['task_submission_id'])) {
            TaskSubmission::where('id', $validated['task_submission_id'])
                ->update([
                    'status' => 'graded',
                    'grade'  => abs($validated['point']),
                ]);
        }

        return response()->json($point, 201);
    }
    public function getTaskSubmissions(Task $task)
    {
        return response()->json(
            $task->submissions()
                ->with(['student.user', 'student.class'])
                ->get()
                ->map(fn($s) => [
                    'id'                 => $s->id,
                    'student_id'         => $s->student_id,                // ← add
                    'teacher_subject_id' => $task->teacher_subject_id,     // ← add
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