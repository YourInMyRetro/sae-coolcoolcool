<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealProductSeeder extends Seeder
{
    public function run()
    {
        $this->ensureBaseData();

        // LISTE COMPLÈTE (Maillots + Nouveaux équipements)
        $produits = [
            // --- MAILLOTS EXISTANTS (Tu les as déjà) ---
            [
                'nom' => 'Maillot Allemagne Domicile 2024',
                'desc' => 'Le maillot officiel de la Mannschaft pour l\'Euro. Design classique.',
                'img' => '/img/produits/maillot-allemagne-domicile-2024.jpg',
                'cat' => 'Maillots', 'nation' => 'Allemagne', 'prix' => 90.00, 'couleur' => 'Blanc'
            ],
            [
                'nom' => 'Maillot France 2026 Authentic',
                'desc' => 'Technologie Dri-Fit ADV pour les joueurs pro.',
                'img' => '/img/produits/maillot-france-2026-authentic.jpg',
                'cat' => 'Maillots', 'nation' => 'France', 'prix' => 140.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Argentine 3 Étoiles',
                'desc' => 'Le maillot des champions du monde.',
                'img' => '/img/produits/maillot-argentine-3-etoiles.jpg',
                'cat' => 'Maillots', 'nation' => 'Argentine', 'prix' => 110.00, 'couleur' => 'Bleu'
            ],

            // --- 1. CHAUSSURES (CRAMPONS) ---
            [
                'nom' => 'Nike Mercurial Superfly 9 Elite',
                'desc' => 'Vitesse explosive. Portée par Kylian Mbappé. Couleur rose flashy pour ne pas passer inaperçu.',
                'img' => '/img/produits/crampons-nike-mercurial-rose.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 270.00, 'couleur' => 'Rose'
            ],
            [
                'nom' => 'Adidas Predator Elite FT',
                'desc' => 'Le retour de la languette repliée. Contrôle absolu. Modèle porté par Jude Bellingham.',
                'img' => '/img/produits/crampons-adidas-predator-noir.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 280.00, 'couleur' => 'Noir'
            ],
            [
                'nom' => 'Puma Future Ultimate',
                'desc' => 'Agilité et créativité. La chaussure de Neymar Jr.',
                'img' => '/img/produits/crampons-puma-future-orange.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 220.00, 'couleur' => 'Orange'
            ],
            [
                'nom' => 'Nike Phantom GX 2',
                'desc' => 'Précision mortelle devant le but. Texture Gripknit.',
                'img' => '/img/produits/crampons-nike-phantom-bleu.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 260.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Adidas Copa Pure 2',
                'desc' => 'Toucher de balle en cuir premium. Élégance et confort.',
                'img' => '/img/produits/crampons-adidas-copa-blanc.jpg',
                'cat' => 'Crampons', 'nation' => null, 'prix' => 230.00, 'couleur' => 'Blanc'
            ],

            // --- 2. BALLONS ---
            [
                'nom' => 'Ballon Euro 2024 Fussballliebe',
                'desc' => 'Ballon officiel de match de l\'UEFA Euro 2024 en Allemagne.',
                'img' => '/img/produits/ballon-euro-2024-officiel.jpg',
                'cat' => 'Ballons', 'nation' => 'Allemagne', 'prix' => 150.00, 'couleur' => 'Blanc'
            ],
            [
                'nom' => 'Ballon Champions League 2025',
                'desc' => 'Le design iconique aux étoiles pour la finale à Munich.',
                'img' => '/img/produits/ballon-ucl-2025-finale.jpg',
                'cat' => 'Ballons', 'nation' => null, 'prix' => 140.00, 'couleur' => 'Multicolore'
            ],
            [
                'nom' => 'Ballon Nike Flight Premier League',
                'desc' => 'Trajectoire stable à 30% supérieure aux ballons standard.',
                'img' => '/img/produits/ballon-premier-league-nike.jpg',
                'cat' => 'Ballons', 'nation' => 'Angleterre', 'prix' => 135.00, 'couleur' => 'Jaune'
            ],

            // --- 3. ACCESSOIRES (Gardiens) ---
            [
                'nom' => 'Gants Adidas Predator Pro',
                'desc' => 'Gants de gardien pro portés par Manuel Neuer. Grip URG 2.0.',
                'img' => '/img/produits/gants-adidas-predator-neuer.jpg',
                'cat' => 'Accessoires', 'nation' => 'Allemagne', 'prix' => 120.00, 'couleur' => 'Rouge'
            ],
            [
                'nom' => 'Echarpe France "Allez les Bleus"',
                'desc' => 'Indispensable pour supporter l\'équipe au stade.',
                'img' => '/img/produits/echarpe-france-bleu.jpg',
                'cat' => 'Accessoires', 'nation' => 'France', 'prix' => 20.00, 'couleur' => 'Bleu'
            ],

            // --- 4. VINTAGE / COLLECTOR ---
            [
                'nom' => 'Maillot France 1998 (Zidane)',
                'desc' => 'Réédition du maillot de la finale contre le Brésil. Flocage 10 inclus.',
                'img' => '/img/produits/maillot-france-1998-zidane.jpg',
                'cat' => 'Vintage', 'nation' => 'France', 'prix' => 180.00, 'couleur' => 'Bleu'
            ],
            [
                'nom' => 'Maillot Naples 1986 (Maradona)',
                'desc' => 'Le maillot légendaire du grand Diego avec le sponsor Mars.',
                'img' => '/img/produits/maillot-naples-maradona-mars.jpg',
                'cat' => 'Vintage', 'nation' => 'Italie', 'prix' => 200.00, 'couleur' => 'Bleu'
            ]
        ];

        foreach ($produits as $p) {
            $this->createProduct($p);
        }
    }

    private function ensureBaseData() {
        // Création des catégories manquantes
        $cats = ['Maillots', 'Crampons', 'Ballons', 'Accessoires', 'Vintage', 'Signés', 'Vêtement'];
        foreach ($cats as $c) DB::table('categorie')->insertOrIgnore(['nom_categorie' => $c]);
        
        // Création des couleurs manquantes
        $couleurs = ['Blanc', 'Bleu', 'Noir', 'Rouge', 'Jaune', 'Rose', 'Orange', 'Vert', 'Multicolore'];
        foreach ($couleurs as $col) DB::table('couleur')->insertOrIgnore(['type_couleur' => $col]);

        // Création des tailles (Chaussures + Vêtements)
        $tailles = ['S', 'M', 'L', 'XL', '39', '40', '41', '42', '43', '44', 'TU']; // TU = Taille Unique
        foreach ($tailles as $t) DB::table('taille')->insertOrIgnore(['type_taille' => $t]);

        // Nations
        $nations = ['France', 'Allemagne', 'Argentine', 'Brésil', 'Italie', 'Angleterre', 'Espagne'];
        foreach ($nations as $n) DB::table('nation')->insertOrIgnore(['nom_nation' => $n]);
    }

    private function createProduct($data) {
        // 1. Récupération des IDs
        $idCat = DB::table('categorie')->where('nom_categorie', $data['cat'])->value('id_categorie');
        $idNation = isset($data['nation']) ? DB::table('nation')->where('nom_nation', $data['nation'])->value('id_nation') : null;
        $idCouleur = DB::table('couleur')->where('type_couleur', $data['couleur'])->value('id_couleur');

        // 2. Création Produit
        $idProduit = DB::table('produit')->insertGetId([
            'id_categorie' => $idCat,
            'nom_produit' => $data['nom'],
            'description_produit' => $data['desc'],
            'visibilite' => 'visible',
            'date_creation' => now(),
        ], 'id_produit');

        // 3. Photo
        DB::table('photo_produit')->insert([
            'id_produit' => $idProduit,
            'url_photo' => $data['img']
        ]);

        // 4. Liaison Nation (Si applicable)
        if ($idNation) {
            DB::table('produit_nation')->insert([
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

        // Définition des tailles selon le type de produit
        if ($data['cat'] === 'Crampons') {
            $taillesDispo = ['39', '40', '41', '42', '43', '44'];
        } elseif (in_array($data['cat'], ['Ballons', 'Accessoires'])) {
            $taillesDispo = ['TU']; // Taille Unique
        } else {
            $taillesDispo = ['S', 'M', 'L', 'XL']; // Vêtements
        }

        foreach ($taillesDispo as $tName) {
            $idTaille = DB::table('taille')->where('type_taille', $tName)->value('id_taille');
            if ($idTaille) {
                DB::table('stock_article')->insert([
                    'id_produit_couleur' => $idProdCoul,
                    'id_taille' => $idTaille,
                    'stock' => rand(0, 30) // Stock aléatoire
                ]);
            }
        }
    }
}