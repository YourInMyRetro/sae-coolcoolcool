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
            // CORRECTION : 'email' au lieu de 'mail'
            $table->string('telephone', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telephone');
        });
    }
};