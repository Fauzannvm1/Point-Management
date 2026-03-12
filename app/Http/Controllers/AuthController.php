<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Point;
use App\Models\PointCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function showRegister()
    {
        return view('auth.register');
    }
    public function dashboard()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin'     => $this->adminDashboard(),
            'teacher'   => $this->teacherDashboard(),
            'student'   => $this->studentDashboard(),
            'parent'    => $this->parentDashboard(),
            default     => abort(403),
        };
    }
    private function adminDashboard() {
        return view('dashboard.admin', [
            'totalStudents'   => User::where('role', 'student')->count(),
            'totalTeachers'   => User::where('role', 'teacher')->count(),
            'totalPoints'     => Point::sum('point'),
            'totalCategories' => PointCategory::count(),
            'recentUsers'     => User::latest()->take(5)->get(),
            'newStudentsMonth'=> User::where('role', 'student')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
            'newTeachersMonth'=> User::where('role', 'teacher')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
            'recentActivity'  => Point::with(['student', 'teacher', 'category'])
                                    ->latest()
                                    ->take(6)
                                    ->get()
                                    ->map(function ($r) {
                                    return [
                                        'student'  => $r->student->name  ?? 'Unknown',
                                        'teacher'  => $r->teacher->name  ?? 'Unknown',
                                        'category' => $r->category->name ?? 'Unknown',
                                        'points'   => $r->point,
                                        'type'     => $r->point >= 0 ? 'positive' : 'negative',
                                        'date'     => $r->created_at->format('Y-m-d'),
                                    ];
                                })
        ]);
    }
    private function teacherDashboard() {
        $teacher = Auth::user();
        return view('dashboard.teacher', [
            'students'      => User::where('role', 'student')->get(),
            'pointsGiven'   => Point::where('teacher_subject_id', $teacher->id)->sum('point'),
            'recentHistory' => Point::where('teacher_subject_id', $teacher->id)
                                    ->with(['student', 'category'])
                                    ->latest()->take(10)->get(),
        ]);
    }
    private function studentDashboard() {
    $student = Auth::user();
    return view('dashboard.student', [
        'totalPoints'  => $student->totalPoints(),
        'history'      => $student->receivedPoints()
                                ->with(['teacher', 'category'])
                                ->latest()->take(10)->get(),
        'monthPoints'  => $student->receivedPoints()
                                ->whereMonth('created_at', now()->month)
                                ->sum('point'), // changed value -> point
        ]);
    }

    private function parentDashboard() {
        $parent = Auth::user();
        $child  = $parent->children()->first();

        return view('dashboard.parent', [
            'child'        => $child,
            'totalPoints'  => $child?->totalPoints(),
            'monthPoints'  => $child?->receivedPoints()
                                    ->whereMonth('created_at', now()->month)
                                    ->sum('point'), // changed value -> point
            'history'      => $child?->receivedPoints()
                                    ->with(['teacher', 'category'])
                                    ->latest()->take(10)->get(),
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:225',
            'username'  => 'required|string|max:15',
            'email'     => 'required|string|email|max:225|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:admin,teacher,student,parent',
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
        ]);

        Auth::login($user);
        return redirect('/dashboard')->with('Success', 'Akun berhasil dibuat dan Anda telah masuk.');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    // Add these to app/Http/Controllers/AuthController.php

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
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
