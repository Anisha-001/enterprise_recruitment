<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('country', 100);
            $table->string('country_code', 5)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('type', ['headquarters', 'branch', 'remote', 'co_working'])->default('branch');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country', 'is_active']);
            $table->index(['city', 'is_active']);
            $table->unique(['name', 'city', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
