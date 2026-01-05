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
        Schema::table('utilisateur', function (Blueprint $table) {
            // Booléen pour savoir si l'utilisateur a activé l'option
            $table->boolean('double_auth_active')->default(false);
            
            // Stockage du code SMS (hashé ou clair selon ton choix, ici clair pour simplifier le débug temporaire)
            $table->string('code_auth_temporaire', 10)->nullable();
            
            // Date d'expiration du code pour la sécurité (ex: valide 10 min)
            $table->timestamp('code_auth_expiration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->dropColumn(['double_auth_active', 'code_auth_temporaire', 'code_auth_expiration']);
        });
    }
};