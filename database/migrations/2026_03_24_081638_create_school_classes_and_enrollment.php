<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //  school_classes 
        if (! Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();

                $table->string('name');

                // Short join code students enter (e.g. MTH-2024).
                // unique + uppercase enforced at app level.
                $table->string('code', 20)->unique();

                $table->foreignId('subject_id')
                      ->constrained('subjects')
                      ->cascadeOnDelete();

                $table->foreignId('teacher_id')
                      ->constrained('users')
                      ->cascadeOnDelete();

                $table->text('description')->nullable();

                $table->timestamps();
            });
        }

        //  class_enrollments 
        if (! Schema::hasTable('class_enrollments')) {
            Schema::create('class_enrollments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('class_id')
                      ->constrained('school_classes')
                      ->cascadeOnDelete();

                $table->foreignId('student_id')
                      ->constrained('users')
                      ->cascadeOnDelete();

                // A student can only be enrolled once per class
                $table->unique(['class_id', 'student_id']);

                $table->timestamps();   
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
        Schema::dropIfExists('school_classes');
    }
};