<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealProductSeeder extends Seeder
{
    public function run()
    {
        // 1. On s'assure que les données de référence (Catégories, Couleurs, etc.) existent
        $this->ensureBaseData();

        // 2. Liste STRICTE des produits avec tes vrais noms de fichiers
        $produits = [
            // --- MAILLOTS ---
            [
                'nom' => 'Maillot Allemagne Domicile 2024',
                'desc' => 'Le maillot officiel de la Mannschaft pour l\'Euro à domicile.',
                'img' => '/img/produits/maillot-allemagne-domicile-2024.jpg',
                'cat' => 'Maillots', 'nation' => 'Allemagne', 'prix' => 90.00, 'couleur' => 'Blanc'
            ],
            [
                'nom' => 'Veste de survêtement Allemagne',
                'desc' => 'Veste noire officielle de l\'équipe d\'Allemagne.',
                'img' => '/img/produits/vestedesurvetementnoireallemagne.jpg',
                'cat' => 'Vêtements', 'nation' => 'Allemagne', 'prix' => 85.00, 'couleur' => 'Noir'
            ],
            [
                'nom' => 'Maillot France 2026 Authentic',
                'desc' => 'Le futur maillot des Bleus, technologie Dri-Fit ADV.',
                'img' => '/img/produits/maillot-france-2026-authentic.jpg',
                'cat' => 'Maillots', 'nation' => 'France', 'prix' => 140.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot France 1998 (Zidane)',
                'desc' => 'Réédition collector de la finale 98.',
                'img' => '/img/produits/maillot-france-1998-zidane.jpg',
                'cat' => 'Vintage', 'nation' => 'France', 'prix' => 180.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Argentine 3 Étoiles',
                'desc' => 'Le maillot des champions du monde en titre.',
                'img' => '/img/produits/maillot-argentine-3-etoiles.jpg',
                'cat' => 'Maillots', 'nation' => 'Argentine', 'prix' => 110.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Échauffement Argentine',
                'desc' => 'Maillot pré-match porté par Messi et ses coéquipiers.',
                'img' => '/img/produits/maillotechauffementargentine.jpg',
                'cat' => 'Maillots', 'nation' => 'Argentine', 'prix' => 65.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Bas de survêtement Argentine',
                'desc' => 'Pantalon d\'entraînement officiel.',
                'img' => '/img/produits/basdesurvetementargentine.jpg',
                'cat' => 'Vêtements', 'nation' => 'Argentine', 'prix' => 55.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Brésil 1970 (Pelé)',
                'desc' => 'Le maillot jaune légendaire du Roi Pelé.',
                'img' => '/img/produits/maillot-bresil-1970-pele.jpg',
                'cat' => 'Vintage', 'nation' => 'Brésil', 'prix' => 200.00, 'couleur' => 'Jaune'
            ],
            [
                'nom' => 'Maillot Naples 1986 (Maradona)',
                'desc' => 'Maillot historique sponsor Mars, époque Diego.',
                'img' => '/img/produits/maillot-naples-maradona-mars.jpg',
                'cat' => 'Vintage', 'nation' => 'Italie', 'prix' => 220.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Japon Féminine 2022',
                'desc' => 'Design unique origami pour l\'équipe nationale féminine.',
                'img' => '/img/produits/maillotjaponfemme2022.jpg',
                'cat' => 'Maillots', 'nation' => 'Japon', 'prix' => 90.00, 'couleur' => 'Rose'
            ],
            [
                'nom' => 'Maillot Portugal 2026',
                'desc' => 'Nouveau design pour la seleçao.',
                'img' => '/img/produits/maillot-portugal-2026.jpg',
                'cat' => 'Maillots', 'nation' => 'Portugal', 'prix' => 90.00, 'couleur' => 'Rouge'
            ],
             [
                'nom' => 'Maillot Officiel Signé Jack Grealish',
                'desc' => 'Pièce de collection signée par le joueur anglais.',
                'img' => '/img/produits/maillot-officiel-signe-jack-grealish.jpg',
                'cat' => 'Signés', 'nation' => 'Angleterre', 'prix' => 450.00, 'couleur' => 'Blanc'
            ],

            // --- CRAMPONS ---
            [
                'nom' => 'Nike Mercurial Rose',
                'desc' => 'Vitesse explosive, coloris flashy.',
                'img' => '/img/produits/crampons-nike-mercurial-rose.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 270.00, 'couleur' => 'Rose'
            ],
            [
                'nom' => 'Adidas Predator Noir',
                'desc' => 'Contrôle et précision, le retour de la languette.',
                'img' => '/img/produits/crampons-adidas-predator-noir.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 280.00, 'couleur' => 'Noir'
            ],
            [
                'nom' => 'Puma Future Orange',
                'desc' => 'Agilité ultime pour les créateurs de jeu.',
                'img' => '/img/produits/crampons-puma-future-orange.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 220.00, 'couleur' => 'Orange'
            ],
            [
                'nom' => 'Nike Phantom Bleu',
                'desc' => 'Gripknit pour un toucher de balle parfait.',
                'img' => '/img/produits/crampons-nike-phantom-bleu.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 260.00, 'couleur' => 'Bleu'
            ],

            // --- BALLONS & ACCESSOIRES ---
            [
                'nom' => 'Ballon Euro 2024 Officiel',
                'desc' => 'Ballon de match Fussballliebe.',
                'img' => '/img/produits/ballon-euro-2024-officiel.jpg',
                'cat' => 'Ballons', 'nation' => 'Allemagne', 'prix' => 150.00, 'couleur' => 'Blanc'
            ],
            [
                'nom' => 'Ballon CDM 2022 Al Rihla',
                'desc' => 'Le ballon officiel de la coupe du monde au Qatar.',
                'img' => '/img/produits/ballon-cdm-2022-al-rihla.jpg',
                'cat' => 'Ballons', 'nation' => null, 'prix' => 140.00, 'couleur' => 'Multicolore'
            ],
            [
                'nom' => 'Ballon Premier League Nike',
                'desc' => 'Ballon officiel du championnat anglais.',
                'img' => '/img/produits/ballon-premier-league-nike.jpg',
                'cat' => 'Ballons', 'nation' => 'Angleterre', 'prix' => 35.00, 'couleur' => 'Jaune'
            ],
            [
                'nom' => 'Gants Adidas Predator Neuer',
                'desc' => 'Gants pro portés par Manuel Neuer.',
                'img' => '/img/produits/gants-adidas-predator-neuer.jpg',
                'cat' => 'Accessoires', 'nation' => 'Allemagne', 'prix' => 120.00, 'couleur' => 'Rouge'
            ],
            [
                'nom' => 'Gants Nike Vapor Grip',
                'desc' => 'Adhérence par tous les temps.',
                'img' => '/img/produits/gants-nike-vapor-grip.jpg',
                'cat' => 'Accessoires', 'nation' => null, 'prix' => 110.00, 'couleur' => 'Noir'
            ],
            [
                'nom' => 'Écharpe France Bleu',
                'desc' => 'Allez les bleus !',
                'img' => '/img/produits/echarpe-france-bleu.jpg',
                'cat' => 'Accessoires', 'nation' => 'France', 'prix' => 20.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Peluche Mascotte CDM',
                'desc' => 'Souvenir officiel pour les enfants.',
                'img' => '/img/produits/pelucheMascottecdm.jpg',
                'cat' => 'Accessoires', 'nation' => null, 'prix' => 25.00, 'couleur' => 'Blanc'
            ],
            [
                'nom' => 'Réplique Trophée Coupe du Monde',
                'desc' => 'Réplique 2014 en métal doré.',
                'img' => '/img/produits/repliqueTropheecoupedumonde2014.jpg',
                'cat' => 'Accessoires', 'nation' => null, 'prix' => 50.00, 'couleur' => 'Jaune'
            ]
        ];

        foreach ($produits as $p) {
            $this->createProduct($p);
        }
    }

    private function ensureBaseData() {
        // Catégories
        $cats = ['Maillots', 'Crampons', 'Ballons', 'Accessoires', 'Vintage', 'Signés', 'Vêtements'];
        foreach ($cats as $c) DB::table('categorie')->insertOrIgnore(['nom_categorie' => $c]);
        
        // Couleurs
        $couleurs = ['Blanc', 'Bleu', 'Noir', 'Rouge', 'Jaune', 'Rose', 'Orange', 'Vert', 'Multicolore'];
        foreach ($couleurs as $col) DB::table('couleur')->insertOrIgnore(['type_couleur' => $col]);

        // Tailles
        $tailles = ['S', 'M', 'L', 'XL', 'XXL', '39', '40', '41', '42', '43', '44', 'TU'];
        foreach ($tailles as $t) DB::table('taille')->insertOrIgnore(['type_taille' => $t]);

        // Nations
        $nations = ['France', 'Allemagne', 'Argentine', 'Brésil', 'Italie', 'Angleterre', 'Espagne', 'Portugal', 'Japon'];
        foreach ($nations as $n) DB::table('nation')->insertOrIgnore(['nom_nation' => $n]);
    }

    private function createProduct($data) {
        // 1. Récupération des IDs
        $idCat = DB::table('categorie')->where('nom_categorie', $data['cat'])->value('id_categorie');
        $idNation = isset($data['nation']) ? DB::table('nation')->where('nom_nation', $data['nation'])->value('id_nation') : null;
        $idCouleur = DB::table('couleur')->where('type_couleur', $data['couleur'])->value('id_couleur');

        if (!$idCat || !$idCouleur) {
            // Sécurité si jamais une catégorie ou couleur est mal écrite
            return; 
        }

        // 2. Création Produit (Correspondance stricte avec Nouveau.sql)
        $idProduit = DB::table('produit')->insertGetId([
            'id_categorie' => $idCat,
            'nom_produit' => $data['nom'],
            'description_produit' => $data['desc'],
            'visibilite' => 'visible',
            'date_creation' => now(),
            // 'id_fiche_fabrication' est NULL par défaut, c'est OK
        ], 'id_produit');

        // 3. Photo (Table séparée dans Nouveau.sql)
        DB::table('photo_produit')->insert([
            'id_produit' => $idProduit,
            'url_photo' => $data['img']
        ]);

        // 4. Liaison Nation (Si applicable)
        if ($idNation) {
            DB::table('produit_nation')->insertOrIgnore([
                'id_produit' => $idProduit,
                'id_nation' => $idNation
            ]);
        }

        // 5. Variantes et Stocks
        $idProdCoul = DB::table('produit_couleur')->insertGetId([
            'id_produit' => $idProduit,
            'id_couleur' => $idCouleur,
            'prix_total' => $data['prix']
        ], 'id_produit_couleur');

        // Définition des tailles logiques
        if ($data['cat'] === 'Crampons') {
            $taillesDispo = ['39', '40', '41', '42', '43', '44'];
        } elseif (in_array($data['cat'], ['Ballons', 'Accessoires', 'Signés'])) {
            $taillesDispo = ['TU']; 
        } else {
            $taillesDispo = ['S', 'M', 'L', 'XL']; // Vêtements
        }

        foreach ($taillesDispo as $tName) {
            $idTaille = DB::table('taille')->where('type_taille', $tName)->value('id_taille');
            if ($idTaille) {
                DB::table('stock_article')->insert([
                    'id_produit_couleur' => $idProdCoul,
                    'id_taille' => $idTaille,
                    'stock' => rand(5, 50) // Stock arbitraire mais positif
                ]);
            }
        }
    }
}