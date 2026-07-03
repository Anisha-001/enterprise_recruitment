<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('name', 200);
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');
            $table->string('disk', 50)->default('local');
            $table->string('collection', 50)->default('default');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id', 'collection']);
            $table->index(['uploaded_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
