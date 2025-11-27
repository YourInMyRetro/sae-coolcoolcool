<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; // <--- Vital pour que ça marche

// Accueil
Route::get('/', [ProduitController::class, 'home'])->name('home');

// Boutique & Détail Produit
Route::get('/boutique', [ProduitController::class, 'index'])->name('produits.index');
Route::get('/produit/{id}', [ProduitController::class, 'show'])->name('produits.show'); // <--- Celle-là manquait aussi

// --- ROUTES DU PANIER (Celles qui te manquent) ---
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::get('/panier/ajouter/{id}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');