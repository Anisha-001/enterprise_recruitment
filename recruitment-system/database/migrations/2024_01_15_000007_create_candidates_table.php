<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_number', 50)->unique()->comment('CAND-2026-000001');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('email', 255);
            $table->string('phone', 20);
            $table->string('alternate_phone', 20)->nullable();
            $table->string('photograph', 500)->nullable();

            $table->enum('gender', ['male', 'female', 'non_binary', 'prefer_not_to_say'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'separated'])->nullable();

            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->string('current_company', 150)->nullable();
            $table->string('current_designation', 150)->nullable();
            $table->decimal('current_salary', 15, 2)->nullable();
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->enum('notice_period', ['immediate', '15_days', '30_days', '60_days', '90_days', 'more_than_90'])->nullable();
            $table->decimal('total_experience_years', 4, 1)->default(0);

            $table->string('highest_qualification', 200)->nullable();
            $table->string('university', 200)->nullable();
            $table->integer('passing_year')->nullable();

            $table->string('linkedin_url', 500)->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('portfolio_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('behance_url', 500)->nullable();
            $table->string('dribbble_url', 500)->nullable();

            $table->string('resume_path', 500)->nullable();
            $table->string('cover_letter_path', 500)->nullable();
            $table->string('resume_original_name', 255)->nullable();

            $table->enum('source', ['careers_page', 'linkedin', 'indeed', 'referral', 'agency', 'job_fair', 'campus', 'social_media', 'other'])->default('careers_page');
            $table->string('referral_code', 50)->nullable();
            $table->string('referral_employee_id', 50)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->string('device', 100)->nullable();

            $table->foreignId('converted_to_employee_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            $table->text('notes')->nullable();
            $table->enum('blacklist_status', ['none', 'blacklisted', 'whitelisted'])->default('none');
            $table->text('blacklist_reason')->nullable();

            $table->boolean('is_duplicate')->default(false);
            $table->foreignId('original_candidate_id')->nullable()->constrained('candidates')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'phone']);
            $table->index(['blacklist_status']);
            $table->index(['source']);
            $table->index(['total_experience_years']);
            $table->index(['current_company']);
            // $table->fullText(['first_name', 'last_name', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
