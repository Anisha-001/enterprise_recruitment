<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_interviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('interviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_required')->default(true);
            $table->enum('response_status', ['pending', 'accepted', 'declined', 'tentative'])->default('pending');
            $table->text('decline_reason')->nullable();
            $table->timestamps();

            $table->unique(['interview_id', 'user_id']);
            $table->index(['user_id', 'response_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_interviewers');
    }
};
