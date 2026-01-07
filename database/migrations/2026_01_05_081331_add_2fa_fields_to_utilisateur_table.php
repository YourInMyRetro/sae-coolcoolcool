<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CORRECTION : Table 'users'
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('double_auth_active')->default(false);
            $table->string('code_auth_temporaire', 10)->nullable();
            $table->timestamp('code_auth_expiration')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['double_auth_active', 'code_auth_temporaire', 'code_auth_expiration']);
        });
    }
};