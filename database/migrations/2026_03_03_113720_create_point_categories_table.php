<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_categories', function(Blueprint $table){
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['positive', 'negative'])->default('positive');
            $table->integer('default_points')->default(0);
            $table->string('description')->nullable();
            $table->string('color', 30)->default('indigo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_categories');
    }
};
