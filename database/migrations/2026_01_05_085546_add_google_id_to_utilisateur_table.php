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
            $table->string('google_id')->nullable()->unique();
            // CORRECTION : La colonne s'appelle 'password', pas 'mot_de_passe_chiffre'
            $table->string('password')->nullable()->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
            // On ne peut pas facilement annuler le changement nullable sans risque, donc on laisse comme ça
        });
    }
};