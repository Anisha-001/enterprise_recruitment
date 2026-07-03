<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('content');
            $table->enum('type', ['general', 'screening', 'interview', 'offer', 'concern', 'highlight'])->default('general');
            $table->boolean('is_private')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['application_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_notes');
    }
};
