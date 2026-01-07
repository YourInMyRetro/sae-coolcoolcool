<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoutiqueSeeder extends Seeder
{
    public function run()
    {
        // 1. Catégories (On précise 'id_categorie')
        $catMaillots = DB::table('categorie')->insertGetId([
            'nom_categorie' => 'Maillots'
        ], 'id_categorie');
        
        $catAccessoires = DB::table('categorie')->insertGetId([
            'nom_categorie' => 'Accessoires'
        ], 'id_categorie');

        // 2. Couleurs et Tailles (On précise 'id_couleur' et 'id_taille')
        $cRouge = DB::table('couleur')->insertGetId(['type_couleur' => 'Rouge'], 'id_couleur');
        $cBleu = DB::table('couleur')->insertGetId(['type_couleur' => 'Bleu'], 'id_couleur');
        $cOr = DB::table('couleur')->insertGetId(['type_couleur' => 'Or'], 'id_couleur');
        
        $tL = DB::table('taille')->insertGetId(['type_taille' => 'L'], 'id_taille');
        $tM = DB::table('taille')->insertGetId(['type_taille' => 'M'], 'id_taille');

        // 3. Produits
        $produits = [
            ['nom' => 'Maillot France 2026', 'img' => '/img/produits/maillot-france-2026-authentic.jpg', 'prix' => 120, 'cat' => $catMaillots],
            ['nom' => 'Maillot Allemagne 2024', 'img' => '/img/produits/maillot-allemagne-domicile-2024.jpg', 'prix' => 90, 'cat' => $catMaillots],
            ['nom' => 'Maillot Argentine 3 étoiles', 'img' => '/img/produits/maillot-argentine-3-etoiles.jpg', 'prix' => 100, 'cat' => $catMaillots],
            ['nom' => 'T-shirt USA 1994', 'img' => '/img/produits/t-shirt-fifa-classics-usa-1994.jpg', 'prix' => 45, 'cat' => $catMaillots],
            ['nom' => 'Echarpe Portugal', 'img' => '/img/produits/maillot-portugal-2026.jpg', 'prix' => 25, 'cat' => $catAccessoires],
        ];

        foreach ($produits as $p) {
            // A. Table 'produit' (On précise 'id_produit')
            $idProduit = DB::table('produit')->insertGetId([
                'id_categorie' => $p['cat'],
                'nom_produit' => $p['nom'],
                'description_produit' => 'Produit officiel FIFA. Qualité authentique.',
                'visibilite' => 'visible',
                'date_creation' => now(),
            ], 'id_produit');

            // B. Table 'photo_produit' (Pas besoin d'insertGetId ici, un simple insert suffit)
            DB::table('photo_produit')->insert([
                'id_produit' => $idProduit,
                'url_photo' => $p['img']
            ]);

            // C. Table 'produit_couleur' (On précise 'id_produit_couleur')
            $idProdCoul = DB::table('produit_couleur')->insertGetId([
                'id_produit' => $idProduit,
                'id_couleur' => $cBleu, // On met Bleu par défaut
                'prix_total' => $p['prix']
            ], 'id_produit_couleur');

            // D. Table 'stock_article' (Pas besoin d'ID retourné ici)
            DB::table('stock_article')->insert([
                'id_produit_couleur' => $idProdCoul,
                'id_taille' => $tL,
                'stock' => 50
            ]);
            
            DB::table('stock_article')->insert([
                'id_produit_couleur' => $idProdCoul,
                'id_taille' => $tM,
                'stock' => 30
            ]);
        }
    }
}