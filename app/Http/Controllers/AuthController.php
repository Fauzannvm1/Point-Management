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
            'admin'     => view('dashboard.admin'),
            'teacher'   => view('dashboard.teacher'),
            'student'   => view('dashboard.student'),
            'parent'    => view('dashboard.parent'),
            default     => abort(403),
        };
    }
    private function adminDashboard() {
        return view('dashboard.admin', [
            'totalStudents'   => User::where('role', 'student')->count(),
            'totalTeachers'   => User::where('role', 'teacher')->count(),
            'totalPoints'     => Point::sum('value'),
            'totalCategories' => PointCategory::count(),
            'recentUsers'     => User::latest()->take(5)->get(),
        ]);
    }
    private function teacherDashboard() {
        $teacher = Auth::user();
        return view('dashboard.teacher', [
            'students'      => User::where('role', 'student')->get(),
            'pointsGiven'   => Point::where('teacher_id', $teacher->id)->sum('value'),
            'recentHistory' => Point::where('teacher_id', $teacher->id)
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
                                    ->sum('value'),
        ]);
    }
    private function parentDashboard() {
        $parent = Auth::user();
        $child  = $parent->children()->first(); // assumes one child for now

        return view('dashboard.parent', [
            'child'        => $child,
            'totalPoints'  => $child?->totalPoints(),
            'monthPoints'  => $child?->receivedPoints()
                                    ->whereMonth('created_at', now()->month)
                                    ->sum('value'),
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
}
