<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\VoteController;

Route::get('/matcher', [ToolController::class, 'index'])->name('matcher.index');
Route::post('/matcher', [ToolController::class, 'store'])->name('matcher.store');
Route::post('/matcher/skip', [ToolController::class, 'skip'])->name('matcher.skip');
Route::delete('/matcher/delete', [ToolController::class, 'delete'])->name('matcher.delete');

Route::get('/', [ProduitController::class, 'home'])->name('home');
Route::get('/boutique', [ProduitController::class, 'index'])->name('produits.index');
Route::get('/boutique/produit/{id}', [ProduitController::class, 'show'])->name('produits.show');

Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::match(['get', 'post'], '/panier/ajouter/{id}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/vider', [PanierController::class, 'vider'])->name('panier.vider');
Route::patch('/panier/update/{id}', [PanierController::class, 'update'])->name('panier.update');

Route::get('/votes', [VoteController::class, 'index'])->name('vote.index');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

Route::get('/inscription/pro', [AuthController::class, 'showRegisterProForm'])->name('register.pro.form');
Route::post('/inscription/pro', [AuthController::class, 'registerPro'])->name('register.pro.submit');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/compte', [CompteController::class, 'index'])->name('compte.index');
    Route::get('/compte/modifier', [CompteController::class, 'edit'])->name('compte.edit');
    Route::post('/compte/modifier', [CompteController::class, 'update'])->name('compte.update');

    Route::get('/commande/livraison', [CommandeController::class, 'livraison'])
        ->name('commande.livraison');
    Route::post('/commande/livraison', [CommandeController::class, 'validerLivraison'])
        ->name('commande.validerLivraison');

    Route::get('/commande/paiement', [CommandeController::class, 'paiement'])
        ->name('commande.paiement');
    Route::post('/commande/payer', [CommandeController::class, 'processPaiement'])
        ->name('commande.processPaiement');
    
    Route::get('/commande/succes', [CommandeController::class, 'succes'])
        ->name('commande.succes');

    Route::get('/votes/{id_competition}', [VoteController::class, 'show'])->name('vote.show');
});