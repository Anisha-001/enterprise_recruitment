<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->enum('proficiency', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->boolean('is_required')->default(true);
            $table->integer('years_experience')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['job_posting_id', 'skill_id']);
            $table->index(['job_posting_id', 'is_required']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_skills');
    }
};
