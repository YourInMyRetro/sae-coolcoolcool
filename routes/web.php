<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\VoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PAGES LÉGALES ---
Route::view('/cgu', 'legal.cgu')->name('cgu');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/mentions-legales', 'legal.mentions')->name('mentions');

// --- OUTILS / MATCHER ---
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
Route::match(['get', 'post'], '/panier/ajouter/{id}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');
Route::patch('/panier/update/{id}', [PanierController::class, 'update'])->name('panier.update');

// --- VOTES (Index Public) ---
Route::get('/votes', [VoteController::class, 'index'])->name('vote.index');

// --- AUTHENTIFICATION (Login / Logout) ---
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- INSCRIPTION ---
// Particulier
Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

// Professionnel
Route::get('/inscription/pro', [AuthController::class, 'showRegisterProForm'])->name('register.pro.form');
Route::post('/inscription/pro', [AuthController::class, 'registerPro'])->name('register.pro.submit');


// --- ESPACE CONNECTÉ (Middleware 'auth') ---
Route::middleware(['auth'])->group(function () {
    
    // GESTION DU COMPTE
    Route::get('/compte', [CompteController::class, 'index'])->name('compte.index');
    Route::get('/compte/modifier', [CompteController::class, 'edit'])->name('compte.edit');
    Route::post('/compte/modifier', [CompteController::class, 'update'])->name('compte.update');

    // DEMANDES SPÉCIALES PRO (C'était l'erreur manquante)
   Route::get('/compte/demande-speciale', [CompteController::class, 'createDemande'])
         ->name('compte.demande.create'); 
         
    Route::post('/compte/demande-speciale', [CompteController::class, 'storeDemande'])
         ->name('compte.demande.store');

    // PROCESSUS DE COMMANDE
    Route::get('/commande/livraison', [CommandeController::class, 'livraison'])->name('commande.livraison');
    Route::post('/commande/livraison', [CommandeController::class, 'validerLivraison'])->name('commande.validerLivraison');

    Route::get('/commande/paiement', [CommandeController::class, 'paiement'])->name('commande.paiement');
    Route::post('/commande/payer', [CommandeController::class, 'processPaiement'])->name('commande.processPaiement');
    
    Route::get('/commande/succes', [CommandeController::class, 'succes'])->name('commande.succes');

    // VOTES (Détail/Action nécessitant connexion)
    Route::get('/votes/{id_competition}', [VoteController::class, 'show'])->name('vote.show');
});

// --- ESPACE DIRECTEUR ---
Route::middleware(['auth', 'directeur'])->group(function () {
    
    Route::get('/directeur/dashboard', [App\Http\Controllers\DirecteurController::class, 'dashboard'])
        ->name('directeur.dashboard');

    Route::get('/directeur/produits-incomplets', [App\Http\Controllers\DirecteurController::class, 'produitsIncomplets'])
        ->name('directeur.produits_incomplets');

    Route::post('/directeur/produit/{id}/fixer-prix', [App\Http\Controllers\DirecteurController::class, 'updatePrix'])
        ->name('directeur.update_prix');
});