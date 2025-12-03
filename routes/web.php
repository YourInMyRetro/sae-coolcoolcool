<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;

Route::get('/matcher', [ToolController::class, 'index'])->name('matcher.index');
Route::post('/matcher', [ToolController::class, 'store'])->name('matcher.store');

Route::post('/matcher/skip', [ToolController::class, 'skip'])->name('matcher.skip');
Route::delete('/matcher/delete', [ToolController::class, 'delete'])->name('matcher.delete');
// Accueil
Route::get('/', [ProduitController::class, 'home'])->name('home');

// Boutique & Détail Produit
Route::get('/boutique', [ProduitController::class, 'index'])->name('produits.index');
Route::get('/boutique/produit/{id}', [ProduitController::class, 'show'])->name('produits.show');

// Routes du panier
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::match(['get', 'post'], '/panier/ajouter/{id}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');
Route::patch('/panier/update/{id}', [PanierController::class, 'update'])->name('panier.update');

// N'oublie pas d'importer le contrôleur tout en haut du fichier avec les autres "use"
use App\Http\Controllers\CommandeController;

// ... tes routes existantes (accueil, boutique, panier) ...

// --- ROUTES COMMANDE (Sécurisées par connexion) ---
Route::middleware(['auth'])->group(function () {
    
    // Étape 1 : Livraison
    Route::get('/commande/livraison', [CommandeController::class, 'livraison'])
        ->name('commande.livraison');
        
    Route::post('/commande/livraison', [CommandeController::class, 'validerLivraison'])
        ->name('commande.validerLivraison');

    // Étape 2 : Paiement
    Route::get('/commande/paiement', [CommandeController::class, 'paiement'])
        ->name('commande.paiement');
        
    Route::post('/commande/payer', [CommandeController::class, 'processPaiement'])
        ->name('commande.processPaiement');
    
    // Étape 3 : Confirmation
    Route::get('/commande/succes', [CommandeController::class, 'succes'])
        ->name('commande.succes');
});

Route::get('/login', [AuthController::class, 'login'])->name('login'); // La fameuse route manquante !
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');