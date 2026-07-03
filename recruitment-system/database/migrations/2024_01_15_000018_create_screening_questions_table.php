<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['text', 'textarea', 'yes_no', 'number', 'date', 'single_choice', 'multiple_choice']);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['job_posting_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_questions');
    }
};
