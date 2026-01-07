<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MegaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $this->command->info('>>> Lancement du MEGA SEEDER (Mode Rigueur)...');

        // ---------------------------------------------------------
        // 1. CONFIGURATION DES DONNÉES DE BASE (Nations, Cats, Couleurs, Tailles)
        // ---------------------------------------------------------

        // A. Nations
        $nationsList = ['France', 'Brésil', 'Argentine', 'Allemagne', 'Espagne', 'Italie', 'Angleterre', 'Portugal', 'Pays-Bas', 'Belgique', 'Japon', 'Maroc', 'Sénégal', 'Croatie'];
        $nationIds = [];
        foreach ($nationsList as $nom) {
            // On utilise updateOrInsert ou firstOrCreate pour éviter les doublons
            $exist = DB::table('nation')->where('nom_nation', $nom)->first();
            if (!$exist) {
                $nationIds[] = DB::table('nation')->insertGetId(['nom_nation' => $nom], 'id_nation');
            } else {
                $nationIds[] = $exist->id_nation;
            }
        }

        // B. Catégories
        $catsList = ['Maillots', 'Shorts', 'Accessoires', 'Ballons', 'Crampons', 'Vestes', 'Gardien'];
        $catIds = [];
        foreach ($catsList as $nom) {
            $exist = DB::table('categorie')->where('nom_categorie', $nom)->first();
            if (!$exist) {
                $catIds[$nom] = DB::table('categorie')->insertGetId(['nom_categorie' => $nom], 'id_categorie');
            } else {
                $catIds[$nom] = $exist->id_categorie;
            }
        }

        // C. Couleurs
        $couleursList = ['Rouge', 'Bleu', 'Vert', 'Noir', 'Blanc', 'Jaune', 'Orange', 'Violet', 'Or', 'Argent', 'Rose', 'Bordeaux'];
        $couleurIds = [];
        foreach ($couleursList as $nom) {
            $exist = DB::table('couleur')->where('type_couleur', $nom)->first();
            if (!$exist) {
                $couleurIds[] = DB::table('couleur')->insertGetId(['type_couleur' => $nom], 'id_couleur');
            } else {
                $couleurIds[] = $exist->id_couleur;
            }
        }

        // D. Tailles (Vêtements + Chaussures)
        $taillesVetement = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $taillesChaussure = ['38', '39', '40', '41', '42', '43', '44', '45'];
        
        $tailleIdsVetement = [];
        foreach ($taillesVetement as $t) {
            $exist = DB::table('taille')->where('type_taille', $t)->first();
            $tailleIdsVetement[] = $exist ? $exist->id_taille : DB::table('taille')->insertGetId(['type_taille' => $t], 'id_taille');
        }

        $tailleIdsChaussure = [];
        foreach ($taillesChaussure as $t) {
            $exist = DB::table('taille')->where('type_taille', $t)->first();
            $tailleIdsChaussure[] = $exist ? $exist->id_taille : DB::table('taille')->insertGetId(['type_taille' => $t], 'id_taille');
        }

        // Images disponibles (On reprend celles que tu as uploadées pour éviter les liens morts)
        $images = [
            '/img/produits/maillot-france-2026-authentic.jpg',
            '/img/produits/maillot-allemagne-domicile-2024.jpg',
            '/img/produits/maillot-argentine-3-etoiles.jpg',
            '/img/produits/maillot-bresil-1970-pele.jpg',
            '/img/produits/maillot-portugal-2026.jpg',
            '/img/produits/maillot-domicile-adidas-italie-2024.jpg',
            '/img/produits/maillot-domicile-adidas-mexique-2024.jpg'
        ];

        // ---------------------------------------------------------
        // 2. GÉNÉRATION DES PRODUITS (Boucle de 150 itérations)
        // ---------------------------------------------------------
        
        for ($i = 1; $i <= 150; $i++) {
            
            // 2.1 Choix de la catégorie et du type de produit
            $catName = $faker->randomElement(array_keys($catIds));
            $idCat = $catIds[$catName];
            
            $isShoe = ($catName === 'Crampons');
            $isAccessory = ($catName === 'Accessoires' || $catName === 'Ballons');

            // 2.2 Création Produit
            $nomProduit = $this->generateProductName($catName, $faker);
            
            $idProduit = DB::table('produit')->insertGetId([
                'id_categorie' => $idCat,
                'nom_produit' => $nomProduit,
                'description_produit' => $faker->paragraph(3),
                'visibilite' => 'visible',
                'date_creation' => $faker->dateTimeBetween('-2 years', 'now'),
            ], 'id_produit');

            // 2.3 Liaison Nation (80% de chance d'être lié à une nation)
            if ($faker->boolean(80)) {
                DB::table('produit_nation')->insert([
                    'id_produit' => $idProduit,
                    'id_nation' => $faker->randomElement($nationIds)
                ]);
            }

            // 2.4 Photo (Au hasard)
            DB::table('photo_produit')->insert([
                'id_produit' => $idProduit,
                'url_photo' => $faker->randomElement($images)
            ]);

            // 2.5 Variantes (Couleurs & Prix)
            // Un produit peut avoir 1 à 4 variantes de couleur
            $nbVariantes = $faker->numberBetween(1, 4);
            $couleursChoisies = $faker->randomElements($couleurIds, $nbVariantes);

            foreach ($couleursChoisies as $idCouleur) {
                
                // Calcul du prix cohérent selon la catégorie
                $prixBase = match($catName) {
                    'Crampons' => $faker->randomFloat(2, 80, 250),
                    'Maillots' => $faker->randomFloat(2, 70, 140),
                    'Shorts' => $faker->randomFloat(2, 30, 60),
                    'Vestes' => $faker->randomFloat(2, 60, 120),
                    default => $faker->randomFloat(2, 15, 50),
                };

                // Promo ? (20% de chance)
                $prixPromo = $faker->boolean(20) ? ($prixBase * 0.8) : null;

                $idProdCoul = DB::table('produit_couleur')->insertGetId([
                    'id_produit' => $idProduit,
                    'id_couleur' => $idCouleur,
                    'prix_total' => $prixBase,
                    'prix_promotion' => $prixPromo
                ], 'id_produit_couleur');

                // 2.6 Stocks & Tailles
                // On détermine quelles tailles appliquer
                $taillesDispo = $isShoe ? $tailleIdsChaussure : ($isAccessory ? [] : $tailleIdsVetement);

                if (empty($taillesDispo)) {
                    // Accessoire Taille Unique (souvent pas de taille ou taille unique générique)
                    // Pour simplifier, on met un stock sans taille (id_taille null) si ta BDD l'autorise, 
                    // sinon on prend une taille 'M' par défaut pour simuler TU.
                    // Supposons que ta table stock_article OBLIGE une taille (FK), on prend la première dispo.
                    if(!empty($tailleIdsVetement)) {
                        DB::table('stock_article')->insert([
                            'id_produit_couleur' => $idProdCoul,
                            'id_taille' => $tailleIdsVetement[2], // M par défaut
                            'stock' => $faker->numberBetween(0, 100)
                        ]);
                    }
                } else {
                    foreach ($taillesDispo as $idTaille) {
                        // Stock aléatoire, parfois 0 pour tester la "Rupture"
                        $stock = $faker->numberBetween(0, 20);
                        // 10% de chance d'être en rupture totale sur une taille
                        if ($faker->boolean(10)) $stock = 0;

                        DB::table('stock_article')->insert([
                            'id_produit_couleur' => $idProdCoul,
                            'id_taille' => $idTaille,
                            'stock' => $stock
                        ]);
                    }
                }
            }
        }
        $this->command->info('>>> MEGA SEEDER TERMINÉ : 150 produits injectés avec succès.');
    }

    private function generateProductName($categorie, $faker) {
        $adjectifs = ['Authentique', 'Pro', 'Elite', 'Training', 'Vintage', '2026', 'Limited Edition', 'Classique'];
        $adj = $faker->randomElement($adjectifs);
        
        switch ($categorie) {
            case 'Maillots':
                return "Maillot " . $faker->country . " $adj";
            case 'Crampons':
                return "Chaussures " . $faker->word . " " . $faker->randomElement(['Speed', 'Phantom', 'X', 'Predator']) . " $adj";
            case 'Ballons':
                return "Ballon Officiel " . $faker->year . " " . $faker->word;
            default:
                return rtrim($categorie, 's') . " " . $faker->word . " " . $adj;
        }
    }
}