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
        // CORRECTION : On vérifie si la colonne existe AVANT d'essayer de la créer
        if (!Schema::hasColumn('utilisateur', 'role')) {
            Schema::table('utilisateur', function (Blueprint $table) {
                $table->string('role')->default('client');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('utilisateur', 'role')) {
            Schema::table('utilisateur', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};