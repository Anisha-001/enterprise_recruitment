<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->json('criteria')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'is_active']);
            $table->index('slug');
        });

        Schema::create('talent_pool_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_pool_id')->constrained('talent_pools')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->constrained('users');
            $table->timestamps();

            $table->unique(['talent_pool_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_pool_candidates');
        Schema::dropIfExists('talent_pools');
    }
};
