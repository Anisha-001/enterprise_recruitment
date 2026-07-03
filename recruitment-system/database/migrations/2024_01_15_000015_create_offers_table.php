<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_number', 50)->unique()->comment('OFF-2026-000001');
            $table->foreignId('application_id')->constrained('applications');
            $table->foreignId('candidate_id')->constrained('candidates');
            $table->foreignId('job_posting_id')->constrained('job_postings');

            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'negotiating', 'withdrawn'])->default('draft');
            $table->integer('version')->default(1);
            $table->foreignId('previous_version_id')->nullable()->constrained('offers')->nullOnDelete();

            $table->string('proposed_designation', 150);
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('designation_id')->constrained('designations');
            $table->foreignId('reporting_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('location_id')->constrained('locations');

            $table->decimal('basic_salary', 15, 2);
            $table->decimal('housing_allowance', 15, 2)->default(0);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('medical_allowance', 15, 2)->default(0);
            $table->decimal('other_allowances', 15, 2)->default(0);
            $table->decimal('bonus_percentage', 5, 2)->nullable();
            $table->decimal('total_ctc', 15, 2);
            $table->string('salary_currency', 3)->default('USD');
            $table->enum('salary_period', ['monthly', 'yearly'])->default('yearly');

            $table->date('joining_date');
            $table->date('offer_expiry_date');
            $table->date('proposed_joining_date');

            $table->text('special_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->string('pdf_path', 500)->nullable();
            $table->string('digital_signature', 500)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_ip', 45)->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'offer_expiry_date']);
            $table->index(['application_id', 'version']);
            $table->index(['candidate_id']);
            $table->index(['created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
