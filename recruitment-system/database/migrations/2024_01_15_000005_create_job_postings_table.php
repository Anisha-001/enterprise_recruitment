<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->text('summary')->nullable();
            $table->longText('description');
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();

            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('designation_id')->constrained('designations');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('hiring_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recruiter_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary', 'internship', 'freelance']);
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'lead', 'manager', 'director']);
            $table->enum('work_arrangement', ['on_site', 'hybrid', 'remote'])->default('on_site');

            $table->integer('min_experience_years')->default(0);
            $table->integer('max_experience_years')->nullable();
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->enum('salary_period', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->default('yearly');
            $table->boolean('show_salary')->default(false);

            $table->integer('vacancies')->default(1);
            $table->date('published_at')->nullable();
            $table->date('closing_date')->nullable();
            $table->integer('apply_before_days')->nullable()->comment('Auto-close after N days from publish');

            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->enum('status', ['draft', 'published', 'closed', 'archived', 'on_hold'])->default('draft');

            $table->string('requisition_number', 50)->nullable()->unique();
            $table->string('source', 50)->default('internal')->comment('internal, external, referral');
            $table->string('external_job_id', 100)->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'department_id']);
            $table->index(['status', 'published_at']);
            $table->index(['employment_type', 'status']);
            $table->index(['location_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['slug']);
            // $table->fullText(['title', 'description', 'requirements']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
