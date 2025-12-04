<?php

use Illuminate\Support\Facades\Route;
// Importation propre et groupée des contrôleurs
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CompteController; // Le nouveau contrôleur

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- OUTILS / MATCHING ---
Route::get('/matcher', [ToolController::class, 'index'])->name('matcher.index');
Route::post('/matcher', [ToolController::class, 'store'])->name('matcher.store');
Route::post('/matcher/skip', [ToolController::class, 'skip'])->name('matcher.skip');
Route::delete('/matcher/delete', [ToolController::class, 'delete'])->name('matcher.delete');

// --- ACCUEIL & BOUTIQUE ---
Route::get('/', [ProduitController::class, 'home'])->name('home');
Route::get('/boutique', [ProduitController::class, 'index'])->name('produits.index');
Route::get('/boutique/produit/{id}', [ProduitController::class, 'show'])->name('produits.show');

// --- PANIER ---
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
// Note : Usage de match pour supporter GET et POST sur l'ajout (legacy code)
Route::match(['get', 'post'], '/panier/ajouter/{id}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');
Route::patch('/panier/update/{id}', [PanierController::class, 'update'])->name('panier.update');

// --- AUTHENTIFICATION (Login / Logout) ---
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- INSCRIPTION (Nouvelles routes) ---
// Visiteur standard
Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

// Professionnel
Route::get('/inscription/pro', [AuthController::class, 'showRegisterProForm'])->name('register.pro.form');
Route::post('/inscription/pro', [AuthController::class, 'registerPro'])->name('register.pro.submit');

// --- ESPACE CONNECTÉ (Middleware 'auth') ---
Route::middleware(['auth'])->group(function () {
    
    // GESTION DU COMPTE (Nouveau)
    Route::get('/compte', [CompteController::class, 'index'])->name('compte.index');
    Route::get('/compte/modifier', [CompteController::class, 'edit'])->name('compte.edit');
    Route::post('/compte/modifier', [CompteController::class, 'update'])->name('compte.update');

    // PROCESSUS DE COMMANDE
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