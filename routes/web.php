<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;

// Page d'accueil style FIFA
Route::get('/', [ProduitController::class, 'home'])->name('home');

// Page de recherche / Boutique
Route::get('/boutique', [ProduitController::class, 'index'])->name('produits.index');