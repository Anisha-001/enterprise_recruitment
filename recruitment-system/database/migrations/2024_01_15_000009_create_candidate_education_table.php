<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->enum('degree_type', [
                'high_school',
                'diploma',
                'associate',
                'bachelor',
                'master',
                'doctorate',
                'post_doctorate',
                'professional_certification',
                'other'
            ]);
            $table->string('degree_name', 200);
            $table->string('field_of_study', 200);
            $table->string('institution', 200);
            $table->string('university', 200)->nullable();
            $table->string('location', 200)->nullable();
            $table->year('start_year');
            $table->year('end_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->decimal('grade_cgpa', 4, 2)->nullable();
            $table->enum('grade_scale', ['4.0', '5.0', '10.0', 'percentage', 'other'])->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'degree_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_education');
    }
};
