<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('emailable');
            $table->string('recipient_email', 255);
            $table->string('recipient_name', 200)->nullable();
            $table->string('template', 100);
            $table->string('subject', 500);
            $table->text('body')->nullable();
            $table->enum('status', ['queued', 'sent', 'delivered', 'failed', 'bounced', 'opened', 'clicked'])->default('queued');
            $table->text('error_message')->nullable();
            $table->string('message_id', 255)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['emailable_type', 'emailable_id', 'template']);
            $table->index(['status', 'created_at']);
            $table->index(['recipient_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
