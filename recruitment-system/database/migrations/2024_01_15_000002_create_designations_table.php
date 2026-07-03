<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 20)->unique();
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->enum('level', ['entry', 'mid', 'senior', 'lead', 'manager', 'director', 'vp', 'c_level'])->default('mid');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'department_id']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
