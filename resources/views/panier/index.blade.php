@extends('layout')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    
    <h1 style="color: #003366; border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 30px;">
        <i class="fas fa-shopping-cart"></i> Mon Panier
        <i class="fas fa-question-circle help-trigger" 
           style="font-size: 0.6em; vertical-align: middle; color: #326295; margin-left: 10px;" 
           data-title="Besoin d'aide ?" 
           data-content="Voici le récapitulatif de vos achats. Vous pouvez modifier les quantités ou supprimer des articles avant de passer à la caisse."
           data-link="{{ route('aide') }}#section-panier"></i>
    </h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if(count($panier) > 0)
        <div style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #326295; color: white;">
                    <tr>
                        <th style="padding: 15px; text-align: left;">
                            Produit
                        </th>
                        <th style="padding: 15px; text-align: right;">
                            Prix Unitaire
                            <i class="fas fa-info-circle help-trigger" 
                               style="color: #81d4fa;"
                               data-title="Prix" 
                               data-content="C'est le prix pour UN seul article."></i>
                        </th>
                        <th style="padding: 15px; text-align: center;">
                            Quantité
                            <i class="fas fa-calculator help-trigger" 
                               style="color: #81d4fa;"
                               data-title="Modifier la quantité" 
                               data-content="Changez le chiffre dans la case et cliquez sur les petites flèches bleues de mise à jour."></i>
                        </th>
                        <th style="padding: 15px; text-align: right;">
                            Total
                            <i class="fas fa-coins help-trigger" 
                               style="color: #81d4fa;"
                               data-title="Sous-total" 
                               data-content="Prix unitaire multiplié par la quantité."></i>
                        </th>
                        <th style="padding: 15px; text-align: center;">
                            Supprimer
                            <i class="fas fa-trash-alt help-trigger" 
                               style="color: #ff8a80;"
                               data-title="Retirer un article" 
                               data-content="Si vous ne voulez plus de cet article, cliquez sur la croix rouge."></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($panier as $id => $details)
                        <tr style="border-bottom: 1px solid #eee;">
                            {{-- Image et Nom --}}
                            <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                                <img src="{{ $details['photo'] }}" width="60" height="60" style="border-radius: 4px; object-fit: cover; border: 1px solid #ddd;">
                                <div>
                                    <strong style="color: #003366; font-size: 1.1em; display: block;">{{ $details['nom'] }}</strong>
                                    <div style="font-size: 0.9em; color: #666; margin-top: 5px;">
                                        @if(isset($details['taille'])) Taille: <strong>{{ strtoupper($details['taille']) }}</strong> @endif
                                        @if(isset($details['couleur'])) | Couleur: <strong>{{ ucfirst($details['couleur']) }}</strong> @endif
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Prix Unitaire --}}
                            <td style="padding: 15px; text-align: right; font-family: monospace; font-size: 1.1em;">
                                {{ number_format($details['prix'], 2) }} €
                            </td>
                            
                            {{-- Quantité --}}
                            <td style="padding: 15px; text-align: center;">
                                <form action="{{ route('panier.update', $id) }}" method="POST" style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantite" value="{{ $details['quantite'] }}" min="1" max="{{ $details['stock_max'] }}" 
                                           style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                                    
                                    <button type="submit" title="Mettre à jour" style="background: #326295; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; transition: background 0.2s;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                                <small style="color: #999; display: block; margin-top: 5px;">
                                    <i class="fas fa-box"></i> Stock dispo: {{ $details['stock_max'] }}
                                </small>
                            </td>
                            
                            {{-- Total Ligne --}}
                            <td style="padding: 15px; text-align: right; font-weight: bold; color: #326295; font-size: 1.1em;">
                                {{ number_format($details['prix'] * $details['quantite'], 2) }} €
                            </td>

                            {{-- Suppression --}}
                            <td style="padding: 15px; text-align: center;">
                                <a href="{{ route('panier.supprimer', $id) }}" 
                                   style="color: #e74c3c; font-size: 1.2rem; transition: transform 0.2s; display: inline-block;" 
                                   title="Supprimer cet article"
                                   onmouseover="this.style.transform='scale(1.2)'"
                                   onmouseout="this.style.transform='scale(1)'">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Résumé et Boutons --}}
        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            
            <a href="{{ route('panier.vider') }}" 
               onclick="return confirm('Êtes-vous sûr de vouloir tout supprimer ?');"
               style="color: #e74c3c; text-decoration: underline; padding: 10px;">
                <i class="fas fa-trash"></i> Vider tout le panier
            </a>
            
            <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: right; min-width: 350px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 1.1em; color: #555;">Sous-total :</span>
                    <h3 style="margin: 0; color: #326295; font-size: 1.8rem;">
                        {{ number_format($total, 2) }} €
                        <i class="fas fa-info-circle help-trigger" 
                           style="font-size: 0.5em; vertical-align: middle; color: #999;"
                           data-title="Information Prix" 
                           data-content="Ce prix ne comprend pas encore les frais de port. Ils seront calculés à l'étape suivante en fonction de votre adresse."></i>
                    </h3>
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 15px;">
                    <i class="fas fa-hand-point-right help-trigger" 
                       style="font-size: 2em; color: #2ecc71;"
                       data-title="Étape Suivante" 
                       data-content="Cliquez sur le bouton vert pour valider. Ne vous inquiétez pas, vous ne payez pas encore ! Vous devrez d'abord entrer votre adresse."></i>

                    <a href="{{ route('commande.livraison') }}" class="btn-fifa-primary" style="padding: 15px 40px; background-color: #2ecc71; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; text-transform: uppercase; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3); transition: transform 0.2s; display: inline-block;">
                        Valider la commande <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                    </a>
                </div>
                
                <div style="margin-top: 15px; font-size: 0.9em; color: #888;">
                    <i class="fas fa-lock"></i> Paiement 100% sécurisé
                </div>
            </div>
        </div>

    @else
        {{-- Panier Vide --}}
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="margin-bottom: 20px; color: #e0e0e0;">
                <i class="fas fa-shopping-basket" style="font-size: 6rem;"></i>
            </div>
            <h3 style="color: #666; font-size: 1.5rem; margin-bottom: 10px;">Votre panier est vide.</h3>
            <p style="color: #999; margin-bottom: 30px;">Vous n'avez pas encore sélectionné de produits.</p>
            
            <a href="{{ route('produits.index') }}" class="btn-fifa-primary" style="display: inline-block; padding: 15px 30px; background-color: #326295; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; transition: background 0.2s;">
                <i class="fas fa-arrow-left"></i> Retourner à la boutique
            </a>
            
            <div style="margin-top: 20px;">
                 <i class="fas fa-question-circle help-trigger" 
                    style="font-size: 1.5em; color: #00cfb7;"
                    data-title="Comment remplir mon panier ?" 
                    data-content="Allez dans la boutique, cliquez sur un produit (maillot, ballon...), choisissez votre taille et cliquez sur 'Ajouter au panier'."></i>
            </div>
        </div>
    @endif
</div>
@endsection