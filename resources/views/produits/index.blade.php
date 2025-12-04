@extends('layout')

@section('content')
<div class="container shop-page-container">

    {{-- Formulaire Global pour les filtres --}}
    <form action="{{ route('produits.index') }}" method="GET" id="filterForm" class="shop-layout">
        
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        {{-- COLONNE GAUCHE : FILTRES --}}
        <aside class="shop-sidebar">
            <div class="sidebar-header">
                <h3>Filtrer par</h3>
                <a href="{{ route('produits.index') }}" class="reset-filters">Tout effacer</a>
            </div>

            {{-- Filtre PRIX MAX --}}
            <div class="filter-group">
                <h4 class="filter-title">Budget Max (€)</h4>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Ex: 100" min="0" step="10"
                           style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="submit" style="background: #326295; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">OK</button>
                </div>
            </div>

            {{-- Filtre Catégories --}}
            <div class="filter-group">
                <h4 class="filter-title">Catégories</h4>
                <div class="filter-options">
                    <label class="custom-radio">
                        <input type="radio" name="categorie" value="" onchange="this.form.submit()" {{ request('categorie') == '' ? 'checked' : '' }}>
                        <span>Tout voir</span>
                    </label>
                    @foreach($allCategories as $cat)
                    <label class="custom-radio">
                        <input type="radio" name="categorie" value="{{ $cat->id_categorie }}" onchange="this.form.submit()" {{ request('categorie') == $cat->id_categorie ? 'checked' : '' }}>
                        <span>{{ $cat->nom_categorie }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Filtre Nations --}}
            <div class="filter-group">
                <h4 class="filter-title">Équipes / Nations</h4>
                <div class="filter-options">
                    <label class="custom-radio">
                        <input type="radio" name="nation" value="" onchange="this.form.submit()" {{ request('nation') == '' ? 'checked' : '' }}>
                        <span>Toutes les équipes</span>
                    </label>
                    @foreach($allNations as $nat)
                    <label class="custom-radio">
                        <input type="radio" name="nation" value="{{ $nat->id_nation }}" onchange="this.form.submit()" {{ request('nation') == $nat->id_nation ? 'checked' : '' }}>
                        <span>{{ $nat->nom_nation }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Filtre Couleurs --}}
            <div class="filter-group">
                <h4 class="filter-title">Couleurs</h4>
                <div class="filter-options">
                    <label class="custom-radio">
                        <input type="radio" name="couleur" value="" onchange="this.form.submit()" {{ request('couleur') == '' ? 'checked' : '' }}>
                        <span>Toutes</span>
                    </label>
                    @foreach($allColors as $c)
                    <label class="custom-radio">
                        <input type="radio" name="couleur" value="{{ $c }}" onchange="this.form.submit()" {{ request('couleur') == $c ? 'checked' : '' }}>
                        <span style="display:inline-block; width:12px; height:12px; background-color: {{ $c == 'Or' ? 'gold' : ($c == 'Multicolore' ? 'linear-gradient(to right, red,blue)' : $c) }}; border-radius:50%; margin-right:5px; border:1px solid #ddd;"></span>
                        <span>{{ ucfirst($c) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Filtre Tailles --}}
            <div class="filter-group">
                <h4 class="filter-title">Tailles</h4>
                <div class="size-grid">
                    @foreach($allSizes as $t)
                    <label class="size-box">
                        <input type="radio" name="taille" value="{{ $t }}" onchange="this.form.submit()" {{ request('taille') == $t ? 'checked' : '' }}>
                        <span>{{ strtoupper($t) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- COLONNE DROITE : RÉSULTATS --}}
        <main class="shop-results">
            
            <div class="results-header">
                <div class="results-count">
                    @if(request('search'))
                        Résultats pour "<strong>{{ request('search') }}</strong>" ({{ $produits->count() }})
                    @else
                        Tous les produits ({{ $produits->count() }})
                    @endif
                </div>
                <div class="sort-wrapper">
                    <label for="sort">Trier par :</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" class="sort-select">
                        <option value="">Pertinence</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    </select>
                </div>
            </div>

            @if($produits->count() > 0)
                <div class="products-grid-shop">
                    @foreach($produits as $produit)

                    <div class="product-card shop-card" style="display: flex; flex-direction: column; height: 100%;">
                        
                        <a href="{{ route('produits.show', ['id' => $produit->id_produit, 'taille' => request('taille')]) }}" class="product-link" style="flex-grow: 1; text-decoration: none; color: inherit;">
                            <div class="product-image-wrapper">
                                @if($produit->premierePhoto)
                                    <img src="{{ asset($produit->premierePhoto->url_photo) }}" alt="{{ $produit->nom_produit }}">
                                @else
                                    <div class="no-image" style="color:#ccc;">Pas d'image</div>
                                @endif
                            </div>
                            <div class="product-details" style="padding: 0 15px;">
                                <span class="product-category-small" style="font-size: 0.8rem; color: #777; text-transform: uppercase;">
                                    {{ $produit->nations->first()->nom_nation ?? ($produit->categorie->nom_categorie ?? 'FIFA') }}
                                </span>
                                <h4 class="product-name" style="margin: 5px 0;">{{ $produit->nom_produit }}</h4>
                                
                                {{-- CALCUL DU STOCK TOTAL --}}
                                @php
                                    $stockTotal = 0;
                                    foreach($produit->variantes as $v) {
                                        foreach($v->stocks as $s) {
                                            // CORRECTION ICI : 'stock' au lieu de 'quantite_stock'
                                            $stockTotal += $s->stock; 
                                        }
                                    }
                                @endphp

                                <div class="card-price" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="font-weight: bold; color: #326295;">{{ number_format($produit->premierPrix->prix_total ?? 0, 2) }} €</span>
                                    
                                    @if($stockTotal > 0)
                                        <span style="font-size: 0.75rem; color: green; background: #e8f5e9; padding: 2px 6px; border-radius: 4px;">Stock: {{ $stockTotal }}</span>
                                    @else
                                        <span style="font-size: 0.75rem; color: red; background: #ffebee; padding: 2px 6px; border-radius: 4px;">Rupture</span>
                                    @endif
                                </div>
                            </div>
                        </a>


                        <div style="padding: 0 15px 20px 15px; margin-top: auto;">
                            @if($stockTotal > 0)
                                <a href="{{ route('produits.show', ['id' => $produit->id_produit]) }}" 
                                   style="display: block; width: 100%; padding: 10px 0; background-color: #55e6c9; color: #0f2d4a; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; border-radius: 4px; text-decoration: none; transition: background 0.2s;">
                                    Choisir taille
                                </a>
                            @else
                                <button disabled style="display: block; width: 100%; padding: 10px 0; background-color: #ddd; color: #888; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; border-radius: 4px; border: none; cursor: not-allowed;">
                                    Indisponible
                                </button>
                            @endif
                        </div>
                        

                    </div>
                    @endforeach
                </div>
            @else
                <div class="no-results" style="text-align: center; padding: 50px;">
                    <i class="fas fa-search" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                    <p>Aucun produit ne correspond à vos critères.</p>
                    <a href="{{ route('produits.index') }}" style="text-decoration: underline; color: #326295;">Voir tous les produits</a>
                </div>
            @endif
        </main>
    </form>
</div>
@endsection