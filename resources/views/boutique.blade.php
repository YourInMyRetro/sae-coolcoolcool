@extends('layout')

@section('content')
<div class="container shop-page-container">

    {{-- Formulaire Global pour les filtres --}}
    <form action="{{ route('produits.index') }}" method="GET" id="filterForm" class="shop-layout">
        
        {{-- Conservation du terme de recherche si existant --}}
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        {{-- COLONNE GAUCHE : FILTRES (Sidebar) --}}
        <aside class="shop-sidebar">
            <div class="sidebar-header">
                <h3>Filtrer par 
    <a href="{{ route('aide') }}#boutique" target="_blank" style="font-size: 0.8em; color: #666;" 
       data-bs-toggle="tooltip" title="Comment utiliser les filtres cumulés ?">
        <i class="fas fa-question-circle"></i>
    </a>
</h3>
                <a href="{{ route('produits.index') }}" class="reset-filters">Tout effacer</a>
            </div>

           {{-- Filtre : Catégories (Accordéon Esthétique) --}}
            <div class="filter-group">
                <h4 class="filter-title">Catégories</h4>
                <div class="filter-options">
                    
                    <label class="custom-radio parent-cat" style="margin-bottom:10px; font-weight:bold;">
                        <input type="radio" name="categorie" value="" onchange="this.form.submit()" {{ request('categorie') == '' ? 'checked' : '' }}>
                        <span class="cat-name">TOUT VOIR</span>
                    </label>

                    @foreach($categoryGroups as $groupName => $categories)
                        <details class="category-accordion" open>
                            <summary class="parent-label">
                                {{ $groupName }} <i class="fas fa-chevron-down"></i>
                            </summary>
                            
                            <div class="subcategory-list">
                                @foreach($categories as $cat)
                                <label class="custom-radio">
                                    <input type="radio" name="categorie" value="{{ $cat->id_categorie }}" onchange="this.form.submit()" {{ request('categorie') == $cat->id_categorie ? 'checked' : '' }}>
                                    <span>{{ $cat->nom_categorie }}</span>
                                </label>
                                @endforeach
                            </div>
                        </details>
                    @endforeach

                </div>
            </div>

            {{-- Filtre : Nations --}}
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

            {{-- Filtre : Couleurs (Version Compacte) --}}
            <div class="filter-group">
                <h4 class="filter-title">Couleurs</h4>
                
                {{-- Option "Toutes" --}}
                <label class="custom-radio" style="margin-bottom: 10px; display: block;">
                    <input type="radio" name="couleur" value="" onchange="this.form.submit()" {{ request('couleur') == '' ? 'checked' : '' }}>
                    <span>Toutes les couleurs</span>
                </label>

                {{-- Grille de couleurs --}}
                <div class="color-grid" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($allColors as $c)
                    <label class="color-swatch" title="{{ ucfirst($c) }}" style="cursor: pointer; position: relative;">
                        <input type="radio" name="couleur" value="{{ $c }}" onchange="this.form.submit()" {{ request('couleur') == $c ? 'checked' : '' }} style="display: none;">
                        
                        {{-- La pastille de couleur --}}
                        <span style="
                            display: block; 
                            width: 25px; 
                            height: 25px; 
                            border-radius: 50%; 
                            background: {{ $c == 'Or' ? 'gold' : ($c == 'Multicolore' ? 'linear-gradient(135deg, red, blue, yellow)' : ($c == 'Argent' ? 'silver' : $c)) }};
                            border: 1px solid #ccc;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                            transition: transform 0.2s, box-shadow 0.2s;
                        "></span>

                        {{-- Indicateur de sélection (coche ou bordure) via CSS inline pour faire vite, ou classe active --}}
                        @if(request('couleur') == $c)
                            <span style="
                                position: absolute; 
                                top: -3px; left: -3px; right: -3px; bottom: -3px; 
                                border: 2px solid #326295; 
                                border-radius: 50%;
                            "></span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Filtre : Tailles --}}
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
            
            {{-- Barre supérieure (Titre + Tri) --}}
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

            {{-- Grille Produits --}}
            @if($produits->count() > 0)
                <div class="products-grid-shop">
                    @foreach($produits as $produit)

                    <div class="product-card shop-card" style="display: flex; flex-direction: column; height: 100%;">
                        
                        {{-- Le lien vers les détails (sur l'image et le texte uniquement) --}}
                        <a href="{{ route('produits.show', ['id' => $produit->id_produit, 'taille' => request('taille')]) }}" class="product-link" style="flex-grow: 1; text-decoration: none; color: inherit;">
                            <div class="product-image-wrapper">
                                @if($produit->premierePhoto)
                                    <img src="{{ $produit->premierePhoto->url_photo }}" alt="{{ $produit->nom_produit }}">
                                @else
                                    <div class="no-image">Pas d'image</div>
                                @endif
                            </div>
                            <div class="product-details" style="padding: 0 15px;">
                                <span class="product-category-small">
                                    {{ $produit->nations->first()->nom_nation ?? ($produit->categorie->nom_categorie ?? 'FIFA') }}
                                </span>
                                <h4 class="product-name">{{ $produit->nom_produit }}</h4>
                                <div class="product-price" style="margin-bottom: 10px;">
                                    {{ $produit->premierPrix ? number_format($produit->premierPrix->prix_total, 2) . ' €' : 'N/A' }}
                                </div>
                            </div>
                        </a>

                        {{-- BOUTON D'AJOUT AU PANIER --}}
                        {{-- NOUVEAU CODE --}}
                        <div style="padding: 0 15px 20px 15px; margin-top: auto;">
                            <a href="{{ route('produits.show', ['id' => $produit->id_produit]) }}" 
                            style="display: block; width: 100%; padding: 10px 0; background-color: #326295; color: white; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; border-radius: 4px; text-decoration: none; transition: background 0.2s;">
                                Détails <i class="fas fa-info-circle"></i>
                            </a>
                        </div>

                    </div>
                    
                    @endforeach
                </div>
            @else
                <div class="no-results">
                    <p>Aucun produit ne correspond à vos critères.</p>
                </div>
            @endif
        </main>
    </form>
</div>
@endsection