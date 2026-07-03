<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 50)->unique()->comment('APP-2026-000001');
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('recruiter_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', [
                'new',
                'screening',
                'shortlisted',
                'technical_interview',
                'manager_interview',
                'final_interview',
                'offer_pending',
                'offer_sent',
                'offer_accepted',
                'offer_rejected',
                'hired',
                'rejected',
                'withdrawn',
                'on_hold'
            ])->default('new');

            $table->enum('rejection_reason', [
                'underqualified',
                'overqualified',
                'poor_interview',
                'salary_mismatch',
                'position_filled',
                'candidate_withdrew',
                'failed_background_check',
                'location_mismatch',
                'no_response',
                'other'
            ])->nullable();

            $table->text('rejection_notes')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();

            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->decimal('offered_salary', 15, 2)->nullable();
            $table->date('expected_joining_date')->nullable();
            $table->date('offered_joining_date')->nullable();
            $table->date('actual_joining_date')->nullable();

            $table->integer('stage_progress')->default(0)->comment('0-100%');
            $table->integer('rating')->nullable()->comment('1-5 star rating');

            $table->text('screening_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('recruiter_notes')->nullable();

            $table->boolean('is_new')->default(true);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('status_changed_at')->nullable();
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('notifications_enabled')->default(true);
            $table->timestamp('last_notification_sent_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'job_posting_id']);
            $table->index(['candidate_id', 'job_posting_id']);
            $table->index(['recruiter_id', 'status']);
            $table->index(['is_new', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['application_number']);
            $table->unique(['candidate_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
