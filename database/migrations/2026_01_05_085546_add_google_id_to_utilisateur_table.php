<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique();
            // On rend le mot de passe nullable car via Google on n'en a pas
            $table->string('mot_de_passe_chiffre')->nullable()->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->dropColumn('google_id');
            // Attention : remettre le password en 'nullable' false peut bloquer s'il y a des comptes Google
        });
    }
};