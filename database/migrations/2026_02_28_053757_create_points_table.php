<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->integer('point');
            $table->text('description')->nullable();
            $table->date('point_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};