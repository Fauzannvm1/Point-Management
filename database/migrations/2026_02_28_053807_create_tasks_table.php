<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('teacher_subject_id')
              ->constrained('teacher_subjects')
              ->cascadeOnDelete();
        $table->string('title', 150);
        $table->text('description')->nullable();
        $table->date('due_date')->nullable();
        $table->integer('max_grade')->default(100);
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};