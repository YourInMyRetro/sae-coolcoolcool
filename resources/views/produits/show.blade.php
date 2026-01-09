@extends('layout')

@section('content')

@php
    // Calcul du stock total
    $stockTotalProduit = 0;
    foreach($produit->variantes as $variante) {
        foreach($variante->stocks as $stock) {
            $stockTotalProduit += $stock->stock;
        }
    }
    
    // Image principale
    $mainPhoto = $produit->photos->first() ? asset($produit->photos->first()->url_photo) : asset('img/placeholder.jpg');
@endphp

<div class="container" style="padding: 50px 20px;">
    
    <div style="margin-bottom: 20px; color: #888; font-size: 0.9em;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">Accueil</a> > 
        <a href="{{ route('produits.index') }}" style="color: #666; text-decoration: none;">Boutique</a> > 
        <span style="color: #326295; font-weight: bold;">{{ $produit->nom_produit }}</span>
    </div>

    <div class="product-detail" style="display: flex; gap: 50px; flex-wrap: wrap; margin-bottom: 80px;">
        
        <div class="product-image" style="flex: 1; min-width: 350px; max-width: 600px;">
            <div style="margin-bottom: 15px; position: relative;">
                <img id="mainImage" src="{{ $mainPhoto }}" 
                     alt="{{ $produit->nom_produit }}" 
                     onclick="openFullscreen()"
                     style="width: 100%; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); object-fit: cover; aspect-ratio: 1/1; cursor: zoom-in;">
                
                <div style="position: absolute; bottom: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem; color: #333; pointer-events: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <i class="fas fa-search-plus"></i> Cliquez pour zoomer
                </div>

                <div style="position: absolute; top: 10px; left: 10px;">
                    <i class="fas fa-camera help-trigger" 
                       style="background: white; padding: 5px; border-radius: 50%; width: 30px; height: 30px; text-align: center; line-height: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                       data-title="Photos du produit" 
                       data-content="Ce sont des photos réelles du produit officiel. Cliquez sur la grande image pour la voir en plein écran."></i>
                </div>
            </div>

            @if($produit->photos->count() > 1)
            <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
                @foreach($produit->photos as $photo)
                    <img src="{{ asset($photo->url_photo) }}" 
                         class="thumbnail-img"
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid transparent; opacity: 0.7; transition: all 0.2s;"
                         onclick="changeImage(this.src)">
                @endforeach
            </div>
            @endif
        </div>

        <div class="product-info" style="flex: 1; min-width: 350px;">
            
            <h1 style="font-size: 2.5rem; margin-bottom: 10px; color: #1a1a1a; line-height: 1.2;">
                {{ $produit->nom_produit }}
                <i class="fas fa-certificate help-trigger" 
                   style="font-size: 0.5em; vertical-align: middle; color: #326295;"
                   data-title="Produit Officiel" 
                   data-content="Ce badge garantit qu'il s'agit d'un produit authentique certifié par la FIFA. Pas de contrefaçon ici !"></i>
            </h1>
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px;">
                <h3 style="color: #326295; font-size: 2rem; margin: 0; font-weight: 800;">
                    {{ number_format($produit->premierPrix->prix_total ?? 0, 2) }} €
                    <i class="fas fa-tag help-trigger" 
                       style="font-size: 0.4em; color: #888; vertical-align: middle;"
                       data-title="Le Prix" 
                       data-content="Ce prix inclut la TVA (Toutes Taxes Comprises). Les frais de livraison seront ajoutés plus tard, au moment de choisir votre adresse."></i>
                </h3>
                
                @if($stockTotalProduit > 0)
                    <span style="background: #e8f5e9; color: #2e7d32; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; border: 1px solid #c8e6c9;">
                        <i class="fas fa-check-circle"></i> En stock
                        <i class="fas fa-info-circle help-trigger" 
                           style="color: #2e7d32; margin-left: 5px;"
                           data-title="Disponibilité" 
                           data-content="L'article est disponible dans nos entrepôts. Il peut être expédié dès demain !"></i>
                    </span>
                @else
                    <span style="background: #ffebee; color: #c62828; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; border: 1px solid #ffcdd2;">
                        <i class="fas fa-times-circle"></i> Rupture de stock
                    </span>
                @endif
            </div>
            
            <div class="description-block" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 30px;">
                <h4 style="margin-top: 0; color: #555;">
                    <i class="fas fa-align-left"></i> Description
                    <i class="fas fa-question-circle help-trigger" 
                       data-title="Détails du produit" 
                       data-content="Vous trouverez ici la composition (coton, polyester...), les conseils de lavage et l'histoire du produit."></i>
                </h4>
                <p style="margin-bottom: 0; line-height: 1.6; color: #666;">
                    {{ $produit->description_produit }}
                </p>
            </div>

            <form action="{{ route('panier.ajouter', $produit->id_produit) }}" method="POST" 
                  style="background: #f8faff; padding: 30px; border-radius: 12px; border: 2px solid #e1e8ed; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                @csrf 
                
                <h3 style="margin-top: 0; margin-bottom: 20px; color: #326295; font-size: 1.2rem; border-bottom: 1px solid #dceefb; padding-bottom: 10px;">
                    Configurer votre article
                </h3>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="id_couleur" style="font-weight: bold; display: block; margin-bottom: 8px; color: #333;">
                        1. Choisissez la couleur :
                        <i class="fas fa-palette help-trigger" 
                           data-title="Pourquoi choisir ?" 
                           data-content="Certains produits existent en plusieurs versions (ex: Domicile/Rouge ou Extérieur/Blanc). Sélectionnez celle que vous préférez."></i>
                    </label>
                    <select name="id_couleur" id="id_couleur" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; background: white;" required>
                        <option value="">-- Sélectionnez une couleur --</option>
                        @foreach($produit->variantes as $variante)
                            <option value="{{ $variante->couleur->id_couleur }}">
                                {{ ucfirst($variante->couleur->type_couleur) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="id_taille" style="font-weight: bold; display: block; margin-bottom: 8px; color: #333;">
                        2. Choisissez la taille :
                        <i class="fas fa-ruler-combined help-trigger" 
                           data-title="Guide des Tailles" 
                           data-content="S=Petit, M=Moyen, L=Grand. Si vous hésitez entre deux tailles, nous vous conseillons de prendre la plus grande pour être à l'aise."
                           data-link="{{ route('aide') }}#section-boutique"></i>
                    </label>
                    <select name="id_taille" id="id_taille" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; background: white;" required>
                        <option value="">-- Sélectionnez une taille --</option>
                        @if(isset($tailles) && $tailles->count() > 0)
                            @foreach($tailles as $taille)
                                @php
                                    $qteTaille = 0;
                                    foreach($produit->variantes as $v) {
                                        $stk = $v->stocks->where('id_taille', $taille->id_taille)->first();
                                        if($stk) $qteTaille += $stk->stock;
                                    }
                                @endphp
                                <option value="{{ $taille->id_taille }}"
                                    {{ $qteTaille == 0 ? 'disabled' : '' }}
                                    {{ (isset($tailleSelectionnee) && strtoupper($tailleSelectionnee) == strtoupper($taille->type_taille)) ? 'selected' : '' }}>
                                    {{ strtoupper($taille->type_taille) }} 
                                    @if($qteTaille > 0) (Disponible) @else (Épuisé) @endif
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>Taille unique</option>
                        @endif
                    </select>
                </div>

                <div style="display: flex; align-items: center; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn-fifa-cta" 
                            style="flex-grow: 1; border: none; cursor: pointer; background: #326295; color: white; padding: 15px 20px; font-size: 1.1rem; font-weight: bold; border-radius: 8px; text-transform: uppercase; transition: background 0.3s; box-shadow: 0 4px 6px rgba(50, 98, 149, 0.3);">
                        <i class="fas fa-cart-plus"></i> Ajouter au panier
                    </button>
                    
                    <div style="text-align: center;">
                        <i class="fas fa-hand-point-left help-trigger" 
                           style="font-size: 2em; color: #e67e22; cursor: help;"
                           data-title="C'est ici pour acheter !" 
                           data-content="Cliquez sur ce gros bouton bleu. L'article sera ajouté à votre panier (en haut à droite). Vous ne paierez pas tout de suite, vous pourrez continuer vos achats."
                           data-link="{{ route('aide') }}#section-panier"></i>
                        <div style="font-size: 0.7em; color: #888; margin-top: 5px;">Aide achat</div>
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 20px; justify-content: center; color: #666; font-size: 0.9rem;">
                    <span><i class="fas fa-truck" style="color: #326295;"></i> Livraison rapide</span>
                    <span><i class="fas fa-lock" style="color: #326295;"></i> Paiement sécurisé</span>
                    <span><i class="fas fa-undo" style="color: #326295;"></i> Retours gratuits</span>
                </div>

            </form>
        </div>
    </div>

    @if(isset($produitsSimilaires) && $produitsSimilaires->count() > 0)
    <div style="margin-bottom: 50px;">
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #333;">
            Cela pourrait aussi vous plaire
            <i class="fas fa-lightbulb help-trigger" 
               data-title="Suggestions" 
               data-content="Notre algorithme a sélectionné ces produits car ils ressemblent à celui que vous regardez (même équipe ou même type de vêtement)."></i>
        </h3>
        <div style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px;">
            @foreach($produitsSimilaires as $similaire)
                <div class="card" style="min-width: 220px; border: 1px solid #eee; border-radius: 8px; transition: transform 0.2s; background: white;">
                    <a href="{{ route('produits.show', $similaire->id_produit) }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div style="height: 200px; overflow: hidden; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <img src="{{ asset($similaire->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                        </div>
                        <div style="padding: 15px;">
                            <h5 style="font-size: 1rem; margin: 0 0 10px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #333;">
                                {{ $similaire->nom_produit }}
                            </h5>
                            <span style="font-weight: bold; color: #326295; font-size: 1.1rem;">
                                {{ number_format($similaire->premierPrix->prix_total ?? 0, 2) }} €
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($produitsVus) && $produitsVus->count() > 0)
    <div>
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #777; font-size: 1.2rem;">
            <i class="fas fa-history"></i> Vous avez récemment consulté
            <i class="fas fa-info-circle help-trigger" 
               data-title="Historique" 
               data-content="Pratique pour retrouver un article que vous avez regardé il y a 5 minutes !"></i>
        </h3>
        <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px;">
            @foreach($produitsVus as $vu)
                <div class="card" style="min-width: 180px; border: 1px solid #eee; border-radius: 8px; opacity: 0.8; background: white;">
                    <a href="{{ route('produits.show', $vu->id_produit) }}" style="text-decoration: none; color: inherit;">
                        <img src="{{ asset($vu->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                             style="width: 100%; height: 120px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                        <div style="padding: 10px;">
                            <h5 style="font-size: 0.9rem; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #555;">
                                {{ $vu->nom_produit }}
                            </h5>
                            <span style="color: #888; font-size: 0.8rem; text-decoration: underline;">Revoir la fiche</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<div id="fullscreenOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 999999; justify-content: center; align-items: center; cursor: zoom-out;" onclick="closeFullscreen()">
    <img id="fullscreenImage" src="" style="max-width: 95%; max-height: 95%; border-radius: 5px; box-shadow: 0 0 30px rgba(0,0,0,0.8);">
    <div style="position: absolute; top: 20px; right: 20px; color: white; font-size: 2rem; cursor: pointer;">&times;</div>
    <div style="position: absolute; bottom: 20px; color: white; font-size: 1rem;">Cliquez n'importe où pour fermer</div>
</div>

<script>
    function changeImage(src) {
        // Change l'image principale
        document.getElementById('mainImage').src = src;
        
        // Gère la classe active sur les miniatures
        const thumbnails = document.querySelectorAll('.thumbnail-img');
        thumbnails.forEach(img => {
            img.style.borderColor = 'transparent';
            img.style.opacity = '0.7';
        });
        
        event.target.style.borderColor = '#326295';
        event.target.style.opacity = '1';
    }

    function openFullscreen() {
        const src = document.getElementById('mainImage').src;
        document.getElementById('fullscreenImage').src = src;
        document.getElementById('fullscreenOverlay').style.display = 'flex';
    }

    function closeFullscreen() {
        document.getElementById('fullscreenOverlay').style.display = 'none';
    }
</script>
@endsection