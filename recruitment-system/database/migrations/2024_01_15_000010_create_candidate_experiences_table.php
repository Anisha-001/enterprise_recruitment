<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('company_name', 200);
            $table->string('designation', 150);
            $table->string('department', 150)->nullable();
            $table->string('location', 200)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->text('responsibilities')->nullable();
            $table->text('achievements')->nullable();
            $table->string('leave_reason', 500)->nullable();
            $table->string('reference_name', 150)->nullable();
            $table->string('reference_contact', 150)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'is_current']);
            $table->index(['company_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_experiences');
    }
};
