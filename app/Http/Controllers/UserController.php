<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Parents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /users?search=&role=
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
            );
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json(
            $query->latest()->get(['id', 'name', 'username', 'email', 'role', 'created_at'])
        );
    }

    // POST /users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,teacher,student,parent',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        match ($user->role) {
            'teacher' => Teacher::create(['user_id' => $user->id]),
            'student' => Student::create(['user_id' => $user->id]),
            'parent'  => Parents::create(['user_id' => $user->id]),
            default   => null,
        };

        return response()->json($user, 201);
    }

    // PUT /users/{user}
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role'     => 'sometimes|required|in:admin,teacher,student,parent',
            'password' => 'nullable|string|min:6',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // DELETE /users/{user}
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}