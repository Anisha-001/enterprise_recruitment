<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->nullableMorphs('causer');
            $table->string('type', 50)->comment('status_change, note, interview, email, call, assessment, document, offer, system');
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['application_id', 'type']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_activities');
    }
};
