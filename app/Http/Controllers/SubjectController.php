<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // GET /subjects
    public function index()
    {
        return response()->json(Subject::orderBy('name')->get(['id', 'name', 'created_at']));
    }

    // POST /subjects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        return response()->json(Subject::create($validated), 201);
    }

    // PUT /subjects/{subject}
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
        ]);

        $subject->update($validated);

        return response()->json($subject);
    }

    // DELETE /subjects/{subject}
    public function destroy(Subject $subject)
    {
        if ($subject->teacherSubjects()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete — subject is assigned to a teacher.',
            ], 422);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted.']);
    }
}