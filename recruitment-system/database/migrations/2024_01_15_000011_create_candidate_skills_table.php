<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->string('skill_name', 100);
            $table->enum('proficiency', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->integer('years_experience')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['candidate_id', 'skill_name']);
            $table->index(['candidate_id', 'proficiency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_skills');
    }
};
