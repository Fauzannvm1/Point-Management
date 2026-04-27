<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            $table->enum('status', ['taken', 'submitted', 'correcting', 'graded'])
                ->default('taken');
            $table->string('photo_path')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('submitted_at')->nullable()->useCurrent();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['task_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};