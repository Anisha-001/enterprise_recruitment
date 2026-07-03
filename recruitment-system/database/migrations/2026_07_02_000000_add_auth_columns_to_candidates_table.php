<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->timestamp('password_set_at')->nullable()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('password_set_at');
            $table->rememberToken()->after('email_verified_at');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['password', 'password_set_at', 'email_verified_at', 'remember_token']);
        });
    }
};
