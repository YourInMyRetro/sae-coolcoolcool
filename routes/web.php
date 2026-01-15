<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PanierController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ServiceCommandeController;
use App\Http\Controllers\ServiceVenteController;
use App\Http\Controllers\ServiceExpeditionController;
use App\Http\Controllers\DirecteurController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlogController;

Route::view('/aide', 'aide.index')->name('aide');
Route::view('/cgu', 'legal.cgu')->name('cgu');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/mentions-legales', 'legal.mentions')->name('mentions');

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
Route::get('/votes/{id}', [VoteController::class, 'show'])->name('vote.show');
Route::post('/votes/{id}', [VoteController::class, 'store'])->middleware('auth')->name('vote.store');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.submit');

Route::get('/inscription/pro', [AuthController::class, 'showRegisterProForm'])->name('register.pro.form');
Route::post('/inscription/pro', [AuthController::class, 'registerPro'])->name('register.pro.submit');

Route::get('/login/2fa', [AuthController::class, 'show2FAForm'])->name('login.2fa.form');
Route::post('/login/2fa', [AuthController::class, 'verify2FA'])->name('login.2fa.verify');
Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reinitialiser-mot-de-passe/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reinitialiser-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/compte', [CompteController::class, 'index'])->name('compte.index');
    Route::get('/compte/modifier', [CompteController::class, 'edit'])->name('compte.edit');
    Route::post('/compte/modifier', [CompteController::class, 'update'])->name('compte.update');
    Route::post('/compte/2fa/send', [CompteController::class, 'send2FACode'])->name('compte.2fa.send');
    Route::post('/compte/2fa/verify', [CompteController::class, 'verify2FACode'])->name('compte.2fa.verify');
    Route::post('/compte/2fa/disable', [CompteController::class, 'disable2FA'])->name('compte.2fa.disable');
    Route::get('/compte/demande-speciale', [CompteController::class, 'createDemande'])->name('compte.demande.create'); 
    Route::post('/compte/demande-speciale', [CompteController::class, 'storeDemande'])->name('compte.demande.store');
    Route::delete('/compte/supprimer', [CompteController::class, 'destroy'])->name('compte.destroy');
    Route::get('/compte/commandes', [CompteController::class, 'mesCommandes'])->name('compte.commandes');

    
    Route::get('/compte/export', [CompteController::class, 'exportData'])->name('compte.export');
    Route::get('/commande/livraison', [CommandeController::class, 'livraison'])->name('commande.livraison');
    Route::post('/commande/livraison', [CommandeController::class, 'validerLivraison'])->name('commande.validerLivraison');
    Route::get('/commande/paiement', [CommandeController::class, 'paiement'])->name('commande.paiement');
    Route::post('/commande/payer', [CommandeController::class, 'processPaiement'])->name('commande.processPaiement');
    Route::get('/commande/succes', [CommandeController::class, 'succes'])->name('commande.succes');

    Route::get('/votes/{id_competition}', [VoteController::class, 'show'])->name('vote.competition.show');
    Route::post('/votes/{id_competition}/voter', [VoteController::class, 'store'])->name('vote.competition.store');

    Route::delete('/compte/supprimer', [App\Http\Controllers\CompteController::class, 'destroy'])->name('compte.destroy');
    Route::delete('/compte/anonymiser', [App\Http\Controllers\CompteController::class, 'anonymiser'])->name('compte.anonymiser');
});

Route::middleware(['auth', 'directeur'])->group(function () {
    Route::get('/directeur/dashboard', [DirecteurController::class, 'dashboard'])->name('directeur.dashboard');
    Route::get('/directeur/produits-incomplets', [DirecteurController::class, 'produitsIncomplets'])->name('directeur.produits_incomplets');
    Route::post('/directeur/produit/{id}/fixer-prix', [DirecteurController::class, 'updatePrix'])->name('directeur.update_prix');
});

Route::get('/service/dashboard', [ServiceCommandeController::class, 'dashboard'])->name('service.dashboard');
Route::post('/service/reserve', [ServiceCommandeController::class, 'storeReserve'])->name('service.reserve.store');
Route::post('/service/commande/{id}/valider', [ServiceCommandeController::class, 'validerReception'])->name('service.commande.valider');

Route::middleware(['auth'])->group(function () {
    Route::get('/service/expedition', [ServiceExpeditionController::class, 'index'])->name('service.expedition');
    Route::post('/service/expedition/prise-en-charge', [ServiceExpeditionController::class, 'priseEnCharge'])->name('service.expedition.pickup');
    Route::post('/service/expedition/sms/{id}', [ServiceExpeditionController::class, 'sendSms'])->name('service.expedition.sms');
});

Route::middleware(['auth'])->prefix('service-vente')->name('vente.')->group(function () {
    Route::get('/', [ServiceVenteController::class, 'index'])->name('dashboard');
    Route::get('/categorie/create', [ServiceVenteController::class, 'createCategorie'])->name('categorie.create');
    Route::post('/categorie', [ServiceVenteController::class, 'storeCategorie'])->name('categorie.store');
    
    Route::get('/produit/create', [ServiceVenteController::class, 'createProduit'])->name('produit.create');
    Route::post('/produit', [ServiceVenteController::class, 'storeProduit'])->name('produit.store');
    Route::get('/produits', [ServiceVenteController::class, 'listProduits'])->name('produits.list');
    Route::post('/produit/{id}/visibilite', [ServiceVenteController::class, 'toggleVisibilite'])->name('produit.visibilite');
    
    Route::post('/produit/{id}/photo/add', [ServiceVenteController::class, 'addPhoto'])->name('produit.photo.add');
    Route::delete('/photo/{id}/delete', [ServiceVenteController::class, 'deletePhoto'])->name('produit.photo.delete');

    Route::get('/votations', [ServiceVenteController::class, 'listVotations'])->name('votation.list');
    Route::get('/votation/create', [ServiceVenteController::class, 'createVotation'])->name('votation.create');
    Route::post('/votation', [ServiceVenteController::class, 'storeVotation'])->name('votation.store');
    Route::post('/votation/{id}/statut', [ServiceVenteController::class, 'toggleStatutVotation'])->name('votation.statut');
    Route::delete('/votation/{id}/delete', [ServiceVenteController::class, 'destroyVotation'])->name('votation.delete');
    Route::get('/votation/{id}/candidats', [ServiceVenteController::class, 'editCandidats'])->name('votation.candidats.edit');
    Route::post('/votation/{id}/candidats', [ServiceVenteController::class, 'updateCandidats'])->name('votation.candidats.update');
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::post('/chat/clear', [ChatController::class, 'clearMessages'])->name('chat.clear');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{id}/comment', [BlogController::class, 'storeComment'])->name('blog.comment.store');