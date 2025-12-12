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
        // CORRECTION 1 : On cible la table 'utilisateur'
        Schema::table('utilisateur', function (Blueprint $table) {
            // CORRECTION 2 : On ajoute la colonne telephone
            // ->nullable() est important car tes utilisateurs existants n'ont pas de numéro
            $table->string('telephone', 20)->nullable()->after('mail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            // Permet de supprimer la colonne si on annule la migration
            $table->dropColumn('telephone');
        });
    }
};