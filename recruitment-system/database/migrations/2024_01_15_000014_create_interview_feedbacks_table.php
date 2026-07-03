<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('interviews')->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('users');
            $table->foreignId('application_id')->constrained('applications');

            $table->integer('technical_skills_rating')->nullable()->comment('1-10');
            $table->integer('communication_rating')->nullable()->comment('1-10');
            $table->integer('problem_solving_rating')->nullable()->comment('1-10');
            $table->integer('cultural_fit_rating')->nullable()->comment('1-10');
            $table->integer('experience_rating')->nullable()->comment('1-10');
            $table->integer('overall_rating')->comment('1-10');

            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('notes')->nullable();
            $table->text('questions_asked')->nullable();
            $table->text('candidate_responses')->nullable();

            $table->enum('recommendation', ['strong_hire', 'hire', 'consider', 'reject', 'strong_reject']);
            $table->text('recommendation_reason')->nullable();

            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();

            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['interview_id', 'interviewer_id']);
            $table->index(['application_id', 'overall_rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_feedbacks');
    }
};
