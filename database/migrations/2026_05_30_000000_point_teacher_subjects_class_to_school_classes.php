<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_subjects') || ! Schema::hasTable('school_classes')) {
            return;
        }

        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
        });

        $validClassIds = DB::table('school_classes')->pluck('id');

        DB::table('teacher_subjects')
            ->whereNotIn('class_id', $validClassIds)
            ->delete();

        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->foreign('class_id')
                ->references('id')
                ->on('school_classes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('teacher_subjects') || ! Schema::hasTable('classes')) {
            return;
        }

        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
        });

        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->cascadeOnDelete();
        });
    }
};
