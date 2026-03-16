<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Parents;
use App\Models\Major;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Point;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. MAJORS ────────────────────────────────────────
        $majors = collect([
            ['name' => 'Computer Science'],
            ['name' => 'Mathematics'],
        ])->map(fn($m) => Major::create($m));

        // ─── 2. CLASSES ───────────────────────────────────────
        $classes = collect([
            ['name' => 'X-CS-1',  'major_id' => $majors[0]->id],
            ['name' => 'X-CS-2',  'major_id' => $majors[0]->id],
            ['name' => 'X-MTK-1', 'major_id' => $majors[1]->id],
        ])->map(fn($c) => Classes::create($c));

        // ─── 3. SUBJECTS ──────────────────────────────────────
        $subjects = collect([
            ['name' => 'Mathematics'],
            ['name' => 'English'],
            ['name' => 'Programming'],
            ['name' => 'Physics'],
        ])->map(fn($s) => Subject::create($s));

        // ─── 4. ADMIN ─────────────────────────────────────────
        User::create([
            'name'     => 'Admin User',
            'username' => 'admin',
            'email'    => 'admin@school.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ─── 5. TEACHERS ──────────────────────────────────────
        $teacherUsers = collect([
            ['name' => 'John Doe',   'username' => 'johndoe',   'email' => 'john@school.edu'],
            ['name' => 'Jane Smith', 'username' => 'janesmith', 'email' => 'jane@school.edu'],
            ['name' => 'Mr. Hasan',  'username' => 'mrhasan',   'email' => 'hasan@school.edu'],
        ])->map(function ($t) {
            $user = User::create([
                'name'     => $t['name'],
                'username' => $t['username'],
                'email'    => $t['email'],
                'password' => Hash::make('password'),
                'role'     => 'teacher',
            ]);
            return Teacher::create(['user_id' => $user->id]);
        });

        // ─── 6. TEACHER SUBJECTS ──────────────────────────────
        $teacherSubjects = collect([
            ['teacher_id' => $teacherUsers[0]->id, 'subject_id' => $subjects[0]->id, 'class_id' => $classes[0]->id], // John → Math → X-CS-1
            ['teacher_id' => $teacherUsers[0]->id, 'subject_id' => $subjects[2]->id, 'class_id' => $classes[1]->id], // John → Programming → X-CS-2
            ['teacher_id' => $teacherUsers[1]->id, 'subject_id' => $subjects[1]->id, 'class_id' => $classes[0]->id], // Jane → English → X-CS-1
            ['teacher_id' => $teacherUsers[2]->id, 'subject_id' => $subjects[3]->id, 'class_id' => $classes[2]->id], // Hasan → Physics → X-MTK-1
        ])->map(fn($ts) => TeacherSubject::create($ts));

        // ─── 7. STUDENTS ──────────────────────────────────────
        $students = collect([
            ['name' => 'Ahmad Riski',  'username' => 'ahmad',  'email' => 'ahmad@school.edu',  'class' => 0],
            ['name' => 'Siti Rahma',   'username' => 'siti',   'email' => 'siti@school.edu',   'class' => 0],
            ['name' => 'Budi Santoso', 'username' => 'budi',   'email' => 'budi@school.edu',   'class' => 1],
            ['name' => 'Dewi Putri',   'username' => 'dewi',   'email' => 'dewi@school.edu',   'class' => 1],
            ['name' => 'Rizki Fajar',  'username' => 'rizki',  'email' => 'rizki@school.edu',  'class' => 2],
        ])->map(function ($s) use ($classes) {
            $user = User::create([
                'name'     => $s['name'],
                'username' => $s['username'],
                'email'    => $s['email'],
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]);
            return Student::create([
                'user_id'  => $user->id,
                'class_id' => $classes[$s['class']]->id,
            ]);
        });

        // ─── 8. PARENTS ───────────────────────────────────────
        collect([
            ['name' => 'Parent Ahmad', 'username' => 'parentahmad', 'email' => 'parentahmad@school.edu', 'child' => 0],
            ['name' => 'Parent Siti',  'username' => 'parentsiti',  'email' => 'parentsiti@school.edu',  'child' => 1],
        ])->each(function ($p) use ($students) {
            $user = User::create([
                'name'     => $p['name'],
                'username' => $p['username'],
                'email'    => $p['email'],
                'password' => Hash::make('password'),
                'role'     => 'parent',
            ]);
            $parent = Parents::create(['user_id' => $user->id]);
            $parent->students()->attach($students[$p['child']]->id);
        });

        // ─── 9. TASKS ─────────────────────────────────────────
        $tasks = collect([
            [
                'teacher_subject_id' => $teacherSubjects[0]->id,
                'title'              => 'Algebra Homework',
                'description'        => 'Solve quadratic equations from chapter 3.',
                'due_date'           => now()->addDays(7)->toDateString(),
                'max_grade'          => 100,
            ],
            [
                'teacher_subject_id' => $teacherSubjects[1]->id,
                'title'              => 'Build a Calculator',
                'description'        => 'Create a simple calculator using any language.',
                'due_date'           => now()->addDays(14)->toDateString(),
                'max_grade'          => 100,
            ],
            [
                'teacher_subject_id' => $teacherSubjects[2]->id,
                'title'              => 'Essay Writing',
                'description'        => 'Write a 500-word essay about your future goals.',
                'due_date'           => now()->addDays(5)->toDateString(),
                'max_grade'          => 100,
            ],
        ])->map(fn($t) => Task::create($t));

        // ─── 10. TASK SUBMISSIONS ─────────────────────────────
        // Ahmad → Algebra → graded
        TaskSubmission::create([
            'task_id'      => $tasks[0]->id,
            'student_id'   => $students[0]->id,
            'status'       => 'graded',
            'grade'        => 85,
            'feedback'     => 'Good work! Keep it up.',
            'submitted_at' => now()->subDays(2),
        ]);

        // Siti → Algebra → submitted
        TaskSubmission::create([
            'task_id'      => $tasks[0]->id,
            'student_id'   => $students[1]->id,
            'status'       => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        // Budi → Calculator → correcting
        TaskSubmission::create([
            'task_id'      => $tasks[1]->id,
            'student_id'   => $students[2]->id,
            'status'       => 'correcting',
            'submitted_at' => now()->subDays(3),
        ]);

        // Dewi → Essay → taken
        TaskSubmission::create([
            'task_id'    => $tasks[2]->id,
            'student_id' => $students[3]->id,
            'status'     => 'taken',
        ]);

        // ─── 11. POINTS ───────────────────────────────────────
        $pointsData = [
            [
                'student_id'         => $students[0]->id,
                'teacher_subject_id' => $teacherSubjects[0]->id,
                'point'              => 85,
                'description'        => 'Grade 85/100 — Algebra Homework',
                'point_date'         => now()->subDays(2)->toDateString(),
            ],
            [
                'student_id'         => $students[1]->id,
                'teacher_subject_id' => $teacherSubjects[2]->id,
                'point'              => 30,
                'description'        => 'Perfect attendance this week',
                'point_date'         => now()->subDays(3)->toDateString(),
            ],
            [
                'student_id'         => $students[2]->id,
                'teacher_subject_id' => $teacherSubjects[3]->id,
                'point'              => 40,
                'description'        => 'Led class project successfully',
                'point_date'         => now()->subDays(4)->toDateString(),
            ],
            [
                'student_id'         => $students[3]->id,
                'teacher_subject_id' => $teacherSubjects[2]->id,
                'point'              => 50,
                'description'        => 'Disciplinary violation',
                'point_date'         => now()->subDays(1)->toDateString(),
            ],
            [
                'student_id'         => $students[4]->id,
                'teacher_subject_id' => $teacherSubjects[3]->id,
                'point'              => 75,
                'description'        => 'Won school basketball tournament',
                'point_date'         => now()->toDateString(),
            ],
        ];

        foreach ($pointsData as $p) {
            Point::create($p);
        }
    }
}