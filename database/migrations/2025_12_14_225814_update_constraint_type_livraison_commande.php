<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. On supprime la vieille contrainte qui bloque
        DB::statement("ALTER TABLE commande DROP CONSTRAINT IF EXISTS ck_commande_type_livraison");

        // 2. On recrée la contrainte en ajoutant 'Relais' à la liste autorisée
        DB::statement("ALTER TABLE commande ADD CONSTRAINT ck_commande_type_livraison CHECK (type_livraison IN ('Standard', 'Express', 'Relais'))");
    }

    public function down()
    {
        // Retour en arrière (si besoin)
        DB::statement("ALTER TABLE commande DROP CONSTRAINT IF EXISTS ck_commande_type_livraison");
        DB::statement("ALTER TABLE commande ADD CONSTRAINT ck_commande_type_livraison CHECK (type_livraison IN ('Standard', 'Express'))");
    }
};