<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function prosesLogin(Request $request)
    {
        if(Auth::attempt($request->only('email', 'password'))){
            return redirect()->route('dashboard');
        }
        return redirect()->route('login')->with('error', 'Email atau Password Salah');

        if (Auth::attempt($credentials)) {

            $role = Auth::user()->role;

            if ($role == 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($role == 'teacher') {
                return redirect('/teacher/dashboard');
            } elseif ($role == 'student') {
                return redirect('/student/dashboard');
            } elseif ($role == 'parent') {
                return redirect('/parent/dashboard');
            }

        }
    }
    public function showRegister()
    {
        return view('auth.register');
    }
    public function dashboard()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return view('dashboard.admin');
        } elseif ($role == 'teacher') {
            return view('dashboard.teacher');
        } elseif ($role == 'student') {
            return view('dashboard.student');
        } elseif ($role == 'parent') {
            return view('dashboard.parent');
        }

        abort(403);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:225',
            'username' => 'required|string|max:15',
            'email' => 'required|string|email|max:225|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,teacher,student,parent',
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);
        return redirect('/dashboard')->with('Success', 'Akun berhasil dibuat dan Anda telah masuk.');
    }
}
