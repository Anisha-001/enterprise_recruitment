<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('slug', 150)->unique();
            $table->enum('type', ['job_board', 'social_media', 'referral', 'agency', 'campus', 'direct', 'event', 'other']);
            $table->string('website', 255)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->decimal('cost_per_hire', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_sources');
    }
};
