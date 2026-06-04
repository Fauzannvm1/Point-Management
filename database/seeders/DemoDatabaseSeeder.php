<?php

namespace Database\Seeders;

use App\Models\ClassEnrollment;
use App\Models\Classes;
use App\Models\Major;
use App\Models\Parents;
use App\Models\Point;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Full demo seed: admin, teachers, students, classes, tasks, submissions, and points.
     * Password for every account: password
     */
    public function run(): void
    {
        // ─── 1. MAJORS (legacy homeroom structure) ────────────
        $majors = collect([
            ['name' => 'Computer Science'],
            ['name' => 'Mathematics'],
        ])->map(fn ($m) => Major::create($m));

        // ─── 2. LEGACY CLASSES ────────────────────────────────
        $classes = collect([
            ['name' => 'X-CS-1',  'major_id' => $majors[0]->id],
            ['name' => 'X-CS-2',  'major_id' => $majors[0]->id],
            ['name' => 'X-MTK-1', 'major_id' => $majors[1]->id],
        ])->map(fn ($c) => Classes::create($c));

        // ─── 3. SUBJECTS ──────────────────────────────────────
        $subjects = collect([
            ['name' => 'Mathematics'],
            ['name' => 'English'],
            ['name' => 'Programming'],
            ['name' => 'Physics'],
        ])->map(fn ($s) => Subject::create($s));

        // ─── 4. ADMIN ─────────────────────────────────────────
        User::create([
            'name'     => 'Admin User',
            'username' => 'admin',
            'email'    => 'admin@school.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ─── 5. TEACHERS ──────────────────────────────────────
        $teacherProfiles = collect([
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

            return [
                'user'    => $user,
                'teacher' => Teacher::create(['user_id' => $user->id]),
            ];
        });

        // ─── 6. SCHOOL CLASSES (teacher-owned, join-code classes) ─
        $schoolClasses = collect([
            [
                'name'        => 'Math X-CS-1',
                'code'        => 'MATH-CS1',
                'subject_id'  => $subjects[0]->id,
                'teacher_id'  => $teacherProfiles[0]['user']->id,
                'description' => 'Algebra and calculus for CS class 1.',
            ],
            [
                'name'        => 'Programming X-CS-2',
                'code'        => 'PROG-CS2',
                'subject_id'  => $subjects[2]->id,
                'teacher_id'  => $teacherProfiles[0]['user']->id,
                'description' => 'Introductory programming projects.',
            ],
            [
                'name'        => 'English X-CS-1',
                'code'        => 'ENG-CS1',
                'subject_id'  => $subjects[1]->id,
                'teacher_id'  => $teacherProfiles[1]['user']->id,
                'description' => 'Reading and essay writing.',
            ],
            [
                'name'        => 'Physics X-MTK-1',
                'code'        => 'PHY-MTK1',
                'subject_id'  => $subjects[3]->id,
                'teacher_id'  => $teacherProfiles[2]['user']->id,
                'description' => 'Mechanics and lab work.',
            ],
        ])->map(fn ($c) => SchoolClass::create($c));

        // ─── 7. TEACHER SUBJECTS ──────────────────────────────
        $teacherSubjects = collect([
            ['teacher_id' => $teacherProfiles[0]['teacher']->id, 'subject_id' => $subjects[0]->id, 'class_id' => $schoolClasses[0]->id],
            ['teacher_id' => $teacherProfiles[0]['teacher']->id, 'subject_id' => $subjects[2]->id, 'class_id' => $schoolClasses[1]->id],
            ['teacher_id' => $teacherProfiles[1]['teacher']->id, 'subject_id' => $subjects[1]->id, 'class_id' => $schoolClasses[2]->id],
            ['teacher_id' => $teacherProfiles[2]['teacher']->id, 'subject_id' => $subjects[3]->id, 'class_id' => $schoolClasses[3]->id],
        ])->map(fn ($ts) => TeacherSubject::create($ts));

        // ─── 8. STUDENTS ──────────────────────────────────────
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

            return [
                'user'    => $user,
                'student' => Student::create([
                    'user_id'  => $user->id,
                    'class_id' => $classes[$s['class']]->id,
                ]),
            ];
        });

        // ─── 9. CLASS ENROLLMENTS ─────────────────────────────
        $enrollments = [
            [$students[0]['user']->id, [$schoolClasses[0]->id, $schoolClasses[2]->id]], // Ahmad → Math, English
            [$students[1]['user']->id, [$schoolClasses[0]->id, $schoolClasses[2]->id]], // Siti → Math, English
            [$students[2]['user']->id, [$schoolClasses[1]->id]],                         // Budi → Programming
            [$students[3]['user']->id, [$schoolClasses[1]->id]],                         // Dewi → Programming
            [$students[4]['user']->id, [$schoolClasses[3]->id]],                         // Rizki → Physics
        ];

        foreach ($enrollments as [$userId, $classIds]) {
            foreach ($classIds as $classId) {
                ClassEnrollment::create([
                    'class_id' => $classId,
                    'user_id'  => $userId,
                ]);
            }
        }

        // ─── 10. PARENTS ──────────────────────────────────────
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
            $parent->students()->attach($students[$p['child']]['student']->id);
        });

        // ─── 11. TASKS ────────────────────────────────────────
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
        ])->map(fn ($t) => Task::create($t));

        // ─── 12. TASK SUBMISSIONS ─────────────────────────────
        $gradedSubmission = TaskSubmission::create([
            'task_id'      => $tasks[0]->id,
            'student_id'   => $students[0]['student']->id,
            'status'       => 'graded',
            'grade'        => 85,
            'feedback'     => 'Good work! Keep it up.',
            'submitted_at' => now()->subDays(2),
            'graded_at'    => now()->subDays(1),
        ]);

        TaskSubmission::create([
            'task_id'      => $tasks[0]->id,
            'student_id'   => $students[1]['student']->id,
            'status'       => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        TaskSubmission::create([
            'task_id'      => $tasks[1]->id,
            'student_id'   => $students[2]['student']->id,
            'status'       => 'correcting',
            'submitted_at' => now()->subDays(3),
        ]);

        TaskSubmission::create([
            'task_id'    => $tasks[2]->id,
            'student_id' => $students[3]['student']->id,
            'status'     => 'taken',
        ]);

        // ─── 13. POINTS ───────────────────────────────────────
        $pointsData = [
            [
                'student_id'         => $students[0]['student']->id,
                'teacher_subject_id' => $teacherSubjects[0]->id,
                'task_submission_id' => $gradedSubmission->id,
                'point'              => 85,
                'description'        => 'Grade 85/100 — Algebra Homework',
                'point_date'         => now()->subDays(2)->toDateString(),
                'type'               => 'positive',
            ],
            [
                'student_id'         => $students[1]['student']->id,
                'teacher_subject_id' => $teacherSubjects[2]->id,
                'point'              => 30,
                'description'        => 'Perfect attendance this week',
                'point_date'         => now()->subDays(3)->toDateString(),
                'type'               => 'positive',
            ],
            [
                'student_id'         => $students[2]['student']->id,
                'teacher_subject_id' => $teacherSubjects[1]->id,
                'point'              => 40,
                'description'        => 'Led class project successfully',
                'point_date'         => now()->subDays(4)->toDateString(),
                'type'               => 'positive',
            ],
            [
                'student_id'         => $students[3]['student']->id,
                'teacher_subject_id' => $teacherSubjects[2]->id,
                'point'              => -50,
                'description'        => 'Disciplinary violation',
                'point_date'         => now()->subDays(1)->toDateString(),
                'type'               => 'negative',
            ],
            [
                'student_id'         => $students[4]['student']->id,
                'teacher_subject_id' => $teacherSubjects[3]->id,
                'point'              => 75,
                'description'        => 'Won school basketball tournament',
                'point_date'         => now()->toDateString(),
                'type'               => 'positive',
            ],
        ];

        foreach ($pointsData as $p) {
            Point::create($p);
        }
    }
}
