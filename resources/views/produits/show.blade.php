@extends('layout')

@section('content')

@php
    $stockTotalProduit = 0;
    foreach($produit->variantes as $variante) {
        foreach($variante->stocks as $stock) {
            $stockTotalProduit += $stock->stock;
        }
    }
@endphp

<div class="container" style="padding: 50px 0;">
    
    <div class="product-detail" style="display: flex; gap: 40px; flex-wrap: wrap; margin-bottom: 80px;">
        
        <div class="product-image" style="flex: 1; min-width: 300px;">
            <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                 alt="{{ $produit->nom_produit }}" 
                 style="width: 100%; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>

        <div class="product-info" style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">{{ $produit->nom_produit }}</h1>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <h3 style="color: #326295; font-size: 1.8rem; margin: 0;">
                    {{ number_format($produit->premierPrix->prix_total ?? 0, 2) }} €
                </h3>
                
                @if($stockTotalProduit > 0)
                    <span style="background: #27ae60; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                        <i class="fas fa-check"></i> {{ $stockTotalProduit }} en stock
                    </span>
                @else
                    <span style="background: #c0392b; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                        <i class="fas fa-times"></i> Rupture de stock
                    </span>
                @endif
            </div>
            
            <p class="description" style="margin-bottom: 30px; line-height: 1.6; color: #555;">
                {{ $produit->description_produit }}
            </p>

            <form action="{{ route('panier.ajouter', $produit->id_produit) }}" method="POST" style="background: #f9f9f9; padding: 25px; border-radius: 8px;">
                @csrf 
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="id_couleur" style="font-weight: bold; display: block; margin-bottom: 8px;">Couleur :</label>
                    <select name="id_couleur" id="id_couleur" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                        <option value="">-- Choisir une couleur --</option>
                        @foreach($produit->variantes as $variante)
                            <option value="{{ $variante->couleur->id_couleur }}">
                                {{ ucfirst($variante->couleur->type_couleur) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="id_taille" style="font-weight: bold; display: block; margin-bottom: 8px;">Taille :</label>
                    <select name="id_taille" id="id_taille" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                        <option value="">-- Choisir une taille --</option>
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
                                    {{ strtoupper($taille->type_taille) }} @if($qteTaille > 0) ({{ $qteTaille }} dispo) @else (Épuisé) @endif
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>Aucune taille détectée</option>
                        @endif
                    </select>
                </div>

                <button type="submit" class="btn-fifa-cta" style="width: 100%; border: none; cursor: pointer;">
                    Ajouter au panier <i class="fas fa-shopping-cart"></i>
                </button>
            </form>
        </div>
    </div>

    @if(isset($produitsSimilaires) && $produitsSimilaires->count() > 0)
    <div style="margin-bottom: 50px;">
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            Cela pourrait aussi vous plaire
        </h3>
        <div style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px;">
            @foreach($produitsSimilaires as $similaire)
                <div class="card" style="min-width: 200px; border: 1px solid #eee; border-radius: 8px;">
                    <a href="{{ route('produits.show', $similaire->id_produit) }}" style="text-decoration: none; color: inherit;">
                        <img src="{{ asset($similaire->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                             style="width: 100%; height: 150px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                        <div style="padding: 10px;">
                            <h5 style="font-size: 1rem; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $similaire->nom_produit }}
                            </h5>
                            <span style="font-weight: bold; color: #326295;">
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
        <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            Vous avez récemment consulté
        </h3>
        <div style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px;">
            @foreach($produitsVus as $vu)
                <div class="card" style="min-width: 200px; border: 1px solid #eee; border-radius: 8px; opacity: 0.8;">
                    <a href="{{ route('produits.show', $vu->id_produit) }}" style="text-decoration: none; color: inherit;">
                        <img src="{{ asset($vu->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                             style="width: 100%; height: 150px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                        <div style="padding: 10px;">
                            <h5 style="font-size: 1rem; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $vu->nom_produit }}
                            </h5>
                            <span style="color: #666; font-size: 0.9rem;">Revoir la fiche</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection