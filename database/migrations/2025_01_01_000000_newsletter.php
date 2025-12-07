<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration "newsletter" : Simple et efficace
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajout de la colonne opt-in newsletter (booléen)
            // On la place après le mot de passe pour la propreté
            if (!Schema::hasColumn('users', 'newsletter_optin')) {
                $table->boolean('newsletter_optin')->default(false)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('newsletter_optin');
        });
    }
};