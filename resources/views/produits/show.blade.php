@extends('layout')

@section('content')

{{-- Calcul du stock total --}}
@php
    $stockTotalProduit = 0;
    foreach($produit->variantes as $variante) {
        foreach($variante->stocks as $stock) {
            // CORRECTION ICI : 'stock'
            $stockTotalProduit += $stock->stock;
        }
    }
@endphp

<div class="container" style="padding: 50px 0;">
    <div class="product-detail" style="display: flex; gap: 40px; flex-wrap: wrap;">
        
        {{-- Partie Image --}}
        <div class="product-image" style="flex: 1; min-width: 300px;">
            <img src="{{ asset($produit->premierePhoto->url_photo ?? 'img/placeholder.jpg') }}" 
                 alt="{{ $produit->nom_produit }}" 
                 style="width: 100%; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>

        {{-- Partie Infos --}}
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
            
            @if ($errors->any())
                <div style="background: #ffdbdb; color: #c0392b; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #c0392b;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="description" style="margin-bottom: 30px; line-height: 1.6; color: #555;">
                {{ $produit->description_produit }}
            </p>

            <form action="{{ route('panier.ajouter', $produit->id_produit) }}" method="POST" style="background: #f9f9f9; padding: 25px; border-radius: 8px;">
                @csrf 
                
                {{-- Choix Couleur --}}
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

                {{-- Choix Taille --}}
                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="id_taille" style="font-weight: bold; display: block; margin-bottom: 8px;">Taille :</label>
                    <select name="id_taille" id="id_taille" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                        <option value="">-- Choisir une taille --</option>
                        @if(isset($tailles) && $tailles->count() > 0)
                            @foreach($tailles as $taille)
                                {{-- Calcul du stock pour CETTE taille --}}
                                @php
                                    $qteTaille = 0;
                                    foreach($produit->variantes as $v) {
                                        $stk = $v->stocks->where('id_taille', $taille->id_taille)->first();
                                        // CORRECTION ICI : 'stock'
                                        if($stk) $qteTaille += $stk->stock;
                                    }
                                @endphp

                                <option value="{{ $taille->id_taille }}"
                                    {{ $qteTaille == 0 ? 'disabled' : '' }}
                                    {{ (isset($tailleSelectionnee) && strtoupper($tailleSelectionnee) == strtoupper($taille->type_taille)) ? 'selected' : '' }}>
                                    
                                    {{ strtoupper($taille->type_taille) }} 
                                    @if($qteTaille > 0) ({{ $qteTaille }} dispo) @else (Épuisé) @endif
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
</div>
@endsection