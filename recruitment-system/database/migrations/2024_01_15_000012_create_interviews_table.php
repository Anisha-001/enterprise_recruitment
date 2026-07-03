<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates');
            $table->foreignId('job_posting_id')->constrained('job_postings');

            $table->enum('round_type', [
                'hr_screening',
                'technical',
                'manager',
                'cultural',
                'final',
                'panel'
            ]);
            $table->integer('round_number')->default(1);

            $table->enum('mode', ['in_person', 'video_call', 'phone']);
            $table->enum('video_platform', ['zoom', 'google_meet', 'microsoft_teams', 'other'])->nullable();
            $table->string('meeting_link', 500)->nullable();
            $table->string('meeting_id', 200)->nullable();
            $table->string('meeting_password', 100)->nullable();

            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(60);

            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('interview_address', 500)->nullable();
            $table->string('room_number', 50)->nullable();

            $table->text('instructions')->nullable();
            $table->text('description')->nullable();

            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled'])->default('scheduled');

            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('follow_up_sent_at')->nullable();

            $table->foreignId('scheduled_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['application_id', 'round_number']);
            $table->index(['scheduled_date', 'status']);
            $table->index(['candidate_id', 'status']);
            $table->index(['interviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
